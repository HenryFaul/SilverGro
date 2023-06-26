<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Staff;
use App\Rules\StaffAssignRule;
use Illuminate\Http\Request;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $customer_id = $request->customer_id;
        $staff_id = $request->staff_id;

        $class = "App\Models\Customer";

        $request->validate([
            'staff_id' => ['required','integer','exists:staff,id',new StaffAssignRule($customer_id,$class)],
            'customer_id' => ['required','integer','exists:customers,id'],
        ]);

        $staff = Staff::find($staff_id);
        $customer = Customer::find($customer_id);

        if ($staff != null && $customer != null){

            $customer->staff()->attach($staff);
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Staff added');
            return redirect()->back();
        }


        $request->session()->flash('flash.bannerStyle', 'danger');
        $request->session()->flash('flash.banner', 'Staff not added');

        return redirect()->back();

    }

    /**
     * Display the specified resource.
     */
    public function show(Staff $staff)
    {
        //
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
        $customer = Customer::find($request->customer_id);
        $res = $customer->staff()->detach($staff);

        if ($res){

            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Staff unlinked');
            return redirect()->back();
        }

        $request->session()->flash('flash.bannerStyle', 'danger');
        $request->session()->flash('flash.banner', 'Staff not unlinked');

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
