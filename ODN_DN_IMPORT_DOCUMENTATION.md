# ODN DN Splitters Import Documentation

## Overview
The `OdnDnImport` class handles importing DN Splitters data from Excel/CSV files into the `dn_splitters` table. This import process creates or updates DN Splitters based on the provided Excel data.

## Excel Format Requirements

The Excel file should have the following columns (header row required):

| Column Name | Description | Required | Example |
|-------------|-------------|----------|---------|
| Cabinet | DN Box name (will be created if doesn't exist) | Yes | "DN-BOX-001" |
| DN | DN Splitter name | Yes | "DN-SPLITTER-001" |
| Linked OLT | PopDevice device_name (must exist) | Yes | "OLT-CENTRAL-01" |
| Source Fiber | FiberCable name (must exist) | Yes | "FIBER-MAIN-01" |
| Source Fiber Core Color & Number | Core color and number | Yes | "Blue-1", "Red-12", "Green-8" |
| Location | GPS coordinates (lat,lng) | No | "16.8661,96.1951" |
| Status | Status of the DN Splitter | Yes | "active" or "inactive" |

## Database Changes Made

### 1. Added `core_color` field to `dn_splitters` table
```sql
ALTER TABLE dn_splitters ADD COLUMN core_color VARCHAR(255) NULL AFTER core_number;
```

### 2. Updated `DnSplitter` model fillable fields
Added `core_color` to the fillable array in `/app/Models/DnSplitter.php`

## Import Logic

The import process:

1. **Skips empty rows** - Rows with empty Cabinet, DN, and Linked OLT fields
2. **Creates/finds DN Box** - Uses `firstOrCreate` to find existing or create new DN Box
3. **Validates relationships** - Ensures PopDevice and FiberCable exist
4. **Parses core information** - Extracts color and number from the combined field
5. **Creates/updates DN Splitter** - Uses `updateOrCreate` to avoid duplicates
6. **Logs operations** - All actions are logged to `storage/logs/odn_dn_import.log`

## Core Color & Number Parsing

The import supports various formats for the "Source Fiber Core Color & Number" field:
- `Blue-1` → Color: "Blue", Number: 1
- `Red-12` → Color: "Red", Number: 12
- `1-Green` → Color: "Green", Number: 1
- `Orange 8` → Color: "Orange", Number: 8

## Usage

### 1. Access the Import Page
Navigate to: `/odnDnImportView`

### 2. Prepare Excel File
Create an Excel file with the required columns and data.

### 3. Import Process
1. Select the Excel/CSV file
2. Click "Import ODN DN Splitters"
3. Check the success message
4. Review `storage/logs/odn_dn_import.log` for detailed results

## Error Handling

The import includes comprehensive error handling:
- **Missing PopDevice**: Logs error and skips row
- **Missing FiberCable**: Logs error and skips row
- **Invalid data**: Logs error with row details
- **Duplicate prevention**: Uses `updateOrCreate` to handle existing records

## Files Modified/Created

### Models
- `/app/Models/DnSplitter.php` - Added `core_color` to fillable

### Imports
- `/app/Imports/OdnDnImport.php` - New import class

### Controllers
- `/app/Http/Controllers/ExcelController.php` - Added import methods

### Views
- `/resources/views/excel/odn_dn_import.blade.php` - Import form

### Routes
- `/routes/web.php` - Added import routes

### Migrations
- `/database/migrations/add_core_color_to_dn_splitters_table.php` - Added core_color field

## Sample Excel Data

```
Cabinet         | DN              | Linked OLT      | Source Fiber    | Source Fiber Core Color & Number | Location        | Status
DN-BOX-001     | DN-SPLITTER-001 | OLT-CENTRAL-01  | FIBER-MAIN-01   | Blue-1                          | 16.8661,96.1951 | active
DN-BOX-002     | DN-SPLITTER-002 | OLT-CENTRAL-02  | FIBER-MAIN-02   | Red-2                           | 16.8662,96.1952 | active
DN-BOX-003     | DN-SPLITTER-003 | OLT-BRANCH-01   | FIBER-DIST-01   | Green-3                         | 16.8663,96.1953 | inactive
```

## Notes

1. **Prerequisites**: Ensure PopDevices and FiberCables exist before importing
2. **Performance**: Large imports may take time; execution time limit is removed
3. **Logging**: All operations are logged for troubleshooting
4. **Validation**: The import validates all required relationships
5. **Flexibility**: Supports both creating new and updating existing records
