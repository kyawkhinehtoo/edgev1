<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ActivityLogController extends Controller
{
    //
    public function __construct(){
        $user = User::with('role')->find(auth()->id());
        if(!$user->role->activity_log){
             abort(403); // Unauthorized access for non-dn_panel users
        }
    }
    
    public function index(Request $request)
    {


        $activities = Activity::when($request->option, function ($query, $option) {
            $query->where('activity_log.description', 'LIKE',  '%' . $option . '%');
        })
            ->when($request->general, function ($query, $general) {
                $query->where('activity_log.description', 'LIKE',  '%' . $general . '%');
            })
            ->when($request->dateRange, function ($query, $dateRange) {
         
                    $startDate = Carbon::parse($dateRange[0])->format('Y-m-d');
                    $endDate = Carbon::parse($dateRange[1])->format('Y-m-d');
              
                    $query->whereBetween('activity_log.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
                
            })
            ->orderBy('id', 'desc')->paginate(15);
        $activities->appends($request->all())->links();
        return Inertia::render('Setup/ActivityLog', [
            'activities' => $activities->through(function ($activity) {
                return [
                    'description' => $activity->description,
                    'causer' => optional($activity->causer)->name ?? 'System',
                    'date' => $activity->created_at->format('d-m-Y H:i:s'),
                    'changes' => $activity->properties['changes'] ?? [],
                ];
            }),
        ]);
    }
}
