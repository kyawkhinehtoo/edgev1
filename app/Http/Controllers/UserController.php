<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Isp;
use App\Models\Partner;
use App\Models\ReceiptRecord;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Subcom;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $users = User::leftjoin('roles', 'users.role', 'roles.id')->when($request->user, function ($query, $tsp) {
            $query->where('users.name', 'LIKE', '%' . $tsp . '%')
                ->orWhere('roles.name', 'LIKE', '%' . $tsp . '%')
                ->orWhere('users.phone', 'LIKE', '%' . $tsp . '%');
        })
            ->select('users.*')
            ->orderby('users.id', 'asc')
            ->paginate(10);
        $roles = Role::get();
        $isps = Isp::get();
        $partners = Partner::get();
        $subcoms = Subcom::get();
        return Inertia::render('Setup/User', ['users' => $users, 'roles' => $roles, 'isps' => $isps, 'partners' => $partners, 'subcoms' => $subcoms]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'user_type' => ['required', 'string', 'in:internal,partner,isp,subcon'],
            'phone' => ['required'],
            'role_id' => ['nullable', 'exists:roles,id'],
            'isp_id' => ['nullable', 'exists:isps,id'],
            'partner_id' => ['nullable', 'exists:partners,id'],
            'password' => ['required', 'string'],
        ])->validate();
           
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'user_type' => $request->user_type,
            'role' => $request->user_type=='internal'?$request->role_id:null,
            'isp_id' => $request->user_type=='isp'?$request->isp_id:null,
            'partner_id' => $request->user_type=='partner'?$request->partner_id:null,
            'subcom_id' => $request->user_type=='subcon'?$request->subcom_id:null,
            'disabled' => $request->disabled,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('user.index')->with('message', 'User Created Successfully.');
        // return redirect()->route('posts.index') 
        // ->with('message', 'Post Created Successfully.');
    }



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
    public function update(Request $request, $id)
    {
        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($request->id)],
            'phone' => ['required'],
            'user_type' => ['required', 'string', 'in:internal,partner,isp,subcon'],
            'role_id' => ['nullable', 'exists:roles,id'],
            'isp_id' => ['nullable', 'exists:isps,id'],
            'partner_id' => ['nullable', 'exists:partners,id'],
        ])->validate();

        if ($request->has('id')) {
            $user = User::find($request->input('id'));
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->user_type = $request->user_type;
            $user->role = $request->user_type=='internal'?$request->role_id:null;
            $user->isp_id = $request->user_type=='isp'?$request->isp_id:null;
            $user->partner_id = $request->user_type=='partner'?$request->partner_id:null;
            $user->subcom_id = $request->user_type=='subcon'?$request->subcom_id:null;
            $user->disabled = $request->disabled;
            if (!empty($request['password'])) {
                $user->password = Hash::make($request->password);
            }
            
            $user->update();

            return redirect()->back()->with('message', 'User Updated Successfully.');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if ($request->has('id')) {
            $bill = ReceiptRecord::where('collected_person', $request->id)->first();
            if ($bill)
                return redirect()->back()->with('message', 'User cannot delete due to foreign key constraint in Receipt Database.');
           
            User::find($request->input('id'))->delete(); // This will now soft delete
            return redirect()->back()->with('message', 'User deleted successfully.');
        }
    }
}
