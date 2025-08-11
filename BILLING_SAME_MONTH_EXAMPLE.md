# Billing Calculation Example

## Scenario: Customer Installed and Terminated in Same Month

### Customer Details:
- **Installation Date**: August 2, 2025
- **Termination Date**: August 20, 2025
- **Monthly Rate (MRC)**: 30,000 MMK
- **Days in August**: 31 days

### Calculation:
1. **Active Days**: August 2 to August 20 = 19 days (inclusive)
2. **Daily Rate**: 30,000 MMK ÷ 31 days = 967.74 MMK per day
3. **Prorated Amount**: 967.74 MMK × 19 days = 18,387 MMK (rounded)

### What the System Will Generate:
- **Billing Type**: ProRatedPortLeasing
- **Start Date**: 2025-08-02 (Service Activation Date)
- **End Date**: 2025-08-20 (Service Termination Date)
- **Amount**: 18,387 MMK
- **Description**: "Prorated MRC for [Customer Name] (Installed & Terminated same month - 19 days)"

### Code Changes Made:
1. **Enhanced New Installation Logic**: Now checks if customer is also terminated in the same month
2. **Accurate Day Calculation**: Uses actual service dates instead of month boundaries
3. **Clear Description**: Indicates this is a same-month installation and termination
4. **Prevents Double Billing**: Ensures customer isn't processed again in existing customer logic

### Key Benefits:
- ✅ Fair billing - only charges for actual service days
- ✅ Clear audit trail with descriptive billing entries
- ✅ Prevents billing errors or double charges
- ✅ Handles edge case of same-month install/terminate scenarios
