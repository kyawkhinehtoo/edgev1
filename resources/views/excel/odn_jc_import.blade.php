<!DOCTYPE html>
<html>
<head>
    <title>ODN JC Boxes Excel/CSV Import</title>
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
    <h2 class="text-title">Import ODN JC Boxes Excel/CSV</h2>
    
    <div class="alert alert-warning" role="alert">
        <strong>Excel Format Requirements:</strong>
        <ul>
            <li><strong>JC Name:</strong> Name of the JC Box (required)</li>
            <li><strong>Location:</strong> Coordinates in latitude,longitude format</li>
            <li><strong>Type:</strong> "jc" or "cabinet" (default: "jc")</li>
            <li><strong>Status:</strong> Status of the JC Box (default: "Active")</li>
        </ul>
        <hr>
        <strong>Optional Core Assignment Fields:</strong>
        <ul>
            <li><strong>Source Fiber:</strong> Name of the source fiber cable</li>
            <li><strong>Dest Fiber:</strong> Name of the destination fiber cable</li>
            <li><strong>Source Color:</strong> Color of the source core</li>
            <li><strong>Source Port:</strong> Port number of the source</li>
            <li><strong>Dest Color:</strong> Color of the destination core</li>
            <li><strong>Dest Port:</strong> Port number of the destination</li>
            <li><strong>Assignment Status:</strong> Status of the core assignment</li>
        </ul>
    </div>

    <div class="alert alert-info" role="alert">
        <strong>📥 Download Sample Template:</strong>
        <a href="{{ asset('templates/jc_boxes_template.csv') }}" class="btn btn-sm btn-outline-primary ml-2" download="jc_boxes_template.csv">
            <i class="fas fa-download"></i> Download JC Boxes Template
        </a>
        <br><small class="text-muted mt-1 d-block">Download this template, fill it with your data, and upload it below.</small>
    </div>

    <form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px;" action="{{ route('importOdnJc') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="import_file">Choose Excel/CSV File:</label>
            <input type="file" name="import_file" id="import_file" class="form-control" accept=".xlsx,.xls,.csv" required />
        </div>
        <button type="submit" class="btn btn-primary">Import ODN JC Boxes</button>
    </form>
    
    <div class="mt-4">
        <small class="text-muted">
            <strong>Note:</strong> The import process will:
            <ul>
                <li>Create or update JC Boxes with the provided information</li>
                <li>Create core assignments if source and destination fiber data is provided</li>
                <li>Validate location format and box types</li>
                <li>Log all operations to odn_jc_import.log</li>
            </ul>
        </small>
    </div>
</div>
   
</body>
</html>
