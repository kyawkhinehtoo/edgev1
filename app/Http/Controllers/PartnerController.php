<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Partner;
use App\Models\Status;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $partners = Partner::query()
            ->when($search, function ($query, $search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('contact_person', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();
    
        return Inertia::render('Partner/Index', [
            'partners' => $partners,
            'filters' => $request->only(['search'])
        ]);
    }

    public function create()
    {
        $customerStatus = Status::all();
        $customer = new Customer();
        $customerColumns = $customer->getTableColumnForOther();
        return Inertia::render('Partner/Create', [
            'customerColumns'=> $customerColumns,
            'customerStatus'=> $customerStatus,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'domain' => [
                'required',
                'string',
                'max:255',
                'unique:isps,domain',
                'unique:partners,domain',
            ],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'permissions' => 'nullable|array',
            'customer_status' => 'nullable|array',
        ]);
         // Handle logo upload
         if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('partner-logos', 'public');
        }

        Partner::create($validated);

        return redirect()->route('partner.index')
            ->with('message', 'Partner created successfully');
    }

    public function show(Partner $partner)
    {
        $partner->load(['users' => function ($query) {
            $query->select('id', 'name', 'email', 'phone', 'disabled', 'partner_id', 'last_login_ip', 'last_login_time');
        }]);

        return Inertia::render('Partner/Show', [
            'partner' => $partner
        ]);
    }

    public function edit(Partner $partner)
    {   
        $customerStatus = Status::all();
        $customer = new Customer();
        $customerColumns = $customer->getTableColumnForOther();
        return Inertia::render('Partner/Edit', [
            'partner' => $partner,
            'customerColumns'=> $customerColumns,
            'customerStatus'=> $customerStatus,
        ]);
    }

    public function update(Request $request, Partner $partner)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'website' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'domain' => [
                'required',
                'string',
                'max:255',
                'unique:isps,domain',
                'unique:partners,domain,'. $partner->id,
            ],
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'permissions' => 'nullable|array',
            'customer_status' => 'nullable|array',
        ]);

        // if (isset($validated['customer_status'])) {
        //     $validated['customer_status'] = json_encode($validated['customer_status']);
        // }
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($partner->logo && Storage::disk('public')->exists($partner->logo)) {
                Storage::disk('public')->delete($partner->logo);
            }
            
            $validated['logo'] = $request->file('logo')->store('partner-logos', 'public');
        }else {
            // Remove logo from validated data to prevent overwriting with null
            unset($validated['logo']);
        }
        $partner->update($validated);
        return redirect()->route('partner.index')
            ->with('message', 'Partner updated successfully');
    }

    public function destroy(Partner $partner)
    {
        $partner->delete();

        return redirect()->route('partner.index')
            ->with('message', 'Partner deleted successfully');
    }
}
