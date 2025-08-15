# Discount Setup Implementation Documentation

## Overview
The `getPriceForCustomerByIsp` function has been enhanced to support discount functionality based on the `discount_setup` table.

## Database Structure
The `discount_setup` table contains:
- `port_sharing_service_id` (FK to port_sharing_services)
- `isp_id` (FK to isps) 
- `start_date` and `end_date` (date range for discount validity)
- `rate_type` ('percentage' or 'fixed')
- `discount_percentage` (for percentage-based discounts)
- `fix_rate` (for fixed-rate pricing)
- `is_active` (boolean flag)

## Discount Logic

### 1. Date Validation
- The system checks if the billing issue date falls between `start_date` and `end_date`
- Only active discounts (`is_active = true`) are considered

### 2. Discount Types

#### Percentage Discount (`rate_type = 'percentage'`)
```php
$discountAmount = ($originalFee * $discountSetup->discount_percentage) / 100;
$finalFee = $originalFee - $discountAmount;
```

#### Fixed Rate (`rate_type = 'fixed'`)
```php
// fix_rate contains JSON format similar to port_sharing_service.rate
// Example: [{"min": 1, "fees": 25000}, {"min": 50, "fees": 20000}]
$discountTiers = json_decode($discountSetup->fix_rate, true);
$finalFee = $appropriateDiscountTierFee; // Based on customer position
```

**Note**: For fixed rate discounts, the `fix_rate` field contains a JSON structure with tiers similar to the port sharing service rates. The system applies the appropriate tier fee based on the customer's position, providing discounted pricing that still respects the tier structure.

## Function Enhancement

### Updated Function Signature
```php
private function getPriceForCustomerByIsp($customer_id, $billingIssueDate = null)
private function applyDiscount($originalFee, $portSharingServiceId, $ispId, $billingIssueDate, $customerPosition = null)
```

### Return Values
```php
return [
    'fee' => $discountedFee,           // Final fee after discount
    'original_fee' => $fee,            // Original fee before discount
    'position' => $position,           // Customer position in ISP
    'discount_applied' => $boolean,    // Whether discount was applied
];
```

## Usage Example

### Scenario 1: Percentage Discount
- Original Fee: 50,000 MMK
- Discount: 20%
- Final Fee: 40,000 MMK

### Scenario 2: Fixed Rate Discount (JSON Tiers)
- Original Pricing: `[{"min": 1, "fees": 50000}, {"min": 50, "fees": 45000}]`
- Discount Pricing: `[{"min": 1, "fees": 35000}, {"min": 50, "fees": 30000}]`
- Customer Position: 25
- Final Fee: 35,000 MMK (from discount tier 1)

### Scenario 2a: Fixed Rate Discount (Higher Tier)
- Original Pricing: `[{"min": 1, "fees": 50000}, {"min": 50, "fees": 45000}]`
- Discount Pricing: `[{"min": 1, "fees": 35000}, {"min": 50, "fees": 30000}]`
- Customer Position: 75
- Final Fee: 30,000 MMK (from discount tier 2)

### Scenario 3: No Discount (outside date range or inactive)
- Original Fee: 50,000 MMK
- Final Fee: 50,000 MMK

## Implementation Notes

1. **Date Comparison**: Uses Carbon for accurate date comparison
2. **Fallback**: Returns original fee if no discount applies
3. **Performance**: Single database query to find applicable discount
4. **Flexibility**: Supports both percentage and fixed-rate discounts
5. **Audit Trail**: Returns both original and discounted fees for transparency

## Error Handling

- If `discount_setup` record not found: Returns original fee
- If `rate_type` is unknown: Returns original fee  
- If billing issue date is null: Returns original fee
- If original fee is null/0: Returns original fee

This implementation ensures backward compatibility while adding powerful discount functionality to the billing system.
