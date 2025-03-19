<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Customer;
use App\Models\Partner;
use Illuminate\Http\Request;
use App\Models\Township;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class TownshipController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $townships = Township::with(['city', 'partner'])
            ->when($request->township, function ($query, $tsp) {
                $query->where('townships.name', 'LIKE', '%' . $tsp . '%')
                    ->orWhere('townships.short_code', 'LIKE', '%' . $tsp . '%');
            })
              ->paginate(10);
     
        return Inertia::render('Setup/Township', [
            'townships' => $townships,
            'cities' => City::all(),
            'partners' => Partner::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'partner_id' => 'required|exists:partners,id',
            'short_code' => 'required|string|max:10',
        ]);

        Township::create($validated);

        return redirect()->back()->with('message', 'Township created successfully');
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
  



    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Township $township)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'partner_id' => 'required|exists:partners,id',
            'short_code' => 'required|string|max:10',
        ]);

        $township->update($validated);
        return redirect()->back()->with('message', 'Township updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->has('id')) {
            $customer = Customer::where('township_id', $request->id)->first();
            if ($customer)
                return redirect()->back()->with('message', 'Township cannot delete due to foreign key constraint in Customer Database.');
            Township::find($request->input('id'))->delete();
            return redirect()->back()->with('message', 'Township deleted successfully.');
        }
    }
}
