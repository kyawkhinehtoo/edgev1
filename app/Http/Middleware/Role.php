<?php

namespace App\Http\Middleware;

use App\Models\Isp;
use App\Models\Partner;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    /**
     * Role 
     * 1 - super admin
     * 2 - admin 
     * 
     **/
    private function checkDomainAccess($user, $request, $type)
    {
        if ($user->$type) {
            $domain = $request->getHost();
            $model = $type === 'isp' ? Isp::findByDomain($user->$type->domain) : Partner::findByDomain($user->$type->domain);
            
            if ($model->domain == $domain) {
                return true;
            }
            
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            session()->flash('error', 'You are not authorized to access this domain.');
            return false;
        }
        return null;
    }
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) { // if the current role is Administrator

            $user = User::with('role','isp','partner','subcom')->find(Auth::user()->id);
            
            // Check ISP domain access
            $ispAccess = $this->checkDomainAccess($user, $request, 'isp');
            if ($ispAccess !== null) {
                return $ispAccess ? $next($request) : redirect()->route('login');
            }

            // Check Partner domain access
            $partnerAccess = $this->checkDomainAccess($user, $request, 'partner');
            if ($partnerAccess !== null) {
                return $partnerAccess ? $next($request) : redirect()->route('login');
            }
            if ($user->incident_only == 1) {
                $route_array = array(
                    'incident',
                    'incident/{incident}',
                    'incidentOverdue',
                    'incidentRemain',
                    'getTask',
                    'getTask/{id}',
                    'getFile/{id}',
                    'getLog',
                    'getLog/{id}',
                    'getHistory',
                    'getHistory/{id}',
                    'getCustomer/{id}',
                    'addTask',
                    'editTask/{id}',
                    'uploadData',
                    'getCustomerHistory/{id}',
                    'getCustomerFile/{id}',
                    'deleteFile/{id}',
                    'logout',
                    'user/profile'
                );
                // $routeName = Route::currentRouteName();
                // dd($routeName);
                $route = Route::getCurrentRoute()->uri;
                //dd($route);
                if (in_array($route, $route_array, false)) {
                    return $next($request);
                } else {

                    return redirect('incident');
                }
            }
        }
        return $next($request);
    }
}
