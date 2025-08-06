<!DOCTYPE html>
<html>
<head>
    <title>ODN Fiber Cables Excel/CSV Import</title>
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
    <h2 class="text-title">Import ODN Fiber Cables Excel/CSV</h2>
    
    <div class="alert alert-warning" role="alert">
        <strong>Excel Format Requirements:</strong>
        <ul>
            <li><strong>Name:</strong> Name of the Fiber Cable (required)</li>
            <li><strong>Core Quantity:</strong> Number of cores (default: 12)</li>
            <li><strong>Type:</strong> "feeder", "sub_feeder", or "distributed_route"</li>
            <li><strong>Cable Layout:</strong> "UG" (Underground) or "OH" (Overhead)</li>
            <li><strong>Cable Length:</strong> Length in kilometers (optional)</li>
            <li><strong>Status:</strong> Status of the cable (default: "Active")</li>
        </ul>
    </div>

    <div class="alert alert-info" role="alert">
        <strong>📥 Download Sample Template:</strong>
        <a href="{{ asset('templates/fiber_cables_template.csv') }}" class="btn btn-sm btn-outline-primary ml-2" download="fiber_cables_template.csv">
            <i class="fas fa-download"></i> Download Fiber Cables Template
        </a>
        <br><small class="text-muted mt-1 d-block">Download this template, fill it with your data, and upload it below.</small>
    </div>

    <form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px;" action="{{ route('importOdnFiberCable') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="import_file">Choose Excel/CSV File:</label>
            <input type="file" name="import_file" id="import_file" class="form-control" accept=".xlsx,.xls,.csv" required />
        </div>
        <button type="submit" class="btn btn-primary">Import ODN Fiber Cables</button>
    </form>
    
    <div class="mt-4">
        <small class="text-muted">
            <strong>Note:</strong> The import process will:
            <ul>
                <li>Create or update Fiber Cables with the provided information</li>
                <li>Validate cable types and layouts</li>
                <li>Set default values for missing fields</li>
                <li>Log all operations to odn_fiber_cable_import.log</li>
            </ul>
        </small>
    </div>
</div>
   
</body>
</html>
