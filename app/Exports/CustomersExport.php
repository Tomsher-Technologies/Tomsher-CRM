<?php
namespace App\Exports;

use App\Models\Customer;
use App\Models\Industry;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Carbon\Carbon;

class CustomersExport implements FromCollection, WithHeadings, WithStyles, WithEvents, WithCustomStartCell
{
    protected $request;
    protected $customers;
    protected $maxContacts = 1;

    public function __construct($request)
    {
        $this->request = $request;
        $this->loadCustomers();
    }

    protected function loadCustomers()
    {
        $request = $this->request;

        $query = Customer::with([
            'contacts',
            'projects',
            'enquiries',
            'sale_person',
            'industry',
            'country',
            'uae_emirate'
        ]);

        // Apply SAME filters as listing
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('customer_code', 'like', "%$keyword%")
                  ->orWhere('company_name', 'like', "%$keyword%")
                  ->orWhere('company_email', 'like', "%$keyword%")
                  ->orWhere('company_country', 'like', "%$keyword%");
            });
        }

        if ($request->filled('industry')) {
            $industryfilter = $request->industry;
            $childIds = [$industryfilter];

            $children = Industry::where('parent_id', $industryfilter)->pluck('id')->toArray();
            $childIds = array_merge($childIds, $children);

            $query->whereIn('industry_id', $childIds);
        }

        if ($request->filled('user_id')) {
            $query->where('sales_person', $request->user_id);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $this->customers = $query->get();

        // Calculate maximum contact count across all matching customers
        $max = 0;
        foreach ($this->customers as $cust) {
            $count = $cust->contacts->count();
            if ($count > $max) {
                $max = $count;
            }
        }
        $this->maxContacts = $max > 0 ? $max : 1;
    }

    public function startCell(): string
    {
        return 'A2'; // Headings will start from row 2
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }

    public function headings(): array
    {
        $headers = [
            'Customer Code',
            'Company Name',
            'Email',
            'Industry',
            'Website',
            'Country',
            'Emirate',
            'Address',
            'Google Location',
            'New To Company',
            'Projects Count',
            'Enquiries Count',
            'Sales Person',
            'Status',
            'Created At'
        ];

        // Add contact columns dynamically
        for ($i = 1; $i <= $this->maxContacts; $i++) {
            $prefix = $i === 1 ? 'Primary Contact' : 'Contact ' . $i;
            $headers[] = $prefix . ' Name';
            $headers[] = $prefix . ' Email';
            $headers[] = $prefix . ' Mobile';
            $headers[] = $prefix . ' Landline';
            $headers[] = $prefix . ' WhatsApp';
            $headers[] = $prefix . ' Designation';
        }

        return $headers;
    }

    public function collection()
    {
        return $this->customers->map(function ($cust) {
            // Sort contacts to make primary contact first
            $contacts = $cust->contacts->sortByDesc('is_primary')->values();

            $row = [
                'Customer Code' => $cust->customer_code,
                'Company Name' => $cust->company_name,
                'Email' => $cust->company_email,
                'Industry' => $cust->industry->name ?? '',
                'Website' => $cust->website_link,
                'Country' => $cust->country->name ?? '',
                'Emirate' => $cust->uae_emirate->name ?? '',
                'Address' => $cust->company_address,
                'Google Location' => $cust->google_location,
                'New To Company' => $cust->ntc ? 'Yes' : 'No',
            ];
            // Fill remaining fields
            $row['Projects Count'] = $cust->projects->count() ?? 0;
            $row['Enquiries Count'] = $cust->enquiries->count() ?? 0;
            $row['Sales Person'] = optional($cust->sale_person)->name;
            $row['Status'] = $cust->is_active ? 'Active' : 'Inactive';
            $row['Created At'] = $cust->created_at;

            // Fill contact info
            for ($i = 0; $i < $this->maxContacts; $i++) {
                $prefix = $i === 0 ? 'Primary Contact' : 'Contact ' . ($i + 1);
                $contact = $contacts->get($i);

                $row[$prefix . ' Name'] = $contact ? $contact->name : '';
                $row[$prefix . ' Email'] = $contact ? $contact->email : '';
                $row[$prefix . ' Mobile'] = $contact ? $contact->mobile_number : '';
                $row[$prefix . ' Landline'] = $contact ? $contact->landline_number : '';
                $row[$prefix . ' WhatsApp'] = $contact ? $contact->whatsapp_number : '';
                $row[$prefix . ' Designation'] = $contact ? $contact->designation : '';
            }

            return $row;
        });
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn();
                $highestRow = $sheet->getHighestRow();

                // Add exported date/time in first row
                $sheet->setCellValue('A1', 'Exported on: ' . Carbon::now()->format('d-M-Y H:i A'));
                $sheet->getStyle("A1:H1")->getFont()->setBold(true)->setSize(12);
                
                // Merge cells A1 to the last column
                $sheet->mergeCells("A1:H1");

                // Center the merged cell and italicize
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                // $sheet->getStyle('A1')->getFont()->setItalic(true);

                // Enable word wrap for all columns
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->getAlignment()->setWrapText(true);

                // Make heading row bold and italicized
                $sheet->getStyle("A2:{$highestColumn}2")->getFont()->setBold(true)->setSize(11)->setItalic(true);

                // Set column widths
                // Base columns
                $sheet->getColumnDimension('A')->setWidth(15); // Customer Code
                $sheet->getColumnDimension('B')->setWidth(30); // Company Name
                $sheet->getColumnDimension('C')->setWidth(25); // Email
                $sheet->getColumnDimension('D')->setWidth(20); // Industry
                $sheet->getColumnDimension('E')->setWidth(20); // Website
                $sheet->getColumnDimension('F')->setWidth(20); // Country
                $sheet->getColumnDimension('G')->setWidth(20); // Emirate
                $sheet->getColumnDimension('H')->setWidth(30); // Address
                $sheet->getColumnDimension('I')->setWidth(40); // Google Location
                $sheet->getColumnDimension('J')->setWidth(20); // New To Company
                $sheet->getColumnDimension('K')->setWidth(15); // Projects Count
                $sheet->getColumnDimension('L')->setWidth(15); // Enquiries Count
                $sheet->getColumnDimension('M')->setWidth(20); // Sales Person
                $sheet->getColumnDimension('N')->setWidth(15); // Status
                $sheet->getColumnDimension('O')->setWidth(20); // Created At

                // Dynamic contact columns (columns 11 to 10 + maxContacts * 6)
                for ($col = 16; $col <= 15 + ($this->maxContacts * 6); $col++) {
                    $letter = Coordinate::stringFromColumnIndex($col);
                    $sheet->getColumnDimension($letter)->setWidth(25);
                }

                // Remaining columns
                $remainingStart = 16 + ($this->maxContacts * 6);
                

                // Center align specific columns
                $sheet->getStyle('A2:A'.$highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Column A (Customer Code)
                $sheet->getStyle('J2:J'.$highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Column J (New To Company)

                // Align remaining count and status columns to center
                for ($i = 0; $i < 5; $i++) {
                    $colLetter = Coordinate::stringFromColumnIndex($remainingStart + $i);
                    $sheet->getStyle($colLetter.'2:'.$colLetter.$highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            },
        ];
    }
}
