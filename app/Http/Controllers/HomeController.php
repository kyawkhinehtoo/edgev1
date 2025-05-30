<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Display the home dashboard.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
    $user = User::with('role')->find(Auth::id());

    if($user->user_type == 'internal'){
        return redirect(route('dashboard'));
    }
    $customers = '';
    if($user->user_type == 'isp'){
        $customers = Customer::where('isp_id', $user->isp_id)->get();
    }
    if($user->user_type == 'partner'){
        $customers = Customer::where('partner_id', $user->partner_id)->get();
    }
    
    if($user->user_type == 'subcon'){

        $customers = Customer::where('subcom_id', $user->subcom_id)->get();
    }
        return Inertia::render('Dashboard/Home', [
            'customers' => $customers,
            'userData' => $user
        ]);
    }
}
