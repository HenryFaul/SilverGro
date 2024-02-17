<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Staff;
use App\Models\Transporter;
use App\Models\User;
use App\Rules\StaffAssignRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Rules\Password;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'searchName',
            'isActive',
            'field',
            'direction',
            'show'
        ]);

        $paginate = $request['show'] ?? 10;
        $staff = User::with('roles')->with('Staff')->paginate($paginate)->withQueryString();

        return inertia(
            'Staff/Index',
            [
                'filters' => $filters,
                'staff' => $staff,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required','string'],
            'last_legal_name' => ['required','string','string'],
            'nickname' => ['nullable','string'],
            'title' => ['nullable','string'],
            'id_reg_no' => ['nullable','string','unique:staff,id_reg_no'],
            'job_description' => ['nullable','string'],
            'password_confirmation' => ['required'],
            'password'=> ['required', 'string', new Password,'required_with:password_confirmation','same:password_confirmation'],
            'email'=> ['required', 'string', 'email', 'max:255', 'unique:users'],
        ]);

        $user =  User::create([
            'name' => $request->first_name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        if($user->exists()){
            Staff::create([
                'first_name' => $request->first_name,
                'last_legal_name' => $request->last_legal_name,
                'nickname' => $request->nickname,
                'title' => $request->title,
                'id_reg_no' => $request->id_reg_no,
                'job_description' => $request->job_description,
                'user_id'=> $user->id
            ]);

            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Staff created');
        }


        return redirect()->back();

    }

    /**
     * Display the specified resource.
     */
    public function show(Staff $staff)
    {
        $staff = User::where('id','=',$staff->user_id)->with('roles')->with('Staff')->first();

        $all_roles_in_database = Role::all();
        $permissions = $staff?->getPermissionsViaRoles()->unique('name')->pluck('name');

        return inertia(
            'Staff/Show',
            [
                'staff' => $staff,
                'all_roles'=>$all_roles_in_database,
                'permissions'=>$permissions
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Staff $staff)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Staff $staff)
    {

        //Update the system user first name field to align with staff
        $user = $staff->User()->first();

        if ($request->email != $user->email){
            $request->validate([
                'email'=> ['required', 'string', 'email', 'max:255', 'unique:users'],
            ]);
        }

        if ($request->password != null || $request->password !=''){
            $request->validate([
                'password'=> ['required', 'string', new Password],
            ]);
        }

        $staff->update(
            $request->validate([
                'first_name' => ['nullable','string'],
                'last_legal_name' => ['nullable','string'],
                'nickname' => ['nullable','string'],
                'title' => ['nullable','string'],
                'id_reg_no' => ['nullable','string'],
                'is_active' => ['nullable','boolean'],
                'job_description' => ['nullable','string'],
            ])
        );



        if($user->exists() ){
            if($request->password != null && $request->password != ''){
                $user->update([
                    'name' => $request->first_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password)
                ]);
            }else{
                $user->update([
                    'name' => $request->first_name,
                    'email' => $request->email
                ]);
            }


        }

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Staff updated');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Staff $staff)
    {
        //

    }
}
