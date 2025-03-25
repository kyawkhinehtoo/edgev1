<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bills;
use App\Models\EmailTemplate;
use App\Models\ReceiptRecord;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DailyReceiptController extends Controller
{
    public function index(Request $request)
    {   
        $bill_list = Bills::all();
        $today_collection = ReceiptRecord::whereDate('receipt_records.receipt_date',Carbon::today())
                                            ->sum('receipt_records.collected_amount');
        $yesterday_collection = ReceiptRecord::whereDate('receipt_records.receipt_date',Carbon::yesterday())
                                            ->sum('receipt_records.collected_amount');
    
        $select_total = ReceiptRecord::join('isps','isps.id','=','receipt_records.isp_id')
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
                                   
                                    $query->whereIn('bills.id',  $b_list );
                                })
                                // ->when($request->date, function ($query, $date) {
                                //     $query->whereBetween('receipt_records.created_at', [$date['startDate'].' 00:00:00', $date['endDate'].' 23:00:00']);
                                // },function($query){
                                //     $query->whereDate('receipt_records.created_at',Carbon::today());
                                // })
                                ->when($request->date, function ($query, $date) {
                                    if(isset($date['0']) && isset($date['1'])){
                                        if($date['0'] != null && $date['1'] != null){
                                            return $query->whereBetween('receipt_records.receipt_date', [$date['0'].' 00:00:00', $date['1'].' 23:00:00']);
                                        }
                                    }
                                    $query->whereDate('receipt_records.receipt_date',Carbon::today());
                                    
                                  
                                },function($query){
                                    
                                     $query->whereDate('receipt_records.receipt_date',Carbon::today());
                                })
                                ->sum('receipt_records.collected_amount');
                               
        $receipt_records = ReceiptRecord::join('isps','isps.id','=','receipt_records.isp_id')
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
                                    $query->whereIn('bills.id',  $b_list );
                                })
                                ->when($request->date, function ($query, $date) {
                                    if(isset($date['0']) && isset($date['1'])){
                                        if($date['0'] != null && $date['1'] != null){
                                            return $query->whereBetween('receipt_records.receipt_date', [$date['0'].' 00:00:00', $date['1'].' 23:00:00']);
                                        }
                                    }
                                    $query->whereDate('receipt_records.receipt_date',Carbon::today());
                                    
                                  
                                },function($query){
                                    
                                     $query->whereDate('receipt_records.receipt_date',Carbon::today());
                                })
                                ->select('bills.name as bill_name','bills.bill_number','receipt_records.receipt_number','isps.name as isp_name','invoices.total_amount','receipt_records.collected_amount','bills.billing_period','receipt_records.receipt_date')
                                ->paginate(10);
      
                                $receipt_records->appends($request->all())->links();
        return Inertia::render("Client/DailyReceipt",
                [
                    'today_collection' => $today_collection,
                    'yesterday_collection' => $yesterday_collection,
                    'receipt_records'=>$receipt_records,
                    'bill_list'=>$bill_list,
                    'select_total'=>$select_total
                
                ]);
    }
    public function show(Request $request){
        return $this->index($request);
    }
}
