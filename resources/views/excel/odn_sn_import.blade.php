<!DOCTYPE html>
<html>
<head>
    <title>ODN SN Boxes & Splitters Excel/CSV Import</title>
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
    <h2 class="text-title">Import ODN SN Boxes & Splitters Excel/CSV</h2>
    
        <div class="alert alert-warning" role="alert">
        <strong>Excel Format Requirements:</strong>
        <ul>
            <li><strong>SN Name:</strong> Name of the SN Box (required)</li>
            <li><strong>DN Splitter Name:</strong> Reference to the parent DN Splitter (required)</li>
            <li><strong>Location:</strong> Coordinates in latitude,longitude format</li>
            <li><strong>Box Type:</strong> "sn_box" or "sn_splitter" (default: "sn_box")</li>
            <li><strong>Status:</strong> Status of the SN Box (default: "Active")</li>
        </ul>
        <hr>
        <strong>Optional SN Splitter Fields (when Box Type = "sn_splitter"):</strong>
        <ul>
            <li><strong>Splitter Type:</strong> "1x2", "1x4", "1x8", "1x16", "1x32"</li>
            <li><strong>Input Fiber Type:</strong> "SM" or "MM" (default: "SM")</li>
            <li><strong>Output Fiber Type:</strong> "SM" or "MM" (default: "SM")</li>
            <li><strong>Loss Rate:</strong> Loss rate in dB (optional)</li>
        </ul>
    </div>

    <div class="alert alert-info" role="alert">
        <strong>📥 Download Sample Template:</strong>
        <a href="{{ asset('templates/sn_boxes_template.csv') }}" class="btn btn-sm btn-outline-primary ml-2" download="sn_boxes_template.csv">
            <i class="fas fa-download"></i> Download SN Boxes Template
        </a>
        <br><small class="text-muted mt-1 d-block">Download this template, fill it with your data, and upload it below.</small>
    </div>

    <form style="border: 4px solid #a1a1a1;margin-top: 15px;padding: 10px;" action="{{ route('importOdnSn') }}" class="form-horizontal" method="post" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="form-group">
            <label for="import_file">Choose Excel/CSV File:</label>
            <input type="file" name="import_file" id="import_file" class="form-control" accept=".xlsx,.xls,.csv" required />
        </div>
        <button type="submit" class="btn btn-primary">Import ODN SN Boxes & Splitters</button>
    </form>
    
    <div class="mt-4">
        <small class="text-muted">
            <strong>Note:</strong> The import process will:
            <ul>
                <li>Create or update SN Boxes linked to existing DN Splitters</li>
                <li>Create SN Splitters if splitter data is provided</li>
                <li>Validate fiber types and port numbers</li>
                <li>Link to fiber cables for distributed_route type</li>
                <li>Log all operations to odn_sn_import.log</li>
            </ul>
        </small>
    </div>
</div>
   
</body>
</html>
