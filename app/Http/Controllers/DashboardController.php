<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Package;
use App\Models\Project;
use App\Models\Status;
use App\Models\Township;
use App\Models\User;
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
}
