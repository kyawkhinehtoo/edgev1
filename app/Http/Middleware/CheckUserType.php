<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserType
{
    public function handle(Request $request, Closure $next, ...$userTypes)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        if (in_array($user->user_type, $userTypes)) {
            return $next($request);
        }

        return abort(403, 'Unauthorized action.');
    }
}