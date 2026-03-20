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
use Carbon\Carbon;

class CustomersExport implements FromCollection, WithHeadings, WithStyles, WithEvents, WithCustomStartCell
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function startCell(): string
    {
        return 'A2'; // Headings will start from row 2
    }

    public function styles(Worksheet $sheet)
    {
        // $sheet->getDefaultRowDimension()->setRowHeight(-1);
        return [
            'K' => [ // Change column if needed
                'alignment' => [
                    'wrapText' => true,
                ],
            ],
        ];
    }

    public function headings(): array
    {
        return [
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
            'All Contacts',
            // 'Primary Contact',
            // 'Primary Mobile',
            'Projects Count',
            'Enquiries Count',
            'Sales Person',
            'Status',
            'Created At',
        ];
    }
    public function collection()
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

        // 🔍 Apply SAME filters as listing
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

        return $query->get()->map(function ($cust) {

            // 🔹 Combine all contacts into one string
           $contactDetails = $cust->contacts->map(function ($c) {

                                return collect([
                                    $c->name ? 'Name: ' . $c->name : null,
                                    $c->email ? 'Email: ' . $c->email : null,
                                    $c->mobile_number ? 'Mobile: ' . $c->mobile_number : null,
                                    $c->landline_number ? 'Landline: ' . $c->landline_number : null,
                                    $c->whatsapp_number ? 'WhatsApp: ' . $c->whatsapp_number : null,
                                    $c->designation ? 'Designation: ' . $c->designation : null,
                                    isset($c->is_primary) ? 'Primary: ' . ($c->is_primary ? 'Yes' : 'No') : null,
                                ])
                                ->filter()
                                ->implode("\n");

                            })
                            ->filter() // remove empty contacts
                            ->map(fn($item) => trim($item)) // ✅ REMOVE extra spaces/newlines
                            ->implode("\n------------------------------------------------------\n"); // join cleanly // separator between contacts

            return [
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

                'All Contacts' => trim($contactDetails), // combined contacts info
                // 'Primary Contact' => optional($cust->main_contact)->name,
                // 'Primary Mobile' => optional($cust->main_contact)->mobile_number,

                'Projects Count' => $cust->projects->count() ?? 0,
                'Enquiries Count' => $cust->enquiries->count()  ?? 0,

                'Sales Person' => optional($cust->sale_person)->name,
                'Status' => $cust->is_active ? 'Active' : 'Inactive',
                'Created At' => $cust->created_at,
            ];
        });
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Add exported date/time in first row
                $sheet->setCellValue('A1', 'Exported on: ' . Carbon::now()->format('d-M-Y H:i:s'));
                $sheet->getStyle('A1')->getFont()->setItalic(true);

                // Merge cells A1 to H1 (adjust H to the last column you need)
                $sheet->mergeCells('A1:H1');

                // Center the merged cell
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A1')->getFont()->setItalic(true);

                // Enable word wrap for all columns
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle("A1:P$highestRow")->getAlignment()->setWrapText(true);

                // Make heading row bold
                $sheet->getStyle('A2:P2')->getFont()->setBold(true)->setSize(11);

                // Optional: italicize exported date
                $sheet->getStyle('A2:P2')->getFont()->setItalic(true);

                // Set fixed width for columns
                $sheet->getColumnDimension('A')->setWidth(15);
                $sheet->getColumnDimension('B')->setWidth(30);
                $sheet->getColumnDimension('C')->setWidth(25);
                $sheet->getColumnDimension('D')->setWidth(20);
                $sheet->getColumnDimension('E')->setWidth(20);
                $sheet->getColumnDimension('F')->setWidth(20);
                $sheet->getColumnDimension('G')->setWidth(20);
                $sheet->getColumnDimension('H')->setWidth(30);
                $sheet->getColumnDimension('I')->setWidth(40);
                $sheet->getColumnDimension('J')->setWidth(20);
                $sheet->getColumnDimension('K')->setWidth(40);
                $sheet->getColumnDimension('L')->setWidth(20);
                $sheet->getColumnDimension('M')->setWidth(20);
                $sheet->getColumnDimension('N')->setWidth(20);
                $sheet->getColumnDimension('O')->setWidth(10);
                $sheet->getColumnDimension('P')->setWidth(20);

                $sheet->getStyle('A2:A'.$highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Column A
                $sheet->getStyle('L2:L'.$highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Column L
                $sheet->getStyle('J2:J'.$highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Column J
                $sheet->getStyle('M2:M'.$highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Column M
                $sheet->getStyle('N2:N'.$highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Column N
                $sheet->getStyle('O2:O'.$highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Column O
                $sheet->getStyle('P2:P'.$highestRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER); // Column P

            },
        ];
    }
}
