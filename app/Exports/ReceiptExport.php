<?php

namespace App\Exports;

use App\Models\Bills;
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
        $bill_list = Bills::all();
        $latestBill = $bill_list->last();
        $receipt_records =  Invoice::leftJoin('receipt_records','receipt_records.id','=','invoices.receipt_id')
                    ->leftJoin('isps','isps.id','=','invoices.isp_id')
                    ->join('bills','bills.id','=','invoices.bill_id')
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
                        $query->whereIn('bills.id',  $b_list );
                    },function($query) use ($latestBill){
                        $query->where('bills.id',  $latestBill->id);
                    })
                    ->when($request->payment_status, function ($query, $payment_status) {
                        if ($payment_status === 'Paid') {
                            $query->whereRaw('receipt_records.collected_amount >= invoices.total_amount');
                        } elseif ($payment_status === 'Pending') {
                            $query->where(function ($subQuery) {
                                $subQuery->whereRaw('receipt_records.collected_amount < invoices.total_amount')
                                        ->orWhereNull('receipt_records.collected_amount');
                            });
                        }
                    })
                    ->when($request->date, function ($query, $date) {
                        if(isset($date['0']) && isset($date['1'])){
                        if($date['0'] != null && $date['1'] != null){
                            return $query->whereBetween('receipt_records.receipt_date', [$date['0'].' 00:00:00', $date['1'].' 23:00:00']);
                        }
                        }
                        $query->whereDate('receipt_records.receipt_date',Carbon::today());
                    })
            ->select('bills.name as bill_name', 'invoices.invoice_number', 'bills.bill_number', 'receipt_records.receipt_number', 'isps.name', 'invoices.total_amount', 'receipt_records.collected_amount', 'bills.billing_period', 'receipt_records.receipt_date',  'receipt_records.status as receipt_status', 'receipt_records.payment_channel', 'receipt_records.transition','invoices.issue_date','invoices.due_date','invoices.total_mrc_amount','invoices.total_mrc_customer','invoices.total_installation_amount','invoices.total_new_customer','invoices.additional_description','invoices.additional_fees','invoices.discount_amount','invoices.sub_total','invoices.tax_amount','invoices.payment_status',
            DB::raw("CASE 
                        WHEN receipt_records.collected_amount >= invoices.total_amount THEN 'Paid' 
                        WHEN receipt_records.collected_amount IS NULL THEN 'Pending'
                        ELSE 'Pending' 
                    END as status")
                    );

        return $receipt_records;
    }
    public function headings(): array
    {
        return [
            'Snr',
            'ISP Name',
            'Issue Date',
            'Invoice Number',
            'Bill Number',
            'Sub Total Amount',// sub_total
            'Discount', // discount_amount
            'Sale after Discount', // sub_total - discount_amount
            'Tax', // tax_amount
            'Grand Total', // total_amount

            'Bill Month',
            'Due Date',
            'Due Days', // if only not paid yet
            'Project Code',
            'Received Amount',
            'Received Date',
            'Balance',
         
        ];
    }

    public function map($receipt_records): array
    {
        // Calculate due days only if not paid yet
        $dueDays = null;
        if ($receipt_records->status === 'Pending' && $receipt_records->due_date) {
            $dueDate = new DateTime($receipt_records->due_date);
            $today = new DateTime();
            $dueDays = $today->diff($dueDate)->days;
            if ($today > $dueDate) {
                $dueDays = -$dueDays; // Negative if overdue
            }
        }

        // Calculate balance (total_amount - collected_amount)
        $balance = $receipt_records->total_amount - ($receipt_records->collected_amount ?? 0);

        return [
            '', // Snr - will be auto-generated by Excel or can be handled separately
            $receipt_records->name, // ISP Name
            $receipt_records->issue_date, // Issue Date
            $receipt_records->invoice_number, // Invoice Number
            $receipt_records->bill_number, // Bill Number
            $receipt_records->sub_total, // Sub Total Amount
            $receipt_records->discount_amount, // Discount
            ($receipt_records->sub_total ?? 0) - ($receipt_records->discount_amount ?? 0), // Sale after Discount
            $receipt_records->tax_amount, // Tax
            $receipt_records->total_amount, // Grand Total
            $receipt_records->billing_period, // Bill Month
            $receipt_records->due_date, // Due Date
            $dueDays, // Due Days
            '', // Project Code - not available in current data
            $receipt_records->collected_amount, // Received Amount
            $receipt_records->receipt_date, // Received Date
            $balance, // Balance
        ];
    }
}
