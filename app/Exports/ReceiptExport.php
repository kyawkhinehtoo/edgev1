<?php

namespace App\Exports;



use App\Models\Invoice;
use App\Models\EmailTemplate;
use App\Models\ReceiptRecord;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use DateTime;

class ReceiptExport implements FromQuery, WithMapping, WithHeadings
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
        $receipt_records =  ReceiptRecord::join('isps','isps.id','=','receipt_records.isp_id')
        ->join('invoices','invoices.receipt_id','=','receipt_records.id')
        ->join('bills','bills.id','=','receipt_records.bill_id')
        ->when($request->general, function ($query, $general) {
            $query->where(function ($query) use ($general) {
                $query->where('isps.name','LIKE', '%'.$general.'%')
                ->orWhere('invoices.invoice_number', 'LIKE', '%' . $general . '%')
                ->orWhere('receipt_records.receipt_number', 'LIKE', '%' . $general . '%');
                    });
            })
            ->when($request->bill_id, function ($query, $bills) {
                $b_list = array();
                foreach ($bills as $value) {
                    array_push($b_list, $value['id']);
                }
                // dd($b_list);
                $query->whereIn('bills.id',  $b_list);
            })
            ->when($request->date, function ($query, $date) {
                if (isset($date['0']) && isset($date['1'])) {
                    if ($date['0'] != null && $date['1'] != null) {
                        return $query->whereBetween('receipt_records.receipt_date', [$date['0'] . ' 00:00:00', $date['1'] . ' 23:00:00']);
                    }
                }
                $query->whereDate('receipt_records.receipt_date', Carbon::today());
            }, function ($query) {

                $query->whereDate('receipt_records.receipt_date', Carbon::today());
            })
            ->select('bills.name as bill_name',  'invoices.invoice_number', 'bills.bill_number', 'receipt_records.receipt_number', 'isps.name', 'invoices.total_amount', 'receipt_records.collected_amount', 'bills.billing_period', 'receipt_records.receipt_date',  'receipt_records.status as receipt_status', 'receipt_records.payment_channel', 'receipt_records.transition','invoices.issue_date','invoices.due_date','invoices.total_mrc_amount','invoices.total_mrc_customer','invoices.total_installation_amount','invoices.total_new_customer','invoices.additional_description','invoices.additional_fees','invoices.discount_amount','invoices.sub_total','invoices.tax_amount','invoices.payment_status');

        return $receipt_records;
    }
    public function headings(): array
    {
        return [
            'Bill Name',
            'Invoice Number',
            'Bill Number',
            'Receipt Number',
            'ISP Name',
            'Total Amount',
            'Collected Amount',
            'Billing Period',
            'Receipt Date',
            'Receipt Status',
            'Payment Channel',
            'Transition',
            'Issue Date',
            'Due Date',
            'Total MRC Amount',
            'Total MRC Customer',
            'Total Installation Amount',
            'Total New Customer',
            'Additional Description',
            'Additional Fees',
            'Discount Amount',
            'Sub Total',
            'Tax Amount',
            'Payment Status'
        ];
    }

    public function map($receipt_records): array
    {
        return [
            $receipt_records->bill_name,
            $receipt_records->invoice_number ? 'INV' . substr($receipt_records->bill_number, 0, 4) . str_pad($receipt_records->invoice_number, 5, "0", STR_PAD_LEFT) : null,
            $receipt_records->bill_number,
            $receipt_records->receipt_number ? 'REC' . substr($receipt_records->bill_number, 0, 4) . str_pad($receipt_records->receipt_number, 5, "0", STR_PAD_LEFT) : null,
            $receipt_records->name,
            $receipt_records->total_amount,
            $receipt_records->collected_amount,
            $receipt_records->billing_period,
            $receipt_records->receipt_date,
            $receipt_records->receipt_status,
            $receipt_records->payment_channel,
            $receipt_records->transition,
            $receipt_records->issue_date,
            $receipt_records->due_date,
            $receipt_records->total_mrc_amount,
            $receipt_records->total_mrc_customer,
            $receipt_records->total_installation_amount,
            $receipt_records->total_new_customer,
            $receipt_records->additional_description,
            $receipt_records->additional_fees,
            $receipt_records->discount_amount,
            $receipt_records->sub_total,
            $receipt_records->tax_amount,
            $receipt_records->payment_status
        ];
    }
}
