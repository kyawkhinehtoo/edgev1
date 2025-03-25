<?php

namespace App\Exports;


use App\Models\Township;
use App\Models\Package;
use App\Models\Project;
use App\Models\User;
use App\Models\Status;
use App\Models\Bills;
use App\Models\BillingTemp;
use App\Models\TempBill;
use App\Models\TempInvoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat as StyleNumberFormat;
class TempBillingExport implements FromQuery, WithMapping,WithHeadings,WithColumnFormatting,ShouldAutoSize
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $request;
  
    public function query()
    {
        $billings = TempInvoice::query()
        ->with(['tempBill','isp']);
        return $billings;
    
    }
    public function headings(): array
    {
        return [
            'ISP',
            'Bill Number',
            'Bill Month',
            'Date Issued',
            'Due Date',
            'MRC Customer',
            'MRC Amount',
            'New Customer',
            'Installation Amount',
            'Additional Charges',
            'Additional Amount',
            'Sub Total',
            'Discount',
            'Tax',
            'Total',
            
        ];
    }
    public function columnFormats(): array
    {
        return [
            'C' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'D' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'E' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => StyleNumberFormat::FORMAT_NUMBER,
            'H' => StyleNumberFormat::FORMAT_NUMBER,
            'G' => StyleNumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => StyleNumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => StyleNumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => StyleNumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'M' => StyleNumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => StyleNumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => StyleNumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
    public function map($billing): array
    {

        return [
            $billing->isp?->name,// ISP',
            $billing->tempBill?->bill_number,// Bill Number
            $billing->tempBill?->billing_period,// Bill Month
            $billing->issue_date,// Date Issued
            $billing->due_date,// Due Date
            $billing->total_mrc_customer,// MRC Customer
            $billing->total_mrc_amount,// MRC Amount
            $billing->total_new_customer,// New Customer
            $billing->total_installation_amount,// Installation Amount
            $billing->additional_description, //Other Charges Description',
            $billing->additional_fees, //Other Charges Amount',
            $billing->sub_total,// Sub Total
            $billing->discount_amount,// Discount',
            $billing->tax_amount,// Tax',
            $billing->total_amount,// Total',
         ];
    }
}
