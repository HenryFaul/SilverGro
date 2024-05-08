<?php

namespace App\Http\Controllers;

use App\Models\AssignedUserComm;
use App\Models\TransportFinance;
use App\Models\TransportStatus;
use Illuminate\Http\Request;

class TransportStatusController extends Controller
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

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $is_created = TransportStatus::create(
            $request->validate([
                'transport_trans_id'=> ['required', 'integer','exists:transport_transactions,id'],
                'status_entity_id'=> ['required', 'integer','exists:status_entities,id'],
                'status_type_id' => ['required', 'integer','exists:status_types,id'],

            ])
        );

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Status created');

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(TransportStatus $transportStatus)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransportStatus $transportStatus)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransportStatus $transportStatus)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, TransportStatus $transportStatus): \Illuminate\Http\RedirectResponse
    {
        $transportStatus->delete();
        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Status deleted');

        return redirect()->back();
    }
}
