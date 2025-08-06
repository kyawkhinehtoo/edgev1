<!DOCTYPE html>
<html>
<head>
    <title>ODN DN Splitters Excel/CSV Import</title>
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
    <h2 class="text-title">Import ODN DN Splitters Excel/CSV</h2>
    
        <div class="alert alert-warning" role="alert">
        <strong>Excel Format Requirements:</strong>
        <ul>
            <li><strong>Splitter Name:</strong> Name of the DN Splitter (required)</li>
            <li><strong>Pop Device Name:</strong> Name of the parent POP device (required)</li>
            <li><strong>Core Number:</strong> Core number assignment</li>
            <li><strong>Core Color:</strong> Core color (e.g., "Xanh", "Cam", "Trang")</li>
            <li><strong>Port Quantity:</strong> Number of ports (default: 8)</li>
            <li><strong>Type:</strong> "splitter" or "switch" (default: "splitter")</li>
            <li><strong>Status:</strong> Status of the DN Splitter (default: "Active")</li>
            <li><strong>Location:</strong> Physical location description</li>
        </ul>
    </div>

    <div class="alert alert-info" role="alert">
        <strong>📥 Download Sample Template:</strong>
        <a href="{{ asset('templates/dn_splitters_template.csv') }}" class="btn btn-sm btn-outline-primary ml-2" download="dn_splitters_template.csv">
            <i class="fas fa-download"></i> Download DN Splitters Template
        </a>
        <br><small class="text-muted mt-1 d-block">Download this template, fill it with your data, and upload it below.</small>
    </div>

    <form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px;" action="{{ route('importOdnDn') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="import_file">Choose Excel/CSV File:</label>
            <input type="file" name="import_file" id="import_file" class="form-control" accept=".xlsx,.xls,.csv" required />
        </div>
        <button type="submit" class="btn btn-primary">Import ODN DN Splitters</button>
    </form>
    
    <div class="mt-4">
        <small class="text-muted">
            <strong>Note:</strong> The import process will:
            <ul>
                <li>Create DN Boxes if they don't exist</li>
                <li>Create or update DN Splitters with the provided information</li>
                <li>Link them to existing PopDevices and FiberCables</li>
                <li>Log all operations to odn_dn_import.log</li>
            </ul>
        </small>
    </div>
</div>
   
</body>
</html>
