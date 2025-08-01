<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;

use App\Models\Township;

use App\Models\Package;
use App\Models\Role;
use App\Models\User;

use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\CustomerHistory;
use App\Models\Incident;
use App\Models\IncidentHistory;

use App\Models\Status;
use App\Models\FileUpload;
use App\Models\InstallationService;
use App\Models\MaintenanceService;
use App\Models\Partner;
use App\Models\Subcom;
use App\Models\Task;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use DateTime;

class ServiceRequestController extends Controller
{
    public function index(Request $request)
    {
        $sorting = array('cid', 'asc');
        if ($request->sort) {
            $sorting[0] = $request->sort;
        }
        if ($request->order) {
            $sorting[1] = $request->order;
        }

        $closed = ($request->status == 'close') ? true : false;

        $packages = Package::get();
        $townships = Township::get();
        $users = User::get();
        $status = Status::get();
        $subcons = Subcom::where('disabled', 0)->get();
        $relocationServices = InstallationService::where('type', 'relocation')
            ->where('status', 1)
            ->get(['id', 'name']);
        $incidents = Incident::with('customer.snPort.snSplitter')
            ->with('customer.snPort.dnSplitter')
            ->with('customer.snPort.popDevice')
            ->with('customer.snPort.pop')
            ->with('customer.snPort.pop.partner')
            ->with('customer.currentAddress.township')
            ->join('users', 'users.id', '=', 'incidents.incharge_id')
            ->join('customers', 'customers.id', '=', 'incidents.customer_id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->leftjoin('tasks', 'incidents.id', 'tasks.incident_id')
            ->leftjoin('subcoms', 'tasks.assigned', 'subcoms.id')
            ->whereIn('incidents.type', ['relocation', 'termination', 'plan_change', 'suspension', 'resume'])
            ->when($closed, function ($query, $closed) {
                $query->whereIN('incidents.status', [3, 4]);
            }, function ($query) {
                $query->whereIN('incidents.status', [1, 2, 5]);
            })
            ->when($request->general, function ($query, $general) {
                $query->where(function ($query) use ($general) {
                    $query->where('customers.name', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.sale_channel', 'LIKE', '%' . $general . '%')
                        ->orWhere('incidents.type', 'LIKE', '%' . $general . '%')
                        ->orWhere('incidents.code', 'LIKE', '%' . $general . '%');
                });
            })
            ->when($sorting, function ($query, $sort = null) {
                $sort_by = 'customers.id';
                if ($sort[0] == 'id') {
                    $sort_by = 'incidents.id';
                } elseif ($sort[0] == 'date') {
                    $sort_by = 'incidents.date';
                } elseif ($sort[0] == 'cid') {
                    $sort_by = 'customers.ftth_id';
                } elseif ($sort[0] == 'type') {
                    $sort_by = 'incidents.type';
                } elseif ($sort[0] == 'start') {
                    $sort_by = 'incidents.start_date';
                } elseif ($sort[0] == 'end') {
                    $sort_by = 'incidents.end_date';
                } elseif ($sort[0] == 'request') {
                    $sort_by = 'incidents.incharge_id';
                } elseif ($sort[0] == 'status') {
                    $sort_by = 'incidents.status';
                }
                $query->orderBy($sort_by, $sort[1]);
            }, function ($query) {
                $query->orderBy('incidents.id', 'ASC');
            })
            ->select(
                'incidents.*',
                'customers.id as customer_id',
                'customers.ftth_id as ftth_id',
                'customers.package_id as current_package',
                'incidents.package_id as new_package',
                'users.name as incharge',
                'status.name as status_name',
                'status.id as status_id',
                'subcoms.id as subcon_id',
                'tasks.target as assign_date',
                'tasks.status as task_status',
                'tasks.complete_date as complete_date',
            )
            ->paginate(10);
        $incidents->appends($request->all())->links();
        $partners  = Partner::select('id', 'name')
            ->where('is_active', 1)
            ->get();
        return Inertia::render(
            'Client/ServiceRequest',
            [
                'incidents' => $incidents,
                'packages' => $packages,
                'townships' => $townships,
                'users' => $users,
                'status' => $status,
                'partners' => $partners,
                'subcons' => $subcons,
                'relocationServices' => $relocationServices
            ]
        );
    }
    public function assign(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'new_pop_id' => 'required|integer|exists:pops,id',
            'new_pop_device_id' => 'required|integer|exists:pop_devices,id',
            'new_sn_splitter_id' => 'required|integer|exists:sn_splitters,id',
            'new_dn_splitter_id' => 'required|integer|exists:dn_splitters,id',
            'new_port_number_id' => 'required|integer',
            'assign_date' => 'required|date',
            'subcon_id' => 'required|integer|exists:subcoms,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->has('id')) {
            $incident = Incident::find($request->input('id'));

            $incident->new_partner_id = $request->new_partner_id;
            $incident->new_pop_id = $request->new_pop_id;
            $incident->new_pop_device_id = $request->new_pop_device_id;
            $incident->new_dn_splitter_id = $request->new_dn_splitter_id;
            $incident->new_sn_splitter_id = $request->new_sn_splitter_id;
            $incident->new_port_number = $request->new_port_number_id;


            $incident->update();

            $task = Task::firstOrNew(['incident_id' => $request->input('id')]);
            $isNew = !$task->exists;
            $task->assigned = $request->subcon_id;
            $task->target = $request->assign_date;
            $task->description = 'Relocation';
            $task->status = !empty($task->status) ? $task->status : 1;
            $task->save();
            if ($isNew) {
                if ($incident->status != 2) {
                    $incident->status = 2;
                }
            } else {
                if ($incident->status != 2 && $incident->status != 3) {
                    $incident->status = 2;
                }
            }
            $incident->update();
        }
        return redirect()->route('servicerequest.index')->with('message', 'Incident has been assigned.');
    }
    public function update(Request $request)
    {
       
        if ($request->has('id')) {
            $incident = Incident::with('customer.snPort.snSplitter')
                ->with('customer.snPort.dnSplitter')
                ->with('customer.snPort.popDevice')
                ->with('customer.snPort.pop')
                ->with('customer.snPort.pop.partner')
                ->with('customer.currentAddress.township')
                ->join('users', 'users.id', '=', 'incidents.incharge_id')
                ->join('customers', 'customers.id', '=', 'incidents.customer_id')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->leftjoin('tasks', 'incidents.id', 'tasks.incident_id')
                ->whereIn('incidents.type', ['relocation', 'termination', 'plan_change', 'suspension', 'resume'])
                ->whereIn('incidents.status', [1, 2, 5])
                ->select(
                    'incidents.*',
                    'customers.id as customer_id',
                    'customers.ftth_id as ftth_id',
                    'customers.package_id as current_package',
                    'incidents.package_id as new_package',
                    'users.name as incharge',
                    'status.name as status_name',
                    'status.id as status_id',
                    'tasks.complete_date as complete_date',
                )
                ->where('incidents.id', '=', $request->input('id'))
                ->first();

            if ($request->type && $incident) {
             
                // Check if incident->code matches "T-0015-MGT" or "T-0015"
                if (!$incident->edge_code) {
                    $customer = Customer::find($incident->customer_id);
                    if ($customer) {
                        $maintenance = MaintenanceService::where('id', $customer->maintenance_service_id)->first();
                            if($maintenance && $maintenance->short_code){

                            $prefix_1 = 'RT';
                            // service_complaint - RT
                            // plan_change - PC 
                            // suspension - SS
                            // resume - RS
                            // termination - TE
                            // relocation - RL 
                            switch ($incident->type) {
                                case 'service_complaint':
                                    $prefix_1 = 'RT';
                                    break;
                                case 'plan_change':
                                    $prefix_1 = 'PC';
                                    break;
                                case 'suspension':
                                    $prefix_1 = 'SS';
                                    break;
                                case 'resume':
                                    $prefix_1 = 'RS';
                                    break;
                                case 'termination':
                                    $prefix_1 = 'TE';
                                    break;
                                case 'relocation':
                                    $prefix_1 = 'RL';
                                    break;
                                default:
                                    $prefix_1 = 'RT'; // Default to RT if type is not recognized
                                    break;
                            }

                            $today = date('Ymd');
                            $prefix_2 = strtoupper(substr($maintenance->short_code, 0, 2));
                            $pattern = '^[A-Z]{2}-[A-Z]{2}' . $today . '-([0-9]{4})$';
                            $maxCode = Incident::where('edge_code', 'REGEXP', $pattern)
                                                ->orderByDesc('edge_code')
                                                ->value('edge_code');
                            $nextNumber = 1;
                            if ($maxCode) {
                                preg_match('/'.$pattern.'/', $maxCode, $matches);
                                if (isset($matches[1])) {
                                    $nextNumber = intval($matches[1]) + 1;
                                }
                            }
                        
                            $incident->edge_code = $prefix_1.'-' . $prefix_2 . $today . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                        
                        }
                    }
                }
                $customer = Customer::find($incident->customer_id);

                //Relocation Customer
                if ($incident->new_address && $incident->location && $incident->new_township && $incident->type = 'relocation') {
                    $oldAddress = CustomerAddress::where('customer_id', $customer->id)
                        ->where('is_current', 1)
                        ->first();
                    if ($oldAddress) {
                        $oldAddress->is_current = 0;
                        $oldAddress->save();
                    }
                    $customerAddress = new CustomerAddress();
                    $customerAddress->customer_id = $customer->id;
                    $customerAddress->address = $incident->new_address;
                    $customerAddress->location = $incident->location;
                    $customerAddress->township_id = $incident->new_township;
                    $customerAddress->type = 'relocated';
                    $customerAddress->installation_date = $incident->complete_date;
                    $customerAddress->installation_service_id = $incident->relocation_service_id;
                    $customerAddress->is_current = 1;
                    $customerAddress->save();
                }

                // if ($incident->new_package)
                // $customer->package_id = $incident->new_package['id'];
                // if($incident->new_township){
                //     $customer->township_id = $incident->new_township;
                // }
                if ($incident->type == 'termination') {
                    $status = Status::where('name', 'LIKE', '%Termina%')->first();
                    $customer->status_id = $status->id;
                }
                if ($incident->type == 'suspension') {

                    $status = Status::where('name', 'LIKE', '%Waiting to Suspend%')->first();
                    $customer->status_id = $status->id;
                }
                if ($incident->type == 'resume') {
                    $status = Status::where('name', '=', 'Waiting to Resume')->first();
                    $customer->status_id = $status->id;
                }

                $customer->update();

                CustomerHistory::where('customer_id', '=', $incident->customer_id)->update(['active' => 0]);

                $new_history = new CustomerHistory();
                $new_history->old_status = $incident->status_id;
                $new_history->customer_id = $customer->id;
                $new_history->general = $incident->type;
                $new_history->type = $incident->type;
                $new_history->actor_id = Auth::user()->id;
                $new_history->active = 1;
                $new_history->date = date("Y-m-j h:m:s");

                if ($incident->start_date) {
                    $new_history->start_date = $incident->start_date;
                }

                if ($incident->end_date)
                    $new_history->end_date = $incident->end_date;

                if ($incident->type == 'relocation') {
                    //new
                    if ($incident->new_address)
                        $new_history->new_address = $incident->new_address;
                    if ($incident->new_location)
                        $new_history->new_location = $incident->new_location;
                    //old
                    $new_history->old_address = $incident->current_address;
                    $new_history->old_location = $incident->location;

                    $status = Status::where('name', '=', 'Active')->first();
                    $new_history->new_status = $status->id;
                    $incident->status = 3;
                }
                if ($incident->type == 'termination') {
                    $status = Status::where('name', 'LIKE', '%Termina%')->first();
                    $new_history->new_status = $status->id;
                    $incident->status = 3;
                }
                if ($incident->type == 'suspension') {
                    $status = Status::where('name', 'LIKE', '%Suspen%')->first();
                    $new_history->new_status = $status->id;
                    $incident->status = 7;
                }
                if ($incident->type == 'resume') {
                    $status = Status::where('name', '=', 'Active')->first();
                   // dd($incident->suspension_incident_id);
                   // $incident->suspension_incident_id = $incident->suspension_incident_id;
                    $new_history->new_status = $status->id;
                    $incident->status = 8;
                }
                if ($incident->type == 'plan change') {
                    //new
                    if ($incident->new_package)
                        $new_history->new_package = $incident->new_package['id'];
                    //old
                    if ($incident->current_package)
                        $new_history->old_package = $incident->current_package['id'];

                    // $status = Status::where('name','=','Active')->first();
                    // $new_history->new_status = $status->id;
                    $myDateTime = new DateTime;
                    $newtime = clone $myDateTime;
                    if ($incident->start_date) {
                        $myDateTime = new DateTime($incident->start_date);
                        $new_history->start_date = $newtime->format('Y-m-j h:m:s');
                    }

                    // if($myDateTime->format('d') <= 7){
                    //     $newtime->modify('first day of this month');
                    //     $new_history->start_date = $newtime->format('Y-m-j h:m:s');
                    // }else{
                    //     $newtime->modify('+1 month');
                    //     $newtime->modify('first day of this month');
                    //     $new_history->start_date = $newtime->format('Y-m-j h:m:s');
                    // }
                    $incident->status = 3;
                }
                $new_history->save();

                // $incident = Incident::find($incident->id);

                $incident->close_date = date("Y-m-j h:m:s");
                $incident->close_time = date("h:m:s");
                $incident->update();
                //close the incident

                $ih = new IncidentHistory();

                $ih->incident_id = $request->input('id');
                $ih->action  = 'Incident closed';
                $ih->datetime = date("Y-m-j h:m:s");
                $ih->actor_id = Auth::user()->id;
                $ih->created_at = date("Y-m-j h:m:s");
                $ih->save();


                return redirect()->route('servicerequest.index')->with('message', 'Incident has been Processed.');
            }
            // $customer = Customer::find($request->input('customer_id'));
            //if($request->status)
            //$customer->status_id = $request->status['id'];
        }
    }
}
