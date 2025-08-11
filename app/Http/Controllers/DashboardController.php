<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Package;
use App\Models\Project;
use App\Models\Status;
use App\Models\Township;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Packages;

class DashboardController extends Controller
{
    public function show(Request $request)
    {
        $user = User::with('role')->find(Auth::id());

        // Ensure user exists before accessing properties
        if (!$user || $user->user_type != 'internal') {
            return redirect(route('home')); // Return to stop execution
        }
        $all_customers = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->join('customer_addresses','customers.id','customer_addresses.customer_id')
            ->whereNotIn('status.type', ['cancel'])
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
           ->when($user->role?->limit_region, function ($query) use ($user) {
               return $query->whereIn('customer_addresses.township_id', $user->role?->townships->pluck('id'));
            })
             ->when($user->role?->installation_supervisor, function ($query) use ($user) {
                $query->where('customers.supervisor_id', $user->id);
            })
          ->where('customer_addresses.is_current',1)
            ->count();
        $total = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->join('customer_addresses','customers.id','customer_addresses.customer_id')
            ->whereIn('status.type', ['active', 'disabled'])
            
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->when($user->role?->limit_region, function ($query) use ($user) {
                return $query->whereIn('customer_addresses.township_id', $user->role?->townships->pluck('id'));
            })
             ->when($user->role?->installation_supervisor, function ($query) use ($user) {
                $query->where('customers.supervisor_id', $user->id);
            })
           ->where('customer_addresses.is_current',1)
            ->count();

        $to_install = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->join('customer_addresses','customers.id','customer_addresses.customer_id')
            ->whereIn('status.type', ['new', 'pending'])
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
             ->when($user->role?->installation_supervisor, function ($query) use ($user) {
                $query->where('customers.supervisor_id', $user->id);
            })
            ->when($user->role?->limit_region, function ($query) use ($user) {
                return $query->whereIn('customer_addresses.township_id', $user->role?->townships->pluck('id'));
            })
          ->where('customer_addresses.is_current',1)
            ->count();

        $suspense = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
           ->join('customer_addresses','customers.id','customer_addresses.customer_id')
            ->where('status.type', '=', 'suspense')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->when($user->role?->limit_region, function ($query) use ($user) {
               return $query->whereIn('customer_addresses.township_id', $user->role?->townships->pluck('id'));
            })
             ->when($user->role?->installation_supervisor, function ($query) use ($user) {
                $query->where('customers.supervisor_id', $user->id);
            })
          ->where('customer_addresses.is_current',1)
            ->count();
        $terminate = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
           ->join('customer_addresses','customers.id','customer_addresses.customer_id')
            ->where('status.type', '=', 'terminate')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->when($user->role?->limit_region, function ($query) use ($user) {
                return $query->whereIn('customer_addresses.township_id', $user->role?->townships->pluck('id'));
            })
             ->when($user->role?->installation_supervisor, function ($query) use ($user) {
                $query->where('customers.supervisor_id', $user->id);
            })
          ->where('customer_addresses.is_current',1)
            ->count();

        $install_week = DB::table('customers')
          ->join('customer_addresses','customers.id','customer_addresses.customer_id')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->whereRaw('week(customers.installation_date)=week(now()) AND year(customers.installation_date)=year(NOW())')
            ->when($user->role?->limit_region, function ($query) use ($user) {
               return $query->whereIn('customer_addresses.township_id', $user->role?->townships->pluck('id'));
            })
             ->when($user->role?->installation_supervisor, function ($query) use ($user) {
                $query->where('customers.supervisor_id', $user->id);
            })
          ->where('customer_addresses.is_current',1)
            ->count();

        $ftth = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->join('customer_addresses','customers.id','customer_addresses.customer_id')
            ->join('port_sharing_services', 'customers.port_sharing_service_id', '=', 'port_sharing_services.id')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->whereIn('status.type', ['active', 'suspense', 'disabled'])
            ->where('port_sharing_services.type', 'ftth')
            ->when($user->role?->limit_region, function ($query) use ($user) {
                 return $query->whereIn('customer_addresses.township_id', $user->role?->townships->pluck('id'));
            })
             ->when($user->role?->installation_supervisor, function ($query) use ($user) {
                $query->where('customers.supervisor_id', $user->id);
            })
          ->where('customer_addresses.is_current',1)
            ->count();

        $b2b = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->join('customer_addresses','customers.id','customer_addresses.customer_id')
            ->join('port_sharing_services', 'customers.port_sharing_service_id', '=', 'port_sharing_services.id')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->whereIn('status.type', ['active', 'suspense', 'disabled'])
            ->where('port_sharing_services.type', '=', 'b2b')
            ->when($user->role?->limit_region, function ($query) use ($user) {
                 return $query->whereIn('customer_addresses.township_id', $user->role?->townships->pluck('id'));
            })
             ->when($user->role?->installation_supervisor, function ($query) use ($user) {
                $query->where('customers.supervisor_id', $user->id);
            })
          ->where('customer_addresses.is_current',1)
            ->count();

        $dia = DB::table('customers')
            ->join('customer_addresses','customers.id','customer_addresses.customer_id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->join('port_sharing_services', 'customers.port_sharing_service_id', '=', 'port_sharing_services.id')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->whereIn('status.type', ['active', 'suspense', 'disabled'])
            ->where('port_sharing_services.type', 'dia')
            ->when($user->role?->limit_region, function ($query) use ($user) {
                 return $query->whereIn('customer_addresses.township_id', $user->role?->townships->pluck('id'));
            })
             ->when($user->role?->installation_supervisor, function ($query) use ($user) {
                $query->where('customers.supervisor_id', $user->id);
            })
          ->where('customer_addresses.is_current',1)
            ->count();
        //SELECT p.name,COUNT(c.ftth_id) AS customers FROM packages p JOIN customers c ON c.package_id=p.id  WHERE p.`type`='ftth' GROUP BY p.name;
        $ftth_total = DB::table('customers')
            ->join('customer_addresses','customers.id','customer_addresses.customer_id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->join('port_sharing_services', 'customers.port_sharing_service_id', '=', 'port_sharing_services.id')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->whereIn('status.type', ['active', 'suspense', 'disabled'])
            ->where('port_sharing_services.type', 'ftth')
            ->when($user->role?->limit_region, function ($query) use ($user) {
                 return $query->whereIn('customer_addresses.township_id', $user->role?->townships->pluck('id'));
            })
            ->select('port_sharing_services.name', DB::raw('COUNT(customers.ftth_id) AS customers'))
            ->groupBy('port_sharing_services.name')
            ->orderBy('port_sharing_services.name', 'DESC')
            ->where('customer_addresses.is_current',1)
             ->when($user->role?->installation_supervisor, function ($query) use ($user) {
                $query->where('customers.supervisor_id', $user->id);
            })
            ->get();

        $b2b_total = DB::table('customers')
            ->join('customer_addresses','customers.id','customer_addresses.customer_id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->join('port_sharing_services', 'customers.port_sharing_service_id', '=', 'port_sharing_services.id')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->whereIn('status.type', ['active', 'suspense', 'disabled'])
            ->where('port_sharing_services.type', 'b2b')
            ->when($user->role?->limit_region, function ($query) use ($user) {
                 return $query->whereIn('customer_addresses.township_id', $user->role?->townships->pluck('id'));
            })
            ->select('port_sharing_services.name', DB::raw('COUNT(customers.ftth_id) AS customers'))
            ->groupBy('port_sharing_services.name')
            ->orderBy('port_sharing_services.name', 'DESC')
          ->where('customer_addresses.is_current',1)
           ->when($user->role?->installation_supervisor, function ($query) use ($user) {
                $query->where('customers.supervisor_id', $user->id);
            })
            ->get();

        $dia_total = DB::table('customers')
            ->join('customer_addresses','customers.id','customer_addresses.customer_id')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->join('port_sharing_services', 'customers.port_sharing_service_id', '=', 'port_sharing_services.id')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->whereIn('status.type', ['active', 'suspense', 'disabled'])
            ->where('port_sharing_services.type', 'dia')
            ->when($user->role?->limit_region, function ($query) use ($user) {
                 return $query->whereIn('customer_addresses.township_id', $user->role?->townships->pluck('id'));
            })
            ->select('port_sharing_services.name', DB::raw('COUNT(customers.ftth_id) AS customers'))
            ->groupBy('port_sharing_services.name')
            ->orderBy('port_sharing_services.name', 'DESC')
          ->where('customer_addresses.is_current',1)
           ->when($user->role?->installation_supervisor, function ($query) use ($user) {
                $query->where('customers.supervisor_id', $user->id);
            })
            ->get();


        return Inertia::render("Dashboard", [
            'total' => $total,
            'to_install' => $to_install,
            'suspense' => $suspense,
            'terminate' => $terminate,
            'install_week' => $install_week,
            'ftth' => $ftth,
            'b2b' => $b2b,
            'dia' => $dia,
            'ftth_total' => $ftth_total,
            'b2b_total' => $b2b_total,
            'dia_total' => $dia_total,
            'all_customers' => $all_customers,
        ]);
    }
    public function maps(Request $request)
    {
        $packages = Package::orderBy('name', 'ASC')->get();
        $all_townships = Township::get();
        $projects = Project::get();
        $status = Status::get();

        $package_except = $request->package_except;
        $township_except = $request->township_except;
        $status_except = $request->status_except;
        $project_except = $request->project_except;

        $townships  = Township::join('customers', 'customers.township_id', 'townships.id')
            ->join('packages', 'packages.id', 'customers.package_id')
            ->when($request->general, function ($query, $general) {
                $query->where(function ($query) use ($general) {
                    $query->where('customers.name', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.email', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.address', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.sale_channel', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.company_name', 'LIKE', '%' . $general . '%');
                });
            })
            ->when($request->type, function ($query, $type) {
                if ($type != 'all')
                    $query->where('port_sharing_services.type',  $type);
            })
            ->when($request->package, function ($query, $packages) use ($package_except) {
                $package_list = array();
                foreach ($packages as $value) {
                    array_push($package_list, $value['id']);
                }
                if ($package_except) {
                    $query->whereNotIn('customers.package_id', $package_list);
                } else {
                    $query->whereIn('customers.package_id', $package_list);
                }
            })
            ->when($request->township, function ($query, $townships) use ($township_except) {
                $township_list = array();
                foreach ($townships as $value) {
                    array_push($township_list, $value['id']);
                }
                if ($township_except) {
                    $query->whereNotIn('customers.township_id', $township_list);
                } else {
                    $query->whereIn('customers.township_id', $township_list);
                }
            })
            ->when($request->status, function ($query, $status) use ($status_except) {

                $status_list = array();
                foreach ($status as $value) {
                    array_push($status_list, $value['id']);
                }
                if ($status_except) {
                    $query->whereNotIn('customers.status_id', $status_list);
                } else {
                    $query->whereIn('customers.status_id', $status_list);
                }
            })
            ->when($request->township, function ($query, $townships) use ($township_except) {
                $township_list = array();
                foreach ($townships as $value) {
                    array_push($township_list, $value['id']);
                }
                if ($township_except) {
                    $query->whereNotIn('customers.township_id', $township_list);
                } else {
                    $query->whereIn('customers.township_id', $township_list);
                }
            })
            ->when($request->project, function ($query, $project) use ($project_except) {

                $project_list = array();
                foreach ($project as $value) {
                    array_push($project_list, $value['id']);
                }
                if ($project_except) {
                    $query->whereNotIn('customers.project_id', $project_list);
                } else {
                    $query->whereIn('customers.project_id', $project_list);
                }
            })
            ->groupBy('townships.name')
            ->select('townships.*', DB::raw('COUNT(*) AS total'))
            ->orderBy('total')
            ->get();

        $customers = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->join('port_sharing_services', 'customers.port_sharing_service_id', '=', 'port_sharing_services.id')
            ->when($request->type, function ($query, $type) {
                if ($type != 'all')
                    $query->where('port_sharing_services.type',  $type);
            })
            ->when($request->general, function ($query, $general) {
                $query->where(function ($query) use ($general) {
                    $query->where('customers.name', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.ftth_id', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_1', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.phone_2', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.email', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.address', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.sale_channel', 'LIKE', '%' . $general . '%')
                        ->orWhere('customers.company_name', 'LIKE', '%' . $general . '%');
                });
            })
            ->when($request->package, function ($query, $packages) use ($package_except) {
                $package_list = array();
                foreach ($packages as $value) {
                    array_push($package_list, $value['id']);
                }
                if ($package_except) {
                    $query->whereNotIn('customers.package_id', $package_list);
                } else {
                    $query->whereIn('customers.package_id', $package_list);
                }
            })
            ->when($request->township, function ($query, $townships) use ($township_except) {
                $township_list = array();
                foreach ($townships as $value) {
                    array_push($township_list, $value['id']);
                }
                if ($township_except) {
                    $query->whereNotIn('customers.township_id', $township_list);
                } else {
                    $query->whereIn('customers.township_id', $township_list);
                }
            })
            ->when($request->status, function ($query, $status) use ($status_except) {

                $status_list = array();
                foreach ($status as $value) {
                    array_push($status_list, $value['id']);
                }
                if ($status_except) {
                    $query->whereNotIn('customers.status_id', $status_list);
                } else {
                    $query->whereIn('customers.status_id', $status_list);
                }
            })
            ->when($request->township, function ($query, $townships) use ($township_except) {
                $township_list = array();
                foreach ($townships as $value) {
                    array_push($township_list, $value['id']);
                }
                if ($township_except) {
                    $query->whereNotIn('customers.township_id', $township_list);
                } else {
                    $query->whereIn('customers.township_id', $township_list);
                }
            })
            ->when($request->project, function ($query, $project) use ($project_except) {

                $project_list = array();
                foreach ($project as $value) {
                    array_push($project_list, $value['id']);
                }
                if ($project_except) {
                    $query->whereNotIn('customers.project_id', $project_list);
                } else {
                    $query->whereIn('customers.project_id', $project_list);
                }
            })
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->select('customers.*', 'status.name as status_name, status.type as status_type')
            ->get();
        return Inertia::render("Map", [
            'customers' => $customers,
            'townships' => $townships,
            'all_townships' => $all_townships,
            'packages' => $packages,
            'projects' => $projects,
            'status' => $status,
        ]);
    }
    public function ispDashboard(Request $request){

        // Accept both GET and POST for date filter
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        $zone_id = $request->input('zone_id');
        $township_id = $request->input('township_id');
        $service_types = $request->input('service_types', ['ALL']); // Default to ALL if not provided
        
        
        // Chart-specific filters
        $chart_date_from = $request->input('chart_date_from');
        $chart_date_to = $request->input('chart_date_to');
        $chart_zone_id = $request->input('chart_zone_id');
        $chart_township_id = $request->input('chart_township_id');
        $chart_status_id = $request->input('chart_status_id');

        $user = User::with('role')->find(Auth::id());
        if (!$user || $user->user_type != 'isp') {
            return redirect(route('home')); // Return to stop execution
        }

        // Get unique status types in correct order
        $status_types = DB::table('status')
            ->select('type')
            ->whereNotIn('type',['new', 'pending', 'cancel'])
            ->groupBy('type')
            ->orderBy('id')
            ->pluck('type')
            ->toArray();

        // Get all port sharing services for this ISP
        $port_sharing_services = DB::table('port_sharing_services')
            ->join('customers', 'port_sharing_services.id', '=', 'customers.port_sharing_service_id')
            ->where('customers.isp_id', $user->isp_id)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->when(!in_array('ALL', $service_types), function ($query) use ($service_types) {
                return $query->whereIn('port_sharing_services.type', $service_types);
            })
            ->select('port_sharing_services.id', 'port_sharing_services.name')
            ->groupBy('port_sharing_services.id', 'port_sharing_services.name')
            ->get();
       
        // Build matrix: for each port sharing service, count by status type
        $matrix = [];
        foreach ($port_sharing_services as $service) {
            $row = [
                'port_sharing_service_id' => $service->id,
                'port_sharing_service_name' => $service->name,
            ];
            foreach ($status_types as $status_type) {
                $query = DB::table('customers')
                    ->join('status', 'customers.status_id', '=', 'status.id')
                    ->where('customers.isp_id', $user->isp_id)
                    ->where('customers.port_sharing_service_id', $service->id)
                    ->where('status.type', $status_type)
                    ->where(function ($query) {
                        return $query->where('customers.deleted', '=', 0)
                            ->orwherenull('customers.deleted');
                    });
                
                // Apply service type filter
                if (!in_array('ALL', $service_types)) {
                    $query->whereIn('customers.service_type', $service_types);
                }
                
                if ($date_from) {
                    $query->whereDate('customers.installation_date', '>=', $date_from);
                }
                if ($date_to) {
                    $query->whereDate('customers.installation_date', '<=', $date_to);
                }
                $row[$status_type] = $query->count();
            }
            $matrix[] = $row;
        }

        // Also provide grand totals by status type
        $grand_total = [];
        foreach ($status_types as $status_type) {
            $query = DB::table('customers')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->where('customers.isp_id', $user->isp_id)
                ->where('status.type', $status_type)
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                });
            
            // Apply service type filter
            if (!in_array('ALL', $service_types)) {
                $query->whereIn('customers.service_type', $service_types);
            }
            
            if ($date_from) {
                $query->whereDate('customers.installation_date', '>=', $date_from);
            }
            if ($date_to) {
                $query->whereDate('customers.installation_date', '<=', $date_to);
            }
            $grand_total[$status_type] = $query->count();
        }
        $grand_total['total'] = array_sum($grand_total);

        // Build matrix by Zone
        $zones = DB::table('zones')->where('is_active', 1)->get();
        $zone_matrix = [];
        $filtered_zones = $zone_id ? $zones->where('id', $zone_id) : $zones;
        foreach ($filtered_zones as $zone) {
            $row = [
                'zone_id' => $zone->id,
                'zone_name' => $zone->name,
            ];
            foreach ($status_types as $status_type) {
                $query = DB::table('customers')
                    ->join('status', 'customers.status_id', '=', 'status.id')
                    ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                    ->join('townships', 'customer_addresses.township_id', '=', 'townships.id')
                    ->join('township_zone', 'townships.id', '=', 'township_zone.township_id')
                    ->where('customers.isp_id', $user->isp_id)
                    ->where('customer_addresses.is_current', 1)
                    ->where('township_zone.zone_id', $zone->id)
                    ->where('status.type', $status_type)
                    ->where(function ($query) {
                        return $query->where('customers.deleted', '=', 0)
                            ->orwherenull('customers.deleted');
                    });
                
                // Apply service type filter
                if (!in_array('ALL', $service_types)) {
                    $query->whereIn('customers.service_type', $service_types);
                }
                
                if ($date_from) {
                    $query->whereDate('customers.installation_date', '>=', $date_from);
                }
                if ($date_to) {
                    $query->whereDate('customers.installation_date', '<=', $date_to);
                }
                $row[$status_type] = $query->count();
            }
            $zone_matrix[] = $row;
        }

        // Grand total by zone
        $zone_grand_total = [];
        foreach ($status_types as $status_type) {
            $query = DB::table('customers')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                ->join('townships', 'customer_addresses.township_id', '=', 'townships.id')
                ->join('township_zone', 'townships.id', '=', 'township_zone.township_id')
                ->where('customers.isp_id', $user->isp_id)
                ->where('customer_addresses.is_current', 1)
                ->where('status.type', $status_type)
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                });
            
            // Apply service type filter
            if (!in_array('ALL', $service_types)) {
                $query->whereIn('customers.service_type', $service_types);
            }
            
            if ($date_from) {
                $query->whereDate('customers.installation_date', '>=', $date_from);
            }
            if ($date_to) {
                $query->whereDate('customers.installation_date', '<=', $date_to);
            }
            $zone_grand_total[$status_type] = $query->count();
        }
        $zone_grand_total['total'] = array_sum($zone_grand_total);

        // Build matrix by Township
        $townships = DB::table('townships')->get();
        $township_matrix = [];
        $filtered_townships = $township_id ? $townships->where('id', $township_id) : $townships;
        foreach ($filtered_townships as $township) {
            $row = [
                'township_id' => $township->id,
                'township_name' => $township->name,
            ];
            foreach ($status_types as $status_type) {
                $query = DB::table('customers')
                    ->join('status', 'customers.status_id', '=', 'status.id')
                    ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                    ->where('customers.isp_id', $user->isp_id)
                    ->where('customer_addresses.is_current', 1)
                    ->where('customer_addresses.township_id', $township->id)
                    ->where('status.type', $status_type)
                    ->where(function ($query) {
                        return $query->where('customers.deleted', '=', 0)
                            ->orwherenull('customers.deleted');
                    });
                
                // Apply service type filter
                if (!in_array('ALL', $service_types)) {
                    $query->whereIn('customers.service_type', $service_types);
                }
                
                if ($date_from) {
                    $query->whereDate('customers.installation_date', '>=', $date_from);
                }
                if ($date_to) {
                    $query->whereDate('customers.installation_date', '<=', $date_to);
                }
                $row[$status_type] = $query->count();
            }
            $township_matrix[] = $row;
        }

        // Grand total by township
        $township_grand_total = [];
        foreach ($status_types as $status_type) {
            $query = DB::table('customers')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                ->where('customers.isp_id', $user->isp_id)
                ->where('customer_addresses.is_current', 1)
                ->where('status.type', $status_type)
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                });
            
            // Apply service type filter
            if (!in_array('ALL', $service_types)) {
                $query->whereIn('customers.service_type', $service_types);
            }
            
            if ($township_id) {
                $query->where('customer_addresses.township_id', $township_id);
            }
            if ($date_from) {
                $query->whereDate('customers.installation_date', '>=', $date_from);
            }
            if ($date_to) {
                $query->whereDate('customers.installation_date', '<=', $date_to);
            }
            $township_grand_total[$status_type] = $query->count();
        }
        $township_grand_total['total'] = array_sum($township_grand_total);

        // Generate the last 12 months for line chart data
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $months[] = now()->subMonths($i)->format('Y-m');
        }

        // Build line chart data for port sharing services
        $chart_data = [];
        foreach ($months as $month) {
            $monthly_data = [];
            foreach ($port_sharing_services as $service) {
                $total_customers = 0;
                foreach ($status_types as $status_type) {
                    $query = DB::table('customers')
                        ->join('status', 'customers.status_id', '=', 'status.id')
                        ->where('customers.isp_id', $user->isp_id)
                        ->where('customers.port_sharing_service_id', $service->id)
                        ->where('status.type', $status_type)
                        ->whereYear('customers.installation_date', '=', substr($month, 0, 4))
                        ->whereMonth('customers.installation_date', '=', substr($month, 5, 2))
                        ->where(function ($query) {
                            return $query->where('customers.deleted', '=', 0)
                                ->orwherenull('customers.deleted');
                        });
                    
                    // // Apply service type filter to chart data
                    // if (!in_array('ALL', $service_types)) {
                    //     $query->whereIn('customers.service_type', $service_types);
                    // }
                    
                    // Apply chart-specific filters
                    if ($chart_date_from) {
                        $query->whereDate('customers.installation_date', '>=', $chart_date_from);
                    }
                    if ($chart_date_to) {
                        $query->whereDate('customers.installation_date', '<=', $chart_date_to);
                    }
                    if ($chart_zone_id) {
                        $query->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                              ->join('townships', 'customer_addresses.township_id', '=', 'townships.id')
                              ->join('township_zone', 'townships.id', '=', 'township_zone.township_id')
                              ->where('customer_addresses.is_current', 1)
                              ->where('township_zone.zone_id', $chart_zone_id);
                    }
                    if ($chart_township_id) {
                        if (!$chart_zone_id) { // Only join if not already joined above
                            $query->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                                  ->where('customer_addresses.is_current', 1);
                        }
                        $query->where('customer_addresses.township_id', $chart_township_id);
                    }
                    if ($chart_status_id) {
                        $query->where('status.type', $chart_status_id);
                    }
                    
                    $total_customers += $query->count();
                }
                $monthly_data[$service->name] = $total_customers;
            }
            $chart_data[$month] = $monthly_data;
        }

        return Inertia::render("Dashboard/IspDashboard", [
            'matrix' => $matrix,
            'status_types' => $status_types,
            'grand_total' => $grand_total,
            'zone_matrix' => $zone_matrix,
            'zone_grand_total' => $zone_grand_total,
            'zones' => $zones,
            'zone_id' => $zone_id,
            'township_matrix' => $township_matrix,
            'township_grand_total' => $township_grand_total,
            'townships' => $townships,
            'township_id' => $township_id,
            'months' => $months,
            'chart_data' => $chart_data,
            'chart_date_from' => $chart_date_from,
            'chart_date_to' => $chart_date_to,
            'chart_zone_id' => $chart_zone_id,
            'chart_township_id' => $chart_township_id,
            'chart_status_id' => $chart_status_id,
            'service_types' => $service_types,
        ]);
    }

    public function ispInstallationDashboard(Request $request){
        
        // Accept both GET and POST for date filter
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        // If no date_from and date_to, default to current week
        if (!$date_from && !$date_to) {
            $date_from = now()->startOfWeek()->format('Y-m-d');
            $date_to = now()->endOfWeek()->format('Y-m-d');
        }
        $service_types = $request->input('service_types', ['ALL']); // Default to ALL if not provided
        $selected_installation_service = $request->input('selected_installation_service', 'ALL'); // New parameter for installation service filter

        $user = User::with('role')->find(Auth::id());
        if (!$user || $user->user_type != 'isp') {
            return redirect(route('home')); // Return to stop execution
        }

        // Get unique status types in correct order (focusing on active customers)
        $status_types = DB::table('status')
            ->select('type')
            ->whereIn('type', ['active', 'disabled', 'suspense', 'terminate'])
            ->groupBy('type')
            ->orderBy('id')
            ->pluck('type')
            ->toArray();

        // Get all port sharing services for this ISP
        $port_sharing_services = DB::table('port_sharing_services')
            ->join('customers', 'port_sharing_services.id', '=', 'customers.port_sharing_service_id')
            ->where('customers.isp_id', $user->isp_id)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->when(!in_array('ALL', $service_types), function ($query) use ($service_types) {
                return $query->whereIn('port_sharing_services.type', $service_types);
            })
            ->when($selected_installation_service !== 'ALL', function ($query) use ($selected_installation_service) {
                return $query->where('customers.installation_service_id', $selected_installation_service);
            })
            ->select('port_sharing_services.id', 'port_sharing_services.name')
            ->groupBy('port_sharing_services.id', 'port_sharing_services.name')
            ->get();

        // Get all installation services for dropdown filter
        $installation_services = DB::table('installation_services')
            ->join('customers', 'installation_services.id', '=', 'customers.installation_service_id')
            ->where('customers.isp_id', $user->isp_id)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->select('installation_services.id', 'installation_services.name')
            ->groupBy('installation_services.id', 'installation_services.name')
            ->get();

        // Build matrix: for each port sharing service, count total customers
        $matrix = [];
        foreach ($installation_services as $service) {
            $row = [
                'installation_service_id' => $service->id,
                'installation_service_name' => $service->name,
            ];
            
            $query = DB::table('customers')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->where('customers.isp_id', $user->isp_id)
                // ->where('customers.installation_service_id', $service->id)
                ->whereIn('status.type', $status_types) // Only count the specified status types
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                });
            
            // Apply service type filter
            if (!in_array('ALL', $service_types)) {
                $query->whereIn('customers.service_type', $service_types);
            }
            
            // Apply installation service filter if specified
            if ($selected_installation_service !== 'ALL') {
                $query->where('customers.installation_service_id', $selected_installation_service);
            }
            
            if ($date_from) {
                $query->whereDate('customers.installation_date', '>=', $date_from);
            }
            if ($date_to) {
                $query->whereDate('customers.installation_date', '<=', $date_to);
            }
            
            $row['total'] = $query->count();
            $matrix[] = $row;
        }

        // Also provide grand total
        $grand_total = [];
        $query = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->where('customers.isp_id', $user->isp_id)
            ->whereIn('status.type', $status_types)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            });
        
        // Apply service type filter
        if (!in_array('ALL', $service_types)) {
            $query->whereIn('customers.service_type', $service_types);
        }
        
        // Apply installation service filter
        if ($selected_installation_service !== 'ALL') {
            $query->where('customers.installation_service_id', $selected_installation_service);
        }
        
        if ($date_from) {
            $query->whereDate('customers.installation_date', '>=', $date_from);
        }
        if ($date_to) {
            $query->whereDate('customers.installation_date', '<=', $date_to);
        }
        $grand_total['total'] = $query->count();

        // Build matrix by Zone
        $zones = DB::table('zones')->where('is_active', 1)->get();
        $zone_matrix = [];
        foreach ($zones as $zone) {
            $row = [
                'zone_id' => $zone->id,
                'zone_name' => $zone->name,
            ];
            
            $query = DB::table('customers')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                ->join('townships', 'customer_addresses.township_id', '=', 'townships.id')
                ->join('township_zone', 'townships.id', '=', 'township_zone.township_id')
                ->where('customers.isp_id', $user->isp_id)
                ->where('customer_addresses.is_current', 1)
                ->where('township_zone.zone_id', $zone->id)
                ->whereIn('status.type', $status_types)
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                });
            
            // Apply service type filter
            if (!in_array('ALL', $service_types)) {
                $query->whereIn('customers.service_type', $service_types);
            }
            
            // Apply installation service filter
            if ($selected_installation_service !== 'ALL') {
                $query->where('customers.installation_service_id', $selected_installation_service);
            }
            
            if ($date_from) {
                $query->whereDate('customers.installation_date', '>=', $date_from);
            }
            if ($date_to) {
                $query->whereDate('customers.installation_date', '<=', $date_to);
            }
            
            $row['total'] = $query->count();
            $zone_matrix[] = $row;
        }

        // Zone grand total
        $zone_grand_total = [];
        $query = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
            ->join('townships', 'customer_addresses.township_id', '=', 'townships.id')
            ->join('township_zone', 'townships.id', '=', 'township_zone.township_id')
            ->where('customers.isp_id', $user->isp_id)
            ->where('customer_addresses.is_current', 1)
            ->whereIn('status.type', $status_types)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            });
        
        // Apply service type filter
        if (!in_array('ALL', $service_types)) {
            $query->whereIn('customers.service_type', $service_types);
        }
        
        // Apply installation service filter
        if ($selected_installation_service !== 'ALL') {
            $query->where('customers.installation_service_id', $selected_installation_service);
        }
        
        if ($date_from) {
            $query->whereDate('customers.installation_date', '>=', $date_from);
        }
        if ($date_to) {
            $query->whereDate('customers.installation_date', '<=', $date_to);
        }
        $zone_grand_total['total'] = $query->count();

        // Build matrix by Township
        $townships = DB::table('townships')
            ->join('customer_addresses', 'townships.id', '=', 'customer_addresses.township_id')
            ->join('customers', 'customer_addresses.customer_id', '=', 'customers.id')
            ->where('customers.isp_id', $user->isp_id)
            ->where('customer_addresses.is_current', 1)
            ->select('townships.id', 'townships.name')
            ->groupBy('townships.id', 'townships.name')
            ->get();
            
        $township_matrix = [];
        foreach ($townships as $township) {
            $row = [
                'township_id' => $township->id,
                'township_name' => $township->name,
            ];
            
            $query = DB::table('customers')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                ->where('customers.isp_id', $user->isp_id)
                ->where('customer_addresses.is_current', 1)
                ->where('customer_addresses.township_id', $township->id)
                ->whereIn('status.type', $status_types)
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                });
            
            // Apply service type filter
            if (!in_array('ALL', $service_types)) {
                $query->whereIn('customers.service_type', $service_types);
            }
            
            // Apply installation service filter
            if ($selected_installation_service !== 'ALL') {
                $query->where('customers.installation_service_id', $selected_installation_service);
            }
            
            if ($date_from) {
                $query->whereDate('customers.installation_date', '>=', $date_from);
            }
            if ($date_to) {
                $query->whereDate('customers.installation_date', '<=', $date_to);
            }
            
            $row['total'] = $query->count();
            $township_matrix[] = $row;
        }

        // Township grand total
        $township_grand_total = [];
        $query = DB::table('customers')
            ->join('status', 'customers.status_id', '=', 'status.id')
            ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
            ->where('customers.isp_id', $user->isp_id)
            ->where('customer_addresses.is_current', 1)
            ->whereIn('status.type', $status_types)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            });
        
        // Apply service type filter
        if (!in_array('ALL', $service_types)) {
            $query->whereIn('customers.service_type', $service_types);
        }
        
        // Apply installation service filter
        if ($selected_installation_service !== 'ALL') {
            $query->where('customers.installation_service_id', $selected_installation_service);
        }
        
        if ($date_from) {
            $query->whereDate('customers.installation_date', '>=', $date_from);
        }
        if ($date_to) {
            $query->whereDate('customers.installation_date', '<=', $date_to);
        }
        $township_grand_total['total'] = $query->count();

        // Build SLA Performance Matrix for FTTH installations
        $installation_services = DB::table('installation_services')
            ->join('customers', 'installation_services.id', '=', 'customers.installation_service_id')
            ->where('customers.isp_id', $user->isp_id)
            ->where('customers.service_type', 'FTTH')
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->whereNotNull('customers.order_date')
            ->whereNotNull('customers.installation_date')
            ->select('installation_services.id', 'installation_services.name', 'installation_services.sla_hours')
            ->groupBy('installation_services.id', 'installation_services.name', 'installation_services.sla_hours')
            ->get();

        $sla_matrix = [];
        foreach ($installation_services as $service) {
            $row = [
                'installation_service_id' => $service->id,
                'installation_service_name' => $service->name,
                'sla_hours' => $service->sla_hours,
            ];
            
            // Get all customers for this installation service
            $customers_query = DB::table('customers')
                ->where('customers.isp_id', $user->isp_id)
                ->where('customers.service_type', 'FTTH')
                ->where('customers.installation_service_id', $service->id)
                ->whereNotNull('customers.subcom_assign_date')
                ->whereNotNull('customers.installation_date')
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                });

            // Apply date filters
            if ($date_from) {
                $customers_query->whereDate('customers.installation_date', '>=', $date_from);
            }
            if ($date_to) {
                $customers_query->whereDate('customers.installation_date', '<=', $date_to);
            }

            $total_customers = $customers_query->count();
            
            // Calculate customers within SLA (installation time <= SLA hours)
            $within_sla = $customers_query->clone()
                ->whereRaw('TIMESTAMPDIFF(HOUR, customers.subcom_assign_date, customers.installation_date) <= ?', [$service->sla_hours])
                ->count();
            
            // Calculate customers over SLA
            $over_sla = $total_customers - $within_sla;
            
            $row['total_customers'] = $total_customers;
            $row['within_sla'] = $within_sla;
            $row['over_sla'] = $over_sla;
            
            $sla_matrix[] = $row;
        }

        // SLA grand total
        $sla_grand_total = [
            'total_customers' => 0,
            'within_sla' => 0,
            'over_sla' => 0,
        ];
        
        foreach ($sla_matrix as $row) {
            $sla_grand_total['total_customers'] += $row['total_customers'];
            $sla_grand_total['within_sla'] += $row['within_sla'];
            $sla_grand_total['over_sla'] += $row['over_sla'];
        }

        // Generate the last 12 months for line chart data
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $months[] = now()->subMonths($i)->format('Y-m');
        }

        // Build line chart data for port sharing services
        $chart_data = [];
        foreach ($months as $month) {
            $monthly_data = [];
            foreach ($port_sharing_services as $service) {
                $query = DB::table('customers')
                    ->join('status', 'customers.status_id', '=', 'status.id')
                    ->where('customers.isp_id', $user->isp_id)
                    ->where('customers.port_sharing_service_id', $service->id)
                    ->whereIn('status.type', $status_types)
                    ->whereYear('customers.installation_date', '=', substr($month, 0, 4))
                    ->whereMonth('customers.installation_date', '=', substr($month, 5, 2))
                    ->where(function ($query) {
                        return $query->where('customers.deleted', '=', 0)
                            ->orwherenull('customers.deleted');
                    });
                
                // Apply service type filter
                if (!in_array('ALL', $service_types)) {
                    $query->whereIn('customers.service_type', $service_types);
                }
                
                // Apply installation service filter if specified
                if ($selected_installation_service !== 'ALL') {
                    $query->where('customers.installation_service_id', $selected_installation_service);
                }
                
                if ($date_from) {
                    $query->whereDate('customers.installation_date', '>=', $date_from);
                }
                if ($date_to) {
                    $query->whereDate('customers.installation_date', '<=', $date_to);
                }
                
                $monthly_data[$service->name] = $query->count();
            }
            $chart_data[$month] = $monthly_data;
        }

        return Inertia::render("Dashboard/IspInstallationDashboard", [
            'matrix' => $matrix,
            'status_types' => $status_types,
            'grand_total' => $grand_total,
            'zone_matrix' => $zone_matrix,
            'zone_grand_total' => $zone_grand_total,
            'zones' => $zones,
            'township_matrix' => $township_matrix,
            'township_grand_total' => $township_grand_total,
            'townships' => $townships,
            'sla_matrix' => $sla_matrix,
            'sla_grand_total' => $sla_grand_total,
            'installation_services' => $installation_services,
            'port_sharing_services' => $port_sharing_services,
            'service_types' => $service_types,
            'selected_installation_service' => $selected_installation_service,
            'months' => $months,
            'chart_data' => $chart_data,
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);
    }

    public function ossTeamDashboard(Request $request){
        
        // Accept both GET and POST for date filter
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        // If no date_from and date_to, default to current week
        if (!$date_from && !$date_to) {
            $date_from = now()->startOfDay()->format('Y-m-d');
            $date_to = now()->endOfDay()->format('Y-m-d');
        }
        $zone_id = $request->input('zone_id');
        $city_id = $request->input('city_id');

        $user = User::with('role')->find(Auth::id());
        if (!$user || ($user->user_type != 'internal' && !$user->role?->incident_oss)) {
            return redirect(route('home'));
        }

        // Define ticket types based on incident types
        $ticket_types = [
            'total_ticket' => 'Total Ticket',
            'new_installation_ticket' => 'New Installation Ticket', 
            'rectification_ticket' => 'Rectification Ticket',
            'suspend_ticket' => 'Suspend Ticket',
            'plan_change_ticket' => 'Plan Change Ticket'
        ];

        // Get all cities
        $cities = DB::table('cities')->orderBy('name')->get();

        // Get zones based on city filter
        $zones_query = DB::table('zones')->where('is_active', 1);
        if ($city_id) {
            $zones_query->where('city_id', $city_id);
        }
        $zones = $zones_query->get();

        // Build matrix by Zone
        $zone_matrix = [];
        $filtered_zones = $zone_id ? $zones->where('id', $zone_id) : $zones;
        
        foreach ($filtered_zones as $zone) {
            $row = [
                'zone_id' => $zone->id,
                'zone_name' => $zone->name,
            ];
            
            // Total Ticket
            $total_query = DB::table('incidents')
                ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                ->join('townships', 'customer_addresses.township_id', '=', 'townships.id')
                ->join('township_zone', 'townships.id', '=', 'township_zone.township_id')
                ->where('customer_addresses.is_current', 1)
                ->where('township_zone.zone_id', $zone->id)
                ->whereNotIn('incidents.status', [4]);
            
            // Add city filter to the query
            if ($city_id) {
                $total_query->where('townships.city_id', $city_id);
            }
            
            if ($date_from) {
                $total_query->whereDate('incidents.date', '>=', $date_from);
            }
            if ($date_to) {
                $total_query->whereDate('incidents.date', '<=', $date_to);
            }
            
            $row['total_ticket'] = $total_query->count();
            
            // New Installation Ticket (customers with new installation status)
            $new_installation_query = DB::table('incidents')
                ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                ->join('townships', 'customer_addresses.township_id', '=', 'townships.id')
                ->join('township_zone', 'townships.id', '=', 'township_zone.township_id')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->where('customer_addresses.is_current', 1)
                ->where('township_zone.zone_id', $zone->id)
                ->where('status.type', 'new')
                ->whereNotIn('incidents.status', [4]);
            
            // Add city filter
            if ($city_id) {
                $new_installation_query->where('townships.city_id', $city_id);
            }
            
            if ($date_from) {
                $new_installation_query->whereDate('incidents.date', '>=', $date_from);
            }
            if ($date_to) {
                $new_installation_query->whereDate('incidents.date', '<=', $date_to);
            }
            
            $row['new_installation_ticket'] = $new_installation_query->count();
            
            // Rectification Ticket (service complaints)
            $rectification_query = DB::table('incidents')
                ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                ->join('townships', 'customer_addresses.township_id', '=', 'townships.id')
                ->join('township_zone', 'townships.id', '=', 'township_zone.township_id')
                ->where('customer_addresses.is_current', 1)
                ->where('township_zone.zone_id', $zone->id)
                ->where('incidents.type', 'service_complaint')
                ->whereNotIn('incidents.status', [4]);
            
            // Add city filter
            if ($city_id) {
                $rectification_query->where('townships.city_id', $city_id);
            }
            
            if ($date_from) {
                $rectification_query->whereDate('incidents.date', '>=', $date_from);
            }
            if ($date_to) {
                $rectification_query->whereDate('incidents.date', '<=', $date_to);
            }
            
            $row['rectification_ticket'] = $rectification_query->count();
            
            // Suspend Ticket
            $suspend_query = DB::table('incidents')
                ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                ->join('townships', 'customer_addresses.township_id', '=', 'townships.id')
                ->join('township_zone', 'townships.id', '=', 'township_zone.township_id')
                ->where('customer_addresses.is_current', 1)
                ->where('township_zone.zone_id', $zone->id)
                ->where('incidents.type', 'suspension')
                ->whereNotIn('incidents.status', [4]);
            
            // Add city filter
            if ($city_id) {
                $suspend_query->where('townships.city_id', $city_id);
            }
            
            if ($date_from) {
                $suspend_query->whereDate('incidents.date', '>=', $date_from);
            }
            if ($date_to) {
                $suspend_query->whereDate('incidents.date', '<=', $date_to);
            }
            
            $row['suspend_ticket'] = $suspend_query->count();
            
            // Plan Change Ticket
            $plan_change_query = DB::table('incidents')
                ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                ->join('townships', 'customer_addresses.township_id', '=', 'townships.id')
                ->join('township_zone', 'townships.id', '=', 'township_zone.township_id')
                ->where('customer_addresses.is_current', 1)
                ->where('township_zone.zone_id', $zone->id)
                ->where('incidents.type', 'plan_change')
                ->whereNotIn('incidents.status', [4]);
            
            // Add city filter
            if ($city_id) {
                $plan_change_query->where('townships.city_id', $city_id);
            }
            
            if ($date_from) {
                $plan_change_query->whereDate('incidents.date', '>=', $date_from);
            }
            if ($date_to) {
                $plan_change_query->whereDate('incidents.date', '<=', $date_to);
            }
            
            $row['plan_change_ticket'] = $plan_change_query->count();
            
            $zone_matrix[] = $row;
        }

        // Build matrix by Township for each zone
        $township_matrix = [];
        foreach ($filtered_zones as $zone) {
            $townships_query = DB::table('townships')
                ->join('township_zone', 'townships.id', '=', 'township_zone.township_id')
                ->where('township_zone.zone_id', $zone->id);
            
            // Add city filter to townships
            if ($city_id) {
                $townships_query->where('townships.city_id', $city_id);
            }
            
            $townships = $townships_query->select('townships.id', 'townships.name')->get();
                
            foreach ($townships as $township) {
                $row = [
                    'zone_id' => $zone->id,
                    'zone_name' => $zone->name,
                    'township_id' => $township->id,
                    'township_name' => $township->name,
                ];
                
                // Total Ticket
                $total_query = DB::table('incidents')
                    ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                    ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                    ->where('customer_addresses.is_current', 1)
                    ->where('customer_addresses.township_id', $township->id)
                    ->whereNotIn('incidents.status', [4]);
                
                if ($date_from) {
                    $total_query->whereDate('incidents.date', '>=', $date_from);
                }
                if ($date_to) {
                    $total_query->whereDate('incidents.date', '<=', $date_to);
                }
                
                $row['total_ticket'] = $total_query->count();
                
                // New Installation Ticket
                $new_installation_query = DB::table('incidents')
                    ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                    ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                    ->join('status', 'customers.status_id', '=', 'status.id')
                    ->where('customer_addresses.is_current', 1)
                    ->where('customer_addresses.township_id', $township->id)
                    ->where('status.type', 'new')
                    ->whereNotIn('incidents.status', [4]);
                
                if ($date_from) {
                    $new_installation_query->whereDate('incidents.date', '>=', $date_from);
                }
                if ($date_to) {
                    $new_installation_query->whereDate('incidents.date', '<=', $date_to);
                }
                
                $row['new_installation_ticket'] = $new_installation_query->count();
                
                // Rectification Ticket
                $rectification_query = DB::table('incidents')
                    ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                    ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                    ->where('customer_addresses.is_current', 1)
                    ->where('customer_addresses.township_id', $township->id)
                    ->where('incidents.type', 'service_complaint')
                    ->whereNotIn('incidents.status', [4]);
                
                if ($date_from) {
                    $rectification_query->whereDate('incidents.date', '>=', $date_from);
                }
                if ($date_to) {
                    $rectification_query->whereDate('incidents.date', '<=', $date_to);
                }
                
                $row['rectification_ticket'] = $rectification_query->count();
                
                // Suspend Ticket
                $suspend_query = DB::table('incidents')
                    ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                    ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                    ->where('customer_addresses.is_current', 1)
                    ->where('customer_addresses.township_id', $township->id)
                    ->where('incidents.type', 'suspension')
                    ->whereNotIn('incidents.status', [4]);
                
                if ($date_from) {
                    $suspend_query->whereDate('incidents.date', '>=', $date_from);
                }
                if ($date_to) {
                    $suspend_query->whereDate('incidents.date', '<=', $date_to);
                }
                
                $row['suspend_ticket'] = $suspend_query->count();
                
                // Plan Change Ticket
                $plan_change_query = DB::table('incidents')
                    ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                    ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                    ->where('customer_addresses.is_current', 1)
                    ->where('customer_addresses.township_id', $township->id)
                    ->where('incidents.type', 'plan_change')
                    ->whereNotIn('incidents.status', [4]);
                
                if ($date_from) {
                    $plan_change_query->whereDate('incidents.date', '>=', $date_from);
                }
                if ($date_to) {
                    $plan_change_query->whereDate('incidents.date', '<=', $date_to);
                }
                
                $row['plan_change_ticket'] = $plan_change_query->count();
                
                $township_matrix[] = $row;
            }
        }

        // Calculate grand totals
        $grand_total = [];
        foreach ($ticket_types as $key => $label) {
            $grand_total[$key] = 0;
        }
        
        foreach ($zone_matrix as $zone_row) {
            foreach ($ticket_types as $key => $label) {
                $grand_total[$key] += $zone_row[$key];
            }
        }

        return Inertia::render("Dashboard/OssTeamDashboard", [
            'ticket_types' => $ticket_types,
            'zone_matrix' => $zone_matrix,
            'township_matrix' => $township_matrix,
            'grand_total' => $grand_total,
            'zones' => $zones,
            'cities' => $cities,
            'city_id' => $city_id,
            'zone_id' => $zone_id,
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);
    }

   public function ispMaintenanceDashboard(Request $request){
        
        // Accept both GET and POST for date filter
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');
        // If no date_from and date_to, default to current month
        if (!$date_from && !$date_to) {
            $date_from = now()->startOfMonth()->format('Y-m-d');
            $date_to = now()->endOfMonth()->format('Y-m-d');
        }
        $service_types = $request->input('service_types', ['ALL']); // Default to ALL if not provided
        $selected_maintenance_service = $request->input('selected_maintenance_service', 'ALL'); // New parameter for port sharing service filter

        $user = User::with('role')->find(Auth::id());
        if (!$user || $user->user_type != 'isp') {
            return redirect(route('home')); // Return to stop execution
        }

        // Get all port sharing services for this ISP that have incidents
        $maintenance_services = DB::table('maintenance_services')
            ->join('customers', 'maintenance_services.id', '=', 'customers.maintenance_service_id')
            ->join('incidents', 'customers.id', '=', 'incidents.customer_id')
            ->where('customers.isp_id', $user->isp_id)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->when(!in_array('ALL', $service_types), function ($query) use ($service_types) {
                return $query->whereIn('maintenance_services.type', $service_types);
            })
            ->when($selected_maintenance_service !== 'ALL', function ($query) use ($selected_maintenance_service) {
                return $query->where('maintenance_services.id', $selected_maintenance_service);
            })
            ->select('maintenance_services.id', 'maintenance_services.name')
            ->groupBy('maintenance_services.id', 'maintenance_services.name')
            ->get();

        // Build matrix: for each port sharing service, count total incidents
        $matrix = [];
        foreach ($maintenance_services as $service) {
            $row = [
                'maintenance_service_id' => $service->id,
                'maintenance_service_name' => $service->name,
            ];
            
            $query = DB::table('incidents')
                ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                ->where('customers.isp_id', $user->isp_id)
                ->where('customers.maintenance_service_id', $service->id)
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                });
            
            if ($date_from) {
                $query->whereDate('incidents.date', '>=', $date_from);
            }
            if ($date_to) {
                $query->whereDate('incidents.date', '<=', $date_to);
            }
            
            $row['total_incidents'] = $query->count();
            $matrix[] = $row;
        }

        // Also provide grand total
        $grand_total = [];
        $query = DB::table('incidents')
            ->join('customers', 'incidents.customer_id', '=', 'customers.id')
            ->where('customers.isp_id', $user->isp_id)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            });
        
        // Apply service type filter
        if (!in_array('ALL', $service_types)) {
            $query->join('maintenance_services as pss', 'customers.maintenance_service_id', '=', 'pss.id')
                  ->whereIn('pss.type', $service_types);
        }
        
        // Apply port sharing service filter
        if ($selected_maintenance_service !== 'ALL') {
            $query->where('customers.maintenance_service_id', $selected_maintenance_service);
        }
        
        if ($date_from) {
            $query->whereDate('incidents.date', '>=', $date_from);
        }
        if ($date_to) {
            $query->whereDate('incidents.date', '<=', $date_to);
        }
        $grand_total['total_incidents'] = $query->count();

        // Build matrix by Zone
        $zones = DB::table('zones')->where('is_active', 1)->get();
        $zone_matrix = [];
        foreach ($zones as $zone) {
            $row = [
                'zone_id' => $zone->id,
                'zone_name' => $zone->name,
            ];
            
            $query = DB::table('incidents')
                ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                ->join('townships', 'customer_addresses.township_id', '=', 'townships.id')
                ->join('township_zone', 'townships.id', '=', 'township_zone.township_id')
                ->where('customers.isp_id', $user->isp_id)
                ->where('customer_addresses.is_current', 1)
                ->where('township_zone.zone_id', $zone->id)
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                });
            
            // Apply service type filter
            if (!in_array('ALL', $service_types)) {
                $query->join('maintenance_services as pss', 'customers.maintenance_service_id', '=', 'pss.id')
                      ->whereIn('pss.type', $service_types);
            }
            
            // Apply port sharing service filter
            if ($selected_maintenance_service !== 'ALL') {
                $query->where('customers.maintenance_service_id', $selected_maintenance_service);
            }
            
            if ($date_from) {
                $query->whereDate('incidents.date', '>=', $date_from);
            }
            if ($date_to) {
                $query->whereDate('incidents.date', '<=', $date_to);
            }
            
            $row['total_incidents'] = $query->count();
            $zone_matrix[] = $row;
        }

        // Zone grand total
        $zone_grand_total = [];
        $query = DB::table('incidents')
            ->join('customers', 'incidents.customer_id', '=', 'customers.id')
            ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
            ->join('townships', 'customer_addresses.township_id', '=', 'townships.id')
            ->join('township_zone', 'townships.id', '=', 'township_zone.township_id')
            ->where('customers.isp_id', $user->isp_id)
            ->where('customer_addresses.is_current', 1)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            });
        
        // Apply service type filter
        if (!in_array('ALL', $service_types)) {
            $query->join('maintenance_services as pss', 'customers.maintenance_service_id', '=', 'pss.id')
                  ->whereIn('pss.type', $service_types);
        }
        
        // Apply port sharing service filter
        if ($selected_maintenance_service !== 'ALL') {
            $query->where('customers.maintenance_service_id', $selected_maintenance_service);
        }
        
        if ($date_from) {
            $query->whereDate('incidents.date', '>=', $date_from);
        }
        if ($date_to) {
            $query->whereDate('incidents.date', '<=', $date_to);
        }
        $zone_grand_total['total_incidents'] = $query->count();

        // Build matrix by Township
        $townships = DB::table('townships')
            ->join('customer_addresses', 'townships.id', '=', 'customer_addresses.township_id')
            ->join('customers', 'customer_addresses.customer_id', '=', 'customers.id')
            ->join('incidents', 'customers.id', '=', 'incidents.customer_id')
            ->where('customers.isp_id', $user->isp_id)
            ->where('customer_addresses.is_current', 1)
            ->select('townships.id', 'townships.name')
            ->groupBy('townships.id', 'townships.name')
            ->get();
            
        $township_matrix = [];
        foreach ($townships as $township) {
            $row = [
                'township_id' => $township->id,
                'township_name' => $township->name,
            ];
            
            $query = DB::table('incidents')
                ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
                ->where('customers.isp_id', $user->isp_id)
                ->where('customer_addresses.is_current', 1)
                ->where('customer_addresses.township_id', $township->id)
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                });
            
            // Apply service type filter
            if (!in_array('ALL', $service_types)) {
                $query->join('maintenance_services as pss', 'customers.maintenance_service_id', '=', 'pss.id')
                      ->whereIn('pss.type', $service_types);
            }
            
            // Apply port sharing service filter
            if ($selected_maintenance_service !== 'ALL') {
                $query->where('customers.maintenance_service_id', $selected_maintenance_service);
            }
            
            if ($date_from) {
                $query->whereDate('incidents.date', '>=', $date_from);
            }
            if ($date_to) {
                $query->whereDate('incidents.date', '<=', $date_to);
            }
            
            $row['total_incidents'] = $query->count();
            $township_matrix[] = $row;
        }

        // Township grand total
        $township_grand_total = [];
        $query = DB::table('incidents')
            ->join('customers', 'incidents.customer_id', '=', 'customers.id')
            ->join('customer_addresses', 'customers.id', '=', 'customer_addresses.customer_id')
            ->where('customers.isp_id', $user->isp_id)
            ->where('customer_addresses.is_current', 1)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            });
        
        // Apply service type filter
        if (!in_array('ALL', $service_types)) {
            $query->join('maintenance_services as pss', 'customers.maintenance_service_id', '=', 'pss.id')
                  ->whereIn('pss.type', $service_types);
        }
        
        // Apply port sharing service filter
        if ($selected_maintenance_service !== 'ALL') {
            $query->where('customers.maintenance_service_id', $selected_maintenance_service);
        }
        
        if ($date_from) {
            $query->whereDate('incidents.date', '>=', $date_from);
        }
        if ($date_to) {
            $query->whereDate('incidents.date', '<=', $date_to);
        }
        $township_grand_total['total_incidents'] = $query->count();

        // Build SLA Performance Matrix for maintenance tickets
        $maintenance_services = DB::table('maintenance_services')
            ->join('customers', 'maintenance_services.id', '=', 'customers.maintenance_service_id')
            ->join('incidents', 'customers.id', '=', 'incidents.customer_id')
            ->where('customers.isp_id', $user->isp_id)
            ->where(function ($query) {
                return $query->where('customers.deleted', '=', 0)
                    ->orwherenull('customers.deleted');
            })
            ->whereNotNull('incidents.date')
            ->whereNotNull('incidents.time')
            ->select('maintenance_services.id', 'maintenance_services.name', 'maintenance_services.sla_hours')
            ->groupBy('maintenance_services.id', 'maintenance_services.name', 'maintenance_services.sla_hours')
            ->get();

        $sla_matrix = [];
        foreach ($maintenance_services as $service) {
            $row = [
                'maintenance_service_id' => $service->id,
                'maintenance_service_name' => $service->name,
                'sla_hours' => $service->sla_hours,
            ];
            
            // Get all incidents for this maintenance service
            $incidents_query = DB::table('incidents')
                ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                ->where('customers.isp_id', $user->isp_id)
                ->where('customers.maintenance_service_id', $service->id)
                ->whereNotNull('incidents.date')
                ->whereNotNull('incidents.time')
                ->where(function ($query) {
                    return $query->where('customers.deleted', '=', 0)
                        ->orwherenull('customers.deleted');
                });

            // Apply date filters
            if ($date_from) {
                $incidents_query->whereDate('incidents.date', '>=', $date_from);
            }
            if ($date_to) {
                $incidents_query->whereDate('incidents.date', '<=', $date_to);
            }

            $total_incidents = $incidents_query->count();
            
            // Calculate incidents within SLA 
            // For resolvedd incidents: check time between open and resolved
            // For unresolvedd incidents: check time between open and current time
            $within_sla_resolvedd = $incidents_query->clone()
                ->whereNotNull('incidents.resolved_date')
                ->whereNotNull('incidents.resolved_time')
                ->whereRaw("TIMESTAMPDIFF(HOUR, 
                    CONCAT(incidents.date, ' ', incidents.time), 
                    CONCAT(incidents.resolved_date, ' ', incidents.resolved_time)
                ) <= ?", [$service->sla_hours])
                ->count();
            
            $within_sla_unresolvedd = $incidents_query->clone()
                ->where(function ($query) {
                    $query->whereNull('incidents.resolved_date')
                          ->orWhereNull('incidents.resolved_time');
                })
                ->whereRaw("TIMESTAMPDIFF(HOUR, 
                    CONCAT(incidents.date, ' ', incidents.time), 
                    NOW()
                ) <= ?", [$service->sla_hours])
                ->count();
            
            $within_sla = $within_sla_resolvedd + $within_sla_unresolvedd;
            
            // Calculate incidents over SLA
            $over_sla = $total_incidents - $within_sla;
            
            $row['total_incidents'] = $total_incidents;
            $row['within_sla'] = $within_sla;
            $row['over_sla'] = $over_sla;
            
            $sla_matrix[] = $row;
        }

        // SLA grand total for maintenance
        $sla_grand_total = [
            'total_incidents' => 0,
            'within_sla' => 0,
            'over_sla' => 0,
        ];
        
        foreach ($sla_matrix as $row) {
            $sla_grand_total['total_incidents'] += $row['total_incidents'];
            $sla_grand_total['within_sla'] += $row['within_sla'];
            $sla_grand_total['over_sla'] += $row['over_sla'];
        }

        // Generate the last 12 months for line chart data
        $months = [];
        for ($i = 11; $i >= 0; $i--) {
            $months[] = now()->subMonths($i)->format('Y-m');
        }

        // Build line chart data for incidents by port sharing services
        $chart_data = [];
        foreach ($months as $month) {
            $monthly_data = [];
            foreach ($maintenance_services as $service) {
                $query = DB::table('incidents')
                    ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                    ->where('customers.isp_id', $user->isp_id)
                    ->where('customers.maintenance_service_id', $service->id)
                    ->whereYear('incidents.date', '=', substr($month, 0, 4))
                    ->whereMonth('incidents.date', '=', substr($month, 5, 2))
                    ->where(function ($query) {
                        return $query->where('customers.deleted', '=', 0)
                            ->orwherenull('customers.deleted');
                    });
                
                if ($date_from) {
                    $query->whereDate('incidents.date', '>=', $date_from);
                }
                if ($date_to) {
                    $query->whereDate('incidents.date', '<=', $date_to);
                }
                
                $monthly_data[$service->name] = $query->count();
            }
            $chart_data[$month] = $monthly_data;
        }

        return Inertia::render("Dashboard/IspMaintenanceDashboard", [
            'matrix' => $matrix,
            'grand_total' => $grand_total,
            'zone_matrix' => $zone_matrix,
            'zone_grand_total' => $zone_grand_total,
            'zones' => $zones,
            'township_matrix' => $township_matrix,
            'township_grand_total' => $township_grand_total,
            'townships' => $townships,
            'sla_matrix' => $sla_matrix,
            'sla_grand_total' => $sla_grand_total,
            'maintenance_services' => $maintenance_services,
            'service_types' => $service_types,
            'selected_maintenance_service' => $selected_maintenance_service,
            'months' => $months,
            'chart_data' => $chart_data,
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);
    }

    public function installationSupervisorDashboard(Request $request)
    {
        $user = User::with('role')->find(Auth::id());

        // Get filter parameters
        $supervisor_id = $request->get('supervisor_id');
        if ($user->role->installation_supervisor) {
            $supervisor_id = $user->id; // If the user is an installation supervisor, filter by their own ID
        }
        $installation_status = $request->get('installation_status');
        $subcom_id = $request->get('subcom_id');
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');

        // Get all installation supervisors (internal users with installation_supervisor role)
        $supervisors = User::join('roles', 'users.role_id', '=', 'roles.id')
            ->where('users.user_type', 'internal')
            ->where('roles.installation_supervisor', 1)
            ->where(function ($query) {
                return $query->where('users.disabled', '=', 0)
                    ->orWhereNull('users.disabled');
            })
            ->when($supervisor_id, function ($query) use ($supervisor_id, $user) {
                if ($user->role->installation_supervisor) {
                    return $query->where('users.id', $supervisor_id);
                }
            })
            ->select('users.id', 'users.name')
            ->orderBy('users.name')
            ->get();

        // Define installation status types
        $installation_statuses = [
            'team_assigned' => 'Team Assigned',
            'installation_started' => 'Installation Started',
            'photo_upload_complete' => 'Photo Upload Complete',
            'supervisor_approved' => 'Supervisor Approved',
            'installation_complete' => 'Installation Complete',
        ];
        
        // Build supervisor matrix
        $supervisor_matrix = [];
        foreach ($supervisors as $supervisor) {
            $row = [
                'supervisor_id' => $supervisor->id,
                'supervisor_name' => $supervisor->name,
            ];

            // Count customers for each installation status
            foreach ($installation_statuses as $status_key => $status_label) {
                $query = DB::table('customers')
                    ->join('status', 'customers.status_id', '=', 'status.id')
                    ->where('customers.supervisor_id', $supervisor->id)
                    ->where('customers.installation_status', $status_key)
                    ->whereNotIn('status.type', ['cancel'])
                    ->where(function ($q) {
                        return $q->where('customers.deleted', '=', 0)
                            ->orWhereNull('customers.deleted');
                    });

                // Apply date filters if provided
                if ($date_from) {
                    $query->whereDate('customers.created_at', '>=', $date_from);
                }
                if ($date_to) {
                    $query->whereDate('customers.created_at', '<=', $date_to);
                }

                $row[$status_key] = $query->count();
            }

            // Calculate total for this supervisor
            $row['total'] = array_sum(array_intersect_key($row, array_flip(array_keys($installation_statuses))));

            $supervisor_matrix[] = $row;
        }
     

        // Calculate grand totals
        $grand_total = [];
        foreach ($installation_statuses as $status_key => $status_label) {
            $grand_total[$status_key] = 0;
        }
        $grand_total['total'] = 0;

        foreach ($supervisor_matrix as $row) {
            foreach ($installation_statuses as $status_key => $status_label) {
                $grand_total[$status_key] += $row[$status_key];
            }
            $grand_total['total'] += $row['total'];
        }

        // Build Team Workload Matrix (Subcoms) - Installation focused
        $subcoms_query = DB::table('subcoms')
            ->where('disabled', 0)
            ->select('id', 'name')
            ->orderBy('name');

        if ($subcom_id) {
            $subcoms_query->where('id', $subcom_id);
        }

        $subcoms = $subcoms_query->get();

        $team_workload_matrix = [];
        foreach ($subcoms as $subcom) {
            $row = [
                'subcom_id' => $subcom->id,
                'subcom_name' => $subcom->name,
            ];

            // Total customers assigned to this subcom
            $total_customers_query = DB::table('customers')
                ->join('status', 'customers.status_id', '=', 'status.id')
                ->where('customers.subcom_id', $subcom->id)
                ->where('status.type', 'active')
                ->where(function ($q) {
                    return $q->where('customers.deleted', '=', 0)
                        ->orWhereNull('customers.deleted');
                });

            if ($date_from) {
                $total_customers_query->whereDate('customers.created_at', '>=', $date_from);
            }
            if ($date_to) {
                $total_customers_query->whereDate('customers.created_at', '<=', $date_to);
            }

            $row['total_customers'] = $total_customers_query->count();

            // Installation Status Counts
            foreach ($installation_statuses as $status_key => $status_label) {
                $status_query = clone $total_customers_query;
                $row[$status_key] = $status_query->where('customers.installation_status', $status_key)->count();
            }

            $team_workload_matrix[] = $row;
        }

        // Calculate team workload grand totals
        $team_grand_total = [
            'total_customers' => 0,
        ];

        foreach ($installation_statuses as $status_key => $status_label) {
            $team_grand_total[$status_key] = 0;
        }

        foreach ($team_workload_matrix as $row) {
            $team_grand_total['total_customers'] += $row['total_customers'];
            foreach ($installation_statuses as $status_key => $status_label) {
                $team_grand_total[$status_key] += $row[$status_key];
            }
        }

        return Inertia::render("Dashboard/InstallationSupervisorDashboard", [
            'installation_statuses' => $installation_statuses,
            'supervisor_matrix' => $supervisor_matrix,
            'grand_total' => $grand_total,
            'team_workload_matrix' => $team_workload_matrix,
            'team_grand_total' => $team_grand_total,
            'subcoms' => DB::table('subcoms')->where('disabled', 0)->orderBy('name')->get(),
            'supervisors' => $supervisors,
            'supervisor_id' => $supervisor_id,
            'installation_status' => $installation_status,
            'subcom_id' => $subcom_id,
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);
    }

    public function incidentTicketDashboard(Request $request)
    {
        // Get filter parameters
         $user = User::with('role')->find(Auth::id());

        // Get filter parameters
        $supervisor_id = $request->get('supervisor_id');
        if ($user->role->installation_supervisor) {
            $supervisor_id = $user->id; // If the user is an installation supervisor, filter by their own ID
        }
        $subcom_id = $request->get('subcom_id');
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');

        // Get all incident supervisors (internal users with incident_supervisor role)
        $supervisors = User::join('roles', 'users.role_id', '=', 'roles.id')
            ->where('users.user_type', 'internal')
            ->where('roles.incident_supervisor', 1)
            ->where(function ($query) {
                return $query->where('users.disabled', '=', 0)
                    ->orWhereNull('users.disabled');
            })
            ->where(function ($query) use ($supervisor_id, $user) {
                if ($user->role->incident_supervisor) {
                    return $query->where('users.id', $supervisor_id);
                }
            })
            ->select('users.id', 'users.name')
            ->orderBy('users.name')
            ->get();

        // Define incident status types based on the image and Incident.vue
        $incident_statuses = [
            6 => 'Supervisor Assign',
            2 => 'Team Assigned',
            1 => 'Pending Team Assign',
            4 => 'Photo Upload Completed',
            2 => 'Photo Approved',
            5 => 'Resolve Opened',
            3 => 'Ticket Closed'
        ];

        // Build supervisor matrix
        $supervisor_matrix = [];
        foreach ($supervisors as $supervisor) {
            $row = [
                'supervisor_id' => $supervisor->id,
                'supervisor_name' => $supervisor->name,
            ];

            // Total tickets for this supervisor
            $total_query = DB::table('incidents')
                ->where('incidents.supervisor_id', $supervisor->id);

            if ($date_from) {
                $total_query->whereDate('incidents.date', '>=', $date_from);
            }
            if ($date_to) {
                $total_query->whereDate('incidents.date', '<=', $date_to);
            }

            $row['total_ticket'] = $total_query->count();

            // Supervisor Assign Tickets (status = 6)
            $supervisor_assign_query = clone $total_query;
            $row['supervisor_assign_tickets'] = $supervisor_assign_query->where('incidents.status', 6)->count();

            // Team Assigned Tickets (status = 2)
            $team_assigned_query = clone $total_query;
            $row['team_assigned_tickets'] = $team_assigned_query->where('incidents.status', 2)->count();

            // Pending Team Assign (status = 1)
            $pending_query = clone $total_query;
            $row['pending_team_assign'] = $pending_query->where('incidents.status', 1)->count();

            // Photo Upload Completed (tasks with completed status)
            $photo_upload_query = DB::table('incidents')
                ->join('tasks', 'incidents.id', '=', 'tasks.incident_id')
                ->where('incidents.supervisor_id', $supervisor->id)
                ->where('tasks.status', 'completed');

            if ($date_from) {
                $photo_upload_query->whereDate('incidents.date', '>=', $date_from);
            }
            if ($date_to) {
                $photo_upload_query->whereDate('incidents.date', '<=', $date_to);
            }

            $row['photo_upload_completed'] = $photo_upload_query->distinct('incidents.id')->count('incidents.id');

            // Photo Approved (tasks with approved status or incidents with specific status)
            $photo_approved_query = DB::table('incidents')
                ->leftJoin('tasks', 'incidents.id', '=', 'tasks.incident_id')
                ->where('incidents.supervisor_id', $supervisor->id)
                ->where(function ($q) {
                    return $q->where('tasks.status', 'approved')
                        ->orWhere('incidents.status', 4); // Assuming status 4 represents photo approved
                });

            if ($date_from) {
                $photo_approved_query->whereDate('incidents.date', '>=', $date_from);
            }
            if ($date_to) {
                $photo_approved_query->whereDate('incidents.date', '<=', $date_to);
            }

            $row['photo_approved'] = $photo_approved_query->distinct('incidents.id')->count('incidents.id');

            // Resolve Opened (status = 5)
            $resolve_opened_query = clone $total_query;
            $row['resolve_opened'] = $resolve_opened_query->where('incidents.status', 5)->count();

            // Ticket Closed (status = 3)
            $ticket_closed_query = clone $total_query;
            $row['ticket_closed'] = $ticket_closed_query->where('incidents.status', 3)->count();

            $supervisor_matrix[] = $row;
        }

        // Calculate grand totals
        $grand_total = [
            'total_ticket' => 0,
            'supervisor_assign_tickets' => 0,
            'team_assigned_tickets' => 0,
            'pending_team_assign' => 0,
            'photo_upload_completed' => 0,
            'photo_approved' => 0,
            'resolve_opened' => 0,
            'ticket_closed' => 0,
        ];

        foreach ($supervisor_matrix as $row) {
            $grand_total['total_ticket'] += $row['total_ticket'];
            $grand_total['supervisor_assign_tickets'] += $row['supervisor_assign_tickets'];
            $grand_total['team_assigned_tickets'] += $row['team_assigned_tickets'];
            $grand_total['pending_team_assign'] += $row['pending_team_assign'];
            $grand_total['photo_upload_completed'] += $row['photo_upload_completed'];
            $grand_total['photo_approved'] += $row['photo_approved'];
            $grand_total['resolve_opened'] += $row['resolve_opened'];
            $grand_total['ticket_closed'] += $row['ticket_closed'];
        }

        // Build Team Workload Matrix (Subcoms)
        $subcoms_query = DB::table('subcoms')
            ->where('disabled', 0)
            ->select('id', 'name')
            ->orderBy('name');

        if ($subcom_id) {
            $subcoms_query->where('id', $subcom_id);
        }

        $subcoms = $subcoms_query->get();

        $team_workload_matrix = [];
        foreach ($subcoms as $subcom) {
            $row = [
                'subcom_id' => $subcom->id,
                'subcom_name' => $subcom->name,
            ];

            // Total tasks assigned to this subcom (through customers)
            $total_tasks_query = DB::table('tasks')
                ->join('incidents', 'tasks.incident_id', '=', 'incidents.id')
                ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                ->where('customers.subcom_id', $subcom->id)
                ->where(function ($q) {
                    return $q->where('customers.deleted', '=', 0)
                        ->orWhereNull('customers.deleted');
                });

            if ($date_from) {
                $total_tasks_query->whereDate('tasks.created_at', '>=', $date_from);
            }
            if ($date_to) {
                $total_tasks_query->whereDate('tasks.created_at', '<=', $date_to);
            }

            $row['total_tasks'] = $total_tasks_query->count();

            // Task Status Counts
            // WIP (status = 1)
            $task_wip_query = clone $total_tasks_query;
            $row['task_wip'] = $task_wip_query->where('tasks.status', '1')->count();

            // Photo Upload Complete (status = 4)
            $task_photo_complete_query = clone $total_tasks_query;
            $row['task_photo_complete'] = $task_photo_complete_query->where('tasks.status', '4')->count();

            // Photo Upload Rejected (status = 5)
            $task_photo_rejected_query = clone $total_tasks_query;
            $row['task_photo_rejected'] = $task_photo_rejected_query->where('tasks.status', '5')->count();

            // Supervisor Approved (status = 2)
            $task_approved_query = clone $total_tasks_query;
            $row['task_approved'] = $task_approved_query->where('tasks.status', '2')->count();

            // Incident Status Counts for this subcom
            $incident_base_query = DB::table('incidents')
                ->join('customers', 'incidents.customer_id', '=', 'customers.id')
                ->where('customers.subcom_id', $subcom->id)
                ->where(function ($q) {
                    return $q->where('customers.deleted', '=', 0)
                        ->orWhereNull('customers.deleted');
                });

            if ($date_from) {
                $incident_base_query->whereDate('incidents.date', '>=', $date_from);
            }
            if ($date_to) {
                $incident_base_query->whereDate('incidents.date', '<=', $date_to);
            }

            // Incident WIP (status = 1)
            $incident_wip_query = clone $incident_base_query;
            $row['incident_wip'] = $incident_wip_query->where('incidents.status', 1)->count();

            // Incident Closed (status = 3)
            $incident_closed_query = clone $incident_base_query;
            $row['incident_closed'] = $incident_closed_query->where('incidents.status', 3)->count();

            $team_workload_matrix[] = $row;
        }

        // Calculate team workload grand totals
        $team_grand_total = [
            'total_tasks' => 0,
            'task_wip' => 0,
            'task_photo_complete' => 0,
            'task_photo_rejected' => 0,
            'task_approved' => 0,
            'incident_wip' => 0,
            'incident_closed' => 0,
        ];

        foreach ($team_workload_matrix as $row) {
            $team_grand_total['total_tasks'] += $row['total_tasks'];
            $team_grand_total['task_wip'] += $row['task_wip'];
            $team_grand_total['task_photo_complete'] += $row['task_photo_complete'];
            $team_grand_total['task_photo_rejected'] += $row['task_photo_rejected'];
            $team_grand_total['task_approved'] += $row['task_approved'];
            $team_grand_total['incident_wip'] += $row['incident_wip'];
            $team_grand_total['incident_closed'] += $row['incident_closed'];
        }

        return Inertia::render("Dashboard/IncidentTicketDashboard", [
            'supervisor_matrix' => $supervisor_matrix,
            'grand_total' => $grand_total,
            'team_workload_matrix' => $team_workload_matrix,
            'team_grand_total' => $team_grand_total,
            'subcoms' => DB::table('subcoms')->where('disabled', 0)->orderBy('name')->get(),
            'supervisors' => $supervisors,
            'supervisor_id' => $supervisor_id,
            'subcom_id' => $subcom_id,
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);
    }

    public function rcaDashboard(Request $request)
    {
        // Get filter parameters
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');

        // Set default date range to current week if not provided
        if (!$date_from && !$date_to) {
            $date_from = now()->startOfWeek()->format('Y-m-d');
            $date_to = now()->endOfWeek()->format('Y-m-d');
        }

        // Get all root causes for maintenance incidents
        $root_causes = DB::table('root_causes')
            ->where('status', 'active') // Active status
            ->where('is_maintenance', 1) // Maintenance incidents only
            ->orderBy('name')
            ->get();
     
        // Build RCA matrix
        $rca_matrix = [];
        foreach ($root_causes as $root_cause) {
            $row = [
                'root_cause_id' => $root_cause->id,
                'root_cause_name' => $root_cause->name,
            ];

            // Count incidents for this root cause
            $incident_query = DB::table('incidents')
                ->where('root_cause_id', $root_cause->id)
                ->where('type', 'service_complaint'); // Assuming to count service complaints only
             

            // Apply date filters
            if ($date_from) {
                $incident_query->whereDate('incidents.date', '>=', $date_from);
            }
            if ($date_to) {
                $incident_query->whereDate('incidents.date', '<=', $date_to);
            }

            $row['total_incidents'] = $incident_query->count();

            // Get incidents by status for this root cause
            $status_counts = DB::table('incidents')
                ->where('root_cause_id', $root_cause->id)
                ->where('type', 'service_complaint')
                ->when($date_from, function ($query) use ($date_from) {
                    return $query->whereDate('incidents.date', '>=', $date_from);
                })
                ->when($date_to, function ($query) use ($date_to) {
                    return $query->whereDate('incidents.date', '<=', $date_to);
                })
                ->select(
                    DB::raw('COUNT(*) as total'),
                    DB::raw('SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) as closed_incidents'),
                    DB::raw('SUM(CASE WHEN status = 5 THEN 1 ELSE 0 END) as resolved_incidents')
                )
                ->first();

         
            $row['closed_incidents'] = $status_counts->closed_incidents ?? 0;
            $row['resolved_incidents'] = $status_counts->resolved_incidents ?? 0;

            $rca_matrix[] = $row;
        }

        // Calculate grand totals
        $grand_total = [
            'resolved_incidents' => 0,
            'closed_incidents' => 0,
        ];

        foreach ($rca_matrix as $row) {
            $grand_total['closed_incidents'] += $row['closed_incidents'];
            $grand_total['resolved_incidents'] += $row['resolved_incidents'];
        }

        // Get top 5 root causes by incident count for chart data
        $top_root_causes = collect($rca_matrix)
            ->sortByDesc('total_incidents')
            ->take(5)
            ->values()
            ->toArray();

        return Inertia::render("Dashboard/RcaDashboard", [
            'rca_matrix' => $rca_matrix,
            'grand_total' => $grand_total,
            'top_root_causes' => $top_root_causes,
            'date_from' => $date_from,
            'date_to' => $date_to,
        ]);
    }
}
