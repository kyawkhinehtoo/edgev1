<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Incident;
use App\Models\Township;
use App\Models\Customer;
use App\Models\Package;
use App\Models\Role;
use App\Models\User;
use App\Models\IncidentHistory;
use App\Models\Task;
use App\Models\FileUpload;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use DateTime;
use App\Events\AddIncident;
use App\Events\UpdateIncident;
use App\Models\RootCause;
use App\Models\Subcom;
use App\Models\SubconChecklist;
use App\Models\SubconChecklistsGroup;
use App\Models\SubconChecklistValue;
use App\Models\SubconChecklistValueHistory;
use App\Models\SubRootCause;
use Illuminate\Support\Facades\Notification;
use App\Notifications\NewIncidentNotification;
use App\Notifications\NewTaskNotification;

class IncidentTaskController extends Controller
{
    public function index(Request $request)
    {
        //Auth::id();
        $user = User::with('role')->where('users.id', '=', Auth::user()->id)->first();
        $pendingRootCause = RootCause::with('subRootCauses')->where('is_maintenance',true)->where('is_pending',true)->get();
        $subRootCause = SubRootCause::get();
        if($user->user_type == 'isp' ){
            return abort(403, 'Unauthorized action.');
        }
        $task_write = true;
        if($user->user_type == 'isp'){
       
            $task_write = false;
        }
        else if($user->user_type == 'partner'){
           
            $task_write = false;
        }
        else if($user->user_type == 'subcon'){
           
            $task_write = true;
        }
        $subcon = Subcom::find($user->subcom_id);
        $id = $subcon?$subcon->id:null;

        $subcons = Subcom::all();
        if($user->user_type == 'subcon'){
            $subcons = Subcom::where('id',$user->subcom_id)->get(); 
        }
        $user = User::find(Auth::id());
        $tasks = Task::join('incidents as i', 'i.id', 'tasks.incident_id')
            ->join('customers as c', 'c.id', 'i.customer_id')
            ->join('users as u1', 'u1.id', 'i.incharge_id')
            ->when($request->keyword, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('c.ftth_id', 'LIKE', '%' . $search . '%')
                        ->orWhere('i.description', 'LIKE', '%' . $search . '%')
                        ->orWhere('tasks.description', 'LIKE', '%' . $search . '%');
                });
            })
            ->when($request->task, function ($query, $task) use ($id) {
                if ($task !== 'all')
                    $query->where('assigned', $id);
            }, function($query) use ($id){
                 $query->where('assigned', $id);
            })
            ->when($user->user_type=='subcon', function ($query) use ($id){
                $query->where('assigned', $id);
            })
            ->when($user->role?->incident_supervisor, function($query) use ($user){
                $query->where('i.supervisor_id',$user->id);
             })
            ->whereIn('i.status',[1,2,3,5])
            ->when($request->status, function ($query, $status) {
                if ($status == 1){
                    $query->whereIn('tasks.status', [1,4,5]);

                }else if ($status == 2 || $status == 3 ){
                    $query->where('tasks.status', '=', $status);
                }
                   
            }, function ($query) {
                $query->where('tasks.status', '=', 1);
            })
            ->select(
                'tasks.*',
                'u1.name as incharge',
                'i.code',
                'i.edge_code',
                'c.ftth_id',
                'c.id as customer_id',
                'i.priority',
                'i.type',
                'i.topic',
                'i.description as incident_description',
                'i.date',
                'i.time',
            )
            ->orderBy('tasks.id', 'DESC')
            ->paginate(10);
        $tasks->getCollection()->transform(function ($task) use ($user) {
                $remaining = $this->checkRemainingItems($task->id);
                $task->remaining = $remaining['remaining_count'];
            
            return $task;
        });
          // Get existing checklist values for this customer
        

        $tasks->appends($request->all())->links();
        return Inertia::render(
            'Client/IncidentTask',
            [
                'tasks' => $tasks,
                'subcon' => $subcon,
                'subcons' => $subcons,
                'user' => $user,
                'task_write' => $task_write,
                'pendingRootCause'=>$pendingRootCause,
                'subRootCause'=>$subRootCause,
            ]
        );
    }
    public function getTaskChecklistValues($task_id){

        $task = Task::with('incident')->findOrFail($task_id);
      
        $subconCheckList = SubconChecklist::where('service_type', $task->incident->type)->get();

        // Get existing checklist values for this customer
        $checklistValues = SubconChecklistValue::where('task_id', $task_id)->get();
        $checkListSummary = $this->checklistSummary($task_id);
        $taskCheckList = Task::with('incident.customer')->findOrFail($task_id);
        $formattedValues = [];
        $checklistImages = [];
        foreach ($checklistValues as $value) {
            $formattedValues[$value->subcon_checklist_id] = [
                'title' => $value->title,
                'attachment' => $value->attachment,
                'status' => $value->status
            ];

            if ($value->attachment) {
                $checklistImages[$value->subcon_checklist_id] = $value->attachment;
            }
        }

        // Add checklist values and images to customer object
        $taskCheckList->checklist_values = $formattedValues;
        $taskCheckList->checklist_images = $checklistImages;
        return response()->json([
            'subconCheckList' => $subconCheckList,
            'checklistValues' => $checklistValues,
            'checkListSummary' => $checkListSummary,
            'taskCheckList' => $taskCheckList,
        ]);
        
    }
      public function checklistSummary($taskId)
    {
        $user = User::with('role')->find(Auth::user()->id);
        $task = Task::with('incident','incident.customer')->where('id', $taskId)->firstOrFail();
        $service_type = ($task->incident->type=='termination')?'termination':'maintenance'; // Default service type, can be changed based on your logic
        $groups = SubconChecklistsGroup::with([
            'checklists' => function ($query) use ($service_type) {
                $query->where('service_type', $service_type);
            },
            'checklists.values' => function ($query) use ($taskId) {
                $query->where('task_id', $taskId);
            }
        ])
        ->where('category', $task->incident->type)
        ->get();
        
        $isSupervisor = $user->role?->incident_supervisor ?? false;
       
        $status = $task?->status ?? null;
        $showValues = !$isSupervisor || in_array($status, [2,4,5]);

        $result = $groups->map(function ($group) use ($showValues) {
            $total = count($group->checklists);
            $approved = $requested = $rejected = $remaining = $skip = 0;

            foreach ($group->checklists as $checklist) {
                if ($showValues) {
                    $value = $checklist->values->first();
                    if (!$value) {
                        $remaining++;
                    } elseif ($value->status === 'requested') {
                        $requested++;
                    } elseif ($value->status === 'skip') {
                        $skip++;
                    } elseif ($value->status === 'approved') {
                        $approved++;
                    } elseif ($value->status === 'declined') {
                        $rejected++;
                    } else {
                        $remaining++;
                    }
                } else {
                    $remaining++;
                }
            }

            return [
                'id' => $group->id,
                'required' => $group->required ,
                'group_name' => $group->name,
                'total' => $total,
                'requested' => $requested,
                'approved' => $approved,
                'rejected' => $rejected,
                'skip' => $skip,
                'remaining' => $remaining,
            ];
        });

        return $result;
    }
    // Check remaining checklist items for a task
    public function checkRemainingItems($taskId)
    {
        // Get all checklist IDs for the task's service type
        $task = Task::with('incident')->findOrFail($taskId);
        $service_type = ($task->incident->type=='termination')?'termination':'maintenance'; 
        
        $checklistIds = SubconChecklist::where('service_type', $service_type)->pluck('id');

        // Get checklist values for this task
        $filledChecklistIds = SubconChecklistValue::where('task_id', $taskId)
            ->whereIn('subcon_checklist_id', $checklistIds)
            ->pluck('subcon_checklist_id');

        // Remaining checklist IDs
        $remainingIds = $checklistIds->diff($filledChecklistIds);

        // Optionally, get the checklist items themselves
       // $remainingItems = SubconChecklist::whereIn('id', $remainingIds)->get();
        $remainingCount = $remainingIds->count();
    
        return [
            'remaining_count' => $remainingCount
            //'remaining_items' => $remainingItems,
        ];
    }
    public function updateTaskCheckList(Request $request, $taskId)
    {
  
        $user = User::with('role')->find(Auth::user()->id);
        if ($user->user_type == 'isp' || $user->user_type == 'partner') {
            return abort(403, 'Unauthorized action.');
        }
        // Get all request keys to identify checklist fields
        $requestKeys = array_keys($request->all());
        $checklistFields = [];
        $validationRules = [];
        // Extract checklist fields from request
        foreach ($requestKeys as $key) {
            if (preg_match('/^checklist_(\d+)_(title|attachment|status)$/', $key, $matches)) {
                $checklistId = $matches[1];
                $fieldType = $matches[2];

                if (!isset($checklistFields[$checklistId])) {
                    $checklistFields[$checklistId] = [];
                }

                $checklistFields[$checklistId][$fieldType] = $key;
            }
        }
         // Add validation rules for each checklist field
        foreach ($checklistFields as $checklistId => $fields) {
            $title = $fields['title'] ?? null;

            if (isset($fields['title'])) {
            $validationRules[$fields['title']] = 'nullable|string';
            }
            if (isset($fields['attachment'])) {
            $validationRules[$fields['attachment']] = 'nullable|image|max:10240';
            }
            if (isset($fields['status'])) {
            // If title is present and not empty in request, status is required
            if ($title && !empty($request->$title)) {
                $validationRules[$fields['status']] = 'required|string|in:requested,skip,approved,declined';
            } else {
                $validationRules[$fields['status']] = 'nullable|string|in:requested,skip,approved,declined';
            }
            }
        }
        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }
         // Process and save checklist values
        foreach ($checklistFields as $checklistId => $fields) {
            $title = isset($fields['title']) ? $request->input($fields['title']) : null;
            $status = isset($fields['status']) ? $request->input($fields['status']) : null;
            $attachmentPath = null;

            // Process attachment if exists
            if (isset($fields['attachment']) && $request->hasFile($fields['attachment'])) {
                $attachmentPath = $request->file($fields['attachment'])
                    ->store('checklist_attachments/' . $taskId, 'public');
            }

            // Find existing checklist value or create new one
            if ($title || $status) {


                $checklistValue = SubconChecklistValue::updateOrCreate(
                    [
                        'subcon_checklist_id' => $checklistId,
                        'task_id' => $taskId,
                    ],
                    [
                        'title' => $title??'NA',
                        'status' => $status,
                        'current_actor_user_id' => Auth::id(),
                        'last_status_changed_at' => now(),
                    ]
                );

                // Update attachment only if a new file was uploaded
                if ($attachmentPath) {
                    // Delete old attachment if exists
                    if ($checklistValue->attachment) {
                        Storage::delete('public/' . $checklistValue->attachment);
                    }
                    $checklistValue->attachment = $attachmentPath;
                    $checklistValue->save();
                }

                // Create history record
                SubconChecklistValueHistory::create([
                    'checklist_value_id' => $checklistValue->id,
                    'title' => $title??'NA',
                    'attachment' => $attachmentPath ?: $checklistValue->attachment,
                    'action' => $status,
                    'actor_id' => Auth::id(),
                    'acted_at' => now(),
                ]);
            }
        }
        

        return redirect()->back()->with('message', 'Task checklist updated successfully.');
    }
    public function update(Request $request, $id)
    {

        Validator::make($request->all(), [
            'incident_id' => ['required'],
            'assigned' => ['required'], 
            'target' => ['required'],
            'description' => ['required'],
            'status' => ['required'],
            'comment' => ['required_if:status,2,3', 'nullable', 'string'],
            'root_causes_id' => ['required_if:status,3', 'nullable'],
            'sub_root_causes_id' => ['required_if:status,3', 'nullable'],
            'complete_date' => ['required_if:status,2', 'nullable'],
        ],
        [
            'comment.required_if' => 'Please write comment before closing the task',
            'root_causes_id.required_if' => 'Please Choose Main Root Cause for Pending Task',
            'sub_root_causes_id.required_if' => 'Please Choose Sub Root Cause for Pending Task',
        ]
        )->validate();
        if ($request->has('id')) {
            $task = Task::find($request->input('id'));
            $originalValues = [
                'incident_id' => $task->incident_id,
                'assigned' => $task->assigned,
                'target' => $task->target,
                'description' => $task->description,
                'status' => $task->status,
                'comment' => $task->comment,
                'root_causes_id' => $task->root_causes_id,
                'sub_root_causes_id' => $task->sub_root_causes_id,
                'complete_date' => $task->complete_date
            ];
          
            $task->incident_id = $request->incident_id;
            $task->assigned = $request->assigned['id'];
            $task->target = $request->target;
            $task->description = $request->description;
            $task->status = $request->status;
            $task->comment = $request->comment;
            $task->root_causes_id = $request->root_causes_id;
            $task->sub_root_causes_id = $request->sub_root_causes_id;
            $task->complete_date = $request->complete_date;
            $task->updated_at = NOW();
            $task->update();

            $newValues = [
                'incident_id' => $request->incident_id,
                'assigned' => $request->assigned['id'],
                'target' => $request->target,
                'description' => $request->description,
                'status' => $request->status,
                'comment' => $request->comment,
                'root_causes_id' => $request->root_causes_id,
                'sub_root_causes_id' => $request->sub_root_causes_id,
                'complete_date' => $request->complete_date
            ];
            
            $hasChanges = false;
            foreach ($originalValues as $field => $value) {
                if ($value != $newValues[$field]) {
                    $hasChanges = true;
                    break;
                }
            }
                // If changes detected, create TaskHistory record
                if ($hasChanges) {
                    TaskHistory::create([
                        'task_id' => $task->id,
                        'incident_id' => $task->incident_id,
                        'assigned' => $task->assigned,
                        'target' => $task->target,
                        'status' => $task->status,
                        'description' => $task->description,
                        'comment' => $task->comment,
                        'root_causes_id' => $task->root_causes_id,
                        'sub_root_causes_id' => $task->sub_root_causes_id,
                        'complete_date' => $task->complete_date,
                    ]);
                }
            $data = array();

            $data['incident_id'] = $request->incident_id;
            $data['action'] = 'Task Updated:' . $request->description;
            $data['datetime'] = date('Y-m-j h:m:s');
            $data['actor_id'] = Auth::id();
            $this->updateStatus($request->incident_id);
            $this->insertHistory($data);

            $notiUsers  = User::whereHas('role', function ($query) {
                $query->where('enable_incident_notification', 1);
            })->get();
            $notificationMessage = 'Task Updated';
            $notificationAction = 'updated';
            // Send notification to users
            Notification::send($notiUsers, new NewTaskNotification($task, $notificationMessage, $notificationAction));
            return redirect()->back()->with('message', 'Task Updated Successfully.');
        }
    }
    public function updateStatus($incident_id)
    {

        $tasks = Task::where('incident_id', '=', $incident_id)->get();
        if ($tasks) {
            $completed = true;
            foreach ($tasks as $task) {
                if ($task->status != 2)
                    $completed = false;
            }
            if ($completed) {

                $update = Incident::where('id', '=', $incident_id)->whereNotIn('status', [3, 4])->update(['status' => 5]);
                if ($update) {
                    broadcast(new UpdateIncident($incident_id, 5))->toOthers();
                }
            } else {

                $update = Incident::where('id', '=', $incident_id)->whereNotIn('status', [3, 4])->update(['status' => 2]);
                if ($update) {
                    broadcast(new UpdateIncident($incident_id, 2))->toOthers();
                }
            }
        }
    }




    function console_log($output, $with_script_tags = true)
    {
        $js_code = 'console.log(' . json_encode($output, JSON_HEX_TAG) .
            ');';
        if ($with_script_tags) {
            $js_code = '<script>' . $js_code . '</script>';
        }
        echo $js_code;
    }

    public function getStatus($data)
    {
        $status = "Open";
        if ($data == 1) {
            $status = "Open";
        } else if ($data == 2) {
            $status = "Escalated";
        } else if ($data == 3) {
            $status = "Closed";
        } else if ($data == 4) {
            $status = "Deleted";
        } else if ($data == 5) {
            $status = "Resolved";
        }
        return $status;
    }
    public function insertHistory($data)
    {
        $ih = new IncidentHistory();

        $ih->incident_id = $data['incident_id'];
        $ih->action  = $data['action'];
        $ih->datetime = $data['datetime'];
        $ih->actor_id = $data['actor_id'];
        $ih->created_at = date("Y-m-j h:m:s");
        $ih->save();
    }
    public function checkInsertData($data)
    {
        $insert = null;
        foreach ($data as $key => $value) {
            if (!empty($value)) {
                if ($key == "customer_id") {
                    $insert .= $key . ':' . $value["name"] . ',';
                } else if ($key == "incharge_id") {
                    $insert .= $key . ':' . $value["name"] . ',';
                } else if ($key == "date" || $key == "start_date" || $key == "end_date") {
                    $insert .= $key . ':' . ucwords($value) . ',';
                } else  if ($key == "time") {
                    $insert .=  $key . ':' . ucwords($value) . ',';
                } else if ($key == "status") {
                    $insert .=  $key . ':' . $this->getStatus($value) . ',';
                } else {
                    $insert .=  $key . ':' . ucwords($value) . ',';
                }
            }
        }
        return $insert;
    }
    public function checkUpdate($old, $new)
    {

        $update = null;
        foreach ($new as $key => $value) {
            if (isset($old->$key) && !empty($key)) {

                if ($key == "customer_id") {
                    $update .= ($old->customer_id != $value['id']) ? $key . ':' . $value["name"] . ',' : '';
                } else if ($key == "incharge_id") {
                    $update .= ($old->incharge_id != $value['id']) ? $key . ':' . $value["name"] . ',' : '';
                } else if ($key == "date" || $key == "start_date" || $key == "end_date") {
                    $update .= (date("Y-m-j", strtotime($old->$key)) != $value) ? $key . ':' . ucwords($value) . ',' : '';
                } else  if ($key == "time") {
                    $update .= ($old->$key != strtotime($value)) ? $key . ':' . ucwords($value) . ',' : '';
                } else if ($key == "status") {
                    $update .=  ($old->$key != $value) ? $key . ':' . $this->getStatus($value) . ',' : '';
                } else if ($key == "package_id") {
                    $update .=  ($old->$key != $value) ? $key . ':' . $value["name"] . ',' : '';
                } else if ($key == "new_township") {
                    $update .=  ($old->$key != $value) ? $key . ':' . $value["name"] . ',' : '';
                } else {
                    try {
                        $update .= ($old->$key != $value) ? $key . ':' . ucwords($value) . ',' : '';
                    } catch (\Throwable $th) {
                        dd($key);
                    }
                }
            }
        }

        return $update;
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            Incident::find($request->input('id'))->delete();

            return redirect()->back();
        }
    }
}