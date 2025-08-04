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
use App\Models\InstallationService;
use App\Models\Isp;
use App\Models\MaintenanceService;
use App\Models\RootCause;
use App\Models\Subcom;
use App\Models\SubRootCause;
use App\Models\TaskHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use DateTime;

use Illuminate\Support\Facades\Notification;
use App\Notifications\NewIncidentNotification;
use App\Notifications\NewTaskNotification;
use Mpdf\Tag\Main;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        //Auth::id();

      //  dd($request->date);
        $user = User::with('role')->where('users.id', '=', Auth::user()->id)->first();

        $rootCause = RootCause::with('subRootCauses')->where('is_maintenance',true)->where('is_pending',false)->get();
        $pendingRootCause = RootCause::with('subRootCauses')->where('is_maintenance',true)->where('is_pending',true)->get();
        $subRootCause = SubRootCause::get();
        $relocationServices = InstallationService::where('type', 'relocation')->get();
        $suspensionTickets = Incident::where('type', 'suspension')
            ->join('customers', 'incidents.customer_id', '=', 'customers.id')
             ->when($user->user_type, function ($query, $user_type) use ($user) {
                if ($user_type == 'partner') {
                    $query->where('customers.partner_id', '=', $user->partner_id);
                }
                if ($user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                }
            })
            ->whereIn('status', [1, 7])
            ->select('incidents.*')
            ->get();    
        $read_permission = false;
        $write_permission = false;
        $task_write = true;
        if ($user->user_type == 'isp') {
            $read_permission = true;
            $write_permission = true;
            $task_write = false;
        } else if ($user->user_type == 'partner' ) {
            $read_permission = true;
            $write_permission = false;
            $task_write = false;
        } else if ($user->user_type == 'subcon') {
            $read_permission = true;
            $write_permission = false;
            $task_write = true;
        } else if ($user->user_type == 'internal') {
           
            $read_permission = true;
            $write_permission = true;
            $task_write = $user->role?->incident_supervisor?? false;
        } else {
            $permission =  DB::table('roles')
                ->join('users', 'users.role_id', '=', 'roles.id')
                ->where('users.id', '=', Auth::id())
                ->select('roles.write_incident', 'roles.read_incident')
                ->get();
            if ($permission) {
                $read_permission = $permission[0]->read_incident;
                $write_permission = $permission[0]->write_incident;
            }
        }

        $myTasks = Task::where('assigned', $user->subcom_id)->pluck('id')->toArray();
        $townships = Township::get();
        $packages = Package::get();
        $critical = Incident::join('customers', 'incidents.customer_id', '=', 'customers.id')
            ->leftjoin('tasks', 'tasks.incident_id', 'incidents.id')
            ->where('priority', '=', 'critical')
            ->when($user->user_type, function ($query, $user_type) use ($user) {
                if ($user_type == 'partner') {
                    $query->where('customers.partner_id', '=', $user->partner_id);
                }
                if ($user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                }
            })
            ->when($user->user_type == 'subcon', function ($query) use ($myTasks) {
                $query->whereIn('tasks.id', $myTasks);
            })
            ->when($user->role?->incident_supervisor, function($query) use ($user){
                $query->where('incidents.supervisor_id',$user->id);
             })
            ->whereNotIn('incidents.status',[3,4])
            //->groupBy('incidents.id')
            ->count();
        $high = Incident::join('customers', 'incidents.customer_id', '=', 'customers.id')
            ->leftjoin('tasks', 'tasks.incident_id', 'incidents.id')
            ->where('priority', '=', 'high')
            ->when($user->user_type, function ($query, $user_type) use ($user) {
                if ($user_type == 'partner') {
                    $query->where('customers.partner_id', '=', $user->partner_id);
                }
                if ($user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                }
            })
            ->when($user->user_type == 'subcon', function ($query) use ($myTasks) {
                $query->whereIn('tasks.id', $myTasks);
            })
            ->when($user->role?->incident_supervisor, function($query) use ($user){
                $query->where('incidents.supervisor_id',$user->id);
             })
             ->whereNotIn('incidents.status',[3,4])
           // ->groupBy('incidents.id')
            ->count();
        $normal = Incident::join('customers', 'incidents.customer_id', '=', 'customers.id')
            ->leftjoin('tasks', 'tasks.incident_id', 'incidents.id')
            ->where('priority',  'normal')
            ->when($user->user_type, function ($query, $user_type) use ($user) {
                if ($user_type == 'partner') {
                    $query->where('customers.partner_id', '=', $user->partner_id);
                }
                if ($user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                }
            })
            ->when($user->user_type == 'subcon', function ($query) use ($myTasks) {
                $query->whereIn('tasks.id', $myTasks);
            })
            ->when($user->role?->incident_supervisor, function($query) use ($user){
                $query->where('incidents.supervisor_id',$user->id);
             })
            ->whereNotIn('incidents.status',[3,4])
            ->groupBy('incidents.id')
            ->count('incidents.id');
          
        $ticketRequest = Incident::join('customers', 'incidents.customer_id', '=', 'customers.id')
            ->leftjoin('tasks', 'tasks.incident_id', 'incidents.id')
            ->whereIn('incidents.status', [1, 5, 6,7,8,9,10])
            ->when($user->user_type, function ($query, $user_type) use ($user) {
                if ($user_type == 'partner') {
                    $query->where('customers.partner_id', '=', $user->partner_id);
                }
                if ($user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                }
            })
            ->when($user->user_type == 'subcon', function ($query) use ($myTasks) {
                $query->whereIn('tasks.id', $myTasks);
            })
            ->when($user->role?->incident_supervisor, function($query) use ($user){
                $query->where('incidents.supervisor_id',$user->id);
             })
            ->when($request->date, function ($query, $date) {
                
                if (!empty(trim($date)) && $date !== null && $date != 'null') {
                    $d = explode(',', $date);
                    $from = date($d[0]);
                    $to = date($d[1]);
                    $query->whereBetween('incidents.date', [$from, $to]);
                }
            })
            ->count();
        $teamAssign = Incident::join('customers', 'incidents.customer_id', '=', 'customers.id')
            ->leftjoin('tasks', 'tasks.incident_id', 'incidents.id')
            ->where('incidents.status', '=', '2')
            ->when($user->user_type, function ($query, $user_type) use ($user) {
                if ($user_type == 'partner') {
                    $query->where('customers.partner_id', '=', $user->partner_id);
                }
                if ($user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                }
            })
            ->when($user->user_type == 'subcon', function ($query) use ($myTasks) {
                $query->whereIn('tasks.id', $myTasks);
            })
            ->when($user->role?->incident_supervisor, function($query) use ($user){
                $query->where('incidents.supervisor_id',$user->id);
             })
            ->when($request->date, function ($query, $date) {
                if (!empty(trim($date)) && $date !== null && $date != 'null') {
                    $d = explode(',', $date);
                    $from = date($d[0]);
                    $to = date($d[1]);
                    $query->whereBetween('incidents.date', [$from, $to]);
                }
            })
            ->count();
         $supervisorAssign = Incident::join('customers', 'incidents.customer_id', '=', 'customers.id')
            ->leftjoin('tasks', 'tasks.incident_id', 'incidents.id')
            ->where('incidents.status', '=', '6')
            ->when($user->user_type, function ($query, $user_type) use ($user) {
                if ($user_type == 'partner') {
                    $query->where('customers.partner_id', '=', $user->partner_id);
                }
                if ($user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                }
            })
            ->when($user->user_type == 'subcon', function ($query) use ($myTasks) {
                $query->whereIn('tasks.id', $myTasks);
            })
            ->when($user->role?->incident_supervisor, function($query) use ($user){
                $query->where('incidents.supervisor_id',$user->id);
             })
            ->when($request->date, function ($query, $date) {
                if (!empty(trim($date)) && $date !== null && $date != 'null') {
                    $d = explode(',', $date);
                    $from = date($d[0]);
                    $to = date($d[1]);
                    $query->whereBetween('incidents.date', [$from, $to]);
                }
            })
            // , function ($query) {
            //     // If no date is provided, filter by today
            //     $today = date('Y-m-d');
            //     $query->whereDate('incidents.date', $today);
            // }
            ->count();
         $close = Incident::join('customers', 'incidents.customer_id', '=', 'customers.id')
            ->leftjoin('tasks', 'tasks.incident_id', 'incidents.id')
            ->where('incidents.status', '=', '3')
            ->when($user->user_type, function ($query, $user_type) use ($user) {
                if ($user_type == 'partner') {
                    $query->where('customers.partner_id', '=', $user->partner_id);
                }
                if ($user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                }
            })
            ->when($user->user_type == 'subcon', function ($query) use ($myTasks) {
                $query->whereIn('tasks.id', $myTasks);
            })
            ->when($user->role?->incident_supervisor, function($query) use ($user){
                $query->where('incidents.supervisor_id',$user->id);
             })
            ->when($request->date, function ($query, $date) {
                if (!empty(trim($date)) && $date !== null && $date != 'null') {
                    $d = explode(',', $date);
                    $from = date($d[0]);
                    $to = date($d[1]);
                    $query->whereBetween('incidents.date', [$from, $to]);
                }
            })
            ->count();
        $isps = Isp::when($user->user_type, function ($query, $user_type) use ($user) {
                if ($user_type == 'isp') {
                    $query->where('id', '=', $user->isp_id);
                }
            })->get();
        $slas = MaintenanceService::select('sla_hours')
            ->groupBy('sla_hours')
            ->get();
        $team = DB::table('users')
            ->select('users.name as name', 'users.id as id')
            ->get();
        $supervisors = DB::table('users')   
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->select('users.name as name', 'users.id as id')
            ->where('roles.incident_supervisor', 1)
            ->get();
        $partners = DB::table('users')   
            ->select('users.name as name', 'users.id as id')
            ->where('users.user_type', 'partner')
            ->get();
         $noc = User::with('role')
            ->where('disabled', '=', 0)
            ->select('name as name', 'id as id')
            ->get();
        $subcons = Subcom::get();
        if ($user->user_type == 'subcon') {
            $subcons = Subcom::where('id', $user->subcom_id)->get();
        }
        $customers = Customer::select('id', 'ftth_id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->join('maintenance_services', 'customers.maintenance_service_id', '=', 'maintenance_services.id')
            ->when($user->user_type, function ($query, $user_type) use ($user) {
                if ($user_type == 'partner') {
                    $query->where('customers.partner_id', '=', $user->partner_id);
                }
                if ($user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                }
            })
            ->whereIn('status.type',['active','suspense','terminate'])
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->select('customers.*', 'maintenance_services.id as maintenance_service_id','maintenance_services.name as maintenance_service_name','maintenance_services.service_type as maintenance_service_type')
            ->get();
        $orderby = null;
        if ($request->sort && $request->order) {
            $orderby = $request->sort . ' ' . $request->order;
        }
      
        $incidents =  DB::table('incidents')
            ->join('customers', 'incidents.customer_id', '=', 'customers.id')
            ->join('partners', 'customers.partner_id', '=', 'partners.id')
            ->join('maintenance_services', 'customers.maintenance_service_id', '=', 'maintenance_services.id')
            ->join('customer_addresses', 'customer_addresses.customer_id', '=', 'customers.id')
            ->join('townships', 'customer_addresses.township_id', '=', 'townships.id')
            ->join('users', 'incidents.incharge_id', '=', 'users.id')
            ->leftjoin('tasks', 'tasks.incident_id', 'incidents.id')
            ->when($user->user_type, function ($query, $user_type) use ($user) {
            if ($user_type == 'partner') {
                $query->where('incidents.partner_id', '=', $user->partner_id);
            }
            if ($user_type == 'isp') {
                $query->where('customers.isp_id', '=', $user->isp_id);
            }
            })
            ->when($user->user_type == 'subcon', function ($query) use ($myTasks) {
            $query->whereIn('tasks.id', $myTasks);
            })
            ->when($request->status, function ($query, $status) use ($user){
                if($status==1){
                  
                    return $query->whereIn('incidents.status', [1, 5, 6,7,8,9,10]);
                }
                 $query->where('incidents.status', '=', $status);
            }, function ($query) use ($user) {
                if($user->role?->incident_supervisor == 1){
                   return $query->whereRaw('incidents.status in (2,5,6,7,8,9,10)');
                }
                if($user->role?->incident_oss == 1){
                return $query->whereRaw('incidents.status in (1,2,5,6,7,8,9,10)');
                }
                return $query->whereRaw('incidents.status in (1,2,3,5,6,7,8,9,10)');
            })
            ->when($request->keyword, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('customers.ftth_id', 'LIKE', '%' . $search . '%')
                ->orWhere('incidents.edge_code', 'LIKE', '%' . $search . '%')
                ->orWhere('incidents.code', 'LIKE', '%' . $search . '%');
            });
            })
            ->when($request->type, function ($query, $type) {
            $query->where('incidents.type', '=', $type);
            })
            ->when($request->member, function ($query, $member) {
            $query->where('users.id', '=', $member);
            })
            ->when($request->priority, function ($query, $priority) {
            $query->where('incidents.priority', '=', $priority);
            })
            ->when($request->date, function ($query, $date) {
                if(!empty(trim($date)) && $date !== null && $date != 'null') {
                 
                $d = explode(',', $date);
                $from = date($d[0]);
                $to = date($d[1]);
                $query->whereBetween('incidents.date', [$from, $to]);
                }
            })
            ->when($user->role?->incident_supervisor, function($query) use ($user){
               $query->where('incidents.supervisor_id',$user->id);
            })
            ->when($request->isp, function ($query, $isp) use ($user) {
                if ($user->user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                } else {
                    $query->where('customers.isp_id', '=', $isp);
                }
            })
            ->when($request->sla_hour, function ($query, $sla_hours) {
                $query->where('maintenance_services.sla_hours', '=', $sla_hours);
            })
            ->when($orderby, function ($query, $sort) {
            $query->orderByRaw($sort);
            }, function ($query) {
            $query->orderBy('incidents.id', 'DESC');
            })
            ->where('customer_addresses.is_current', '=', 1)
            ->select(
            'incidents.*',
            'customers.ftth_id as ftth_id',
            'townships.short_code as township_short',
            'users.name as incharge',
            'maintenance_services.id as maintenance_service_id',
            'maintenance_services.name as maintenance_service_name',
            'maintenance_services.service_type as maintenance_service_type',
            'maintenance_services.sla_hours as maintenance_sla_hours',
            'partners.name as customer_partner_name',
            'partners.id as customer_partner_id',
            )
            ->groupBy('incidents.id')
            ->paginate(10);
          
        $maintenanceServices = MaintenanceService::where('status', '=', 1)->get();
        // foreach ($incidents as $value) {
        //     $max_invoice_id =  DB::table('tasks')
        //                                 ->where('tasks.invoice_id', '=', $value->id)
        //                                 ->latest('id')->first();
        // }

        // $incidents_2 =  DB::table('incidents')
        //     ->join('customers', 'incidents.customer_id', '=', 'customers.id')
        //     ->join('users', 'incidents.incharge_id', '=', 'users.id')
        //     ->when($user->user_type, function ($query, $user_type) use ($user) {
        //         if ($user_type == 'partner') {
        //             $query->where('customers.partner_id', '=', $user->partner_id);
        //         }
        //         if ($user_type == 'isp') {
        //             $query->where('customers.isp_id', '=', $user->isp_id);
        //         }
        //         if ($user_type == 'subcon') {
        //             $query->where('customers.subcom_id', '=', $user->subcom_id);
        //         }
        //     })
        //     ->when($request->status, function ($query, $status) use ($user){
        //          $query->where('incidents.status', '=', $status);
        //     }, function ($query) use ($user) {
        //         if($user->role?->incident_supervisor == 1){
        //            return $query->whereRaw('incidents.status in (2,5,6)');
        //         }
        //         if($user->role?->incident_oss == 1){
        //         return $query->whereRaw('incidents.status in (1,5,6)');
        //         }
        //         return $query->whereRaw('incidents.status in (1,2,3,5,6)');
        //     })
        //     ->when($request->keyword, function ($query, $search) {
        //         $query->where(function ($query) use ($search) {
        //             $query->where('customers.ftth_id', 'LIKE', '%' . $search . '%')
        //                 ->orWhere('incidents.edge_code', 'LIKE', '%' . $search . '%')
        //                 ->orWhere('incidents.code', 'LIKE', '%' . $search . '%');
        //         });
        //     })
        //     ->when($orderby, function ($query, $sort) {
        //         $query->orderByRaw($sort);
        //     }, function ($query) {
        //         $query->orderBy('incidents.id', 'DESC');
        //     })
        //     ->when($user->role?->incident_supervisor, function($query) use ($user){
        //         $query->where('incidents.supervisor_id',$user->id);
        //      })
        //     ->select(
        //         'incidents.*',
        //         'incidents.status as status',
        //         'customers.ftth_id as ftth_id',
        //         'customers.id as customer_id',
        //         'users.name as incharge',
        //     )
        //     ->get();
        $incidents->appends($request->all())->links();
        return Inertia::render(
            'Client/Incident',
            [
                'incidents' => $incidents,
                // 'incidents_2' => $incidents_2,
                'packages' => $packages,
                'team' => $team,
                'townships' => $townships,
                'customers' => $customers,
                'critical' => $critical,
                'high' => $high,
                'normal' => $normal,
                'read_permission' => $read_permission,
                'write_permission' => $write_permission,
                'task_write' => $task_write,
                'user' => $user,
                'subcons' => $subcons,
                'rootCause' => $rootCause,
                'subRootCause' => $subRootCause,
                'pendingRootCause' => $pendingRootCause,
                'relocationServices'=> $relocationServices,
                'supervisors'=> $supervisors,
                'ticketRequest' => $ticketRequest,
                'teamAssign' => $teamAssign,
                'supervisorAssign' => $supervisorAssign,
                'close' => $close,
                'suspensionTickets' => $suspensionTickets,
                'maintenanceServices' => $maintenanceServices,
                'isps' => $isps,
                'slas' => $slas,
                'noc' => $noc,
            ]
        );
    }

    public function getTask($id)
    {
        if ($id) {
            $query = DB::table('tasks')
                ->where('tasks.incident_id', '=', $id)
                ->where('tasks.status', '<>', 0);

            $user = User::with('role')->where('users.id', '=', Auth::user()->id)->first();

            if ($user->user_type == 'subcon') {
                $query->where('tasks.assigned', '=', $user->subcom_id);
            }

            $tasks = $query->orderBy('tasks.id', 'DESC')
                ->get();

            return response()->json($tasks, 200);
        }
    }
    public function getIncident($id)
    {
        if ($id) {
            $incident = DB::table('incidents')
                ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                ->join('users', 'incidents.incharge_id', '=', 'users.id')
                ->where('incidents.id', '=', $id)
                ->select(
                    'incidents.*',
                    'incidents.status as status',
                    'customers.ftth_id as ftth_id',
                    'customers.id as customer_id',
                    'users.name as incharge',
                )

                ->first();
            return response()->json($incident, 200);
        }
    }
    public function getLog($id)
    {
        if ($id) {
            $tasks = DB::table('incident_histories')
                ->where('incident_histories.incident_id', '=', $id)
                ->join('users', 'incident_histories.actor_id', '=', 'users.id')
                ->orderBy('incident_histories.id', 'DESC')
                ->select('users.name as name', 'incident_histories.*')
                ->get();
            return response()->json($tasks, 200);
        }
    }
    public function getCustomer($id)
    {
        if ($id) {
            $customer = DB::table('incidents')
                ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                ->join('customer_addresses','customers.id','customer_addresses.customer_id')
                ->leftJoin('sn_ports', 'sn_ports.customer_id', '=', 'customers.id')
                ->leftJoin('sn_splitters','sn_splitters.id', '=', 'sn_ports.sn_splitter_id')
                ->leftJoin('dn_splitters','dn_splitters.id', '=','sn_ports.dn_splitter_id')
                ->leftJoin('sn_splitters as new_sn','new_sn.id', '=', 'incidents.new_sn_splitter_id')
                ->leftJoin('dn_splitters as new_dn','new_dn.id', '=','incidents.new_dn_splitter_id')
                ->where('incidents.id', '=', $id)
                ->where('customer_addresses.is_current',1)
                ->select('customers.*','customer_addresses.*', 'sn_splitters.name as sn_splitter_name', 'dn_splitters.name as dn_splitter_name', 'sn_splitters.location as sn_location','dn_splitters.location as dn_location','sn_ports.port_number as port_number','new_sn.name as new_sn_name','new_sn.location as new_sn_location','new_dn.name as new_dn_name','new_dn.location as new_dn_location','incidents.new_port_number','incidents.type')
                ->get();
            return response()->json($customer, 200);
        }
    }
    public function getHistory($id)
    {
        if ($id) {
            $incident =  Incident::find($id);
            if ($incident) {
                $tasks = DB::table('incidents')
                    ->join('users', 'incidents.incharge_id', '=', 'users.id')
                    ->join('customers', 'customers.id', '=', 'incidents.customer_id')
                    ->where('customers.id', '=', $incident->customer_id)
                    ->orderBy('incidents.id', 'DESC')
                    ->select('users.name', 'incidents.date', 'incidents.time', 'incidents.code', 'customers.ftth_id', 'incidents.status', 'incidents.type', 'incidents.topic')
                    ->get();
                return response()->json($tasks, 200);
            }
        }
    }
    public function getFile($id)
    {
        if ($id) {

            $files = DB::table('file_uploads')
                ->where('file_uploads.incident_id', '=', $id)
                ->orderBy('file_uploads.id', 'DESC')
                ->get();
            return response()->json($files, 200);
        }
    }
        public function getCustomerFile($id)
        {
            $user = User::with('role')->where('users.id', '=', Auth::user()->id)->first();
            if ($id) {
                $query = FileUpload::with('user')
                    ->where('file_uploads.customer_id', '=', $id);

                if ($user->user_type == 'subcon') {
                    $query->where('file_uploads.created_by', '=', $user->id);
                }

                $files = $query->orderBy('file_uploads.id', 'DESC')->get();

                $files = $files->filter(function ($file) use ($user) {
                    $file_extension = method_exists($file, 'getFileTypeAttribute') ? $file->getFileTypeAttribute() : '';
                    if ($user->user_type == 'isp' && in_array(strtolower($file_extension), ['kml', 'kmz'])) {
                        return false;
                    }
                    return true;
                })->values();

                $files->each(function ($file) {
                    $file->user_name = $file->user ? $file->user->name : 'Unknown';
                    $file->file_extension = method_exists($file, 'getFileTypeAttribute') ? $file->getFileTypeAttribute() : '';
                });

                return response()->json($files, 200);
            }
        }
    public function deleteFile(Request $request, $id)
    {
        if ($request->has('id')) {
            File::delete(public_path($request->input('path')));
            FileUpload::find($request->input('id'))->delete();
            if (isset($request->incident_id)) {
                $data = array();
                $data['incident_id'] = $request->incident_id;
                $data['action'] = 'File Deleted:' . $request->name;
                $data['datetime'] = date('Y-m-j h:m:s');
                $data['actor_id'] = Auth::id();
                $this->insertHistory($data);
            }

            return redirect()->back();
        }
    }
    public function addTask(Request $request)
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
            'complete_date' => ['required_if:status,3', 'nullable'],
        ],
        [
            'comment.required_if' => 'Please write comment before closing the task',
            'root_causes_id.required_if' => 'Please Choose Main Root Cause for Pending Task',
            'sub_root_causes_id.required_if' => 'Please Choose Sub Root Cause for Pending Task',
        ]
        )->validate();
        $task = new Task();
        $task->incident_id = $request->incident_id;
        $task->assigned = $request->assigned['id'];
        $task->target = $request->target;
        $task->description = $request->description;
        $task->status = $request->status;
        $task->comment = $request->comment;
        $task->root_causes_id = $request->root_causes_id;
        $task->sub_root_causes_id = $request->sub_root_causes_id;
        $task->complete_date = $request->complete_date??null;
        $task->created_at = NOW();
        $task->updated_at = NOW();
        $task->save();


        $data = array();
        $data['incident_id'] = $request->incident_id;
        $data['action'] = 'Task Created:' . $request->description;
        $data['datetime'] = date('Y-m-j h:m:s');
        $data['actor_id'] = Auth::id();
        $this->updateStatus($request->incident_id);
        $this->insertHistory($data);
        // $notiUsers  = User::whereHas('role', function ($query) {
        //     $query->where('enable_incident_notification', 1);
        // })->get();
        // $notificationMessage = 'Task Created';
        // $notificationAction = 'created';
        // Send notification to users
        // Notification::send($notiUsers, new NewTaskNotification($task, $notificationMessage, $notificationAction));
        return redirect()->back()->with('message', 'Task Created Successfully.');
    }
    public function editTask(Request $request, $id)
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
            
            // Store original values before updating
            $originalValues = [
                'incident_id' => $task->incident_id,
                'assigned' => $task->assigned,
                'target' => $task->target,
                'description' => $task->description,
                'status' => $task->status,
                'comment' => $task->comment,
                'root_causes_id' => $task->root_causes_id,
                'sub_root_causes_id' => $task->sub_root_causes_id,
                'complete_date' => $task->complete_date,
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
                'complete_date' => $request->complete_date,
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
            // $notiUsers  = User::whereHas('role', function ($query) {
            //     $query->where('enable_incident_notification', 1);
            // })->get();
            // $notificationMessage = ($request->status == 0) ? 'Task Deleted' : 'Task Updated';
            // $notificationAction = ($request->status == 0) ? 'deleted' : 'updated';
            // // Send notification to users
            // Notification::send($notiUsers, new NewTaskNotification($task, $notificationMessage, $notificationAction));
            return redirect()->back()->with('message', 'Task Updated Successfully.');
        }
    }
    function isJsonObject($jsonString)
    {
        try {
            $decodedData = json_decode($jsonString);
            return $decodedData !== null;
        } catch (\Throwable $th) {

            return false;
        }


        // Check if decoding was successful and the result is an object

    }
    public function updateStatus($incident_id)
    {

        $tasks = Task::where('incident_id', '=', $incident_id)->where('status', '<>', 0)
            ->get();
        if ($tasks) {
            $completed = true;
            foreach ($tasks as $task) {
                if ($task->status != 2)
                    $completed = false;
            }
            if ($completed) {

                $update = Incident::where('id', '=', $incident_id)->whereNotIn('status', [3, 4])->update(['status' => 5]);
                //    if($update){
                //     broadcast(new UpdateIncident($incident_id,5))->toOthers();
                //    }
            } else {

                $update = Incident::where('id', '=', $incident_id)->whereNotIn('status', [3, 4])->update(['status' => 2]);
                // if($update){
                //     broadcast(new UpdateIncident($incident_id,2))->toOthers();
                // }
            }
        }
    }


    public function store(Request $request)
    {

        Validator::make($request->all(), [
            'date' => ['required'],
            'priority' => ['required'],
            'time' => ['required'],
            'incharge_id' => ['required'],
            'type' => ['required'],
            'status' => ['required'],
            'description' => ['required'],
            'customer_id' => ['required'],
            'new_bandwidth' => ['required_if:type,plan_change'],
            'new_maintenance_plan_id' => ['required_if:type,plan_change'],
        ])->validate();
        // dd($request->all());
        $user = User::with('role')->where('id', Auth::id())->first();
        if ($request->customer_id) {
            $incident = new Incident();
            // $incident->code = $request->code;
            $user = User::with('role')->where('id', Auth::id())->first();
            if($user->user_type != 'isp'){
                throw new \Exception('You are not allowed to create incident');
            }
            $isp = Isp::where('id', $user->isp_id)->first();


            $incident->customer_id = $request->customer_id;
            $incident->incharge_id = $request->incharge_id['id'];
            $incident->type = $request->type;
            $incident->priority = $request->priority;
            $incident->topic = $request->topic;
            $incident->status = $request->status;
            $incident->partner_id = $request->partner_id?? null;
             $incident->start_date = $request->start_date?? null;
            if ($request->type == 'plan_change') {

                $incident->new_bandwidth = $request->new_bandwidth;
                $incident->new_maintenance_service_id = $request->new_maintenance_plan_id;
                
                // $myDateTime = new DateTime;
                // $newtime = clone $myDateTime;
                // if ($request->start_date)
                //     $myDateTime = new DateTime($request->start_date);
                // if ($myDateTime->format('d') <= 7) {
                //     $newtime->modify('first day of this month');
                //     $incident->start_date = $newtime->format('Y-m-j h:m:s');
                // } else {
                //     $newtime->modify('+1 month');
                //     $newtime->modify('first day of this month');
                //     $incident->start_date = $newtime->format('Y-m-j h:m:s');
                // }
            } 

           
            $incident->suspension_incident_id = $request->suspension_incident_id;

            $incident->end_date = $request->end_date;
            if (!empty($request->new_township)) {
                $incident->new_township = $request->new_township['id'];
            }

            $incident->new_address = $request->new_address;
            if (!empty($request->latitude) && !empty($request->longitude))
                $incident->location = $request->latitude . ',' . $request->longitude;
            if (!empty($request->package_id)) {
                $incident->package_id = $request->package_id['id'];
            }

            if (isset($request->close_date) && $request->status == 3)
                $incident->close_date = $request->close_date;

            if (isset($request->close_time) && $request->status == 3)
                $incident->close_time = $request->close_time;

            if (isset($request->resolved_date) && $request->status == 5)
                $incident->resolved_date = $request->resolved_date;

            if (isset($request->resolved_time) && $request->status == 5)
                $incident->resolved_time = $request->resolved_time;

            if (isset($request->supervisor_id)){
                if($user->role?->incident_oss == 1 ){
                    $incident->supervisor_id = $request->supervisor_id;
                    $incident->assigned_by = $user->id;
                }
                
            }
               
            $incident->date = $request->date;
            $incident->time = $request->time;
            $incident->description = $request->description;
            $incident->relocation_service_id = $request->relocation_service_id?? null;
            $incident->save();
            $incident->code = 'T-' . str_pad($incident->id, 4, "0", STR_PAD_LEFT).'-' . $isp->short_code;
            $incident->edge_code =null;
            $incident->update();


            $data = array();
            $data['incident_id'] = $incident->id;
            $data['action'] = 'Incident Created';
            $data['datetime'] = date('Y-m-j h:m:s');
            $data['actor_id'] = Auth::id();
            $this->insertHistory($data);
            $incident_data =  DB::table('incidents')
                ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                ->select(
                    'incidents.*',
                    'incidents.status as status',
                    'customers.ftth_id as ftth_id',
                    'customers.id as customer_id',
                )->first();
            // broadcast(new AddIncident($incident_data))->toOthers();
            // $notiUsers  = User::whereHas('role', function ($query) {
            //     $query->where('enable_incident_notification', 1);
            // })->get();
            // Notification::send($notiUsers, new NewIncidentNotification($incident, 'Incident Created', 'created'));
            return redirect()->route('incident.index')->with('message', 'Incident Created Successfully.')
                ->with('id', $incident->id);
            return redirect()->route('incident.index')->with('message', 'Incident Created Successfully.')
                ->with('id', $incident->id);
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
    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'code' => ['required'],
            'priority' => ['required'],
            'date' => ['required'],
            'time' => ['required'],
            'incharge_id' => ['required'],
            'type' => ['required'],
            'status' => ['required'],
            'description' => ['required'],
 
            'new_bandwidth' => ['required_if:type,plan_change'],
            'new_maintenance_plan_id' => ['required_if:type,plan_change'],
        ], [
            'root_cause_id.required_if' => 'Please mention the RCA before closing the incident.',
            'sub_root_cause_id.required_if' => 'Please mention the sub RCA before closing the incident.',
            'rca_notes.required_if' => 'Please mention the RCA notes before closing the incident.',
        ]);
        $validator->sometimes(['root_cause_id', 'sub_root_cause_id', 'rca_notes'], 'required', function ($input) {
            return $input->type === 'service_complaint' && $input->status == 3;
        });
        $validator->after(function ($validator) use ($request) {
            if ($request->supervisor_id && $request->status == 1) {
                $validator->errors()->add('status', 'Please choose Supervsior Assign');
            }
        });
        $validator->validate();

    
        $user = User::with('role')->where('id', Auth::id())->first();
        if ($request->has('id')) {
            $old_incident = Incident::find($request->input('id'));

         //   $update = $this->checkUpdate($old_incident, $request->request);
            // Compare each relevant field to check for changes
            
          //  if ($update) {
            //     $data = array();
            //     $data['incident_id'] = $request->input('id');
            //     $data['action'] = 'Incident Update :' . $update;
            //     $data['datetime'] = date('Y-m-j h:m:s');
            //     $data['actor_id'] = Auth::id();
            //     $this->insertHistory($data);
            // }


         //   if ($update) {


                $incident = Incident::find($request->input('id'));
                // Check if incident->code matches "T-0015-MGT" or "T-0015"
                if (!$incident->edge_code && $user->user_type == 'internal'){
                   $customer = Customer::with('city')->find($incident->customer_id);
                   if($customer){
                    $maintenance = MaintenanceService::where('id', $customer->maintenance_service_id)->first();
                    if($maintenance && $maintenance->short_code){
                        
                        $prefix_1 = 'RT';
                        // service_complaint - RT
                        // plan_change - PC 
                        // suspension - SS
                        // resume - RS
                        // termination - TE
                        // Relocation - RL
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
                 //       $prefix_2 = strtoupper(substr($maintenance->short_code, 0, 2));
                        $pattern = '^[A-Z]{2}-' .$customer->city->short_code. $today . '-([0-9]{4})$';
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
                       
                     //   $incident->edge_code = $prefix_1.'-' . $prefix_2 . $today . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                        $incident->edge_code = $prefix_1.'-' .$customer->city->short_code. $today . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                       
                    }
                   }
                   
                   
                }

                $incident->code = $request->code;
                $incident->customer_id = $request->customer_id;
                $incident->incharge_id = $request->incharge_id['id'];
                $incident->type = $request->type;
                $incident->priority = $request->priority;
                $incident->topic = $request->topic;
                $incident->status = $request->status;
            
                $incident->start_date = $request->start_date?? null;
                if ($request->type == 'plan_change') {

                $incident->new_bandwidth = $request->new_bandwidth;
                $incident->new_maintenance_service_id = $request->new_maintenance_plan_id;
                if($request->partner_id ){
                      $incident->partner_id = $request->partner_id;
                
                      if($incident->status == 1){
                        $incident->status = 10;
                      }
                }
                  
                // $myDateTime = new DateTime;
                // $newtime = clone $myDateTime;
                // if ($request->start_date)
                //     $myDateTime = new DateTime($request->start_date);
                // if ($myDateTime->format('d') <= 7) {
                //     $newtime->modify('first day of this month');
                //     $incident->start_date = $newtime->format('Y-m-j h:m:s');
                // } else {
                //     $newtime->modify('+1 month');
                //     $newtime->modify('first day of this month');
                //     $incident->start_date = $newtime->format('Y-m-j h:m:s');
                // }
             } 
                $incident->end_date = $request->end_date;
                if (!empty($request->new_township)) {
                    $incident->new_township = $request->new_township['id'];
                }

                $incident->new_address = $request->new_address;
                if (!empty($request->latitude) && !empty($request->longitude))
                    $incident->location = $request->latitude . ',' . $request->longitude;
                if (!empty($request->package_id)) {
                    $incident->package_id = $request->package_id['id'];
                }
                $incident->date = $request->date;
                $incident->time = $request->time;

                if (isset($request->close_date) && $request->status == 3)
                    $incident->close_date = $request->close_date;

                if (isset($request->close_time) && $request->status == 3)
                    $incident->close_time = $request->close_time;

                if (isset($request->resolved_date) && $request->status == 5)
                    $incident->resolved_date = $request->resolved_date;

                if (isset($request->resolved_time) && $request->status == 5)
                    $incident->resolved_time = $request->resolved_time;
            
                if (isset($request->supervisor_id)){
                   
                    if($user->role?->incident_oss == 1 ){
                        $incident->supervisor_id = $request->supervisor_id;
                        $incident->assigned_by = $user->id;
                    }
                   
                }
                $incident->description = $request->description;

                $incident->root_cause_id = $request->root_cause_id;
                $incident->sub_root_cause_id = $request->sub_root_cause_id;
                $incident->rca_notes = $request->rca_notes;
                $incident->relocation_service_id = $request->relocation_service_id?? null;
                //  broadcast(new UpdateIncident($request->input('id'),$request->status))->toOthers();

                $incident->closed_by = Auth::user()->id;
                $incident->update();
                // $notiUsers  = User::whereHas('role', function ($query) {
                //     $query->where('enable_incident_notification', 1);
                // })->get();
                // $notificationMessage = ($request->status == 4) ? 'Incident Deleted' : 'Incident Updated';
                // $notificationAction = ($request->status == 4) ? 'deleted' : 'updated';
                // Send notification to users
                // Notification::send($notiUsers, new NewIncidentNotification($incident, $notificationMessage, $notificationAction));
          //  }

            return redirect()->route('incident.index')->with('message', 'Incident Updated Successfully.');
        }
    }
    public function getStatus($data)
    {
        $status = "Request";
        if ($data == 1) {
            $status = "Request";
        } else if ($data == 2) {
            $status = "Team Assign";
        } else if ($data == 3) {
            $status = "Closed";
        } else if ($data == 4) {
            $status = "Deleted";
        } else if ($data == 5) {
            $status = "Resolved";
        }else if ($data == 6) {
            $status = "Supervisor Assign";
        }
        else if ($data == 7) {
            $status = "Waiting to Suspense";
        }
        else if ($data == 8) {
            $status = "Waiting to Reopen";
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
    public function checkUpdate($old_incident, $request)
    {
        $updatedFields = [];
        $fieldsToCompare = [
            'code','edge_code', 'priority', 'date', 'time', 'incharge_id', 'type', 'status', 'description',
            'customer_id', 'topic', 'root_cause_id', 'sub_root_cause_id', 'rca_notes',
            'package_id', 'new_township', 'supervisor_id', 'start_date', 'end_date',
            'new_address', 'latitude', 'longitude', 'relocation_service_id'
        ];

        foreach ($fieldsToCompare as $field) {
            $oldValue = $old_incident->$field;
            $newValue = null;
            if ($request->has($field)) {
                $value = $request->get($field);
                if (in_array($field, ['customer_id', 'incharge_id', 'package_id', 'new_township', 'supervisor_id'])) {
                    $newValue = (is_array($value) && isset($value['id'])) ? $value['id'] : $value;
                } else {
                    $newValue = $value;
                }
            }
            if ($oldValue != $newValue) {
                $updatedFields[$field] = [
                    'old' => $oldValue,
                    'new' => $newValue
                ];
            }
        }
        return $updatedFields;
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            Incident::find($request->input('id'))->delete();

            return redirect()->back();
        }
    }
}
