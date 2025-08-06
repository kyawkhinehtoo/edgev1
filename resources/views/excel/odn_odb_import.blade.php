<!DOCTYPE html>
<html>
<head>
    <title>ODN ODBs Excel/CSV Import</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.css" rel="stylesheet">
</head>
<body>
  
<div class="container" style="margin-top: 5rem;">
    @if($message = Session::get('success'))
        <div class="alert alert-info alert-dismissible fade in" role="alert">
          <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">×</span>
          </button>
          <strong>Success!</strong> {{ $message }}
        </div>
    @endif
    {!! Session::forget('success') !!}
    <br />
    <h2 class="text-title">Import ODN ODBs Excel/CSV</h2>
    
        <div class="alert alert-warning" role="alert">
        <strong>Excel Format Requirements:</strong>
        <ul>
            <li><strong>ODB Name:</strong> Name of the ODB (required)</li>
            <li><strong>Location:</strong> Coordinates in latitude,longitude format</li>
            <li><strong>Type:</strong> "wall", "pole", or "underground" (default: "wall")</li>
            <li><strong>Port Count:</strong> Number of ports (default: 8)</li>
            <li><strong>Status:</strong> Status of the ODB (default: "Active")</li>
        </ul>
        <hr>
        <strong>ODF (Optical Distribution Frame) Dependency:</strong>
        <ul>
            <li><strong>ODF Name:</strong> ODF that feeds this ODB (required for ODB connections)</li>
        </ul>
        <hr>
        <strong>Fiber Cable Connection (Optional):</strong>
        <ul>
            <li><strong>Fiber Cable Name:</strong> Cable connected to this ODB</li>
            <li><strong>Connected Core:</strong> Core number in the cable</li>
            <li><strong>Connected Color:</strong> Core color in the cable</li>
        </ul>
    </div>

    <div class="alert alert-info" role="alert">
        <strong>📥 Download Sample Template:</strong>
        <a href="{{ asset('templates/odbs_template.csv') }}" class="btn btn-sm btn-outline-primary ml-2" download="odbs_template.csv">
            <i class="fas fa-download"></i> Download ODBs Template
        </a>
        <br><small class="text-muted mt-1 d-block">Download this template, fill it with your data, and upload it below.</small>
    </div>

    <form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px;" action="{{ route('importOdnOdb') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="import_file">Choose Excel/CSV File:</label>
            <input type="file" name="import_file" id="import_file" class="form-control" accept=".xlsx,.xls,.csv" required />
        </div>
        <button type="submit" class="btn btn-primary">Import ODN ODBs</button>
    </form>
    
    <div class="mt-4">
        <small class="text-muted">
            <strong>Note:</strong> The import process will:
            <ul>
                <li>Create or update ODBs linked to existing ODFs</li>
                <li>Create ODB fiber cable connections if connection data is provided</li>
                <li>Validate port numbers and status values</li>
                <li>Log all operations to odn_odb_import.log</li>
            </ul>
        </small>
    </div>
</div>
   
</body>
</html>
