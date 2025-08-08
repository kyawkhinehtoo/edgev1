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
            <li><strong>DN Splitter:</strong> Name of the parent DN Splitter (required)</li>
            <li><strong>Location:</strong> Coordinates in latitude,longitude format (required)</li>
            <li><strong>Status:</strong> "active" or "inactive" (default: "active")</li>
        </ul>
        <hr>
        <strong>SN Splitter Fields (Optional):</strong>
        <ul>
            <li><strong>SN Splitter Name:</strong> Name of the SN Splitter (optional)</li>
            <li><strong>Fiber Type:</strong> "patch_chord" or "distributed_route" (default: "patch_chord")</li>
            <li><strong>Port Number:</strong> Port number (1-8, default: 1)</li>
            <li><strong>Fiber Cable:</strong> Name of the fiber cable (required if fiber_type = "distributed_route")</li>
            <li><strong>Fiber Core Color:</strong> Format: "Color Number" (e.g., "Blue 1", "Orange 2")</li>
      
        </ul>
        <hr>
        <strong>Fiber Core Color Format:</strong>
        <ul>
            <li><strong>Format:</strong> "Color Number" (e.g., "Blue 1", "Orange 2")</li>
            <li><strong>Supported Colors:</strong> Blue, Orange, Green, Brown, Gray, White, Red, Black, Yellow, Purple, Pink, Aqua</li>
            <li><strong>Core Numbers:</strong> Any positive integer (e.g., 1, 2, 3, 12, 24)</li>
            <li><strong>Examples:</strong> "Blue 1", "Orange 12", "Green 5", "Purple 24"</li>
            <li><strong>Color Only:</strong> You can also specify just "Blue" without a number</li>
            <li><strong>Priority:</strong> If both "Fiber Core Color" and "Core Number" are provided, "Fiber Core Color" takes priority</li>
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
