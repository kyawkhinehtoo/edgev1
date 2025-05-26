<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Customer;
use App\Models\Menu;
use App\Models\Status;
use App\Models\Township;
use App\Models\User;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use DB;

class RoleController extends Controller
{
    public function __construct(){
        $user = User::with('role')->find(auth()->id());
        if(!$user->role->system_setting){
             abort(403); // Unauthorized access for non-dn_panel users
        }
    }
    public function index(Request $request)
    {
        $roles = Role::with('townships')->when($request->role, function ($query, $pkg) {
            $query->where('name', 'LIKE', '%' . $pkg . '%');
        })
            ->paginate(10);
        $menus = Menu::all();
        $customerStatus = Status::all();
        $customer = new Customer();
        $townships = Township::all();
        $col = $customer->getTableColumns();
        return Inertia::render('Setup/Role', [
            'roles' => $roles,
            'col' => $col,
            'menus' => $menus,
            'customerStatus' => $customerStatus,
            'townships' => $townships
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'read_customer' => 'nullable|boolean',
            'read_incident' => 'nullable|boolean',
            'delete_customer' => 'nullable|boolean',
            'write_incident' => 'nullable|boolean',
            'bill_generation' => 'nullable|boolean',
            'edit_invoice' => 'nullable|boolean',
            'delete_invoice' => 'nullable|boolean',
            'bill_receipt' => 'nullable|boolean',
            'radius_read' => 'nullable|boolean',
            'radius_write' => 'nullable|boolean',
            'incident_report' => 'nullable|boolean',
            'bill_report' => 'nullable|boolean',
            'radius_report' => 'nullable|boolean',
            'incident_only' => 'nullable|boolean',
            'read_only_ip' => 'nullable|boolean',
            'add_ip' => 'nullable|boolean',
            'edit_ip' => 'nullable|boolean',
            'delete_ip' => 'nullable|boolean',
            'ip_report' => 'nullable|boolean',
            'activity_log' => 'nullable|boolean',
            'system_setting' => 'nullable|boolean',
            'enable_customer_export' => 'nullable|boolean',
            'admin_panel' => 'nullable|boolean',
            'customer_panel' => 'nullable|boolean',
            'incident_panel' => 'nullable|boolean',
            'billing_panel' => 'nullable|boolean',
            'report_panel' => 'nullable|boolean',
            'limit_region' => 'nullable|boolean',
            'dn_panel' => 'nullable|boolean',
            'installation_supervisor' => 'nullable|boolean',
            'townships' => 'nullable|array',
        ]);

       $role = Role::create($validated);
        if ($request->townships) {
            $role->townships()->attach(collect($request->townships)->pluck('id'));
        }
        return redirect()->route('role.index')->with('message', 'Role Created Successfully.');
    }
    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'permissions' => 'nullable|array',
            'read_customer' => 'nullable|boolean',
            'read_incident' => 'nullable|boolean',
            'delete_customer' => 'nullable|boolean',
            'write_incident' => 'nullable|boolean',
            'bill_generation' => 'nullable|boolean',
            'edit_invoice' => 'nullable|boolean',
            'delete_invoice' => 'nullable|boolean',
            'bill_receipt' => 'nullable|boolean',
            'radius_read' => 'nullable|boolean',
            'radius_write' => 'nullable|boolean',
            'incident_report' => 'nullable|boolean',
            'bill_report' => 'nullable|boolean',
            'radius_report' => 'nullable|boolean',
            'incident_only' => 'nullable|boolean',
            'read_only_ip' => 'nullable|boolean',
            'add_ip' => 'nullable|boolean',
            'edit_ip' => 'nullable|boolean',
            'delete_ip' => 'nullable|boolean',
            'ip_report' => 'nullable|boolean',
            'activity_log' => 'nullable|boolean',
            'system_setting' => 'nullable|boolean',
            'enable_customer_export' => 'nullable|boolean',
            'admin_panel' => 'nullable|boolean',
            'customer_panel' => 'nullable|boolean',
            'incident_panel' => 'nullable|boolean',
            'billing_panel' => 'nullable|boolean',
            'report_panel' => 'nullable|boolean',
            'limit_region' => 'nullable|boolean',
            'customer_status' => 'nullable|array',
            'dn_panel' => 'nullable|boolean',
            'installation_supervisor' => 'nullable|boolean',
            'townships' => 'nullable|array',
        ]);

        $role->update($validated);
       if ($request->townships) {
        $role->townships()->sync(collect($request->townships)->pluck('id'));
        }
        return redirect()->back()
            ->with('message', 'Data updated successfully');
    }
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            $user = User::where('role', $request->id)->first();
            if ($user)
                return redirect()->back()->with('message', 'Role cannot delete due to foreign key constraint in User Database.');
            Role::find($request->input('id'))->delete();
            return redirect()->back()->with('message', 'Role deleted successfully.');
        }
    }
}
