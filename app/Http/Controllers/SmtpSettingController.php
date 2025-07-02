<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\SmtpSetting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
class SmtpSettingController extends Controller
{
    function __construct()
    {
       $data = Role::join('users', 'users.role_id', 'roles.id')
            ->where('roles.smtp_setting', 1)
            ->where('users.id', Auth::id())
            ->first();
        if (!$data) {
            abort(403);
        }
    }
    public function index()
    {
        $smtpSettings = SmtpSetting::all();
        return Inertia::render('SmtpSettings/Index', [
            'smtpSettings' => $smtpSettings
        ]);
    }

    public function create()
    {
         $smtpSettings = SmtpSetting::all();
         if(!$smtpSettings->isEmpty()){
            return redirect()->route('smtp-settings.index')->with('message', 'SMTP Setting already exists.');
         }
        return Inertia::render('SmtpSettings/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'host' => 'required|string',
            'port' => 'required|integer',
            'encryption' => 'nullable|string',
            'username' => 'required|string',
            'password' => 'required|string',
            'from_address' => 'required|email',
            'from_name' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        SmtpSetting::create($validated);
        return redirect()->route('smtp-settings.index')->with('success', 'SMTP Setting created successfully.');
    }

    public function edit(SmtpSetting $smtpSetting)
    {
        return Inertia::render('SmtpSettings/Edit', [
            'smtpSetting' => $smtpSetting
        ]);
    }

    public function update(Request $request, SmtpSetting $smtpSetting)
    {
        $validated = $request->validate([
            'host' => 'required|string',
            'port' => 'required|integer',
            'encryption' => 'nullable|string',
            'username' => 'required|string',
            'password' => 'required|string',
            'from_address' => 'required|email',
            'from_name' => 'nullable|string',
            'is_active' => 'boolean',
        ]);
        $smtpSetting->update($validated);
        return redirect()->route('smtp-settings.index')->with('success', 'SMTP Setting updated successfully.');
    }

    public function destroy(SmtpSetting $smtpSetting)
    {
        $smtpSetting->delete();
        return redirect()->route('smtp-settings.index')->with('success', 'SMTP Setting deleted successfully.');
    }
}
