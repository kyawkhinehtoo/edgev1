<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\SystemSetting;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = Auth::user();
        $role = null;
        if ($user != null) {

            $role = Role::join('users', 'users.role_id', '=', 'roles.id')
                ->where('users.id', '=', $user->id)
                ->first();
        }
        return array_merge(parent::share($request), [
            'role' => $role,
            'logo_large' => SystemSetting::first()?->logo_large,
            'logo_small' => SystemSetting::first()?->logo_small,
            'application_name' => SystemSetting::first()?->application_name,
            'theme_color' => SystemSetting::first()?->theme_color,
            'accent_color' => SystemSetting::first()?->accent_color,

        ]);
        
    }
}
