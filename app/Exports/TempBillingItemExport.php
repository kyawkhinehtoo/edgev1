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
class TempBillingItemExport implements FromQuery, WithMapping,WithHeadings,WithColumnFormatting,ShouldAutoSize
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

        $billings = TempInvoiceItem::query()
        ->with(['tempInvoice','customer','tempInvoice.isp','customer.package','tempInvoice.tempBill'])
        ->where('temp_invoice_id',$request->id);
       // dd($billings->toSQL());
        return $billings;
    
    }
    public function headings(): array
    {
        return [
            'Bill Name',
            'Bill Number',
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
            'C' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'D' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'E' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'G' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'H' => StyleNumberFormat::FORMAT_DATE_DDMMYYYY,
            'M' => StyleNumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => StyleNumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Q' => StyleNumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'R' => StyleNumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
    public function map($billing): array
    {

        return [
            $billing->tempInvoice->tempBill->name, //Bill Name',
            $billing->tempInvoice->tempBill->bill_number, //Bill Number',
            $billing->tempInvoice->tempBill->billing_period, //Bill Month',
            $billing->tempInvoice->issue_date, //Date Issued',
            $billing->tempInvoice->due_date, //Due Date',
            $billing->tempInvoice->tempBill->exchange_rate, //Exchange Rate',
            $billing->start_date, //Start Date',
            $billing->end_date, //End Date',
            $billing->tempInvoice->isp->name, //ISP Name',
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
