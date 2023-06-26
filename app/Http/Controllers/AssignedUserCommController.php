<?php

namespace App\Http\Controllers;

use App\Models\AssignedUserComm;
use App\Models\TransportDriverVehicle;
use App\Models\TransportFinance;
use Illuminate\Http\Request;

class AssignedUserCommController extends Controller
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
        //

       /* 'transport_trans_id','transport_finance_id','assigned_user_supplier_id','assigned_user_customer_id',
        'supplier_comm','customer_comm','notes'*/

        $is_created = AssignedUserComm::create(
            $request->validate([
                'transport_trans_id'=> ['required', 'integer','exists:transport_transactions,id'],
                'transport_finance_id'=> ['required', 'integer','exists:transport_finances,id'],
                'assigned_user_supplier_id' => ['required', 'integer','exists:staff,id'],
                'assigned_user_customer_id' => ['required', 'integer','exists:staff,id'],
                'notes' => ['nullable', 'string'],
            ])
        );

        $transport_finance = TransportFinance::where('id',$request->transport_finance_id)->first();
        $transport_finance->calculateFields();

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Comm entry added & split updated.');

        return redirect()->back();


    }

    /**
     * Display the specified resource.
     */
    public function show(AssignedUserComm $assignedUserComm)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssignedUserComm $assignedUserComm)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssignedUserComm $assignedUserComm)
    {
        $is_created = $assignedUserComm->update(
            $request->validate([
                'transport_trans_id'=> ['required', 'integer','exists:transport_transactions,id'],
                'transport_finance_id'=> ['required', 'integer','exists:transport_finances,id'],
                'assigned_user_supplier_id' => ['required', 'integer','exists:staff,id'],
                'assigned_user_customer_id' => ['required', 'integer','exists:staff,id'],
                'notes' => ['nullable', 'string'],
            ])
        );

        $transport_finance = TransportFinance::where('id',$assignedUserComm->transport_finance_id)->first();
        $transport_finance->calculateFields();

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Comm entry Updated & split updated.');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,AssignedUserComm $assignedUserComm)
    {
        $transport_finance = TransportFinance::where('id',$assignedUserComm->transport_finance_id)->first();
        $assignedUserComm->delete();
        $transport_finance->calculateFields();

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Comm entry deleted & split updated.');

        return redirect()->back();

    }
}
