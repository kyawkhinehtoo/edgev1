<?php

namespace App\Http\Controllers;

use App\Models\BillAdjustment;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\BillingTemp;
use App\Models\Bills;
use App\Models\Township;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Status;
use App\Models\CustomerHistory;
use App\Models\EmailTemplate;
use App\Models\User;
use App\Models\Role;
use App\Models\ReceiptRecord;
use App\Models\ReceiptSummery;
use ArrayIterator;
use CachingIterator;
use Inertia\Inertia;
use NumberFormatter;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mccarlosen\LaravelMpdf\Facades\LaravelMpdf as PDF;
use Illuminate\Support\Facades\Storage;
use Mail;
use DateTime;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Traits\PdfTrait;
use App\Traits\MarkupTrait;
use App\Traits\SMSTrait;
use App\Models\SmsGateway;
use App\Jobs\PDFCreateJob;
use App\Jobs\BillingSMSJob;
use App\Jobs\ReminderSMSJob;
use App\Models\BillingConfig;
use App\Models\BundleEquiptment;
use App\Models\DiscountSetup;
use App\Models\InstallationService;
use App\Models\InvoiceItem;
use App\Models\Isp;
use App\Models\MaintenanceService;
use App\Models\PortSharingService;
use App\Models\SystemSetting;
use App\Models\TempBill;
use App\Models\TempInvoice;
use App\Models\TempInvoiceItem;
use Carbon\Carbon;
class BillingController extends Controller
{
    use PdfTrait, MarkupTrait, SMSTrait;
    public function BillGenerator()
    {

        $isps = Isp::all();
        $bill = Bills::get();
        return Inertia::render('Client/BillGenerator', [
            'isps' => $isps,
            'bill' => $bill,
        ]);
    }
    public function doGenerate(Request $request)
    {
 
        $validated = $request->validate([
            'bill_year' => 'required|integer',
            'bill_month' => 'required|integer|between:1,12',
            'bill_number' => 'required',
            'issue_date' => 'required',
            'due_date' => 'required',
            'usd_exchange_rate' => 'required|numeric'
        ]);
        $bill_year = $validated['bill_year'];
        $bill_month = str_pad($validated['bill_month'], 2, '0', STR_PAD_LEFT);
        $billingPeriod = $bill_year . '-' . $bill_month;
        $bill_number = $validated['bill_number'];
        // Get first and last day of the month
        $firstDayOfMonth = date('Y-m-d', strtotime("$bill_year-$bill_month-01"));
        $lastDayOfMonth = date('Y-m-t', strtotime("$bill_year-$bill_month-01"));
       
        TempInvoiceItem::query()->delete();
        TempInvoice::query()->delete();
        TempBill::query()->delete();
       
        $cal_days = cal_days_in_month(CAL_GREGORIAN, $bill_month, $bill_year);
        $temp_date = Date('Y-m-d', strtotime($cal_days . '-' . $bill_month . '-' . $bill_year));
        $customers = Customer::with(['package', 'isp'])  // Include isp relationship
            ->select('customers.*', 'isps.id as isp_id', 'isps.name as isp_name', 'status.type as status_type')
            ->join('isps', 'customers.isp_id', '=', 'isps.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->when($request->bill_id, function ($query) use ($request) {
                return $query->whereNotIn('customers.id', function($subquery) use ($request) {
                    $subquery->select('customer_id')
                            ->from('invoices')
                            ->where('bill_id', $request->bill_id['id']);
                });
            })
          //  ->whereDate('customers.installation_date', '<', $temp_date)
            ->whereDate('customers.service_activation_date', '<=', $temp_date)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->when($request->isp_id, function ($query) use ($request) {
                return $query->where('customers.isp_id', $request->isp_id['id']);
            })
            ->whereNotIn('status.type', ['new', 'pending', 'cancel'])
            ->get();

            if ($customers->isEmpty()) {
                return redirect()->back()->with('message', 'No customers found for billing generation');
            }

            try {
                $tempBill = TempBill::create([
                    'name' => "Billing for $billingPeriod",
                    'bill_number' => $validated['bill_number'],
                    'billing_period' => $billingPeriod . '-01',
                    'exchange_rate' => $validated['usd_exchange_rate'],
                    'status' => 'Draft',
                ]);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Failed to create temporary bill');
            }

         
            foreach ($customers->groupBy('isp_id') as $ispId => $customerList) {
       
            

                $totalPortLeasingCustomer = 0;
                $totalMaintenanceCustomer = 0;
                $totalSuspensionCustomer = 0;
                $totalInstallationCustomer = 0;
                $totalRelocationCustomer = 0;
                $totalMaterialCustomer = 0;

                $totalPortLeasingAmount = 0;
                $totalMaintenanceAmount = 0;
                $totalSuspensionAmount = 0;
                $totalInstallationAmount = 0;
                $totalRelocationAmount = 0;
                $totalMaterialAmount = 0;

                $tempInvoice = TempInvoice::create([
                    'temp_bill_id' => $tempBill->id,
                    'isp_id' => $ispId,

                    'total_port_leasing_customer'=> 0,
                    'total_maintenance_customer' => 0,
                    'total_suspension_customer' => 0,
                    'total_installation_customer' => 0,
                    'total_relocation_customer' => 0,
                    'total_material_customer' => 0,
                    'total_port_leasing_amount' => 0,
                    'total_maintenance_amount' => 0,
                    'total_suspension_amount' => 0,
                    'total_relocation_amount' => 0,
                    'total_material_amount' => 0,

                    'total_mrc_amount' => 0,
                    'total_installation_amount' => 0,
                    'total_mrc_customer' => 0,
                    'total_new_customer' => 0,
                    'discount_amount' => 0,
                    'total_amount' => 0,
                ]);
        
                foreach ($customerList as $customer) {
                    $customerAddress = $customer->currentAddress()->first();
                    $portLeasing = $this->getPriceForCustomerByIsp($customer->id, $validated['issue_date']);
                    $mrc = $portLeasing['fee'];
                    try {
                        $installationService = InstallationService::find($customer->installation_service_id);
                        $installationFee = $installationService ? $installationService->fees : 0;
                    } catch (\Exception $e) {
                        $installationFee = 0; // Default to 0 if installation service not found                         $installationFee = 0;
                    }

                    // **New Installations** (Prorated MRC)
                    if ($customer->service_activation_date && $customer->service_activation_date->format('Y-m') == $billingPeriod) {
               
                        $totalInstallationCustomer++;

                        if ($customer->service_activation_date && $customer->service_activation_date->format('Y-m') == $billingPeriod) {
                        
                            $totalPortLeasingCustomer++;
                            
                            // Check if customer is also terminated in the same month
                            $isTerminatedSameMonth = $customer->service_termination_date && 
                                                    $customer->service_termination_date->format('Y-m') == $billingPeriod;
                            
                            if ($isTerminatedSameMonth) {
                                // Customer installed and terminated in same month - charge only for active days
                                $daysUsed = $customer->service_activation_date->diffInDays($customer->service_termination_date) + 1;
                                $daysUsed = round($daysUsed);
                                $endDate = $customer->service_termination_date;
                                $description = "Prorated MRC for {$customer->name} (Installed & Terminated same month - {$daysUsed} days)";
                            } else {
                                // Normal new installation - charge from activation to end of month
                                $daysUsed = round($customer->service_activation_date->diffInDays($lastDayOfMonth) + 1);
                                $endDate = $lastDayOfMonth;
                                $description = "Prorated MRC for {$customer->name} ({$daysUsed} days)";
                            }
                            
                            $proratedMRC = ($mrc / $cal_days) * $daysUsed;
                            $proratedMRC = $proratedMRC?round($proratedMRC):0;

                            TempInvoiceItem::create([
                                'temp_invoice_id' => $tempInvoice->id,
                                'customer_id' => $customer->id,
                                'type' => 'ProRatedPortLeasing',
                                'start_date' => $customer->service_activation_date,
                                'end_date' => $endDate,
                                'quantity' => 1,
                                'unit_price' => $mrc / $cal_days,
                                'total_amount' => $proratedMRC,
                                'description' => $description,
                            ]);
                        
                            $totalPortLeasingAmount += $proratedMRC;
                        }
                        if($customer->bundle){
                            $bundleArray = explode(',', $customer->bundle);
                            $bundleEquipments = BundleEquiptment::whereIn('id', $bundleArray)
                                ->where('is_active', 1)
                                ->get();
                            $description = '';
                            $totalPrice = 0;
                            foreach ($bundleEquipments as $bundle) {
                                 $description .= "{$bundle->name} : {$bundle->price} MMK, ";
                                 
                            }
                            TempInvoiceItem::create([
                                'temp_invoice_id' => $tempInvoice->id,
                                'customer_id' => $customer->id,
                                'type' => 'Materials',
                                'quantity' => 1,
                                'unit_price' => $bundleEquipments->sum('price'),
                                'total_amount' => $bundleEquipments->sum('price'),
                                'description' => $description,
                            ]);
                            $totalMaterialCustomer++;
                            $totalMaterialAmount += $bundleEquipments->sum('price');
                        }
            
                        // Installation Fee
                        TempInvoiceItem::create([
                            'temp_invoice_id' => $tempInvoice->id,
                            'customer_id' => $customer->id,
                            'type' => 'NewInstallation',
                            'quantity' => 1,
                            'unit_price' => $installationFee,
                            'total_amount' => $installationFee,
                            'description' => "Installation Fee for {$customer->name}",
                        ]);
            
                     
                        
                        $totalInstallationAmount += $installationFee;
                    }
                    // **Normal Monthly Recurring Charge (MRC)**
                    elseif ($customer->installation_date?->format('Y-m') < $billingPeriod) {
                        $totalPortLeasingCustomer++;
                        $isTerminated = $customer->service_termination_date && $customer->service_termination_date->format('Y-m') == $billingPeriod;
                        $isSuspended = $customer->status->type == 'suspense';
                        
                        // Skip if customer was installed in the same month (already processed above)
                        $isInstalledSameMonth = $customer->service_activation_date && 
                                               $customer->service_activation_date->format('Y-m') == $billingPeriod;
                        
                        if ($isInstalledSameMonth) {
                            // Skip - already processed in new installation section
                            continue;
                        }
                        
                        // Prorated if terminated
                        if ($isTerminated) {
                            $daysUsed = round($customer->service_termination_date->diffInDays($firstDayOfMonth) + 1);
                            $proratedMRC = ($mrc / $cal_days) * $daysUsed;
                            $proratedMRC = $proratedMRC?round($proratedMRC):0;
                            TempInvoiceItem::create([
                                'temp_invoice_id' => $tempInvoice->id,
                                'customer_id' => $customer->id,
                                'type' => 'ProRatedPortLeasing',
                                'start_date' => $firstDayOfMonth,
                                'end_date' => $customer->service_termination_date,
                                'quantity' => 1,
                                'unit_price' => $mrc / $cal_days,
                                'total_amount' => $proratedMRC,
                                'description' => "Prorated MRC for {$customer->name} ({$daysUsed} days)",
                            ]);
            
                            $totalPortLeasingAmount += $proratedMRC;
                        } else if($isSuspended) {
                            $billConfig = BillingConfig::first();
                            $portMaintenanceFees = $billConfig?->port_maintenance_fee?? 2000;
                            // Suspended customers get no MRC charge
                            TempInvoiceItem::create([
                                'temp_invoice_id' => $tempInvoice->id,
                                'customer_id' => $customer->id,
                                'type' => 'Suspension',
                                'start_date' => now()->startOfMonth(),
                                'end_date' => now()->endOfMonth(),
                                'quantity' => 1,
                                'unit_price' => $portMaintenanceFees,
                                'total_amount' => $portMaintenanceFees,
                                'description' => "MRC for {$customer->name} due to suspension",
                            ]);
                            $totalSuspensionCustomer++;
                            $totalSuspensionAmount += $portMaintenanceFees;
                        }else {
                            // Full month charge

                            $maintenance = MaintenanceService::find($customer->maintenance_service_id);
                           
                            //Maintenance Services 
                            TempInvoiceItem::create([
                                'temp_invoice_id' => $tempInvoice->id,
                                'customer_id' => $customer->id,
                                'type' => 'Maintenance',
                                'start_date' => now()->startOfMonth(),
                                'end_date' => now()->endOfMonth(),
                                'quantity' => 1,
                                'unit_price' => $maintenance->fees,
                                'total_amount' => $maintenance->fees,
                                'description' => $maintenance->name." Maintenance Service fees for {$customer->name}",
                            ]);

                            //Port Leasing
                            TempInvoiceItem::create([
                                'temp_invoice_id' => $tempInvoice->id,
                                'customer_id' => $customer->id,
                                'type' => 'FullPortLeasing',
                                'start_date' => now()->startOfMonth(),
                                'end_date' => now()->endOfMonth(),
                                'quantity' => 1,
                                'unit_price' => $mrc,
                                'total_amount' => $mrc,
                                'description' => "Port Leasing for {$customer->name}",
                            ]);

                            //Relocation 
                            if ( !empty($customerAddress->installation_date) and $customerAddress->type == 'relocated') {
                                // Ensure installation_date is a Carbon instance before calling format()
                                $relocationDate = Carbon::parse($customerAddress->installation_date);

                                if($relocationDate->format('Y-m') == $billingPeriod){
                                      // **Relocated Customers** (OTC for Relocation)
                                    $relocationFee = InstallationService::find($customerAddress->installation_service_id)->first()->fees;
                                
                                    TempInvoiceItem::create([
                                        'temp_invoice_id' => $tempInvoice->id,
                                        'customer_id' => $customer->id,
                                        'type' => 'Relocation',
                                        'start_date' => $customerAddress->installation_date,
                                        'quantity' => 1,
                                        'unit_price' => $relocationFee,
                                        'total_amount' => $relocationFee,
                                        'description' => "Relocation OTC for {$customer->name}",
                                    ]);
                                    $totalRelocationCustomer++;
                                    $totalRelocationAmount += $relocationFee;
                                }
                               
                            }

                            $totalMaintenanceCustomer++;
                            $totalMaintenanceAmount += $maintenance->fees;
                            $totalPortLeasingCustomer++;
                            $totalPortLeasingAmount += $mrc;
            
                          
                        }
                    } 
                }
                 // Update total amounts in invoice
                 $tempInvoice->update([
                    'issue_date' => $validated['issue_date'],
                    'due_date' => $validated['due_date'],
                    
                    'total_port_leasing_customer' => $totalPortLeasingCustomer,
                    'total_maintenance_customer' => $totalMaintenanceCustomer,
                    'total_suspension_customer' => $totalSuspensionCustomer,
                    'total_installation_customer' => $totalInstallationCustomer,
                    'total_relocation_customer' => $totalRelocationCustomer,
                    'total_material_customer' => $totalMaterialCustomer,

                    'total_port_leasing_amount' => $totalPortLeasingAmount,
                    'total_maintenance_amount' => $totalMaintenanceAmount,
                    'total_suspension_amount' => $totalSuspensionAmount,
                    'total_installation_amount' => $totalInstallationAmount,
                    'total_relocation_amount' => $totalRelocationAmount,
                    'total_material_amount' => $totalMaterialAmount,



                    'sub_total' => $totalPortLeasingAmount + $totalMaintenanceAmount + $totalSuspensionAmount + $totalInstallationAmount + $totalRelocationAmount + $totalMaterialAmount,
                    'total_amount' => $totalPortLeasingAmount + $totalMaintenanceAmount + $totalSuspensionAmount + $totalInstallationAmount + $totalRelocationAmount + $totalMaterialAmount,
                ]);
            }
           
        return redirect()->route('tempBilling')->with('message', 'Billing Created Successfully.');
         //   return redirect()->back()->with('message', 'Billing Created Successfully.');
        



      
    }
    private function getPriceForCustomerByIsp($customer_id, $billingIssueDate = null)
    {
        $customer = Customer::find($customer_id);
    
        if (!$customer) {
            return null;
        }
    
        // Get how many valid customers (with smaller or equal ID) exist for the same ISP
        $position = Customer::join('status', 'status.id', 'customers.status_id')
            ->where('customers.isp_id', $customer->isp_id)
            ->where('customers.id', '<=', $customer->id)
            ->whereIn('status.type', ['active', 'suspense'])
            ->count();
    
        $bandwidth = (int) $customer->bandwidth;
    
        $selectedService = PortSharingService::where('max_speed', '>=', $bandwidth)
            ->orderBy('max_speed', 'asc')
            ->first();
    
        if (!$selectedService) {
            return null;
        }
        
        $tiers = json_decode($selectedService->rate, true);
       
        $fee = null;
        foreach ($tiers as $tier) {
            if ($position >= $tier['min']) {
                $fee = $tier['fees'];
            }
        }

        // Check for applicable discount
        $discountedFee = $this->applyDiscount($fee, $selectedService->id, $customer->isp_id, $billingIssueDate, $position);
        
        return [
            'fee' => $discountedFee,
            'original_fee' => $fee,
            'position' => $position,
            'discount_applied' => $discountedFee != $fee,
        ];
    }

    private function applyDiscount($originalFee, $portSharingServiceId, $ispId, $billingIssueDate, $customerPosition = null)
    {
        if (!$billingIssueDate || !$originalFee) {
            return $originalFee;
        }

        // Convert billing issue date to Carbon instance if it's not already
        $issueDate = Carbon::parse($billingIssueDate);

        // Find applicable discount setup
        $discountSetup = DiscountSetup::where('port_sharing_service_id', $portSharingServiceId)
            ->where('isp_id', $ispId)
            ->where('is_active', 1)
            ->where('start_date', '<=', $issueDate)
            ->where('end_date', '>=', $issueDate)
            ->first();

        if (!$discountSetup) {
            return $originalFee; // No discount applicable
        }

        if ($discountSetup->rate_type === 'percentage') {
            // Apply percentage discount to the original fee
            $discountAmount = ($originalFee * $discountSetup->discount_percentage) / 100;
            return $originalFee - $discountAmount;
        } elseif ($discountSetup->rate_type === 'fixed') {
            // For fixed rate, parse the JSON and apply tier logic with discount rates
            if ($customerPosition !== null && $discountSetup->fix_rate) {
                $discountTiers = json_decode($discountSetup->fix_rate, true);
                
                if ($discountTiers && is_array($discountTiers)) {
                    $discountFee = null;
                    foreach ($discountTiers as $tier) {
                        if ($customerPosition >= $tier['min']) {
                            $discountFee = $tier['fees'];
                        }
                    }
                    
                    if ($discountFee !== null) {
                        return $discountFee;
                    }
                }
            }
            
            // Fallback: return original fee if discount tiers couldn't be processed
            return $originalFee;
        }

        return $originalFee; // Default return original fee if rate_type is unknown
    }
    public function goTemp(Request $request)
    {
        //$billings = Billing::paginate(10);

        $packages = Package::orderBy('name', 'ASC')->get();
        $townships = Township::get();
        $status = Status::get();
        $bill = Bills::where('status', 'active')->get();
        $tempInvoices = TempInvoice::with('tempInvoiceItems','isp','tempBill')->paginate(20);
        $tempInvoices->appends($request->all())->links();
        return Inertia::render('Client/TempBilling', [
            'packages' => $packages,
            'townships' => $townships,
            'status' => $status,
            'bill' => $bill,
            'tempInvoices' => $tempInvoices,
        ]);
   
    }
    public function updateTempInvoice(Request $request, $id)
    {
        $validated = $request->validate([
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'discount_amount' => 'required|numeric|min:0',
            'tax_percent' => 'numeric|min:0',
            'additional_description' => 'nullable|string',
            'additional_fees' => 'numeric|min:0',
        ]);

        $invoice = TempInvoice::findOrFail($id);
        
        // Calculate sub total (MRC + Installation + Additional Fees)
        $subTotal = $invoice->total_mrc_amount + 
                   $invoice->total_installation_amount + 
                   $validated['additional_fees'] - 
                   $validated['discount_amount'];

        // Calculate tax amount based on percentage
        $taxAmount = ($subTotal * $validated['tax_percent']) / 100;

        // Calculate final total amount
        $totalAmount = $subTotal + $taxAmount;

        // Merge calculated values with validated data
        $updateData = array_merge($validated, [
            'sub_total' => $subTotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount
        ]);

        $invoice->update($updateData);

        return redirect()->back()->with('message', 'Invoice updated successfully');
    }
    public function viewTempInvoiceDetails($id, Request $request)
    {   
        $tempInvoiceItems = TempInvoiceItem::with(['customer', 'tempInvoice.isp', 'tempInvoice.tempBill'])
            ->where('temp_invoice_id', $id)
            ->paginate(10);
        $tempInvoices  = DB::table('temp_invoice_items AS tii')
            ->join('customers AS c', 'tii.customer_id', '=', 'c.id')
            ->join('port_sharing_services AS p', 'c.port_sharing_service_id', '=', 'p.id')
            ->join('maintenance_services AS m', 'c.maintenance_service_id', '=', 'm.id')
            ->join('installation_services AS i', 'c.installation_service_id', '=', 'i.id')
            ->selectRaw("
                CASE 
                    WHEN tii.type = 'FullPortLeasing' THEN CONCAT('MRC ', p.name)
                    WHEN tii.type = 'ProRatedPortLeasing' THEN 'MRC ProRated'
                    WHEN tii.type = 'NewInstallation' THEN 'New Installation'
                    WHEN tii.type = 'Materials' THEN 'Materials'
                    WHEN tii.type = 'Maintenance' THEN 'Maintenance'
                    WHEN tii.type = 'Suspension' THEN 'Suspension'
                    ELSE 'Other'
                END AS category,
                COUNT(*) AS total_customers,
                SUM(tii.total_amount) AS total_amount
            ")
            ->where('tii.temp_invoice_id', $id)
            ->groupBy('category')
            ->orderBy('category','DESC')
            ->get();
        $tempInvoiceItems->appends($request->all())->links();
        return Inertia::render('Client/TempInvoiceDetails', [
            'tempInvoiceItems' => $tempInvoiceItems,
            'tempInvoices'=> $tempInvoices
        ]);
    }
    public function updateTempInvoiceItem(Request $request, $id)
    {
        $validated = $request->validate([
            'unit_price' => 'required|numeric',
            'total_amount' => 'required|numeric',
        ]);

        $invoiceItem = TempInvoiceItem::findOrFail($id);
        $invoiceItem->update($validated);

        $invoice = $invoiceItem->tempInvoice;

        // Recalculate all totals based on type
        $totalPortLeasingAmount = TempInvoiceItem::where('temp_invoice_id', $invoice->id)
            ->whereIn('type', ['FullPortLeasing', 'ProRatedPortLeasing'])
            ->sum('total_amount');
        $totalPortLeasingCustomer = TempInvoiceItem::where('temp_invoice_id', $invoice->id)
            ->whereIn('type', ['FullPortLeasing', 'ProRatedPortLeasing'])
            ->count();

        $totalMaintenanceAmount = TempInvoiceItem::where('temp_invoice_id', $invoice->id)
            ->where('type', 'Maintenance')
            ->sum('total_amount');
        $totalMaintenanceCustomer = TempInvoiceItem::where('temp_invoice_id', $invoice->id)
            ->where('type', 'Maintenance')
            ->count();

        $totalSuspensionAmount = TempInvoiceItem::where('temp_invoice_id', $invoice->id)
            ->where('type', 'Suspension')
            ->sum('total_amount');
        $totalSuspensionCustomer = TempInvoiceItem::where('temp_invoice_id', $invoice->id)
            ->where('type', 'Suspension')
            ->count();

        $totalInstallationAmount = TempInvoiceItem::where('temp_invoice_id', $invoice->id)
            ->where('type', 'NewInstallation')
            ->sum('total_amount');
        $totalInstallationCustomer = TempInvoiceItem::where('temp_invoice_id', $invoice->id)
            ->where('type', 'NewInstallation')
            ->count();

        $totalRelocationAmount = TempInvoiceItem::where('temp_invoice_id', $invoice->id)
            ->where('type', 'Relocation')
            ->sum('total_amount');
        $totalRelocationCustomer = TempInvoiceItem::where('temp_invoice_id', $invoice->id)
            ->where('type', 'Relocation')
            ->count();

        $totalMaterialAmount = TempInvoiceItem::where('temp_invoice_id', $invoice->id)
            ->where('type', 'Materials')
            ->sum('total_amount');
        $totalMaterialCustomer = TempInvoiceItem::where('temp_invoice_id', $invoice->id)
            ->where('type', 'Materials')
            ->count();

        // Calculate sub total (sum of all types + additional fees)
        $subTotal = $totalPortLeasingAmount + $totalMaintenanceAmount + $totalSuspensionAmount +
            $totalInstallationAmount + $totalRelocationAmount + $totalMaterialAmount +
            ($invoice->additional_fees ?? 0);

        $discountAmount = $invoice->discount_amount ?? 0;
        $grossTotal = $subTotal - $discountAmount;
        $taxAmount = ($grossTotal * ($invoice->tax_percent ?? 0)) / 100;
        $finalTotal = $grossTotal + $taxAmount;

        $invoice->update([
            'total_port_leasing_customer' => $totalPortLeasingCustomer,
            'total_maintenance_customer' => $totalMaintenanceCustomer,
            'total_suspension_customer' => $totalSuspensionCustomer,
            'total_installation_customer' => $totalInstallationCustomer,
            'total_relocation_customer' => $totalRelocationCustomer,
            'total_material_customer' => $totalMaterialCustomer,
            'total_port_leasing_amount' => $totalPortLeasingAmount,
            'total_maintenance_amount' => $totalMaintenanceAmount,
            'total_suspension_amount' => $totalSuspensionAmount,
            'total_installation_amount' => $totalInstallationAmount,
            'total_relocation_amount' => $totalRelocationAmount,
            'total_material_amount' => $totalMaterialAmount,
            'sub_total' => $subTotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $finalTotal
        ]);

        return redirect()->back()->with('message', 'Invoice Updated successfully');
    }
    public function destroyTempInvoiceItem($id)
    {
        $invoiceItem = TempInvoiceItem::findOrFail($id);
        $invoice = $invoiceItem->tempInvoice;
        
        $invoiceItem->delete();
       
        // Recalculate invoice totals
        $totalMRC = $invoice->tempInvoiceItems()->where('type', 'like', '%Recurring%')->sum('total_amount');
        $totalMRCCustomer = $invoice->tempInvoiceItems()->where('type', 'like', '%Recurring%')->count();
        $totalInstallation = $invoice->tempInvoiceItems()->where('type', 'NewInstallation')->sum('total_amount');
        $totalInstallationCustomer = $invoice->tempInvoiceItems()->where('type', 'NewInstallation')->count();

        // Calculate sub total (MRC + Installation + Additional Fees)
        $subTotal = $totalMRC + $totalInstallation + ($invoice->additional_fees ?? 0);

        // Calculate discount amount from sub total
        $discountAmount = $invoice->discount_amount;

        // Calculate gross total after discount
        $grossTotal = $subTotal - $discountAmount;

        // Calculate tax amount based on discounted total
        $taxAmount = ($grossTotal * ($invoice->tax_percent ?? 0)) / 100;

        // Calculate final total (gross + tax)
        $finalTotal = $grossTotal + $taxAmount;

        $invoice->update([
            'total_mrc_amount' => $totalMRC,
            'total_installation_amount' => $totalInstallation,
            'total_mrc_customer' => $totalMRCCustomer,
            'total_new_customer' => $totalInstallationCustomer,
            'sub_total' => $subTotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $finalTotal
        ]);

        return redirect()->back()->with('message', 'Invoice item deleted successfully');
    }
   
  
  
    public function preview_1($id)
    {
        $isp = TempInvoice::with('isp','tempBill')->find($id);
        $tempInvoices  = DB::table('temp_invoice_items AS tii')
        ->join('customers AS c', 'tii.customer_id', '=', 'c.id')
            ->join('port_sharing_services AS p', 'c.port_sharing_service_id', '=', 'p.id')
            ->join('maintenance_services AS m', 'c.maintenance_service_id', '=', 'm.id')
            ->join('installation_services AS i', 'c.installation_service_id', '=', 'i.id')
            ->selectRaw("
                CASE 
                    WHEN tii.type = 'FullPortLeasing' THEN CONCAT('MRC ', p.name)
                    WHEN tii.type = 'ProRatedPortLeasing' THEN 'MRC ProRated'
                    WHEN tii.type = 'NewInstallation' THEN CONCAT('New Installation for ', i.sla_hours, ' hour')
                    WHEN tii.type = 'Materials' THEN 'Materials'
                    WHEN tii.type = 'Maintenance' THEN 'Maintenance'
                    WHEN tii.type = 'Suspension' THEN 'Suspension'
                    ELSE 'Other'
                END AS category,
                COUNT(*) AS total_customers,
                SUM(tii.total_amount) AS total_amount
            ")
            ->where('tii.temp_invoice_id', $id)
            ->groupBy('category')
            ->orderBy('category','DESC')
            ->get();

        return view('preview', [
            'tempInvoices' => $tempInvoices,
            'isp' => $isp
        ]);
    }
    public function preview_2(Request $request)
    {

        $billing_invoice = Invoice::join('receipt_records', 'receipt_records.invoice_id', '=', 'invoices.id')
            ->leftjoin('users', 'users.id', '=', 'receipt_records.receipt_person')
            ->join('customers', 'receipt_records.customer_id', 'customers.id')
            ->join('packages', 'customers.package_id', 'packages.id')
            ->where('receipt_records.id', '=', $request->id)
            ->select('invoices.*', 'packages.type as service_type', 'receipt_records.remark as remark', 'receipt_records.collected_amount as collected_amount', 'receipt_records.receipt_date as receipt_date', 'receipt_records.receipt_number as receipt_number', 'users.name as collector')
            ->first();

        return view('voucher', $billing_invoice);
    }
    public function showInvoice(Request $request)
    {

        $billing_invoice = Invoice::join('customers', 'invoices.customer_id', 'customers.id')
            ->join('packages', 'customers.package_id', 'packages.id')
            ->where('invoices.id', '=', $request->id)
            ->select('invoices.*', 'packages.type as service_type')
            ->first();

        return view('invoice', $billing_invoice);
    }

    public function saveSingle(Request $request)
    {
        //   dd($request);
        if ($request->bill_number && $request->ftth_id) {
            $temp = BillingTemp::where('ftth_id', 'LIKE', '%' . $request->ftth_id . '%')->first();
            $bill = Bills::where('name', 'LIKE', '%' . $request->bill_number . '%')
                ->first();
            if ($temp && $bill) {
                $max_invoice_id =  DB::table('invoices')
                    ->where('invoices.bill_id', '=', $bill->id)
                    ->select(DB::raw('max(invoices.invoice_number) as max_invoice_number'))
                    ->first();

                $billing = new Invoice();
                $billing->period_covered = $temp->period_covered;
                $billing->bill_number = $temp->bill_number;
                $billing->bill_id = $bill->id;
                $billing->invoice_number = ($max_invoice_id) ? ($max_invoice_id->max_invoice_number + 1) : 1;
                $billing->customer_id = $temp->customer_id;
                $billing->ftth_id = $temp->ftth_id;
                $billing->date_issued = $temp->date_issued;
                $billing->bill_to = $temp->bill_to;
                $billing->attn = $temp->attn;
                $billing->previous_balance = $temp->previous_balance;
                $billing->current_charge = $temp->current_charge;
                $billing->compensation = $temp->compensation;
                $billing->otc = $temp->otc;
                $billing->sub_total = $temp->sub_total;
                $billing->payment_duedate = $temp->payment_duedate;
                $billing->service_description = $temp->service_description;
                $billing->qty = $temp->qty;
                $billing->usage_days = $temp->usage_days;
                $billing->customer_status = $temp->customer_status;
                $billing->normal_cost = $temp->normal_cost;
                $billing->type = $temp->type;
                $billing->total_payable = $temp->total_payable;
                $billing->discount = $temp->discount;
                $billing->amount_in_word = $temp->amount_in_word;
                $billing->commercial_tax = $temp->commercial_tax;
                $billing->tax = $temp->tax;
                $billing->phone = $temp->phone;
                $billing->bill_month = $temp->bill_month;
                $billing->bill_year = $temp->bill_year;
                $billing->save();
            }
        }
    }
    public function saveFinal(Request $request)
    {
        // Validate bill name
 
        if (!$request->has('bill_name')) {
            return redirect()->back()->with('error', 'Bill name is required');
        }

        // Check for duplicate bill name
        if (Bills::where('name', $request->bill_name)->exists()) {
            return redirect()->back()->with('error', 'Bill name already exists');
        }

        try {
            DB::beginTransaction();

            // Get temp bill data
            $tempBill = TempBill::with(['tempInvoices.tempInvoiceItems'])->first();
            if (!$tempBill) {
                throw new \Exception('No temporary bill found');
            }

            // Create new bill
            $bill = Bills::create([
                'name' => $request->bill_name,
                'bill_number' => $tempBill->bill_number,
                'billing_period' => $tempBill->billing_period,
                'exchange_rate' => $tempBill->exchange_rate,
                'status' => 'Active'
            ]);

            // Copy temp invoices and their items
            foreach ($tempBill->tempInvoices as $tempInvoice) {
                $invoiceNumber = 'INV_'.$bill->bill_number.'_'.Invoice::generateInvoiceNumber($bill->id);
                $invoice = Invoice::create([
                    'bill_id' => $bill->id,
                    'invoice_number' => $invoiceNumber,
                    'isp_id' => $tempInvoice->isp_id,
                    'issue_date' => $tempInvoice->issue_date,
                    'due_date' => $tempInvoice->due_date,


                    'total_port_leasing_customer' => $tempInvoice->total_port_leasing_customer,
                    'total_maintenance_customer' => $tempInvoice->total_maintenance_customer,
                    'total_suspension_customer' => $tempInvoice->total_suspension_customer,
                    'total_installation_customer' => $tempInvoice->total_installation_customer,
                    'total_relocation_customer' => $tempInvoice->total_relocation_customer,
                    'total_material_customer' => $tempInvoice->total_material_customer,
                    'total_port_leasing_amount' => $tempInvoice->total_port_leasing_amount,
                    'total_maintenance_amount' => $tempInvoice->total_maintenance_amount,
                    'total_suspension_amount' => $tempInvoice->total_suspension_amount,
                    'total_relocation_amount' => $tempInvoice->total_relocation_amount,
                    'total_material_amount' => $tempInvoice->total_material_amount,


                    'sub_total' => $tempInvoice->sub_total,
                    'tax_percent' => $tempInvoice->tax_percent,
                    'tax_amount' => $tempInvoice->tax_amount,
                    'discount_amount' => $tempInvoice->discount_amount,
                    'total_amount' => $tempInvoice->total_amount,
                    'additional_description' => $tempInvoice->additional_description,
                    'additional_fees' => $tempInvoice->additional_fees
                ]);

                // Copy invoice items
                foreach ($tempInvoice->tempInvoiceItems as $tempItem) {
                    InvoiceItem::create([
                        'invoice_id' => $invoice->id,
                        'customer_id' => $tempItem->customer_id,
                        'type' => $tempItem->type,
                        'start_date' => $tempItem->start_date,
                        'end_date' => $tempItem->end_date,
                        'quantity' => $tempItem->quantity,
                        'unit_price' => $tempItem->unit_price,
                        'total_amount' => $tempItem->total_amount,
                        'description' => $tempItem->description
                    ]);
                }
            }

            // Clean up temp data
            $tempBill->delete(); // This will cascade delete temp invoices and items

            DB::commit();
            return redirect()->route('showbill')->with('message', 'Billing finalized successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('message', 'Failed to finalize billing: ' . $e->getMessage());
        }
    }

    public function showList()
    {
        $lists = Bills::all();
        return Inertia::render('Client/BillList', [
            'lists' => $lists
        ]);
    }
    public function showBill(Request $request)
    {
        $bills  = Bills::orderBy('id','desc')->get();
        $billingTeam = User::join('roles','roles.id','users.role_id')
                    ->where('roles.name','like','%bill%')
                    ->where('users.user_type','internal')
                    ->select('users.*')
                    ->get();
        $selectedBill = null;

        if ($request->bill_id) {
            $selectedBill = Bills::find($request->bill_id);
        }
        $invoices = null;
        $smsgateway = SmsGateway::first();  
        
        if ($request->bill_id) {
            $invoices = Invoice::with('invoiceItem','isp','bill','receiptRecord')->where('bill_id',$request->bill_id)->paginate(20);
            $invoices->appends($request->all())->links();
           
        } 
        return Inertia::render('Client/BillList', [
            'bills' => $bills,
            'selectedBill' => $selectedBill,
            'invoices' => $invoices,
            'smsgateway'=> $smsgateway,
            'billingTeam'=>$billingTeam
        ]);
    }
    public function updateInvoice(Request $request, $id)
    {
    
        $validated = $request->validate([
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'discount_amount' => 'required|numeric|min:0',
            'tax_percent' => 'numeric|min:0',
            'additional_description' => 'nullable|string',
            'additional_fees' => 'numeric|min:0',
        ]);

        $invoice = Invoice::findOrFail($id);
        
        // Calculate sub total (MRC + Installation + Additional Fees)
        $subTotal = $invoice->total_mrc_amount + 
                   $invoice->total_installation_amount + 
                   $validated['additional_fees'] - 
                   $validated['discount_amount'];

        // Calculate tax amount based on percentage
        $taxAmount = ($subTotal * $validated['tax_percent']) / 100;

        // Calculate final total amount
        $totalAmount = $subTotal + $taxAmount;

        // Merge calculated values with validated data
        $updateData = array_merge($validated, [
            'sub_total' => $subTotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount
        ]);

        $invoice->update($updateData);
        if($invoice->receipt_id){
           // ReceiptRecord::find($invoice->receipt_id)->delete();
            $invoice->receipt_id = null;
            $invoice->payment_status = "Pending";
            $invoice->save();
        }
     
        return redirect()->back()->with('message', 'Invoice updated successfully');
    }
    public function viewInvoiceDetails($id, Request $request)
    {   
        $invoiceItems = InvoiceItem::with(['customer', 'invoice.isp', 'invoice.bill'])
            ->where('invoice_id', $id)
            ->paginate(10);
        $invoices  = DB::table('invoice_items AS tii')
            ->join('customers AS c', 'tii.customer_id', '=', 'c.id')
            ->join('port_sharing_services AS p', 'c.port_sharing_service_id', '=', 'p.id')
            ->join('maintenance_services AS m', 'c.maintenance_service_id', '=', 'm.id')
            ->join('installation_services AS i', 'c.installation_service_id', '=', 'i.id')
            ->selectRaw("
                CASE 
                    WHEN tii.type = 'FullPortLeasing' THEN CONCAT('MRC ', p.name)
                    WHEN tii.type = 'ProRatedPortLeasing' THEN 'MRC ProRated'
                    WHEN tii.type = 'NewInstallation' THEN CONCAT('New Installation for ', i.sla_hours, ' hour')
                    WHEN tii.type = 'Materials' THEN 'Materials'
                    WHEN tii.type = 'Maintenance' THEN 'Maintenance'
                    WHEN tii.type = 'Suspension' THEN 'Suspension'
                    ELSE 'Other'
                END AS category,
                COUNT(*) AS total_customers,
                SUM(tii.total_amount) AS total_amount
            ")
            ->where('tii.invoice_id', $id)
            ->groupBy('category')
            ->orderBy('category','DESC')
            ->get();
        $invoiceItems->appends($request->all())->links();
        return Inertia::render('Client/InvoiceDetails', [
            'invoiceItems' => $invoiceItems,
            'invoices'=> $invoices
        ]);
    }
    public function updateInvoiceItem(Request $request, $id)
    {
        $validated = $request->validate([
            'unit_price' => 'required|numeric',
            'total_amount' => 'required|numeric',
        ]);

        $invoiceItem = InvoiceItem::findOrFail($id);
        $invoiceItem->update($validated);

        $invoice = $invoiceItem->invoice;

        // Recalculate all totals based on type
        $totalPortLeasingAmount = InvoiceItem::where('invoice_id', $invoice->id)
            ->whereIn('type', ['FullPortLeasing', 'ProRatedPortLeasing'])
            ->sum('total_amount');
        $totalPortLeasingCustomer = InvoiceItem::where('invoice_id', $invoice->id)
            ->whereIn('type', ['FullPortLeasing', 'ProRatedPortLeasing'])
            ->count();

        $totalMaintenanceAmount = InvoiceItem::where('invoice_id', $invoice->id)
            ->where('type', 'Maintenance')
            ->sum('total_amount');
        $totalMaintenanceCustomer = InvoiceItem::where('invoice_id', $invoice->id)
            ->where('type', 'Maintenance')
            ->count();

        $totalSuspensionAmount = InvoiceItem::where('invoice_id', $invoice->id)
            ->where('type', 'Suspension')
            ->sum('total_amount');
        $totalSuspensionCustomer = InvoiceItem::where('invoice_id', $invoice->id)
            ->where('type', 'Suspension')
            ->count();

        $totalInstallationAmount = InvoiceItem::where('invoice_id', $invoice->id)
            ->where('type', 'NewInstallation')
            ->sum('total_amount');
        $totalInstallationCustomer = InvoiceItem::where('invoice_id', $invoice->id)
            ->where('type', 'NewInstallation')
            ->count();

        $totalRelocationAmount = InvoiceItem::where('invoice_id', $invoice->id)
            ->where('type', 'Relocation')
            ->sum('total_amount');
        $totalRelocationCustomer = InvoiceItem::where('invoice_id', $invoice->id)
            ->where('type', 'Relocation')
            ->count();

        $totalMaterialAmount = InvoiceItem::where('invoice_id', $invoice->id)
            ->where('type', 'Materials')
            ->sum('total_amount');
        $totalMaterialCustomer = InvoiceItem::where('invoice_id', $invoice->id)
            ->where('type', 'Materials')
            ->count();

        // Calculate sub total (sum of all types + additional fees)
        $subTotal = $totalPortLeasingAmount + $totalMaintenanceAmount + $totalSuspensionAmount +
            $totalInstallationAmount + $totalRelocationAmount + $totalMaterialAmount +
            ($invoice->additional_fees ?? 0);

        $discountAmount = $invoice->discount_amount ?? 0;
        $grossTotal = $subTotal - $discountAmount;
        $taxAmount = ($grossTotal * ($invoice->tax_percent ?? 0)) / 100;
        $finalTotal = $grossTotal + $taxAmount;

        $invoice->update([
            'total_port_leasing_customer' => $totalPortLeasingCustomer,
            'total_maintenance_customer' => $totalMaintenanceCustomer,
            'total_suspension_customer' => $totalSuspensionCustomer,
            'total_installation_customer' => $totalInstallationCustomer,
            'total_relocation_customer' => $totalRelocationCustomer,
            'total_material_customer' => $totalMaterialCustomer,
            'total_port_leasing_amount' => $totalPortLeasingAmount,
            'total_maintenance_amount' => $totalMaintenanceAmount,
            'total_suspension_amount' => $totalSuspensionAmount,
            'total_installation_amount' => $totalInstallationAmount,
            'total_relocation_amount' => $totalRelocationAmount,
            'total_material_amount' => $totalMaterialAmount,
            'sub_total' => $subTotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $finalTotal
        ]);

        return redirect()->back()->with('message', 'Invoice Updated successfully');
    }
    public function destroyInvoiceItem($id)
    {
        $invoiceItem = InvoiceItem::findOrFail($id);
        $invoice = $invoiceItem->invoice;
        
        $invoiceItem->delete();
        // Recalculate invoice totals using InvoiceItem model directly
        $totalMRC = InvoiceItem::where('invoice_id', $invoice->id)
            ->where('type', 'like', '%Recurring%')
            ->sum('total_amount');

        $totalMRCCustomer = InvoiceItem::where('invoice_id', $invoice->id)
            ->where('type', 'like', '%Recurring%')
            ->count();

        $totalInstallation = InvoiceItem::where('invoice_id', $invoice->id)
            ->where('type', 'NewInstallation')
            ->sum('total_amount');

        $totalInstallationCustomer = InvoiceItem::where('invoice_id', $invoice->id)
            ->where('type', 'NewInstallation')
            ->count();

        // Calculate sub total (MRC + Installation + Additional Fees)
        $subTotal = $totalMRC + $totalInstallation + ($invoice->additional_fees ?? 0);

        // Calculate discount amount from sub total
        $discountAmount = $invoice->discount_amount;

        // Calculate gross total after discount
        $grossTotal = $subTotal - $discountAmount;

        // Calculate tax amount based on discounted total
        $taxAmount = ($grossTotal * ($invoice->tax_percent ?? 0)) / 100;

        // Calculate final total (gross + tax)
        $finalTotal = $grossTotal + $taxAmount;

        $invoice->update([
            'total_mrc_amount' => $totalMRC,
            'total_installation_amount' => $totalInstallation,
            'total_mrc_customer' => $totalMRCCustomer,
            'total_new_customer' => $totalInstallationCustomer,
            'sub_total' => $subTotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $finalTotal
        ]);

        return redirect()->back()->with('message', 'Invoice item deleted successfully');
    }
    public function makeSinglePDF(Request $request)
    {
       
        $invoice = Invoice::with('isp','bill','receiptRecord')->find($request->id);
        $invoiceItems  = DB::table('invoice_items AS tii')
         ->join('customers AS c', 'tii.customer_id', '=', 'c.id')
            ->join('port_sharing_services AS p', 'c.port_sharing_service_id', '=', 'p.id')
            ->join('maintenance_services AS m', 'c.maintenance_service_id', '=', 'm.id')
            ->join('installation_services AS i', 'c.installation_service_id', '=', 'i.id')
            ->selectRaw("
                CASE 
                    WHEN tii.type = 'FullPortLeasing' THEN CONCAT('MRC ', p.name)
                    WHEN tii.type = 'ProRatedPortLeasing' THEN 'MRC ProRated'
                    WHEN tii.type = 'NewInstallation' THEN CONCAT('New Installation for ', i.sla_hours, ' hour')
                    WHEN tii.type = 'Materials' THEN 'Materials'
                    WHEN tii.type = 'Maintenance' THEN 'Maintenance'
                    WHEN tii.type = 'Suspension' THEN 'Suspension'
                    ELSE 'Other'
                END AS category,
                COUNT(*) AS total_customers,
                SUM(tii.total_amount) AS total_amount
            ")
            ->where('tii.invoice_id', $request->id)
            ->groupBy('category')
            ->orderBy('category','DESC')
            ->get();
       
        
        $options = [
            'format' => 'A4',
            'default_font_size' => '11',
            'orientation' => 'P',
            'encoding' => 'UTF-8',
            'margin_top' => 0,
            'margin_bottom' => 45,  // Ensure bottom margin is enough
            'margin_footer' => 0,
            'title' => $invoice->isp->name,
            'setAutoBottomMargin' => 'stretch', 
        ];

        $name = date("ymdHis") . '-' . $invoice->bill->bill_number . ".pdf";
        $path = $invoice->isp->name . '/' . $name;
        $pdf = $this->createPDF($options, 'pdf.invoice', [
            'invoiceItems' => $invoiceItems,
            'invoice' => $invoice
        ], $name, $path);
        $invoice_no = $invoice->invoice_number;
        if ($pdf['status'] == 'success') {

            // Successfully stored. Return the full path.
            $invoice->invoice_file =  $pdf['disk_path'];
            $invoice->invoice_url = $pdf['shortURL'];
 
            $invoice->update();
            activity()
                ->causedBy(User::find(Auth::id()))
                ->performedOn($invoice)
                ->log('Create Single PDF for' . $invoice->isp->name . ', Invoice ID : ' .  $invoice_no);
            return redirect()->back()->with('message', 'PDF Generated Successfully.');
        }

        activity()
            ->causedBy(User::find(Auth::id()))
            ->performedOn($invoice)
            ->log('Single PDF Creation Failed for ' . $invoice->ftth_id . ', Invoice ID : ' .  $invoice_no);
        // download PDF file with download method
        return redirect()->back()->with('message', 'PDF Generation Fails.');
    }



    public function makeAllPDF(Request $request)
    {
        // dd($request);
        $invoices =  Invoice::join('customers', 'customers.id', '=', 'invoices.customer_id')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->where('invoices.total_payable', '>', 0)
            ->where('bill_id', '=', $request->bill_id)
            ->whereNull('invoice_file')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->select('invoices.*')
            ->get();
        if ($invoices) {

            foreach ($invoices as $invoice) {

                //event(new PdfGenerationProgress($count++));
                dispatch(new PDFCreateJob($invoice->id));
            }
            // download PDF file with download method
            return redirect()->back()->with('message', 'PDF Generation is running background.');
        }
    }
    public function sendSingleEmail(Request $request)
    {
        if ($request->id) {
            $invoice = Invoice::find($request->id);



            if ($invoice->email) {

                $email_template = EmailTemplate::where('default', '=', 1)
                    ->where('type', '=', 'email')
                    ->first();
                if ($email_template) {

                    $billing_email = $invoice->email;
                    // $data["email"] = $invoices->email;
                    $data["email"] = $billing_email;
                    if (strpos($billing_email, ',') !== false) {
                        $data["email"] = explode(",", $billing_email);
                    }
                    if (strpos($billing_email, ';') !== false) {
                        $data["email"] = explode(';', $billing_email);
                    }
                    if (strpos($billing_email, ':') !== false) {
                        $data["email"] = explode(':', $billing_email);
                    }
                    if (strpos($billing_email, ' ') !== false) {
                        $data["email"] = explode(' ', $billing_email);
                    }
                    if (strpos($billing_email, '/') !== false) {
                        $data["email"] = explode('/', $billing_email);
                    }

                    $email_title = $email_template->title;
                    $email_body = $email_template->body;
                    $email_title = $this->replaceMarkup($email_title, $request->id);
                    $email_body = $this->replaceMarkup($email_body, $request->id);

                    $cc_emails = $email_template->cc_email;
                    if (strpos($cc_emails, ','))
                        $cc_emails = explode(",", $cc_emails);
                    $data["cc"] = $cc_emails;
                    $data["title"] = $email_title;
                    $data["body"] = $email_body;
                    $attachment =  $invoice->file;
                    Mail::send('emailtemplate', $data, function ($message) use ($data, $attachment) {
                        $message->to($data["email"], $data["email"])
                            ->cc($data["cc"])
                            ->subject($data["title"]);
                        if ($attachment) {
                            $message->attach($attachment);
                        }
                    });

                    $billing_data = Invoice::find($invoice->id);
                    $billing_data->sent_date = date('j M Y');
                    if (Mail::failures()) {
                        $billing_data->mail_sent_status = "error";
                    } else {
                        $billing_data->mail_sent_status = "sent";
                    }
                    $billing_data->update();

                    if (Mail::failures()) {
                        return redirect()->back()->with('message', 'Email Cannot Send');
                    } else {
                        return redirect()->back()->with('message', 'Sent Email Successfully.');
                    }
                }
            }
        } else {
            return redirect()->back()->with('message', 'Customer does not have email address !');
        }
    }

    public function sendAllEmail(Request $request)
    {
        ini_set('max_execution_time', '0');
        $invoices =  Invoice::join('customers', 'customers.id', '=', 'invoices.customer_id')
            ->join('packages', 'customers.package_id', '=', 'packages.id')
            ->join('townships', 'customers.township_id', '=', 'townships.id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where('invoices.total_payable', '>', 0)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->where('bill_id', '=', $request->bill_id)
            ->when($request->keyword, function ($query, $search = null) {
                $query->where('customers.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('customers.ftth_id', 'LIKE', '%' . $search . '%')
                    ->orWhere('packages.name', 'LIKE', '%' . $search . '%')
                    ->orWhere('townships.name', 'LIKE', '%' . $search . '%');
            })->when($request->general, function ($query, $general) {
                $query->where(function ($query) use ($general) {
                    $query->where('customers.name', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%');
                });
            })
            ->when($request->installation, function ($query, $installation) {
                $startDate = Carbon::parse($installation[0])->format('Y-m-d');
                $endDate = Carbon::parse($installation[1])->format('Y-m-d');
                $query->whereBetween('customers.installation_date', [$startDate, $endDate]);
            })
            ->when($request->total_payable_min, function ($query, $total_payable_min) {
                $query->where('invoices.total_payable', '>=', $total_payable_min);
            })
            ->when($request->total_payable_max, function ($query, $total_payable_max) {
                $query->where('invoices.total_payable', '<=', $total_payable_max);
            })
            ->when($request->package, function ($query, $package) {
                $query->where('customers.package_id', '=', $package);
            })

            ->when($request->township, function ($query, $township) {
                $query->where('customers.township_id', '=', $township);
            })
            ->when($request->status, function ($query, $status) {
                $query->where('customers.status_id', '=', $status);
            })
            ->when($request->order, function ($query, $order) {
                $query->whereBetween('customers.order_date', $order);
            })
            ->when($request->installation, function ($query, $installation) {
                $query->whereBetween('customers.installation_date', $installation);
            })
            ->select('invoices.*')
            ->get();
        $email_template = EmailTemplate::where('default', '=', 1)
            ->where('type', '=', 'email')
            ->first();
        foreach ($invoices as $invoice) {
            if ($invoice->email && $invoice->mail_sent_status != 'sent') {

                $billing_email =  preg_replace('/\s+/', '', $invoice->email); //remove space from email

                $data["email"] = $billing_email;
                if (strpos($billing_email, ',') !== false) {
                    $data["email"] = explode(",", $billing_email);
                }
                if (strpos($billing_email, ';') !== false) {
                    $data["email"] = explode(';', $billing_email);
                }
                if (strpos($billing_email, ':') !== false) {
                    $data["email"] = explode(':', $billing_email);
                }
                if (strpos($billing_email, ' ') !== false) {
                    $data["email"] = explode(' ', $billing_email);
                }
                if (strpos($billing_email, '/') !== false) {
                    $data["email"] = explode('/', $billing_email);
                }
                // $data["email"] = 'kkhinehtoo@gmail.com';
                $email_title = $email_template->title;
                $email_body = $email_template->body;
                $email_title = $this->replaceMarkup($email_title, $request->id);
                $email_body = $this->replaceMarkup($email_body, $request->id);
                $cc_emails = $email_template->cc_email;
                if (strpos($cc_emails, ','))
                    $cc_emails = explode(",", $cc_emails);

                $data["cc"] = $cc_emails;
                $data["title"] = $email_title;
                $data["body"] = $email_body;
                $attachment =  $invoice->file;
                Mail::send('emailtemplate', $data, function ($message) use ($data, $attachment) {
                    $message->to($data["email"], $data["email"])
                        ->cc($data["cc"])
                        ->subject($data["title"]);
                    if ($attachment) {
                        $message->attach($attachment);
                    }
                });

                $biling_data = Invoice::find($invoice->id);

                if (Mail::failures()) {
                    $biling_data->mail_sent_status = "error";
                } else {
                    $biling_data->sent_date = date('j M Y');
                    $biling_data->mail_sent_status = "sent";
                }
                $biling_data->save();
            }
        }
        if (Mail::failures()) {
            return redirect()->back()->with('message', 'Email Cannot Send');
        } else {
            return redirect()->back()->with('message', 'Sent Email Successfully.');
        }
    }

    public function sendSingleSMS(Request $request)
    {

        if ($request->id) {

            $invoice = Invoice::where('invoices.total_payable', '>', 0)
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                })
                ->where('sms_sent_status', '<>', 'sent')
                ->find($request->id);

            if ($invoice->phone && $invoice->sms_sent_status != 'sent') {

                $sms_template = EmailTemplate::whereJsonContains('default_name', ['key' => 'bill_invoice'])
                    ->where('type', '=', 'sms')
                    ->first();
                //check sms template
                if ($sms_template) {

                    // $sms_message = 'Testing';
                    $sms_message = $sms_template->body;
                    $sms_response = null;
                    $success = false;
                    $message = $this->replaceInvoiceMarkup($request->id, $sms_message);
                    $success = $this->deliverSMS($invoice->phone, $message);
                    $billing_data = Invoice::find($invoice->id);
                    $billing_data->sent_date = date('j M Y');
                    $billing_data->sms_sent_status = ($success) ? "sent" : "error";
                    $billing_data->update();
                    if ($success) {
                        return redirect()->back()->with('message', 'Sent SMS Successfully.');
                    } else {
                        return redirect()->back()->with('message', 'SMS Cannot Send');
                    }
                } // end of check sms template
            } // end of check phone exists or not  
            else {
                return redirect()->back()->with('message', 'Customer does not have phone number !');
            }
        } //end of check ID exists or not
    }

    public function sendAllSMS(Request $request)
    {
        $invoices =  Invoice::join('customers', 'customers.id', '=', 'invoices.customer_id')
            ->where('invoices.total_payable', '>', 0)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->where('bill_id', '=', $request->bill_id)
            ->where(function ($query) {
                return $query->where('invoices.sms_sent_status', '<>', 'sent')
                    ->orwherenull('invoices.sms_sent_status');
            })
            ->select('invoices.*')
            ->get();

        foreach ($invoices as $invoice) {
            dispatch(new BillingSMSJob($invoice->id));
        } //end of foreach invoices
        return redirect()->back()->with('message', 'SMS Sending Process Running in Background.');
    }
    public function sendBillReminder(Request $request)
    {
        $receipts = ReceiptRecord::where('bill_id', '=', $request->bill_id)
            ->select('invoice_id')
            ->get()
            ->toArray();
        $invoices =  Invoice::join('customers', 'customers.id', '=', 'invoices.customer_id')
            ->where('invoices.total_payable', '>', 0)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->whereNotIn('invoices.id', $receipts)
            ->where('bill_id', '=', $request->bill_id)
            ->where(function ($query) {
                return $query->where('invoices.reminder_sms_sent_status', '<>', 'sent')
                    ->orwherenull('invoices.reminder_sms_sent_status');
            })
            ->select('invoices.*')
            ->get();

        foreach ($invoices as $invoice) {
            dispatch(new ReminderSMSJob($invoice->id, count($invoices), Auth::id()));
        } //end of foreach invoices
        return redirect()->back()->with('message', 'Bill Reminder Sending Process Running in Background.');
    }
    public function destroyInvoice(Request $request, $id)
    {
        if ($request->has('id')) {
            $invoice = Invoice::find($request->input('id'));
            $receipt = ReceiptRecord::where('invoice_id', '=', $request->input('id'))->first();
            if ($receipt) {
                $receipt_id = $receipt->id;
                ReceiptRecord::find($receipt_id)->delete();
                $months = 12;
                while ($months > 0) {
                    $status =  ReceiptSummery::where($months, '=', $receipt_id)
                        ->where('customer_id', '=', $invoice->customer_id)
                        ->first();
                    if ($status) {
                        $status->$months = null;
                        $status->update();
                    }
                    $months--;
                }
                $receipt->delete();
            }
            $invoice->delete();
            return redirect()->back();
        }
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            BillingTemp::find($request->input('id'))->delete();
            return redirect()->back();
        }
    }
    public function destroyall()
    {
        BillingTemp::truncate();
        return redirect()->back();
    }
}
