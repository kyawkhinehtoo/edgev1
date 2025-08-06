# Service Type Filter Implementation

## Overview
Added a comprehensive service type filter with checkboxes for customers.service_type that affects all data matrices and charts in the ISP Dashboard. The filter includes options for 'FTTH', 'DIA', 'DPLC', 'IPLC', and 'ALL'.

## Changes Made

### 1. Backend Controller Changes (`app/Http/Controllers/DashboardController.php`)

#### Added Service Type Filter Parameter
- Added `$service_types = $request->input('service_types', ['ALL']);` to accept filter input
- Default value is `['ALL']` if not provided

#### Updated All Database Queries
Applied service type filtering to all major query sections:

1. **Customer Matrix by Port Sharing Service**
   - Matrix generation query
   - Grand total calculation

2. **Customer Matrix by Zone**
   - Zone matrix generation query
   - Zone grand total calculation

3. **Customer Matrix by Township**
   - Township matrix generation query
   - Township grand total calculation

4. **Customer Trends by Port Sharing Service (Monthly Chart)**
   - Chart data generation query

#### Filter Logic
```php
// Apply service type filter
if (!in_array('ALL', $service_types)) {
    $query->whereIn('customers.service_type', $service_types);
}
```

#### Updated Return Data
- Added `'service_types' => $service_types` to the response data

### 2. Frontend Vue Component Changes (`resources/js/Pages/Dashboard/IspDashboard.vue`)

#### Added Props
```javascript
service_types: {
    type: Array,
    default: () => ['ALL'],
}
```

#### Added Reactive Data
- `const service_types = ref([...props.service_types]);`

#### Added Service Type Filter UI
Created a new filter section with checkboxes for:
- ALL (exclusive selection)
- FTTH 
- DIA
- DPLC
- IPLC

#### Added Filter Logic Functions

1. **handleServiceTypeChange(serviceType)**
   - Handles checkbox interactions
   - When 'ALL' is selected, clears other selections
   - When specific service types are selected, removes 'ALL'
   - Automatically applies filter on change

2. **applyServiceTypeFilter()**
   - Sends POST request with all current filter parameters
   - Refreshes chart data on success

#### Updated All Existing Filter Functions
Updated `applyDateFilter()`, `applyZoneFilter()`, `applyTownshipFilter()`, and `applyChartFilter()` to include `service_types` parameter in requests.

## User Interface Features

### Service Type Filter Section
- Located at the top of the dashboard
- Clean checkbox interface with labels
- Shows currently selected service types
- Real-time filtering (no submit button needed)

### Filter Behavior
- **Exclusive ALL**: Selecting 'ALL' deselects all specific service types
- **Multiple Selection**: Users can select multiple specific service types (FTTH, DIA, DPLC, IPLC)
- **Auto-fallback**: If no service types are selected, automatically falls back to 'ALL'
- **Persistent State**: Filter state is maintained across page interactions

## Technical Implementation Details

### Database Integration
- Filters are applied using Laravel's `whereIn()` method on `customers.service_type`
- When 'ALL' is selected, no WHERE clause is added (shows all records)
- When specific service types are selected, only those records are included

### Data Flow
1. User selects/deselects checkboxes
2. `handleServiceTypeChange()` updates reactive state
3. `applyServiceTypeFilter()` sends POST request to backend
4. Controller applies filters to all queries
5. Updated data is returned and displayed
6. Charts are refreshed with new data

### Performance Considerations
- Filters are applied at the database level for optimal performance
- Single request updates all dashboard sections simultaneously
- Chart data regeneration uses the `key` refresh pattern

## Files Modified

1. **Backend**
   - `/app/Http/Controllers/DashboardController.php` - Added service type filtering to all queries

2. **Frontend**
   - `/resources/js/Pages/Dashboard/IspDashboard.vue` - Added UI and filtering logic

## Global Impact

The service type filter affects all four major dashboard sections:

1. **Customer Matrix by Port Sharing Service** - Counts filtered by service type
2. **Customer Matrix by Zone** - Zone-based counts filtered by service type  
3. **Customer Matrix by Township** - Township-based counts filtered by service type
4. **Customer Trends by Port Sharing Service (Monthly)** - Time-series chart data filtered by service type

## Usage

1. Navigate to ISP Dashboard
2. Use the Service Type Filter section at the top
3. Select desired service types (FTTH, DIA, DPLC, IPLC) or choose ALL
4. All dashboard data automatically updates to reflect the filter
5. Filters persist when using other dashboard features (date filters, zone filters, etc.)

The implementation provides a seamless, user-friendly filtering experience that enhances the dashboard's analytical capabilities for different service types.
