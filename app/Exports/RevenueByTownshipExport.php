<?php

namespace App\Exports;

use App\Models\Invoice;
use App\Models\City;
use App\Models\Township;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class RevenueByTownshipExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles
{
    protected $request;
    protected $townships;
    protected $revenueData;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->generateData();
    }

    protected function generateData()
    {
        $year = $this->request->year ?? date('Y');
        $cityId = $this->request->city_id;
        $revenueType = $this->request->revenue_type ?? 'all';

        // Get townships based on city filter
        $townshipsQuery = Township::orderBy('name');
        if ($cityId) {
            $townshipsQuery->where('city_id', $cityId);
        }
        $this->townships = $townshipsQuery->get();

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

        $this->revenueData = [];

        foreach ($months as $index => $month) {
            $monthNumber = $index + 1;
            
            $townshipRevenues = [];
            $monthTotal = 0;

            foreach ($this->townships as $township) {
                $query = \DB::table('invoice_items')
                    ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
                    ->join('bills','invoices.bill_id','bills.id')
                    ->join('customers', 'customers.id', '=', 'invoice_items.customer_id')
                    ->join('customer_addresses', 'customer_addresses.customer_id', '=', 'customers.id')
                    ->join('townships', 'townships.id', '=', 'customer_addresses.township_id')
                    ->where('townships.id', $township->id)
                    ->where('customer_addresses.is_current', true)
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

                $townshipRevenues[$township->id] = $revenue ?: 0;
                $monthTotal += $revenue ?: 0;
            }

            $this->revenueData[] = [
                'month' => $month,
                'townships' => $townshipRevenues,
                'total' => $monthTotal
            ];
        }

        // Add total row
        $totalRow = ['month' => 'TOTAL', 'townships' => [], 'total' => 0];
        foreach ($this->townships as $township) {
            $townshipTotal = 0;
            foreach ($this->revenueData as $monthData) {
                $townshipTotal += $monthData['townships'][$township->id] ?? 0;
            }
            $totalRow['townships'][$township->id] = $townshipTotal;
        }
        $totalRow['total'] = array_sum(array_column($this->revenueData, 'total'));
        
        $this->revenueData[] = $totalRow;
    }

    public function collection()
    {
        return collect($this->revenueData);
    }

    public function headings(): array
    {
        $headings = ['Month'];
        
        foreach ($this->townships as $township) {
            $headings[] = $township->name;
        }
        
        $headings[] = 'Total';
        
        return $headings;
    }

    public function map($row): array
    {
        $mapped = [$row['month']];
        
        foreach ($this->townships as $township) {
            $revenue = $row['townships'][$township->id] ?? 0;
            $mapped[] = $revenue;
        }
        
        $mapped[] = $row['total'];
        
        return $mapped;
    }

    public function title(): string
    {
        $year = $this->request->year ?? date('Y');
        $cityName = $this->request->city_id ? City::find($this->request->city_id)->name : 'All Cities';
        
        return "Revenue by Township - {$cityName} - {$year}";
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($this->townships) + 2);
        $lastRow = count($this->revenueData) + 1;
        
        return [
            // Header row styling
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB']
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER
                ]
            ],
            
            // Total row styling
            $lastRow => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FEF3C7']
                ]
            ],
            
            // Total column styling
            $lastColumn . '2:' . $lastColumn . $lastRow => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'FEF3C7']
                ]
            ],
            
            // All borders
            'A1:' . $lastColumn . $lastRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            ],
            
            // Center align all data except first column
            'B1:' . $lastColumn . $lastRow => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER
                ]
            ]
        ];
    }
}
