<?php

namespace App\Exports;


use App\Models\Township;
use App\Models\Package;
use App\Models\Project;
use App\Models\User;
use App\Models\Status;
use App\Models\Bills;
use App\Models\BillingTemp;
use App\Models\InvoiceItem;
use App\Models\TempBill;
use App\Models\TempInvoice;
use App\Models\TempInvoiceItem;
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
class BillingItemExport implements FromQuery, WithMapping,WithHeadings,WithColumnFormatting,ShouldAutoSize
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }
  
    public function query()
    {   
        $request = $this->request;

        $billings = InvoiceItem::query()
        ->with(['invoice','customer','invoice.isp','customer.package','invoice.bill'])
        ->where('invoice_id',$request->id);
       // dd($billings->toSQL());
        return $billings;
    
    }
    public function headings(): array
    {
        return [
            'Bill Name',
            'Bill Number',
            'Invoice Number',
            'Bill Month',
            'Date Issued',
            'Due Date',
            'Exchange Rate',
            'Start Date',
            'End Date',
            'ISP Name',
            'FTTH ID',
            'Customer Name',
            'Package Name',
            'Package MRC Price',
            'Package OTC Price',
            'Package Installation Timeline',
            'Invoice Type',
            'Unit Price',
            'Total Amount',
            'Remark',
        
        ];
    }
    public function columnFormats(): array
    {
        return [
            'D' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'E' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'F' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'H' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'I' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'N' => StyleNumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => StyleNumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'R' => StyleNumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'S' => StyleNumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
    public function map($billing): array
    {

        return [
            $billing->invoice->bill->name, //Bill Name',
            $billing->invoice->bill->bill_number, //Bill Number',
            $billing->invoice->invoice_number, //Bill Month',
            $billing->invoice->bill->billing_period, //Bill Month',
            $billing->invoice->issue_date, //Date Issued',
            $billing->invoice->due_date, //Due Date',
            $billing->invoice->bill->exchange_rate, //Exchange Rate',
            $billing->start_date, //Start Date',
            $billing->end_date, //End Date',
            $billing->invoice->isp->name, //ISP Name',
            $billing->customer->ftth_id, //FTTH ID',
            $billing->customer->name, //Customer Name',
            $billing->customer->package->name, //Package Name',
            $billing->customer->package->price, //Package MRC Price
            $billing->customer->package->otc, //Package OTC Price
            $billing->customer->package->installation_timeline, //Package Installation Timeline
            $billing->type, //Invoice Type',
            $billing->unit_price, //Unit Price',
            $billing->total_amount, //Total Amount',
            $billing->description, //Description',

         ];
    }
}
