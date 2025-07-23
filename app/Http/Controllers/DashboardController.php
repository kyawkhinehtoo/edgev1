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
}
