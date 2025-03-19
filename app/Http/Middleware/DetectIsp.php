<?php

namespace App\Http\Middleware;

use App\Models\Isp;
use App\Models\Partner;
use App\Models\Subcom;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class DetectIsp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $domain = $request->getHost(); // Get current domain
        $isp = Isp::findByDomain($domain);
        $partner = Partner::findByDomain($domain);
        if(auth()->id()){
            $user = User::find(auth()->id());
            $subcon = Subcom::where('id', $user?->subcom_id)->first();
            if($subcon){
                Inertia::share('login_type', 'subcon');
                return $next($request);
            }
        }
       
        if($isp){
            Inertia::share('login_type', 'isp');
            Inertia::share('isp', $isp);
            return $next($request);
        }else if($partner){
            Inertia::share('login_type', 'partner');
            Inertia::share('partner', $partner);
            return $next($request);
        }else {
            Inertia::share('login_type', 'internal');
        }
        return $next($request);
        
    }
}
