<?php

namespace App\Http\Controllers;

use App\Models\BundleEquiptment;
use App\Models\City;
use App\Models\CoreAssignment;
use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\CustomerAddress;
use App\Models\Package;
use App\Models\Project;
use App\Models\User;
use App\Models\Status;
use App\Models\Township;
use App\Models\Role;
use App\Models\SnPorts;
use App\Models\DnPorts;
use App\Models\CustomerHistory;
use App\Models\DnBox;
use App\Models\FileUpload;
use App\Models\InstallationService;
use App\Models\Isp;
use App\Models\MaintenanceService;
use App\Models\OdbFiberCable;
use App\Models\Partner;
use App\Models\Pop;
use App\Models\PopDevice;
use App\Models\PortSharingService;
use App\Models\PublicIpAddress;
use App\Models\SnPort;
use App\Models\SnSplitter;
use App\Models\Subcom;
use App\Models\SubconChecklist;
use App\Models\SubconChecklistsGroup;
use App\Models\SubconChecklistValue;
use App\Models\SubconChecklistValueHistory;
use Carbon\Carbon;
use Inertia\Inertia;
use DateTime;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->show($request);
    }
    public function show(Request $request)
    {
        //   dd($request

        $user = User::with('role')->where('users.id', '=', Auth::user()->id)->first();
        $packages = Package::get();
        $townships = Township::when($user->role?->limit_region, function ($query) use ($user) {
            return $query->whereIn('townships.id', $user->role?->townships->pluck('id'));
        })->get();
        $partners = Partner::get();
        $status = Status::get();
        $isps = Isp::get();
        $dn = DnPorts::get();

        $dnBoxes = DnBox::get();

        $onuSerials = Customer::where('customers.deleted', '=', 0)
            ->orWhereNull('customers.deleted')
            ->whereNotNull('onu_serial')
            ->select('onu_serial')
            ->groupBy('onu_serial')
            ->orderBy('onu_serial')
            ->get();
        $installationTeams = Subcom::all();
        $bundle_equiptments = BundleEquiptment::get();
        $active = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->join('customer_addresses', 'customers.id', 'customer_addresses.customer_id')
            ->whereIn('status.type', ['active', 'disabled'])
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->when($user->role?->installation_supervisor, function ($query) use ($user) {
                $query->where('customers.supervisor_id', $user->id);
            })
            ->when($user->user_type, function ($query, $user_type) use ($user) {
                if ($user_type == 'partner') {
                    $query->where('customers.partner_id', '=', $user->partner_id);
                }
                if ($user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                }
                if ($user_type == 'subcon') {
                    $query->where('customers.subcom_id', '=', $user->subcom_id);
                }
            })
            ->count();

        // $relocation = DB::table('customers')
        // ->join('status', 'customers.status_id', '=', 'status.id')
        // ->where('status.name', 'LIKE', '%Relocation%')
        // ->where('customers.deleted', '<>', '1')
        // ->count();
        $suspense = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->join('customer_addresses', 'customers.id', 'customer_addresses.customer_id')
            ->where('status.type', '=', 'suspense')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->when($user->role?->limit_region, function ($query) use ($user) {
                return $query->whereIn('customer_addresses.township_id', $user->role?->townships->pluck('id'));
            })
            ->when($user->role?->installation_supervisor, function ($query) use ($user) {
                $query->where('customers.supervisor_id', $user->id);
            })
            ->when($user->user_type, function ($query, $user_type) use ($user) {
                if ($user_type == 'partner') {
                    $query->where('customers.partner_id', '=', $user->partner_id);
                }
                if ($user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                }
                if ($user_type == 'subcon') {
                    $query->where('customers.subcom_id', '=', $user->subcom_id);
                }
            })
            ->count();
        $installation_request = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->join('customer_addresses', 'customers.id', 'customer_addresses.customer_id')
            ->where('status.type', '=', 'new')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->when($user->role?->limit_region, function ($query) use ($user) {
                return $query->whereIn('customer_addresses.township_id', $user->role?->townships->pluck('id'));
            })
            ->when($user->role?->installation_supervisor, function ($query) use ($user) {
                $query->where('customers.supervisor_id', $user->id);
            })
            ->when($user->user_type, function ($query, $user_type) use ($user) {
                if ($user_type == 'partner') {
                    $query->where('customers.partner_id', '=', $user->partner_id);
                }
                if ($user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                }
                if ($user_type == 'subcon') {
                    $query->where('customers.subcom_id', '=', $user->subcom_id);
                }
            })
            ->count();
        $terminate = DB::table('customers')
            ->join('customer_addresses', 'customers.id', 'customer_addresses.customer_id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where('status.type', '=', 'terminate')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            ->when($user->role?->limit_region, function ($query) use ($user) {
                return $query->whereIn('customer_addresses.township_id', $user->role?->townships->pluck('id'));
            })
            ->when($user->role?->installation_supervisor, function ($query) use ($user) {
                $query->where('customers.supervisor_id', $user->id);
            })
            ->when($user->user_type, function ($query, $user_type) use ($user) {
                if ($user_type == 'partner') {
                    $query->where('customers.partner_id', '=', $user->partner_id);
                }
                if ($user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                }
                if ($user_type == 'subcon') {
                    $query->where('customers.subcom_id', '=', $user->subcom_id);
                }
            })
            ->count();


        $orderform = null;
        if ($request->orderform)
            $orderform['status'] = ($request->orderform == 'signed') ? 1 : 0;

        $all_township = Township::select('id')
            ->get()
            ->toArray();
        $all_packages = Package::select('id')
            ->get()
            ->toArray();
        $package_speed = Package::select('speed', 'type')
            ->groupBy('speed', 'type')
            ->orderBy('speed', 'ASC')->get();
        //dd($request);
        $customers =  Customer::with('package', 'currentAddress.township', 'isp', 'status')
            ->join('customer_addresses', 'customers.id', 'customer_addresses.customer_id')
            ->join('townships', 'townships.id', 'customer_addresses.township_id')
            ->leftjoin('installation_services', 'customers.installation_service_id', '=', 'installation_services.id')
            ->leftjoin('sn_ports', 'customers.id', '=', 'sn_ports.customer_id')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orWhereNull('customers.deleted');
            })
            // ->when($user->role?->limit_region, function ($query) use ($user) {
            //     return $query->whereIn('customers.township_id', $user->role?->townships->pluck('id'));
            // })
            ->when($user->role?->limit_region, function ($query) use ($user) {
                $query->whereHas('currentAddress', function ($q) use ($user) {
                    $q->whereIn('township_id', $user->role?->townships->pluck('id'));
                });
            })
            ->when($user->user_type, function ($query, $user_type) use ($user) {

                if ($user_type == 'partner') {
                    $query->where('customers.partner_id', '=', $user->partner_id);
                }
                if ($user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                }
                if ($user_type == 'subcon') {
                    $query->where('customers.subcom_id', '=', $user->subcom_id);
                }
            })
            ->when($request->keyword, function ($query, $search = null) {
                $query->where(function ($query) use ($search) {
                    $query->where('customers.name', 'LIKE', '%' . $search . '%')
                        ->orWhere('customers.isp_ftth_id', 'LIKE', '%' . $search . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $search . '%');
                });
            })->when($request->general, function ($query, $general) {
                $query->where(function ($query) use ($general) {
                    $query->where('customers.name', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.isp_ftth_id', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%');
                });
            })
            ->when($request->installation, function ($query, $installation) {
                $startDate = Carbon::parse($installation[0])->format('Y-m-d');
                $endDate = Carbon::parse($installation[1])->format('Y-m-d');
                $query->whereBetween('customers.installation_date', [$startDate, $endDate]);
            })
            ->when($request->order, function ($query, $order) {
                $startDate = Carbon::parse($order[0])->format('Y-m-d');
                $endDate = Carbon::parse($order[1])->format('Y-m-d');
                $query->whereBetween('customers.order_date',  [$startDate, $endDate]);
            })
            ->when($request->prefer, function ($query, $prefer) {
                $startDate = Carbon::parse($prefer[0])->format('Y-m-d');
                $endDate = Carbon::parse($prefer[1])->format('Y-m-d');
                $query->whereBetween('customers.prefer_install_date', [$startDate, $endDate]);
            })
            ->when($request->dn, function ($query, $dn_2) {
                $query->where('sn_ports.dn_splitter_id', '=', $dn_2['id']);
            })
            ->when($request->sn, function ($query, $sn) {
                $query->where('sn_ports.sn_splitter_id', '=', $sn['id']);
            })
            ->when($request->pop, function ($query, $pop) {
                $query->where('sn_ports.pop_id', '=', $pop['id']);
            })
            ->when($request->pop_device, function ($query, $pop_device) {
                $query->where('sn_ports.pop_device_id', '=', $pop_device['id']);
            })
            ->when($request->package, function ($query, $package) use ($all_packages) {
                if ($package == 'empty') {
                    $query->whereNotIn('customers.package_id', $all_packages);
                } else {
                    $query->where('customers.package_id', '=', $package);
                }
            })
            ->when($request->package_speed, function ($query, $package_speed) {
                $speed_type =  explode("|", $package_speed);
                $speed = $speed_type[0];
                $type = $speed_type[1];
                $query->whereHas('package', function ($q) use ($speed, $type) {
                    $q->where('speed', '=', $speed);
                    $q->where('type', '=', $type);
                });
            })
            ->when($request->township, function ($query, $township) use ($all_township) {
                if ($township == 'empty') {
                    $query->whereDoesntHave('currentAddress', function ($q) use ($all_township) {
                        $q->whereIn('township_id', $all_township);
                    });
                } else {
                    $query->whereHas('currentAddress', function ($q) use ($township) {
                        $q->where('township_id', $township);
                    });
                }
            })
            ->when($request->status, function ($query, $status) {
                $query->where('customers.status_id', '=', $status);
            })
            ->when($request->sh_isp, function ($query, $sh_isp) {
                $query->where('customers.isp_id', '=', $sh_isp['id']);
            })
            ->when($request->partner, function ($query, $partner) {
                $query->where('customers.partner_id', '=', $partner['id']);
            })
            ->when($request->status_type, function ($query, $status_type) {
                $query->whereHas('status', function ($q) use ($status_type) {
                    $q->where('type', '=', $status_type);
                });
            })
            ->when($request->order, function ($query, $order) {
                $query->whereBetween('customers.order_date', $order);
            })
            ->when($request->installation, function ($query, $installation) {
                $query->whereBetween('customers.installation_date', $installation);
            })
            ->when($request->sh_onu_serial, function ($query, $sh_onu_serial) {
                $query->where('customers.onu_serial', $sh_onu_serial);
            })
            ->when($request->sh_installation_team, function ($query, $sh_installation_team) {
                $query->where('customers.subcom_id', $sh_installation_team['id']);
            })

            ->when($request->assign_date, function ($query, $assign_date) {
                $startDate = Carbon::parse($assign_date[0])->format('Y-m-d');
                $endDate = Carbon::parse($assign_date[1])->format('Y-m-d');
                $query->whereBetween('customers.subcom_assign_date', [$startDate, $endDate]);
            })
            ->when($request->installation_status, function ($query, $sh_installation_status) {

                $query->where('customers.installation_status', $sh_installation_status);
            })
            ->when($request->sh_installation_timeline, function ($query, $sh_installation_timeline) {
                $query->whereHas('package', function ($q) use ($sh_installation_timeline) {
                    $q->where('installation_timeline', '=', $sh_installation_timeline);
                });
            })
            ->when($user->role?->installation_supervisor, function ($query) use ($user) {
                $query->where('customers.supervisor_id', $user->id);
            })
            ->when($request->sort, function ($query, $sort) use ($request) {
                $direction = $request->direction ?? 'asc';

                // Handle special cases for related tables
                switch ($sort) {
                    case 'package':

                        $query->orderBy('package_id', $direction);
                        break;
                    case 'township':
                        $query->orderBy('township_id', $direction);
                        break;
                    case 'status':
                        $query->orderBy('status_id', $direction);
                        break;
                    default:
                        // For direct customer table columns
                        $query->orderBy("customers.{$sort}", $direction);
                }
            }, function ($query) {
                // Default sorting if no sort parameter is provided
                $query->orderBy('updated_at', 'desc');
            })
            ->select('customers.*', 'customer_addresses.address as address', 'customer_addresses.location as location', 'customer_addresses.township_id as township_id', 'townships.name as township_name','installation_services.name as installation_service_name','installation_services.sla_hours as installation_service_sla_hours')

            ->paginate(10);
        $dynamicRanderPage = "Client/Customer";
        if ($user->user_type == 'subcon') {
            $dynamicRanderPage = "Client/CustomerSubcom";
        }

        // dd($customers->toSQL(), $customers->getBindings());
        $customers->appends($request->all())->links();
        return Inertia::render($dynamicRanderPage, [
            'packages' => $packages,
            'package_speed' => $package_speed,
            'townships' => $townships,
            'status' => $status,
            'customers' => $customers,
            'dn' => $dn,
            'active' => $active,
            'suspense' => $suspense,
            'installation_request' => $installation_request,
            'terminate' => $terminate,
            'user' => $user,
            'bundle_equiptments' => $bundle_equiptments,
            'installationTeams' => $installationTeams,
            'onuSerials' => $onuSerials,
            'isps' => $isps,
            'partners' => $partners,
            'dnBoxes' => $dnBoxes,
            
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function create()
    {

        $packages = Package::get();
        $sn = SnPort::get();
        $projects = Project::get();
        $pops = Pop::get();
          $user = User::with('role')->find(Auth::user()->id);
        $bundle_equiptments = BundleEquiptment::get()->map(function ($item) {
            $item['$isDisabled'] = ($item->is_active !== 1);
            return $item;
        });
        $dnBoxes = DnBox::get()->map(function ($item) {
              $item['$isDisabled'] = ($item->status !== 'active');
            return $item;
        });
        $oldCustomers = Customer::join('status','customers.status_id','status.id')->where(function ($query) {
            $query->where('customers.deleted', '=', 0)
                ->orWhereNull('customers.deleted');
            })
            ->whereNotIn('customers.id', function ($subQuery) {
            $subQuery->select('old_customer_id')
                ->from('customers')
                ->whereNotNull('old_customer_id');
            })
            ->when($user->user_type, function ($query, $user_type) use ($user) {
                if ($user_type == 'partner') {
                    $query->where('customers.partner_id', '=', $user->partner_id);
                }
                if ($user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                }
                if ($user_type == 'subcon') {
                    $query->where('customers.subcom_id', '=', $user->subcom_id);
                }
            })
            ->where('status.name', 'LIKE', '%Terminate%')
            ->get();
        $installationServices = InstallationService::where('type', 'new')->get();
        $portSharingServices = PortSharingService::get();
        $maintenanceServices = MaintenanceService::get();

        $cities = City::get();
        $dn = DB::table('dn_ports')
            ->get();

        $auth_role = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->where('roles.name', 'NOT LIKE', '%Admin%')
            ->where('users.id', '=', Auth::user()->id)
            ->select('users.name as name', 'users.id as id')
            ->get();

        if (!$auth_role->isEmpty()) {
            $sale_persons = $auth_role;
        }
        $userPerm = $this->getPermision();
        $subcoms = Subcom::all();
        $isps = Isp::all();
        $partners = Partner::all();
        // $townships = Township::join('cities', 'townships.city_id', '=', 'cities.id')->select('townships.*', 'cities.name as city_name', 'cities.short_code as city_code', 'cities.id as city_id')->get();
        $status_list = $this->getStatusList();
        $max_id = $this->getmaxid();
      
        return Inertia::render(
            'Client/AddCustomer',
            [
                'packages' => $packages,
                'projects' => $projects,
                'status_list' => $status_list,
                'subcoms' => $subcoms,
                'user' => $user,
                'userPerm' => $userPerm,
                'sn' => $sn,
                'dn' => $dn,
                'pops' => $pops,
                'max_id' => $max_id,
                'partners' => $partners,
                'isps' => $isps,
                'bundle_equiptments' => $bundle_equiptments,
                'installationServices' => $installationServices,
                'portSharingServices' => $portSharingServices,
                'maintenanceServices' => $maintenanceServices,
                'cities' => $cities,
                'oldCustomers' => $oldCustomers,
                'dnBoxes' => $dnBoxes,
            ]
        );
    }

    public function store(Request $request)
    {
        $user = User::with('role')->find(Auth::user()->id);
        $userPerms = $this->getPermision();

        $validator = Validator::make($request->all(), [
            'customer_installation_type' => 'required|max:255',
            'old_customer_id' => 'nullable|exists:customers,id|required_if:customer_installation_type,relocation',
            'name' => 'required|max:255',
            'phone_1' => 'required|max:255',
            'address' => 'required',
            'latitude' => 'required|max:255',
            'longitude' => 'required|max:255',
            'dob' => 'nullable|date',
            'status' => 'required',
            'order_date' => 'date',
            'city_id' => 'required',
            'township_id' => 'required',
            'installation_date' => 'nullable|date',
            'isp_ftth_id' => 'required',
            'installation_service_id' => 'required_if:service_type,FTTH',
            'maintenance_service_id' => 'required',
            'service_type' => 'required',
            'bandwidth' => 'required|integer',
            // New installation/collection fields
            // 'customer_installation_type' => 'required|in:new_installation,relocation',
            // 'old_customer_id' => 'nullable|exists:customers,id|required_if:customer_installation_type,relocation',
            // 'onu_collected_by' => 'nullable|string|max:255|required_if:onu_collection_status,collected',
            // 'onu_collection_status' => 'required|string|in:no_action,collected,reused',
            // 'onu_collection_date' => 'nullable|date|required_if:onu_collection_status,collected',
            // 'drop_cable_collected_by' => 'nullable|string|max:255|required_if:drop_cable_collection_status,collected',
            // 'drop_cable_collection_status' => 'required|string|in:no_action,collected,reused',
            // 'drop_cable_collection_date' => 'nullable|date|required_if:drop_cable_collection_status,collected',
           
        ]);

        if ($user->user_type === 'internal') {
            $validator->addRules([
                'isp_id' => 'required',
            ]);
        }

        // Run validation first to catch basic rule errors
        $validator->validate();

        // Now check business logic condition
        $bandwidth = (int) $request->bandwidth;
          $selectedService = PortSharingService::when($request->service_type, function ($query,$service_type) use ($bandwidth) {
            if($service_type == 'FTTH'){
                  $query->where('max_speed', '>=', $bandwidth);
            }
            })->orderBy('max_speed', 'asc')
            ->first();
 
 
        if (!$selectedService) {
            // Manually throw ValidationException with custom message
            throw \Illuminate\Validation\ValidationException::withMessages([
                'bandwidth' => 'Invalid bandwidth value, please check.',
            ]);
        }
        
        if($request->service_type == 'FTTH'){
        $installationService = InstallationService::find($request->installation_service_id);
        if (!$installationService) {
            // Manually throw ValidationException with custom message
            throw \Illuminate\Validation\ValidationException::withMessages([
                'installation_service_id' => 'Invalid Installation value, please check.',
            ]);
        }
        }
        $maintenanceService = MaintenanceService::find($request->maintenance_service_id);
        if (!$maintenanceService) {
            // Manually throw ValidationException with custom message
            throw \Illuminate\Validation\ValidationException::withMessages([
                'maintenance_service_id' => 'Invalid Maintenance value, please check.',
            ]);
        }


        $isp = null;
        if ($user->user_type == 'isp') {
            $isp = Isp::find($user->isp_id);
        } else {
            $isp = Isp::find($request->isp_id['id']);
        }
        //already exists
        if ($isp) {
            $max_c_id = $this->getmaxid();
            $city_id = $request->city_id;
            $result = null;
            foreach ($max_c_id as $value) {
                if ((int)$value['id'] == (int)$city_id) {
                    $result = $value['value'];
                }
            }
            //   $max_id = $max_c_id [$request->city_id];
            // $auto_ftth_id = $isp->short_code . $request->city['short_code'] . str_pad($result + 1, 7, '0', STR_PAD_LEFT) . substr($selectedService->type, 0, 2) . $selectedService->short_code . $installationService->sla_hours;
            $auto_ftth_id = $isp->short_code . $request->city['short_code'] . str_pad($result + 1, 7, '0', STR_PAD_LEFT) . substr($request->service_type, 0, 2);
            $auto_ftth_id = strtoupper($auto_ftth_id);
        } else {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'isp_id' => 'Something missing value, please check.',
            ]);
        }


        DB::beginTransaction();
        try {
            $customer = new Customer();
            $customer->customer_installation_type = $request->customer_installation_type;
            $customer->old_customer_id = $request->old_customer_id;
            $customer->name = $request->name;
            $customer->phone_1 = $request->phone_1;
            $customer->isp_id = $isp ? $isp->id : null;
            $customer->ftth_id = $auto_ftth_id ?? '';
            $customer->isp_ftth_id = $request->isp_ftth_id ?? '';
            $customer->status_id = isset($request->status['id']) ? $request->status['id'] : null;

            $customer->order_date = $request->order_date ?? null;
            $customer->prefer_install_date = $request->prefer_install_date ?? null;
            $customer->created_by = Auth::user()->id;
            $customer->service_type = $request->service_type ?? '';
            $customer->port_sharing_service_id = $selectedService ? $selectedService->id : null;
            $customer->pppoe_username = $request->pppoe_username ?? '';
            $customer->pppoe_password = $request->pppoe_password ?? '';
            if ($request->service_type == 'FTTH') {
          
                $customer->installation_service_id = $request->installation_service_id ?? null;
            }
   
       
            $customer->maintenance_service_id = $request->maintenance_service_id ?? null;
            $customer->order_remark = $request->order_remark ?? '';
            $customer->deleted = 0;
            $customer->bandwidth = $request->bandwidth ?? 0;
            $customer->city_id = $request->city_id ?? null;
               if (!empty($request->bundles)) {
               
                        $bundle_list = $request->bundles;
                        $customer->bundle = '';
                        foreach ($bundle_list as $key => $value) {

                            if ($key !== array_key_last($bundle_list))
                                $customer->bundle .= $value['id'] . ',';
                            else
                                $customer->bundle .= $value['id'];
                        }
                    }
            $customer->save();

            $customerAddress = new CustomerAddress();
            $customerAddress->customer_id = $customer->id;
            $customerAddress->address = $request->address ?? '';
            $customerAddress->location = ($request->latitude ?? '') . ',' . ($request->longitude ?? '');
            $customerAddress->township_id = isset($request->township['id']) ? $request->township['id'] : null;
            $customerAddress->type = 'new_installation';
            $customerAddress->is_current = 1;
            
            $customerAddress->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }


        $logData = [];
        //  $changes = $customer->getChanges();
        $changes = $customer->getAttributes();

        app(\App\Services\CustomerHistoryService::class)->storeCustomerHistory(
            $customer,         // newly updated Customer
            null,      // old snapshot
            $changes,          // the changes array
            $customer->id,
            $request->input('start_date') // optional
        );
        foreach ($changes as $key => $newValue) {
            $logData[$key] = [
                'to' => $newValue                   // New value
            ];
        }
        activity()
            ->causedBy(User::find(Auth::id()))
            ->performedOn($customer)
            ->withProperties(['changes' => $logData])  // Log the changes with from-to values
            ->log('Customer Created: ' . $customer->ftth_id);
        $logData = [];
        $changes = $customerAddress->getAttributes();

        activity()
            ->causedBy(User::find(Auth::id()))
            ->performedOn($customerAddress)
            ->withProperties(['changes' => $logData])  // Log the changes with from-to values
            ->log('Customer Address Created: ' . $customer->ftth_id);
        return redirect()->route('customer.index')->with('message', 'Customer Created Successfully.');
    }

    public function getPermision(): array
    {
        $user = User::find(Auth::user()->id);
        switch ($user->user_type) {
            case 'partner':
                $partner = Partner::find($user->partner_id);
                return $partner && $partner->permissions ? $partner->permissions : [];
                break;
            case 'isp':
                $isp = Isp::find($user->isp_id);
                return $isp && $isp->permissions ? $isp->permissions : [];
                break;
            case 'subcom':
                $subcom = Subcom::find($user->subcom_id);
                return $subcom && $subcom->permissions ? $subcom->permissions : [];
                break;
            case 'internal':
                $role = Role::find($user->role_id);
                return $role && $role->permissions ? $role->permissions : [];
                break;
            default:
                return [];
                break;
        }
    }
    public function getStatusList(): array
    {
        $user = User::find(Auth::user()->id);
        switch ($user->user_type) {
            case 'partner':
                $partner = Partner::find($user->partner_id);
                return $partner && $partner->customer_status ? $partner->customer_status : [];
                break;
            case 'isp':
                $isp = Isp::find($user->isp_id);
                return $isp && $isp->customer_status ? $isp->customer_status : [];
                break;
            case 'internal':
                $role = $user->role;
                return $role && $role->customer_status ? $role->customer_status : [];
                break;
            default:
                return [];
                break;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!$id) {
            return redirect()->back()->with('error', 'Invalid customer ID.');
        }
        $user = User::with('role')->find(Auth::user()->id);
        $townshipArray = Township::when($user->role?->limit_region, function ($query) use ($user) {
            return $query->whereIn('townships.id', $user->role?->townships->pluck('id'));
        })->get()->toArray();
        $cities = City::get();

        $typePermissions = [
            'subcon' => ['model' => Subcom::class, 'field' => 'subcom_id'],
            'isp' => ['model' => Isp::class, 'field' => 'isp_id'],
            'partner' => ['model' => Partner::class, 'field' => 'partner_id']
        ];

        if (isset($typePermissions[$user->user_type])) {
            $permission = $typePermissions[$user->user_type];
            $model = $permission['model'];
            $field = $permission['field'];

            $entity = $model::find($user->{$field});
            $hasAccess = Customer::where(function ($query) {
                $query->where('deleted', 0)->orWhereNull('deleted');
            })
                ->where($field, $entity->id)
                ->where('id', $id)
                ->exists();

            if (!$hasAccess) {
                return abort(403, 'Unauthorized access.');
            }
        }
        $customer = Customer::with('currentAddress.township','status','city', 'partner', 'isp', 'snPort', 'snPort.snSplitter','snPort.dnBox' ,'snPort.snSplitter.snBox.dnSplitter.fiberCable', 'installationService', 'maintenanceService', 'portSharingService', 'supervisor')
            ->where(function ($query) {
                $query->where('deleted', 0)->orWhereNull('deleted');
            })->find($id);
        $supervisors = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->join('role_township', 'roles.id', '=', 'role_township.role_id')
            ->select('users.name as name', 'users.id as id')
            // ->where($customer->currentAddress.township,'=', 'role_township.township_id')
            ->where('role_township.township_id', '=', $customer->currentAddress->township->id)
            ->where('roles.installation_supervisor', 1)
            ->groupBy('users.id', 'users.name')
            ->get();
        $snPort =  SnPort::with('snSplitter', 'dnSplitter', 'pop', 'popDevice', 'dnBox')->where('customer_id', $id)->first();
         $bundle_equiptments = BundleEquiptment::get()->map(function ($item) {
            $item['$isDisabled'] = ($item->is_active !== 1);
            return $item;
        });

        $dnBoxes = DnBox::get()->map(function ($item) {
            $item['$isDisabled'] = ($item->status !== 'active');
            return $item;
        });

        $installationServices = InstallationService::where('type', 'new')->get();
        $portSharingServices = PortSharingService::get();
        $maintenanceServices = MaintenanceService::get();
        // if($user->user_type == 'subcon') {
        //     $subconCheckList = SubconChecklist::where('service_type','installation')->get();
        //     return Inertia::render('Client/SubcomCustomerView', [
        //         'customer' => $customer,
        //         'bundle_equiptments'=>$bundle_equiptments,
        //         'subconCheckList' => $subconCheckList
        //     ]);
        // }
        $subconCheckList = SubconChecklist::where('service_type', 'installation')->get();

        // Get existing checklist values for this customer
        $checklistValues = SubconChecklistValue::where('customer_id', $id)->get();
        $checkListSummary = $this->checklistSummary($id,'installation');
        // Format checklist values and images for easy access in the frontend
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
        $customer->checklist_values = $formattedValues;
        $customer->checklist_images = $checklistImages;
        if ($user->user_type == 'subcon') {

            return Inertia::render('Client/SubcomCustomerView', [
                'customer' => $customer,
                'bundle_equiptments' => $bundle_equiptments,
                'subconCheckList' => $subconCheckList,
                'checkListSummary' => $checkListSummary,
                'snPort' => $snPort
            ]);
        }
        $customer_history = CustomerHistory::where('customer_id', '=', $id)
            ->where('active', '=', 1)
            ->first();
        $isps = Isp::all();
        $partners = Partner::all();
        $pops = Pop::all();
        $popDevices = PopDevice::all();
        $projects = Project::get();

        $subcoms = Subcom::all();
        $townships = Township::join('cities', 'townships.city_id', '=', 'cities.id')->select('townships.*', 'cities.name as city_name', 'cities.short_code as city_code', 'cities.id as city_id')->get();
        $allStatus = Status::get()->toArray();
        $status_list = $this->getStatusList();

        $userPerm = $this->getPermision();




        $total_documents = FileUpload::where('customer_id', $customer->id)->whereNull('incident_id')->count();

         // Get customers whose id is never referenced as old_customer_id and whose status type is not 'new'
         $oldCustomers = Customer::join('status','customers.status_id','status.id')->where(function ($query) {
            $query->where('customers.deleted', '=', 0)
                ->orWhereNull('customers.deleted');
            })
            ->whereNotIn('customers.id', function ($subQuery) {
            $subQuery->select('old_customer_id')
                ->from('customers')
                ->whereNotNull('old_customer_id');
            })
            ->when($user->user_type, function ($query, $user_type) use ($user) {
                if ($user_type == 'partner') {
                    $query->where('customers.partner_id', '=', $user->partner_id);
                }
                if ($user_type == 'isp') {
                    $query->where('customers.isp_id', '=', $user->isp_id);
                }
                if ($user_type == 'subcon') {
                    $query->where('customers.subcom_id', '=', $user->subcom_id);
                }
            })
            ->where('status.name', 'LIKE', '%Terminate%')
            ->get();

        return Inertia::render(
            'Client/EditCustomer',
            [
                'customer' => $customer,
                'installationServices' => $installationServices,
                'portSharingServices' => $portSharingServices,
                'maintenanceServices' => $maintenanceServices,
                'projects' => $projects,
                'allStatus' => $allStatus,
                'townships' => $townships,
                'status_list' => $status_list,
                'subcoms' => $subcoms,
                'user' => $user,
                'userPerm' => $userPerm,
                'customer_history' => $customer_history,
                'popDevices' => $popDevices,
                'pops' => $pops,
                'partners' => $partners,
                'isps' => $isps,
                'total_documents' => $total_documents,
                'bundle_equiptments' => $bundle_equiptments,
                'subconCheckList' => $subconCheckList,
                'snPort' => $snPort,
                'supervisors' => $supervisors,
                'cities' => $cities,
                'checkListSummary' => $checkListSummary,
                'oldCustomers' => $oldCustomers,
                'dnBoxes' => $dnBoxes,
            ]
        );
    }
    public function checklistSummary($customerId, $service_type)
    {
        $user = User::with('role')->find(Auth::user()->id);

        $groups = SubconChecklistsGroup::with([
            'checklists' => function ($query) use ($service_type) {
                $query->where('service_type', $service_type);
            },
            'checklists.values' => function ($query) use ($customerId) {
                $query->where('customer_id', $customerId);
            }
        ])
        ->where('category',$service_type)
        ->get();

        $isSupervisor = $user->role?->installation_supervisor ?? false;
        $customer = $isSupervisor ? Customer::find($customerId) : null;
        $status = $customer?->installation_status ?? null;
        $showValues = !$isSupervisor || in_array($status, ['photo_upload_complete', 'supervisor_approved']);

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
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::with('role')->find(Auth::user()->id);
        $userPerms = $this->getPermision();


        Validator::make($request->all(), [
            'name' => 'required|max:255',
            'phone_1' => 'required|max:255',
            'city_id' => 'required',
            'township_id' => 'required',
            'address' => 'required',
            'latitude' => 'required|max:255',
            'longitude' => 'required|max:255',
            'ftth_id' => 'required|max:255',
            'dob' => 'nullable|date',
            'order_date' => 'date',
            'status' => 'required',
            'installation_date' => 'nullable|date',
            'bandwidth' => 'required|integer',
            'service_type' => 'required',
            // New installation/collection fields
            'customer_installation_type' => 'required|in:new_installation,relocation',
            'old_customer_id' => 'nullable|exists:customers,id|required_if:customer_installation_type,relocation',
            'onu_collected_by' => 'nullable|string|max:255|required_if:onu_collection_status,collected',
            'onu_collection_status' => 'required|string|in:no_action,collected,reused',
            'onu_collection_date' => 'nullable|date|required_if:onu_collection_status,collected',
            'drop_cable_collected_by' => 'nullable|string|max:255|required_if:drop_cable_collection_status,collected',
            'drop_cable_collection_status' => 'required|string|in:no_action,collected,reused',
            'drop_cable_collection_date' => 'nullable|date|required_if:drop_cable_collection_status,collected',
        ])->validate();
        if ($request->has('id') && !$user?->roles?->read_customer && $userPerms) {
            $customer = Customer::find($request->input('id'));
            $oldCustomers = clone $customer;



            foreach ($userPerms as $key => $value) {

               
              

                if ($value == 'status_id'){
                     $customer->$value = $request->status ? json_decode($request->status)?->id : null;
                     continue;
                }
                   

                if ($value == 'project_id') {
                    if (!empty($request->project)){
                    $customer->$value =  $request->project ? json_decode($request->project)?->id : null;
                    }
                     continue;
                }
                if ($value == 'pop_id') {
                    if (isset($request->pop_id))
                        $customer->$value =  $request->pop_id ? json_decode($request->pop_id)?->id : null;
                     continue;
                }
                if ($value == 'pop_device_id') {
                    if (isset($request->pop_device_id))
                        $customer->$value =  $request->pop_device_id ? json_decode($request->pop_device_id)?->id : null;
                     continue;
                }
                if ($value == 'gpon_ontid') {
                    if (isset($request->gpon_ontid))
                        $customer->$value =  $request->gpon_ontid ? json_decode($request->gpon_ontid)?->name : null;
                     continue;
                }
                if ($value == 'partner_id') {
                    if (isset($request->partner_id)){
                       $customer->$value =  $request->partner_id;
                    }
                      continue;   
                }
                if ($value == 'isp_id') {
                    if (isset($request->isp_id))
                        $customer->$value =  $request->isp_id ? json_decode($request->isp_id)?->id : null;
                     continue;
                }
                if ($value == 'bundle') {
                    if (!empty($request->bundles)) {
                        $bundle_list = json_decode($request->bundles);
                        $customer->bundle = '';
                        foreach ($bundle_list as $key => $value) {

                            if ($key !== array_key_last($bundle_list))
                                $customer->bundle .= $value->id . ',';
                            else
                                $customer->bundle .= $value->id;
                        }
                    }
                     continue;
                }
                if ($value == 'bandwidth') {
                    if ($request->bandwidth) {
                        $bandwidth = (int) $request->bandwidth;
                        $selectedService = PortSharingService::where('max_speed', '>=', $bandwidth)
                            ->orderBy('max_speed', 'asc')
                            ->first();
                        $customer->bandwidth = $bandwidth;
                        $customer->port_sharing_service_id = $selectedService->id;
                    }
                     continue;
                }
                // if ($value == 'sn_id') {
                //     if (!empty($request->sn_id))
                //         $customer->$value = $request->sn_id?json_decode($request->sn_id)?->id:null;
                // }
                // if ($value == 'dn_id') {
                //     if (!empty($request->dn_id))
                //         $customer->$value =  $request->dn_id?json_decode($request->dn_id)?->id:null;
                // }
                // if ($value == 'splitter_no') {
                //     if (isset($request->splitter_no))
                //         $customer->$value =  $request->splitter_no?json_decode($request->splitter_no)?->name:null;
                // }

                //Subcom info
                if ($value == 'installation_status') {
                    $customer->$value =  $request->installation_status ? json_decode($request->installation_status)?->id : null;
                     continue;
                }

                if ($value == 'subcom_id') {
                    $customer->$value =  $request->subcom ? json_decode($request->subcom)?->id : null;
                     continue;
                }
                if ($value == 'subcom_assign_date') {
                    if (!empty($request->subcom) && !$oldCustomers->subcom_assign_date) {
                        if (!$customer->subcom_assign_date) {
                            $customer->$value = Carbon::now()->format('Y-m-d H:i:s');
                        }
                    }
                     continue;
                }
                if ($value == 'supervisor_id' && isset($request->supervisor_id)) {
                    if ($user->role->installation_oss == 1) {
                        $customer->supervisor_id = $request->supervisor_id;
                        $customer->assigned_by = $user->id;
                    }
                     continue;
                }

                // check snPort table, if sn_id exists, then update the status to active
                if (!empty($request->sn_id)) {
                    $snId = json_decode($request->sn_id)->id;
                    $snPort = SnPort::where('customer_id', $customer->id)
                        ->first();
                    $portNumber = null;
                    if (!empty($request->splitter_no)) {
                        $portNumber = json_decode($request->splitter_no)->id;
                    }
                    if ($snPort) {
                        $snPort->sn_splitter_id = $snId;
                        $snPort->port_number = $portNumber;
                        $snPort->status = 'active';
                        $snPort->update();
                    } else {
                        $snPort = new SnPort();
                        $snPort->customer_id = $customer->id;
                        $snPort->port_number = $portNumber;
                        $snPort->sn_splitter_id = $snId;
                        $snPort->status = 'active';
                        $snPort->save();
                    }
                    
                }

                if (!empty($request->pop_device_id)) {
                    $popDeviceId = json_decode($request->pop_device_id)->id;
                    $snPort = SnPort::where('customer_id', $customer->id)
                        ->first();
                    if ($snPort) {
                        $snPort->pop_device_id = $popDeviceId;
                        $snPort->status = 'active';
                        $snPort->update();
                    } else {
                        $snPort = new SnPort();
                        $snPort->customer_id = $customer->id;
                        $snPort->pop_device_id = $popDeviceId;
                        $snPort->status = 'active';
                        $snPort->save();
                    }
                    
                }
                if (!empty($request->pop_id)) {
                    $popId = json_decode($request->pop_id)->id;
                    $snPort = SnPort::where('customer_id', $customer->id)
                        ->first();
                    if ($snPort) {
                        $snPort->pop_id = $popId;
                        $snPort->status = 'active';
                        $snPort->update();
                    } else {
                        $snPort = new SnPort();
                        $snPort->customer_id = $customer->id;
                        $snPort->pop_id = $popId;
                        $snPort->status = 'active';
                        $snPort->save();
                    }
                    
                }
                if (!empty($request->dn_box_id)) {
                    $dnBoxId = $request->dn_box_id;
                    $snPort = SnPort::where('customer_id', $customer->id)
                        ->first();
                    if ($snPort) {
                        $snPort->dn_box_id = $dnBoxId;
                        $snPort->status = 'active';
                        $snPort->update();
                    } else {
                        $snPort = new SnPort();
                        $snPort->customer_id = $customer->id;
                        $snPort->dn_box_id = $dnBoxId;
                        $snPort->status = 'active';
                        $snPort->save();
                    }
                    
                }
                if (!empty($request->dn_id)) {
                    $dnSplitterId = json_decode($request->dn_id)->id;
                    $snPort = SnPort::where('customer_id', $customer->id)
                        ->first();
                    if ($snPort) {
                        $snPort->dn_splitter_id = $dnSplitterId;
                        $snPort->status = 'active';
                        $snPort->update();
                    } else {
                        $snPort = new SnPort();
                        $snPort->customer_id = $customer->id;
                        $snPort->dn_splitter_id = $dnSplitterId;
                        $snPort->status = 'active';
                        $snPort->save();
                    }
                   
                }
               
                 // check $value is in the list of customers table fields
                if (!\Schema::hasColumn('customers', is_object($value) ? '' : $value)) {
                    continue;
                }
                if ($value != 'id' && $value != 'created_at' && $value != 'updated_at' && $value != 'deleted')
                    $customer->$value = $request->$value;
            }// end of foreach loop

            $customerAddress = CustomerAddress::firstOrNew([
                'customer_id' => $customer->id,
                'is_current' => 1
            ]);
            $customerAddress->township_id = isset($request->township_id) ? $request->township_id : null;
            $customerAddress->location = ($request->latitude ?? '') . ',' . ($request->longitude ?? '');
            $customerAddress->actual_location = ($request->actual_latitude ?? '') . ',' . ($request->actual_longitude ?? '');
            $customerAddress->address = $request->address ?? '';
            $customerAddress->type = $customerAddress->type ?? 'new_installation';
            $customerAddress->is_current = 1;
            $customerAddress->save();

            $original = $customer->getOriginal();  // Get the original values before update
            $customer->update();                   // Perform the update
            $changes = $customer->getChanges();    // Get the updated values after the update
            app(\App\Services\CustomerHistoryService::class)->storeCustomerHistory(
                $customer,         // newly updated Customer
                $oldCustomers,      // old snapshot
                $changes,          // the changes array
                $request->input('id'),
                $request->input('start_date') // optional
            );
            $logData = [];
            foreach ($changes as $key => $newValue) {
                $logData[$key] = [
                    'from' => $original[$key] ?? null,  // Original value
                    'to' => $newValue                   // New value
                ];
            }
            activity()
                ->causedBy(User::find(Auth::id()))
                ->performedOn($customer)
                ->withProperties(['changes' => $logData])  // Log the changes with from-to values
                ->log('Customer updated: ' . $customer->ftth_id);


            return redirect()->back()->with('message', 'Customer Updated Successfully.');
        }
        return redirect()->back()->with('message', 'No Changed has updated.');
    }
    public function getHistory($id)
    {
        if ($id) {
            $customer_history =  CustomerHistory::where('customer_id', '=', $id)
                ->leftjoin('status', 'status.id', '=', 'customer_histories.old_status')
                ->leftjoin('status as s', 's.id', '=', 'customer_histories.new_status')
                ->join('users', 'users.id', '=', 'customer_histories.actor_id')
                ->leftjoin('packages as p1', DB::raw('p1.id'), '=', 'customer_histories.old_package')
                ->leftjoin('packages as p2', DB::raw('p2.id'), '=', 'customer_histories.new_package')
                ->select('customer_histories.*', 'status.name as old_status_name', 'status.color as status_color', 'users.name as actor_name', DB::raw('p1.name as old_package_name'), DB::raw('p2.name as new_package_name'), DB::raw('s.name as new_status_name'))
                ->OrderBy('customer_histories.id', 'DESC')
                ->get();
            return response()->json($customer_history, 200);
        }
    }
    public function getmaxid()
    {
        $customers = Customer::where(function ($query) {
            return $query->where('customers.deleted', '=', 0)
                ->orWhereNull('customers.deleted');
        })->get();
        $cities = City::all();
        $max_c_id = array();

        foreach ($cities as $city) {

            $cid = array();
            foreach ($customers as $customer) {
                ///(^TCL[0-9]{5}-[A-Z]{3,})$/
                $reg = "/(^[A-Z0-9]{3}" . $city->short_code . "[0-9]{7}[A-Z]{2})$/";
                if (preg_match($reg, $customer->ftth_id)) {
                    $pattern = '/\d+/'; // Regular expression to match integers
                    preg_match($pattern, $customer->ftth_id, $matches);

                    if (isset($matches[0])) {
                        $integer = (int)$matches[0];
                        array_push($cid, $integer);
                    }
                }
            }
            if (!empty($cid)) {
                $max_id = max($cid);
                //  dd($max_id);
            } else {
                $max_id = 0;
            }


            $id_array = array('id' => $city->id, 'value' => $max_id);

            array_push($max_c_id, $id_array);
        }

        //   if(sizeof($max_c_id))
        //dd($max_c_id);
        return $max_c_id;
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function syncRadius()
    {
        $customers = Customer::where('customers.deleted', '=', 0)
            ->orWhereNull('customers.deleted')
            ->get();
        if (RadiusController::checkRadiusEnable()) {
            foreach ($customers as $customer) {
                if ($customer->pppoe_account) {
                    $radius = RadiusController::checkCustomer($customer->pppoe_account);
                    if ($radius != 'no account') {
                        RadiusController::setExpiry($customer->pppoe_account, $customer->service_off_date);
                        //RadiusController::createRadius($customer->id);
                        echo $customer->pppoe_account . ' Created! <br />';
                    } else {
                        echo $customer->pppoe_account . ' Already exists ! <br />';
                    }
                } else {
                    echo $customer->ftth_id . ' No PPPOE Account ! <br />';
                }
            }
        }
        echo "Done";
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            $customer = Customer::find($request->input('id'));
            $customer->deleted = 1;
            $customer->update();
            if (RadiusController::checkRadiusEnable()) {
                RadiusController::deleteUser($customer->id);
            }
            return redirect()->back();
        }
    }
    public function subcomView($id)
    {
        $bundle_equiptments = BundleEquiptment::get();
        $subconCheckList = SubconChecklist::where('service_type', 'installation')->get();

        // Load the customer with all necessary relationships
        $customer = Customer::with([
            'township',
            'package',
            'status',
            'isp',
            'pop',
            'dn',
            'sn'
        ])->findOrFail($id);

        // Load checklist values for this customer
        $checklistValues = SubconChecklistValue::where('customer_id', $id)->get();

        // Format checklist values for easy access in the frontend
        $formattedChecklistValues = [];
        $checklistImages = [];

        foreach ($checklistValues as $value) {
            $formattedChecklistValues[$value->subcon_checklist_id] = [
                'id' => $value->id,
                'title' => $value->title,
                'status' => $value->status
            ];

            if ($value->attachment) {
                $checklistImages[$value->subcon_checklist_id] = $value->attachment;
            }
        }

        // Add checklist values and images to the customer object
        $customer->checklist_values = $formattedChecklistValues;
        $customer->checklist_images = $checklistImages;

        return Inertia::render('Client/SubcomCustomerView', [
            'customer' => $customer,
            'bundle_equiptments' => $bundle_equiptments,
            'subconCheckList' => $subconCheckList
        ]);
    }

    public function subcomUpdate(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        // Get all request keys to identify checklist fields
        $requestKeys = array_keys($request->all());
        $checklistFields = [];

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

        // Add validation rules for checklist fields
        $validationRules = [
            'installation_status' => 'required|array',
            'way_list_date' => 'nullable|date',
            'installation_date' => 'nullable|date',
            'fiber_distance' => 'nullable|integer',
            'bundles' => 'nullable',
            'onu_serial' => 'nullable|string',
            'onu_power' => 'nullable|string',
            'installation_remark' => 'nullable|string'
        ];

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
          
        if($request->installation_status['name'] == 'Installation Completed' || $request->installation_status['name'] == 'Photo Upload Complete') {
            $validationRules['installation_date'] = 'required|date';
            $validationRules['way_list_date'] = 'required';
            $validationRules['fiber_distance'] = 'required';
            $validationRules['onu_serial'] = 'required';
            $validationRules['onu_power'] = 'required';
            $validationRules['actual_latitude'] = 'required';
            $validationRules['actual_longitude'] = 'required';
        } else {
            $validationRules['installation_date'] = 'nullable|date';
        }

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        $oldCustomers = clone $customer;



        $customer->installation_status = ($request->installation_status) ? $request->installation_status['id'] : null;
        $customer->way_list_date = $request->way_list_date;
        $customer->installation_date = $request->installation_date;
        $customer->fiber_distance = $request->fiber_distance;

        $customer->onu_serial = $request->onu_serial;
        $customer->onu_power = $request->onu_power;
        $customer->installation_remark = $request->installation_remark;
         $customerAddress = CustomerAddress::firstOrNew([
                'customer_id' => $customer->id,
                'is_current' => 1
            ]);
             $customerAddress->actual_location = ($request->actual_latitude ?? '') . ',' . ($request->actual_longitude ?? '');
             $customerAddress->save();

        if (!empty($request->bundles)) {
            $customer->bundle = '';
            foreach ($request->bundles as $key => $value) {
                if ($key !== array_key_last($request->bundles))
                    $customer->bundle .= $value['id'] . ',';
                else
                    $customer->bundle .= $value['id'];
            }
        }

        // Process and save checklist values
        foreach ($checklistFields as $checklistId => $fields) {
            $title = isset($fields['title']) ? $request->input($fields['title']) : null;
            $status = isset($fields['status']) ? $request->input($fields['status']) : null;
            $attachmentPath = null;

            // Process attachment if exists
            if (isset($fields['attachment']) && $request->hasFile($fields['attachment'])) {
                $attachmentPath = $request->file($fields['attachment'])
                    ->store('checklist_attachments/' . $customer->id, 'public');
            }

            // Find existing checklist value or create new one
            if ($title || $status) {


                $checklistValue = SubconChecklistValue::updateOrCreate(
                    [
                        'subcon_checklist_id' => $checklistId,
                        'customer_id' => $customer->id,
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

        $original = $customer->getOriginal();  // Get the original values before update
        $customer->update();                   // Perform the update
        $changes = $customer->getChanges();    // Get the updated values after the update

        app(\App\Services\CustomerHistoryService::class)->storeCustomerHistory(
            $customer,         // newly updated Customer
            $oldCustomers,      // old snapshot
            $changes,          // the changes array
            $id,
            $request->input('start_date') // optional
        );

        $logData = [];
        foreach ($changes as $key => $newValue) {
            $logData[$key] = [
                'from' => $original[$key] ?? null,  // Original value
                'to' => $newValue                   // New value
            ];
        }

        activity()
            ->causedBy(User::find(Auth::id()))
            ->performedOn($customer)
            ->withProperties(['changes' => $logData])  // Log the changes with from-to values
            ->log('Customer updated: ' . $customer->ftth_id);

        return redirect()->back()->with('message', 'Customer updated successfully');
    }
    public function installationApproval(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        // Get all request keys to identify checklist fields
        $requestKeys = array_keys($request->all());
        $checklistFields = [];

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

        // Add validation rules for checklist fields


        // Add validation rules for each checklist field
        foreach ($checklistFields as $checklistId => $fields) {
            if (isset($fields['title'])) {
                $validationRules[$fields['title']] = 'nullable|string';
            }
            if (isset($fields['status'])) {
                $validationRules[$fields['status']] = 'nullable|string|in:requested,approved,declined';
            }
        }





        // Process and save checklist values
        foreach ($checklistFields as $checklistId => $fields) {
            $title = isset($fields['title']) ? $request->input($fields['title']) : null;
            $status = isset($fields['status']) ? $request->input($fields['status']) : '';

            // Check if checklist value exists
            $checklistValue = SubconChecklistValue::where([
                'subcon_checklist_id' => $checklistId,
                'customer_id' => $customer->id,
            ])->first();

            // Only proceed if record exists
            if ($checklistValue) {

                $checklistValue->update([
                    'title' => $title,
                    'status' => $status,
                    'current_actor_user_id' => Auth::id(),
                    'last_status_changed_at' => now(),
                ]);

                // Create history if data changed
                if ($checklistValue->wasChanged()) {
                    SubconChecklistValueHistory::create([
                        'checklist_value_id' => $checklistValue->id,
                        'title' => $title,
                        'attachment' => $checklistValue->attachment,
                        'action' => $status,
                        'actor_id' => Auth::id(),
                        'acted_at' => now(),
                    ]);
                }
            }
        }



        // activity()
        //     ->causedBy(User::find(Auth::id()))
        //     ->performedOn($customer)
        //     ->withProperties(['changes' => $logData])  // Log the changes with from-to values
        //     ->log('Customer updated: ' . $customer->ftth_id);

        return redirect()->back()->with('message', 'Customer updated successfully');
    }
    public function fixCustomerPackage()
    {
        $customers = Customer::all();
        $bandwidthOptions = [10, 20, 30, 35, 40, 50, 55, 60, 80, 100, 120, 150, 200];

        foreach ($customers as $customer) {
            // if($customer->bandwidth == 0 || $customer->bandwidth == null){
            $bandwidth = $bandwidthOptions[array_rand($bandwidthOptions)];
            $portSharingServices = PortSharingService::where('max_speed', '>=', $bandwidth)->orderByDesc('max_speed',)->first();
            $maintenanceServices = MaintenanceService::all()->random();
            dd($maintenanceServices);
            $installationServices = InstallationService::all()->random();
            $customer->bandwidth = $bandwidth;
            $customer->port_sharing_service_id = $portSharingServices->id;

            // }
            $customer->package_id = 1;
            $customer->update();
        }
    }
    public function migrateAdresses()
    {
        $customers = Customer::all();

        foreach ($customers as $customer) {
            // Check if address already exists for this customer
            $exists = CustomerAddress::where('customer_id', $customer->id)
                ->where('address', $customer->address)
                ->exists();

            if (!$exists) {
                CustomerAddress::create([
                    'customer_id' => $customer->id,
                    'address' => $customer->address,
                    'township_id' => $customer->township_id ?? 1,
                    'location' => $customer->location,
                    'is_current' => true,
                ]);
            }
        }

        return response()->json(['message' => 'Addresses migrated successfully.']);
    }

    public function getTownshipByCityId($cityId)
    {
        if (!$cityId) {
            return response()->json(['error' => 'City ID is required'], 400);
        }
        $townships = Township::where('city_id', $cityId)
            ->when(Auth::user()->role?->limit_region, function ($query) {
                return $query->whereIn('townships.id', Auth::user()->role?->townships->pluck('id'));
            })
            ->get();
        return response()->json($townships);
    }
}
