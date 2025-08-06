# ODN Import System Documentation

## Overview
The ODN (Optical Distribution Network) Import System has been extended with four additional import classes to handle different components of the network infrastructure.

## ⚠️ CRITICAL: Import Sequence Guide

Due to foreign key dependencies, imports **MUST** be performed in the following order:

### Phase 1: Foundation Infrastructure (No Dependencies)
**Import Order: 1-2**

#### 1. Fiber Cables (`OdnFiberCableImport`) - **FIRST**
- **Why First**: No dependencies on other ODN tables
- **Route**: `/odnFiberCableImportView`
- **Creates**: Base fiber infrastructure
- **Dependencies**: None

#### 2. POP Devices & ODFs (Manual/Existing)
- **Why Second**: Required for ODB creation
- **Note**: These should already exist in your system
- **Dependencies**: POP infrastructure

### Phase 2: Distribution Infrastructure
**Import Order: 3-4**

#### 3. JC Boxes (`OdnJcImport`) - **THIRD**
- **Why Third**: May create core assignments linking fiber cables
- **Route**: `/odnJcImportView`
- **Dependencies**: Fiber Cables (for core assignments)
- **Creates**: Junction closures and fiber-to-fiber connections

#### 4. ODBs (`OdnOdbImport`) - **FOURTH**
- **Why Fourth**: Requires existing ODFs
- **Route**: `/odnOdbImportView`
- **Dependencies**: ODFs, Fiber Cables (for connections)
- **Creates**: Optical distribution boxes and ODF-fiber connections

### Phase 3: Service Infrastructure
**Import Order: 5-6**

#### 5. DN Splitters (`OdnDnImport`) - **FIFTH**
- **Why Fifth**: Creates DN infrastructure needed for SN boxes
- **Route**: `/odnDnImportView`
- **Dependencies**: Fiber Cables, POP Devices
- **Creates**: DN Boxes and DN Splitters

#### 6. SN Boxes & Splitters (`OdnSnImport`) - **LAST**
- **Why Last**: Depends on DN Splitters from previous step
- **Route**: `/odnSnImportView`
- **Dependencies**: DN Splitters, Fiber Cables (for distributed_route type)
- **Creates**: Service node infrastructure

## Dependency Matrix

| Import Type | Depends On | Creates For |
|-------------|------------|-------------|
| **Fiber Cables** | None | JC, ODB, DN, SN |
| **JC Boxes** | Fiber Cables | Core assignments |
| **ODBs** | ODFs, Fiber Cables | ODB connections |
| **DN Splitters** | Fiber Cables, POP Devices | SN Boxes |
| **SN Boxes** | DN Splitters, Fiber Cables | End service points |

## Import Validation Checklist

### Before Starting ANY Import:
- [ ] Verify POP infrastructure exists (POP sites, POP devices)
- [ ] Verify ODF infrastructure exists
- [ ] Prepare Excel files in dependency order
- [ ] Test with small datasets first

### Pre-Import Data Validation:

#### For Fiber Cables (Import #1):
- [ ] All cable names are unique
- [ ] Types are: feeder, sub_feeder, or distributed_route
- [ ] Layouts are: UG or OH
- [ ] Core quantities are valid numbers

#### For JC Boxes (Import #3):
- [ ] Source/dest fiber cables exist (if using core assignments)
- [ ] Location coordinates are valid
- [ ] JC names are unique

#### For ODBs (Import #4):
- [ ] Referenced ODF names exist in system
- [ ] Fiber cable names exist (if creating connections)
- [ ] POP device names exist (if specified)
- [ ] Port numbers are within valid ranges

#### For DN Splitters (Import #5):
- [ ] Referenced fiber cable names exist
- [ ] Referenced POP device names exist
- [ ] Cabinet/DN Box names don't conflict
- [ ] Core colors and numbers are valid

#### For SN Boxes (Import #6):
- [ ] Referenced DN Splitter names exist from step #5
- [ ] Fiber cable names exist (for distributed_route type)
- [ ] Core numbers are valid for distributed_route

## Sample Import Workflow

### Step 1: Prepare Fiber Cables Excel
```
name,core_quantity,type,cable_layout,cable_length,status
"Main-Feeder-01",144,feeder,UG,2.5,Active
"Sub-Feeder-01",48,sub_feeder,OH,1.2,Active
"Dist-Route-01",12,distributed_route,UG,0.8,Active
```

### Step 2: Import Fiber Cables
- Navigate to `/odnFiberCableImportView`
- Upload Excel file
- Verify success in logs

### Step 3: Prepare JC Boxes Excel
```
jc_name,location,type,status,source_fiber,dest_fiber,source_color,source_port
"JC-001","16.8661,96.1951",jc,Active,"Main-Feeder-01","Sub-Feeder-01","Blue",1
```

### Step 4: Import JC Boxes
- Navigate to `/odnJcImportView`
- Upload Excel file
- Verify core assignments created

### Step 5: Prepare ODBs Excel
```
odb_name,odf_name,total_ports,status,fiber_cable,odb_port,pop_device,olt_port
"ODB-001","ODF-Main-01",96,active,"Sub-Feeder-01",1,"OLT-Device-01",1
```

### Step 6: Import ODBs
- Navigate to `/odnOdbImportView`
- Upload Excel file
- Verify ODB-fiber connections

### Step 7: Prepare DN Splitters Excel
```
cabinet,dn,linked_olt,source_fiber,source_fiber_core_color_number,location,status
"DN-Box-01","DN-Split-01","OLT-Device-01","Dist-Route-01","Blue-1","16.8661,96.1951",active
```

### Step 8: Import DN Splitters
- Navigate to `/odnDnImportView`
- Upload Excel file
- Verify DN infrastructure created

### Step 9: Prepare SN Boxes Excel
```
sn_name,dn_splitter,location,status,sn_splitter_name,fiber_type,port_number,fiber_cable,core_number
"SN-Box-01","DN-Split-01","16.8661,96.1951",active,"SN-Split-01",distributed_route,1,"Dist-Route-01",2
```

### Step 10: Import SN Boxes
- Navigate to `/odnSnImportView`
- Upload Excel file
- Verify final service infrastructure

## Error Recovery

### If Import Fails Mid-Process:
1. **Check log files** for specific errors
2. **Fix data issues** in Excel files
3. **Re-run imports** from the failed step onwards
4. **Use updateOrCreate behavior** - safe to re-import corrected data

### Common Dependency Errors:
- **"PopDevice not found"** → Verify POP infrastructure exists
- **"FiberCable not found"** → Import fiber cables first
- **"DN Splitter not found"** → Import DN splitters before SN boxes
- **"ODF not found"** → Create ODFs through admin interface first

## Data Integrity Notes

### Foreign Key Relationships:
```
FiberCables (no deps)
    ↓
JcBoxes → CoreAssignments
    ↓
ODFs → ODBs → OdbFiberCables
    ↓
PopDevices → DnSplitters
    ↓
DnSplitters → SnBoxes → SnSplitters
```

### Referential Integrity:
- All imports use `updateOrCreate` for idempotency
- Failed lookups are logged but don't stop processing
- Partial imports are supported
- Data validation prevents orphaned records

## Import Classes

### 1. OdnDnImport (Existing - Updated)
**File:** `app/Imports/OdnDnImport.php`
**Purpose:** Import DN (Distribution Node) Splitters
**Route:** `/odnDnImportView` (GET), `/importOdnDn` (POST)

**Excel Columns:**
- `cabinet` → DN Box name
- `dn` → DN Splitter name
- `linked_olt` → PopDevice device_name
- `source_fiber` → FiberCable name
- `source_fiber_core_color_number` → Core info (e.g., "Blue-1")
- `location` → Coordinates (lat,lng)
- `status` → active/inactive

### 2. OdnFiberCableImport (New)
**File:** `app/Imports/OdnFiberCableImport.php`
**Purpose:** Import Fiber Cables
**Route:** `/odnFiberCableImportView` (GET), `/importOdnFiberCable` (POST)

**Excel Columns:**
- `name` → Fiber cable name (required)
- `core_quantity` → Number of cores (default: 12)
- `type` → feeder/sub_feeder/distributed_route
- `cable_layout` → UG/OH (Underground/Overhead)
- `cable_length` → Length in kilometers (optional)
- `status` → Cable status (default: Active)

### 3. OdnJcImport (New)
**File:** `app/Imports/OdnJcImport.php`
**Purpose:** Import JC (Joint Closure) Boxes and Core Assignments
**Route:** `/odnJcImportView` (GET), `/importOdnJc` (POST)

**Excel Columns:**
- `jc_name` → JC Box name (required)
- `location` → Coordinates (lat,lng)
- `type` → jc/cabinet (default: jc)
- `status` → Status (default: Active)

**Optional Core Assignment Columns:**
- `source_fiber` → Source fiber cable name
- `dest_fiber` → Destination fiber cable name
- `source_color` → Source core color
- `source_port` → Source port number
- `dest_color` → Destination core color
- `dest_port` → Destination port number
- `assignment_status` → Assignment status

### 4. OdnOdbImport (New)
**File:** `app/Imports/OdnOdbImport.php`
**Purpose:** Import ODBs (Optical Distribution Boxes) and their connections
**Route:** `/odnOdbImportView` (GET), `/importOdnOdb` (POST)

**Excel Columns:**
- `odb_name` → ODB name (required)
- `odf_name` → Existing ODF name (required)
- `total_ports` → Port count (1-96, default: 96)
- `status` → active/inactive/maintenance

**Optional Connection Columns:**
- `fiber_cable` → Fiber cable name
- `odb_port` → ODB port number
- `pop_device` → PopDevice device_name
- `olt_port` → OLT port number
- `olt_cable_label` → Cable label
- `description` → Connection description
- `connection_status` → Connection status

### 5. OdnSnImport (New)
**File:** `app/Imports/OdnSnImport.php`
**Purpose:** Import SN (Service Node) Boxes and Splitters
**Route:** `/odnSnImportView` (GET), `/importOdnSn` (POST)

**Excel Columns:**
- `sn_name` → SN Box name (required)
- `dn_splitter` → Existing DN Splitter name (required)
- `location` → Coordinates (lat,lng)
- `status` → active/inactive

**Optional SN Splitter Columns:**
- `sn_splitter_name` → SN Splitter name
- `fiber_type` → patch_chord/distributed_route
- `port_number` → Port number (1-8, default: 1)
- `fiber_cable` → Fiber cable name (for distributed_route)
- `core_number` → Core number (for distributed_route)

## Features

### Error Handling
- All imports include comprehensive error logging
- Invalid data is logged and skipped without stopping the import
- Separate log files for each import type:
  - `odn_dn_import.log`
  - `odn_fiber_cable_import.log`
  - `odn_jc_import.log`
  - `odn_odb_import.log`
  - `odn_sn_import.log`

### Data Validation
- Column existence validation
- Format validation (coordinates, types, status values)
- Foreign key relationship validation
- Range validation for numeric fields

### Import Behavior
- Uses `updateOrCreate` for idempotent imports
- Creates related records where appropriate
- Maintains referential integrity
- Supports partial data imports

## User Interface

### Blade Templates
All import views follow consistent structure:
- `resources/views/excel/odn_fiber_cable_import.blade.php`
- `resources/views/excel/odn_jc_import.blade.php`
- `resources/views/excel/odn_odb_import.blade.php`
- `resources/views/excel/odn_sn_import.blade.php`

### Features
- File upload with format validation (.xlsx, .xls, .csv)
- Clear column requirements documentation
- Success/error message display
- Bootstrap styling for consistency

## Controller Integration

### ExcelController Updates
**File:** `app/Http/Controllers/ExcelController.php`

**New Methods:**
- `odnFiberCableImportView()` → Display fiber cable import form
- `importOdnFiberCable()` → Process fiber cable import
- `odnJcImportView()` → Display JC import form
- `importOdnJc()` → Process JC import
- `odnOdbImportView()` → Display ODB import form
- `importOdnOdb()` → Process ODB import
- `odnSnImportView()` → Display SN import form
- `importOdnSn()` → Process SN import

## Routes

### Web Routes
**File:** `routes/web.php`

```php
Route::get('odnFiberCableImportView',[ExcelController::class,'odnFiberCableImportView'])->name('odnFiberCableImportView');
Route::post('importOdnFiberCable',[ExcelController::class,'importOdnFiberCable'])->name('importOdnFiberCable');
Route::get('odnJcImportView',[ExcelController::class,'odnJcImportView'])->name('odnJcImportView');
Route::post('importOdnJc',[ExcelController::class,'importOdnJc'])->name('importOdnJc');
Route::get('odnOdbImportView',[ExcelController::class,'odnOdbImportView'])->name('odnOdbImportView');
Route::post('importOdnOdb',[ExcelController::class,'importOdnOdb'])->name('importOdnOdb');
Route::get('odnSnImportView',[ExcelController::class,'odnSnImportView'])->name('odnSnImportView');
Route::post('importOdnSn',[ExcelController::class,'importOdnSn'])->name('importOdnSn');
```

## Usage

### ⚠️ MANDATORY Import Sequence
**Follow this exact order to avoid dependency errors:**

1. **First**: `/odnFiberCableImportView` - Import all fiber cables
2. **Second**: Manual ODF creation (if not exists) via admin interface
3. **Third**: `/odnJcImportView` - Import JC boxes and core assignments
4. **Fourth**: `/odnOdbImportView` - Import ODBs and connections
5. **Fifth**: `/odnDnImportView` - Import DN splitters and boxes
6. **Last**: `/odnSnImportView` - Import SN boxes and splitters

### Excel Preparation Strategy

#### Phase 1 Files:
- `01_fiber_cables.xlsx` - All network fiber infrastructure
- Verify ODFs exist in system

#### Phase 2 Files:
- `02_jc_boxes.xlsx` - Junction closures with fiber references
- `03_odbs.xlsx` - Distribution boxes with ODF/fiber references

#### Phase 3 Files:
- `04_dn_splitters.xlsx` - Distribution splitters with fiber/POP references
- `05_sn_infrastructure.xlsx` - Service nodes with DN splitter references

### Import Validation Process:
1. **Prepare data files** in dependency order
2. **Validate references** exist before importing
3. **Import in sequence** - do not skip steps
4. **Check logs** after each import
5. **Fix errors** before proceeding to next phase

### Rollback Strategy:
- Each import logs operations for troubleshooting
- Safe to re-run imports with corrected data
- `updateOrCreate` prevents duplicate entries
- Delete problematic records manually if needed

## Dependencies

### Models Used
- `FiberCable` → Fiber cable management
- `JcBox` → Junction box management
- `CoreAssignment` → Core assignments in JC boxes
- `Odb` → Optical distribution box
- `Odf` → Optical distribution frame
- `OdbFiberCable` → ODB-fiber connections
- `SnBox` → Service node boxes
- `SnSplitter` → Service node splitters
- `DnSplitter` → Distribution node splitters
- `PopDevice` → POP equipment

### Laravel Packages
- `maatwebsite/excel` for Excel processing
- Standard Laravel validation and storage

## Maintenance

### Log Management
- Import logs are stored in `storage/logs/`
- Each import type has separate log files
- Logs include timestamps and detailed error information

### Performance
- `ini_set('max_execution_time', 0)` for large imports
- Batch processing for memory efficiency
- Transaction handling for data integrity

## Quick Reference Card

### 🚀 Correct Import Order (MEMORIZE THIS!)
```
1. 🔗 Fiber Cables          (No dependencies)
2. 🏢 ODFs                  (Manual/existing)
3. 🔧 JC Boxes              (Needs: Fiber Cables)
4. 📦 ODBs                  (Needs: ODFs, Fiber Cables)
5. 🌟 DN Splitters          (Needs: Fiber Cables, POP Devices)
6. 🎯 SN Boxes              (Needs: DN Splitters, Fiber Cables)
```

### 🔍 Pre-Import Checklist
- [ ] POP infrastructure exists
- [ ] ODF infrastructure exists  
- [ ] Excel files prepared in correct order
- [ ] Column headers match requirements
- [ ] Reference data validated
- [ ] Test import with small dataset

### 🚨 Common Errors & Solutions
| Error | Solution |
|-------|----------|
| "FiberCable not found" | Import fiber cables first |
| "PopDevice not found" | Verify POP infrastructure |
| "DN Splitter not found" | Import DN splitters before SN boxes |
| "ODF not found" | Create ODFs manually first |
| "Invalid location format" | Use "lat,lng" format |

### 📋 Import URLs Quick Access
```
/odnFiberCableImportView  # Step 1: Fiber Cables
/odnJcImportView          # Step 3: JC Boxes  
/odnOdbImportView         # Step 4: ODBs
/odnDnImportView          # Step 5: DN Splitters
/odnSnImportView          # Step 6: SN Boxes
```

### 📊 Success Metrics
- All imports complete without errors
- Log files show successful record creation
- Database relationships properly established
- No orphaned records
- All foreign keys resolved correctly
