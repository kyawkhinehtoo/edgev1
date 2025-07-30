<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FileUpload;

class FileController extends Controller
{
    public function upload(Request $request){
       
        
      $request->validate([
    'name' => 'required',
    'file' => [
        'required',
        'file',
        'max:20480', // 20MB
        function ($attribute, $value, $fail) {
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'csv', 'txt', 'xls', 'xlsx', 'pdf', 'kml', 'kmz'];
            $ext = strtolower($value->getClientOriginalExtension());

            if (!in_array($ext, $allowedExtensions)) {
                $fail("The $attribute must be a file of type: " . implode(', ', $allowedExtensions));
            }
        },
    ],
]);

        $fileUpload = new FileUpload;
        $user = auth()->user();
    
        if($request->file()) {
            
            $file_name = time().'_'.$request->file->getClientOriginalName();
            $file_path = $request->file('file')->storeAs('uploads', $file_name, 'public');
            if($request->incident_id){
                $fileUpload->incident_id = $request->incident_id;
            }
            if($request->customer_id){
                $fileUpload->customer_id = $request->customer_id;
            }
            $fileUpload->name = $request->name;
            $fileUpload->path = '/storage/' . $file_path;
            $fileUpload->created_by = $user->id; // Set the user who uploaded
            $fileUpload->save();

            return redirect()->back()->with('message', 'File Uploaded Successfully.');
        }
   }
}
