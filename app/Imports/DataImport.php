<?php

namespace App\Imports;

use App\Models\Data;
use App\Models\User;
use App\Models\EnquirySource;
use App\Models\Industry;
use App\Models\Country;
use App\Models\Emirate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class DataImport implements ToCollection, WithHeadingRow
{
    protected array $rowErrors = [];

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {

            foreach ($rows as $index => $row) {

                $rowNumber = $index + 2; // Excel row number (with header)

                // if($rowNumber == 2){
                //     echo '<pre>';
                //     print_r($row);
                //     die;
                // }

                $required = ['entry_date', 'source', 'company_name', 'contact_1_name'];
                foreach ($required as $field) {
                    if (empty($row[$field])) {
                        $this->rowErrors[] = [
                            'row' => $rowNumber,
                            'error' => "Field '{$field}' is required"
                        ];
                        continue 2; 
                    }
                }
                
                // Lookups
                $sourceId = EnquirySource::where('name', trim($row['source']))->value('id');
                $industryId = Industry::where('name', trim($row['industry']))->value('id');
                $countryId = Country::where('name', trim($row['country']))->value('id');
                $emirateId = Emirate::where('name', trim($row['emirate']))->value('id');

                if (!$sourceId) {
                    $this->rowErrors[] = ['row' => $rowNumber, 'error' => "Invalid source"];
                    continue;
                }

                // Generate data code safely
                $dataCode = $this->generateDataCode();

                $entryDate = $last_updated = $next_followup = null;

                if (!empty($row['entry_date'])) {
                    $entryDate = Date::excelToDateTimeObject($row['entry_date'])->format('Y-m-d');
                }

                if (!empty($row['last_updated_date'])) {
                    $last_updated = Date::excelToDateTimeObject($row['last_updated_date'])->format('Y-m-d');
                }

                if (!empty($row['next_followup_date'])) {
                    $next_followup = Date::excelToDateTimeObject($row['next_followup_date'])->format('Y-m-d');
                }

                // Create main data
                $data = Data::create([
                    'data_code'       => $dataCode,
                    'entry_date'      => $entryDate ?? NULL,
                    'company_name'    => trim($row['company_name']) ?? null,
                    'company_email'   => trim($row['company_email']) ?? null,
                    'company_address' => trim($row['company_address']) ?? null,
                    'industry_id'     => $industryId ?? NULL,
                    'website_link'    => trim($row['website_link']) ?? null,
                    'company_country' => $countryId ?? null,
                    'emirate'         => $emirateId ?? null,
                    'google_location' => trim($row['google_map_link']) ?? null,
                    'requirement'     => trim($row['requirement']) ?? null,
                    'status'          => trim($row['status']) ?? 'to_be_contacted',
                    'source_id'       => $sourceId ?? NULL,
                    'sales_person'    => auth()->user()->id,
                    'last_updated'    => $last_updated ?? NULL,
                    'next_followup'   => $next_followup ?? NULL,
                    'last_comment'    => trim($row['comment']) ?? NULL
                ]);

                // Store contacts
                $this->storeContacts($data, $row);

                // Status history
                $data->statusHistories()->create([
                    'status'      => $data->status,
                    'status_date' => $data->entry_date,
                    'changed_by'  => auth()->id(),
                    'comment'    => trim($row['comment']) ?? NULL,
                    'followup_date' => $next_followup ?? NULL,
                ]);
            }

        });
    }

    protected function storeContacts(Data $data, $row)
    {
        for ($i = 1; $i <= 3; $i++) {
            if (!empty($row["contact_{$i}_name"])) {
                $data->contacts()->create([
                    'name'            => trim($row["contact_{$i}_name"]) ?? NULL,
                    'email'           => trim($row["contact_{$i}_email"]) ?? null,
                    'landline_number' => trim($row["contact_{$i}_landline"]) ?? null,
                    'mobile_number'   => trim($row["contact_{$i}_mobile"]) ?? null,
                    'whatsapp_number' => trim($row["contact_{$i}_whatsapp"]) ?? null,
                    'designation'     => trim($row["contact_{$i}_designation"]) ?? null,
                    'is_primary'      => ($i == 1) ? 1 : 0,
                ]);
            }
        }
    }

    protected function generateDataCode(): string
    {
        $lastId = Data::lockForUpdate()->max('id') ?? 0;
        return 'DATA' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
    }

    public function getRowErrors(): array
    {
        return $this->rowErrors;
    }
}
