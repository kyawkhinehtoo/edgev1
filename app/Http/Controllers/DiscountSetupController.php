<?php

namespace App\Http\Controllers;

use App\Models\DiscountSetup;
use App\Models\PortSharingService;
use App\Models\Isp;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DiscountSetupController extends Controller
{
    public function index(Request $request)
    {
        $port_sharing_services = PortSharingService::where('status',1)->get();
        $isps = Isp::where('is_active',1)->get();
        $discounts = DiscountSetup::with('portSharingService', 'isp', 'creator')
            ->when($request->input('general'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->input('general') . '%')
                        ->orWhereHas('portSharingService', function ($q) use ($request) {
                            $q->where('name', 'like', '%' . $request->input('general') . '%');
                        })
                        ->orWhereHas('isp', function ($q) use ($request) {
                            $q->where('name', 'like', '%' . $request->input('general') . '%');
                        });
                });
            })
            ->when($request->input('port_sharing_service_id'), function ($query) use ($request) {
                return $query->where('port_sharing_service_id', $request->input('port_sharing_service_id'));
            })
            ->when($request->input('isp_id'), function ($query) use ($request) {
                return $query->where('isp_id', $request->input('isp_id'));
            })
            ->when($request->input('name'), function ($query) use ($request) {
                return $query->where('name', 'like', '%' . $request->input('name') . '%');
            })
            ->when($request->input('is_active'), function ($query) use ($request) {
                return $query->where('is_active', $request->input('is_active'));
            })
            ->when($request->input('start_date'), function ($query) use ($request) {
                return $query->whereDate('start_date', '>=', $request->input('start_date'));
            })
            ->when($request->input('end_date'), function ($query) use ($request) {
                return $query->whereDate('end_date', '<=', $request->input('end_date'));
            })
            ->latest()
            ->paginate(10);
        return Inertia::render('DiscountSetup/Index', [
            'discounts' => $discounts,
            'port_sharing_services' => $port_sharing_services,
            'isps' => $isps,
        ]);
    }

    public function create()
    {
        return Inertia::render('DiscountSetup/Create', [
            'services' => PortSharingService::all(['id', 'name']),
            'isps' => Isp::all(['id', 'name'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|unique:discount_setup,name',
            'port_sharing_service_id' => 'required|exists:port_sharing_services,id',
            'isp_id' => 'required|exists:isps,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'fix_rate' => 'nullable',
            'rate_type' => 'required|in:fixed,percentage',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'description' => 'nullable',
            'is_active' => 'boolean',
        ]);

        // Check for conflict
       if ($validated['is_active'] ?? false) {
            $conflict = DiscountSetup::where('isp_id', $validated['isp_id'])
                ->where('port_sharing_service_id', $validated['port_sharing_service_id'])
                ->where('is_active', 1)
                ->where(function($query) use ($validated) {
                    $query->where(function($q) use ($validated) {
                        $q->where('start_date', '<=', $validated['end_date'])
                        ->where('end_date', '>=', $validated['start_date']);
                    });
                })
                ->exists();

            if ($conflict) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['conflict' => 'A discount setup with the same ISP, Port Sharing Service, and overlapping Date Range already exists and is Active.']);
            }
        }
        $validated['fix_rate'] = is_array($validated['fix_rate']) ? json_encode($validated['fix_rate']) : $validated['fix_rate'];
        $validated['created_by'] = auth()->id();
        DiscountSetup::create($validated);
        return redirect()->back()->with('success', 'Discount setup created successfully.');
    }

    public function show(DiscountSetup $discount_setup)
    {
        $discount_setup->load(['portSharingService', 'isp', 'creator']);
        return Inertia::render('Setup/DiscountSetupShow', [
            'discount_setup' => $discount_setup
        ]);
    }

    public function edit(DiscountSetup $discount_setup)
    {
        $discount_setup->load(['portSharingService', 'isp', 'creator']);
        return Inertia::render('Setup/DiscountSetupEdit', [
            'discount_setup' => $discount_setup,
            'services' => PortSharingService::all(['id', 'name']),
            'isps' => Isp::all(['id', 'name'])
        ]);
    }

    public function update(Request $request, DiscountSetup $discount_setup)
    {
        $validated = $request->validate([
            'name' => 'required|unique:discount_setup,name,' . $discount_setup->id,
            'port_sharing_service_id' => 'required|exists:port_sharing_services,id',
            'isp_id' => 'required|exists:isps,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'fix_rate' => 'nullable',
            'rate_type' => 'required|in:fixed,percentage',
            'discount_percentage' => 'required|numeric|min:0|max:100',
            'description' => 'nullable',
            'is_active' => 'boolean',
        ]);

        // Check for conflict (exclude current record)
        if ($validated['is_active'] ?? false) {
            $conflict = DiscountSetup::where('isp_id', $validated['isp_id'])
            ->where('port_sharing_service_id', $validated['port_sharing_service_id'])
            ->where('is_active', 1)
            ->where('id', '!=', $discount_setup->id)
            ->where(function($query) use ($validated) {
                $query->where(function($q) use ($validated) {
                $q->where('start_date', '<=', $validated['start_date'])
                  ->where('end_date', '>=', $validated['end_date']);
                });
            })
            ->exists();
          //  dd($conflict);
            if ($conflict) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['conflict' => 'A discount setup with the same ISP, Port Sharing Service, and date range already exists and is active.']);
            }
        }
        $validated['fix_rate'] = is_array($validated['fix_rate']) ? json_encode($validated['fix_rate']) : $validated['fix_rate'];
       
        $discount_setup->update($validated);
        return redirect()->back()->with('success', 'Discount setup updated successfully.');
    }

    public function destroy(DiscountSetup $discount_setup)
    {
        $discount_setup->delete();
        return redirect()->route('discount-setup.index')->with('success', 'Discount setup deleted successfully.');
    }
}