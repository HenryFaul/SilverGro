<?php

namespace App\Http\Controllers;

use App\Models\DealTicket;
use App\Models\TradeRule;
use App\Models\TransportApproval;
use App\Models\TransportTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DealTicketController extends Controller
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

    }

    /**
     * Display the specified resource.
     */
    public function show(DealTicket $dealTicket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DealTicket $dealTicket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DealTicket $dealTicket)
    {

        $user = Auth::user();
        $roles = $user?->getRoleNames();
        $permissions = $user->getPermissionsViaRoles()->pluck('name');
        $dealTicket->calculateRules();

        $can_approve = $permissions->contains('approve_deal_ticket');


        $request->validate([
            'is_printed' => ['nullable', 'boolean'],
            'is_active'=>['nullable','boolean',Rule::prohibitedIf(!$can_approve)],
        ],
        ['is_active'=>'You need permissions to activate a deal ticket & must be approved!']);

        $is_updated = $dealTicket->update(
            ['is_active' =>$request->is_active]);


        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Deal Ticket updated');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DealTicket $dealTicket)
    {
        //
    }

}
