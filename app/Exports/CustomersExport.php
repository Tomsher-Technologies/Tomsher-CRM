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
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

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

    protected function getFiltersString()
    {
        $request = $this->request;
        $filters = [];

        if ($request->filled('keyword')) {
            $filters[] = "Keyword: " . $request->keyword;
        }

        if ($request->filled('industry')) {
            $industry = Industry::find($request->industry);
            if ($industry) {
                $filters[] = "Industry: " . $industry->name;
            }
        }

        if ($request->filled('user_id')) {
            $user = \App\Models\User::find($request->user_id);
            if ($user) {
                $filters[] = "Sales Person: " . $user->name;
            }
        }

        if ($request->filled('is_active')) {
            $filters[] = "Status: " . ($request->is_active == '1' ? 'Active' : 'Inactive');
        }

        if ($request->filled('ntc')) {
            $filters[] = "New To Company: " . ($request->ntc == '1' ? 'Yes' : 'No');
        }

        if ($request->filled('date_range')) {
            $filters[] = "Created Date: " . $request->date_range;
        }

        return count($filters) > 0 ? "Filters: " . implode('  •  ', $filters) : "Filters: All Customers";
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

        if (auth()->user()->user_type !== 'admin') {
            $allowedIds = auth()->user()->getAllowedUserIds();
            $query->whereIn('sales_person', $allowedIds);
        }

        if ($request->filled('user_id')) {
            $userId = $request->user_id;
            if (auth()->user()->user_type === 'admin' || in_array($userId, auth()->user()->getAllowedUserIds())) {
                $query->where('sales_person', $userId);
            } else {
                $query->where('sales_person', 0);
            }
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('ntc')) {
            $query->where('ntc', $request->ntc);
        }

        if ($request->filled('date_range')) {
            $date = $request->date_range;
            [$fromRaw, $toRaw] = explode(" to ", $date);
            $from = Carbon::createFromFormat('d-m-Y', trim($fromRaw))->startOfDay();
            $to   = Carbon::createFromFormat('d-m-Y', trim($toRaw))->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
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
        return 'A4'; // Headings will start from row 4
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

                // Set row heights
                $sheet->getRowDimension(1)->setRowHeight(30);
                $sheet->getRowDimension(2)->setRowHeight(22);
                $sheet->getRowDimension(3)->setRowHeight(22);
                $sheet->getRowDimension(4)->setRowHeight(28);

                // Add report name in first row
                $sheet->setCellValue('A1', 'Customer Report');
                $sheet->mergeCells("A1:H1");
                $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("A1:H1")->getFont()->setBold(true)->setSize(12)->getColor()->setARGB('FF0F172A');

                // Add exported date/time in second row
                $sheet->setCellValue('A2', 'Exported on: ' . Carbon::now()->format('d-M-Y h:i A'));
                $sheet->mergeCells("A2:H2");
                $sheet->getStyle('A2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("A2:H2")->getFont()->setItalic(true)->setSize(10);
                
                // Add applied filters in third row with RichText to bold filter values
                $richText = new RichText();
                // $richText->createText('Filters-> ');
                
                $filtersCount = 0;
                $request = $this->request;

                if ($request->filled('keyword')) {
                    if ($filtersCount > 0) $richText->createText('  •  ');
                    $richText->createText('Keyword: ');
                    $run = $richText->createTextRun($request->keyword);
                    $run->getFont()->setBold(true)->setItalic(true)->getColor()->setARGB('FF64748B');
                    $filtersCount++;
                }

                if ($request->filled('industry')) {
                    $industry = Industry::find($request->industry);
                    if ($industry) {
                        if ($filtersCount > 0) $richText->createText('  •  ');
                        $richText->createText('Industry: ');
                        $run = $richText->createTextRun($industry->name);
                        $run->getFont()->setBold(true)->setItalic(true)->getColor()->setARGB('FF64748B');
                        $filtersCount++;
                    }
                }

                if ($request->filled('user_id')) {
                    $user = \App\Models\User::find($request->user_id);
                    if ($user) {
                        if ($filtersCount > 0) $richText->createText('  •  ');
                        $richText->createText('Sales Person: ');
                        $run = $richText->createTextRun($user->name);
                        $run->getFont()->setBold(true)->setItalic(true)->getColor()->setARGB('FF64748B');
                        $filtersCount++;
                    }
                }

                if ($request->filled('is_active')) {
                    if ($filtersCount > 0) $richText->createText('  •  ');
                    $richText->createText('Status: ');
                    $run = $richText->createTextRun($request->is_active == '1' ? 'Active' : 'Inactive');
                    $run->getFont()->setBold(true)->setItalic(true)->getColor()->setARGB('FF64748B');
                    $filtersCount++;
                }

                if ($filtersCount === 0) {
                    $run = $richText->createTextRun('All Customers');
                    $run->getFont()->setBold(true)->setItalic(true)->getColor()->setARGB('FF64748B');
                }

                $sheet->setCellValue('A3', $richText);
                $sheet->mergeCells("A3:H3");
                $sheet->getStyle('A3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle("A3:H3")->getFont()->setItalic(false)->setSize(10)->getColor()->setARGB('FF000000');

                // Apply soft background fill to metadata rows (1 to 3)
                $sheet->getStyle("A1:H3")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF8FAFC');

                // Add thin bottom border to Row 3
                $sheet->getStyle("A3:H3")->getBorders()->getBottom()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()->setARGB('FFCBD5E1');

                // Enable word wrap for all columns
                $sheet->getStyle("A1:{$highestColumn}{$highestRow}")->getAlignment()->setWrapText(true);

                // Make heading row bold and italicized (Row 4)
                $sheet->getStyle("A4:{$highestColumn}4")->getFont()->setBold(true)->setSize(11)->setItalic(true);

                // Enable interactive AutoFilter on header row (Row 4)
                // $sheet->setAutoFilter("A4:{$highestColumn}4");

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

                // Dynamic contact columns (columns 16 onwards)
                for ($col = 16; $col <= 15 + ($this->maxContacts * 6); $col++) {
                    $letter = Coordinate::stringFromColumnIndex($col);
                    $sheet->getColumnDimension($letter)->setWidth(25);
                }

                // Center align specific columns
                $sheet->getStyle('A4:A'.$highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Column A (Customer Code)
                $sheet->getStyle('J4:J'.$highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Column J (New To Company)

                // Align count and status columns (K to O -> columns 11 to 15) to center
                for ($col = 11; $col <= 15; $col++) {
                    $colLetter = Coordinate::stringFromColumnIndex($col);
                    $sheet->getStyle($colLetter.'4:'.$colLetter.$highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }
            },
        ];
    }
}
