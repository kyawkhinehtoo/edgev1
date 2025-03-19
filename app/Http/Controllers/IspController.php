<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Isp;
use App\Models\Status;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class IspController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $isps = Isp::query()
            ->when($search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('contact_person', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('service_type', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
    
        return Inertia::render('ISPS/Index', [
            'isps' => $isps,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        $customer = new Customer();
        $customerStatus = Status::all();
        $customerColumns = $customer->getTableColumnForOther();
        
        return Inertia::render('ISPS/Create', [
            'customerColumns' => $customerColumns,
            'customerStatus' => $customerStatus,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_code' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'domain' => [
                'required',
                'string',
                'max:255',
                'unique:isps,domain',
                'unique:partners,domain',
            ],
            'description' => 'nullable|string',
            'service_type' => 'nullable|string|max:255',
            'bandwidth_capacity' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'permissions' => 'nullable|array',
            'customer_status' => 'nullable|array',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('isp-logos', 'public');
        }

        Isp::create($validated);

        return redirect()->route('isps.index')
            ->with('message', 'ISP created successfully');
    }

    public function show(Isp $isp)
    {
        
        $isp->load(['users' => function ($query) {
            $query->select('id', 'name', 'email', 'phone', 'disabled', 'isp_id', 'last_login_ip', 'last_login_time');
        }]);
  
        return Inertia::render('ISPS/Show', [
            'isp' => $isp
        ]);
    }

    public function edit(Isp $isp)
    {
        $customer = new Customer();
        $customerStatus = Status::all();
        $customerColumns = $customer->getTableColumnForOther();
  
        return Inertia::render('ISPS/Edit', [
            'isp' => $isp,
            'customerColumns' => $customerColumns,
            'customerStatus' => $customerStatus,
        ]);
    }
    public function update(Request $request, Isp $isp)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'short_code' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            
            'description' => 'nullable|string',
            'service_type' => 'nullable|string|max:255',
            'bandwidth_capacity' => 'nullable|string|max:255',
            'domain' => [
                'required',
                'string',
                'max:255',
                'unique:isps,domain,'.$isp->id,
                'unique:partners,domain',
            ],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'permissions' => 'nullable|array',
            'customer_status' => 'nullable|array',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($isp->logo && Storage::disk('public')->exists($isp->logo)) {
                Storage::disk('public')->delete($isp->logo);
            }
            
            $validated['logo'] = $request->file('logo')->store('isp-logos', 'public');
        }else {
            // Remove logo from validated data to prevent overwriting with null
            unset($validated['logo']);
        }

        $isp->update($validated);

        return redirect()->route('isps.index')
            ->with('message', 'ISP updated successfully');
    }

    public function destroy(Isp $isp)
    {
        $isp->delete();

        return redirect()->route('isps.index')
            ->with('message', 'ISP deleted successfully');
    }
}
