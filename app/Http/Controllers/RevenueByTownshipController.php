<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\City;
use App\Models\Township;
use App\Exports\RevenueByTownshipExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class RevenueByTownshipController extends Controller
{
    public function index()
    {
        $city_list = City::orderBy('name')->get();
        
        return Inertia::render('Client/RevenueByTownship', [
            'city_list' => $city_list,
        ]);
    }

    public function generate(Request $request)
    {
        $year = $request->year ?? date('Y');
        $cityId = $request->city_id;
        $revenueType = $request->revenue_type ?? 'all';

        // Get townships based on city filter
        $townshipsQuery = Township::orderBy('name');
        if ($cityId) {
            $townshipsQuery->where('city_id', $cityId);
        }
        $townships = $townshipsQuery->get();

        // Generate 12 months data
        $months = [
            'Jan-' . substr($year, -2),
            'Feb-' . substr($year, -2),
            'Mar-' . substr($year, -2),
            'Apr-' . substr($year, -2),
            'May-' . substr($year, -2),
            'Jun-' . substr($year, -2),
            'Jul-' . substr($year, -2),
            'Aug-' . substr($year, -2),
            'Sep-' . substr($year, -2),
            'Oct-' . substr($year, -2),
            'Nov-' . substr($year, -2),
            'Dec-' . substr($year, -2),
        ];

        $revenueData = [];

        foreach ($months as $index => $month) {
            $monthNumber = $index + 1;
            
            // Get revenue data for each township for this month
            $townshipRevenues = [];
            $monthTotal = 0;

            foreach ($townships as $township) {
                $query = DB::table('invoice_items')
                    ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
                    ->join('bills','invoices.bill_id','bills.id')
                    ->join('customers', 'customers.id', '=', 'invoice_items.customer_id')
                    ->join('customer_addresses', 'customer_addresses.customer_id', '=', 'customers.id')
                    ->join('townships', 'townships.id', '=', 'customer_addresses.township_id')
                    ->where('townships.id', $township->id)
                    ->where('customer_addresses.is_current', 1)
                    ->whereYear('bills.billing_period', $year)
                    ->whereMonth('bills.billing_period', $monthNumber)
                    ->when($cityId, function ($query) use ($cityId) {
                        return $query->where('townships.city_id', $cityId);
                    });

                // Apply revenue type filter
                if ($revenueType === 'receivables') {
                    // For receivables only - invoices without receipts or partial payments
                    $query->leftJoin('receipt_records', 'receipt_records.id', '=', 'invoices.receipt_id')
                        ->where(function ($q) {
                            $q->whereNull('receipt_records.id') // No receipt at all
                              ->orWhereRaw('receipt_records.collected_amount < invoices.total_amount'); // Partial payment
                        });
                }

                $revenue = $query->sum('invoice_items.total_amount');
                
                $townshipRevenues[] = [
                    'township_id' => $township->id,
                    'revenue' => $revenue ?: 0
                ];

                $monthTotal += $revenue ?: 0;
            }

            $revenueData[] = [
                'month' => $month,
                'townships' => $townshipRevenues,
                'total' => $monthTotal
            ];
        }

        $city_list = City::orderBy('name')->get();

        return Inertia::render('Client/RevenueByTownship', [
            'city_list' => $city_list,
            'revenueData' => $revenueData,
            'townships' => $townships,
        ]);
    }

    public function export(Request $request)
    {
        $year = $request->year ?? date('Y');
        $cityId = $request->city_id;
        $revenueType = $request->revenue_type ?? 'all';
        
        $cityName = $cityId ? City::find($cityId)->name : 'All_Cities';
        $typePrefix = $revenueType === 'receivables' ? 'receivables' : 'revenue';
        $filename = "{$typePrefix}_by_township_{$cityName}_{$year}.xlsx";

        return Excel::download(new RevenueByTownshipExport($request), $filename);
    }
}
