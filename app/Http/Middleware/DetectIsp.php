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
       // $isp = Isp::findByDomain($domain);
       // $partner = Partner::findByDomain($domain);
        if(auth()->id()){
          
          switch (auth()->user()->user_type) {
            
            case 'isp':
                $isp = Isp::find(auth()->user()->isp_id);
            //    $isp = Isp::findByDomain($domain);
                Inertia::share('login_type', 'isp');
                Inertia::share('isp', $isp);
                 return $next($request);
                break;
            case 'partner':
                $partner = Partner::find(auth()->user()->partner_id);
                Inertia::share('login_type', 'partner');
                Inertia::share('isp', $partner);
                 return $next($request);
                break;
            case 'subcon':
                $subcom = Subcom::find(auth()->user()->subcom_id);
                Inertia::share('login_type', 'subcon');
                Inertia::share('subcon', $subcom);
                 return $next($request);
                break;
            default:
                Inertia::share('login_type', 'internal');
                break;
          }
        }
       
        // if($isp){
        //     Inertia::share('login_type', 'isp');
        //     Inertia::share('isp', $isp);
        //     return $next($request);
        // }else if($partner){
        //     Inertia::share('login_type', 'partner');
        //     Inertia::share('partner', $partner);
        //     return $next($request);
        // }else {
        //     Inertia::share('login_type', 'internal');
        // }
         Inertia::share('login_type', 'internal');
        return $next($request);
        
    }
}
