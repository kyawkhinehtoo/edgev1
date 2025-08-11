<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Customer;
use App\Models\TempInvoiceItem;
use App\Models\TempInvoice;
use App\Models\TempBill;
use App\Models\Isp;
use App\Models\Status;
use App\Models\Package;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BillingControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_calculates_prorated_billing_for_same_month_installation_and_termination()
    {
        // Create necessary test data
        $isp = Isp::factory()->create();
        $status = Status::factory()->create(['type' => 'active']);
        $package = Package::factory()->create(['price' => 30000]); // 30,000 MMK per month
        
        // Create a customer installed on Aug 2nd and terminated on Aug 20th, 2025
        $customer = Customer::factory()->create([
            'isp_id' => $isp->id,
            'status_id' => $status->id,
            'package_id' => $package->id,
            'service_activation_date' => Carbon::parse('2025-08-02'),
            'service_termination_date' => Carbon::parse('2025-08-20'),
            'installation_date' => Carbon::parse('2025-08-01'),
            'name' => 'Test Customer'
        ]);

        // Make request to generate billing for August 2025
        $response = $this->post('/billing/generate', [
            'bill_year' => 2025,
            'bill_month' => 8,
            'bill_number' => 'TEST-001',
            'issue_date' => '2025-08-01',
            'due_date' => '2025-08-31',
            'usd_exchange_rate' => 2100
        ]);

        // Check that billing was created
        $tempInvoiceItem = TempInvoiceItem::where('customer_id', $customer->id)
            ->where('type', 'ProRatedPortLeasing')
            ->first();

        $this->assertNotNull($tempInvoiceItem);
        
        // Calculate expected amount
        // Aug 2 to Aug 20 = 19 days (2nd to 20th inclusive)
        $expectedDays = 19;
        $monthlyRate = 30000;
        $daysInAugust = 31;
        $expectedAmount = round(($monthlyRate / $daysInAugust) * $expectedDays);
        
        $this->assertEquals($expectedAmount, $tempInvoiceItem->total_amount);
        $this->assertEquals($customer->service_activation_date->format('Y-m-d'), $tempInvoiceItem->start_date);
        $this->assertEquals($customer->service_termination_date->format('Y-m-d'), $tempInvoiceItem->end_date);
        $this->assertStringContainsString('Installed & Terminated same month', $tempInvoiceItem->description);
        $this->assertStringContainsString("{$expectedDays} days", $tempInvoiceItem->description);
    }

    /** @test */
    public function it_calculates_normal_prorated_billing_for_new_installation_without_termination()
    {
        // Create necessary test data
        $isp = Isp::factory()->create();
        $status = Status::factory()->create(['type' => 'active']);
        $package = Package::factory()->create(['price' => 30000]);
        
        // Create a customer installed on Aug 2nd without termination
        $customer = Customer::factory()->create([
            'isp_id' => $isp->id,
            'status_id' => $status->id,
            'package_id' => $package->id,
            'service_activation_date' => Carbon::parse('2025-08-02'),
            'service_termination_date' => null,
            'installation_date' => Carbon::parse('2025-08-01'),
            'name' => 'Test Customer 2'
        ]);

        // Make request to generate billing for August 2025
        $response = $this->post('/billing/generate', [
            'bill_year' => 2025,
            'bill_month' => 8,
            'bill_number' => 'TEST-002',
            'issue_date' => '2025-08-01',
            'due_date' => '2025-08-31',
            'usd_exchange_rate' => 2100
        ]);

        // Check that billing was created
        $tempInvoiceItem = TempInvoiceItem::where('customer_id', $customer->id)
            ->where('type', 'ProRatedPortLeasing')
            ->first();

        $this->assertNotNull($tempInvoiceItem);
        
        // Calculate expected amount
        // Aug 2 to Aug 31 = 30 days
        $expectedDays = 30;
        $monthlyRate = 30000;
        $daysInAugust = 31;
        $expectedAmount = round(($monthlyRate / $daysInAugust) * $expectedDays);
        
        $this->assertEquals($expectedAmount, $tempInvoiceItem->total_amount);
        $this->assertEquals($customer->service_activation_date->format('Y-m-d'), $tempInvoiceItem->start_date);
        $this->assertEquals('2025-08-31', $tempInvoiceItem->end_date);
        $this->assertStringNotContainsString('Installed & Terminated same month', $tempInvoiceItem->description);
        $this->assertStringContainsString("{$expectedDays} days", $tempInvoiceItem->description);
    }
}
