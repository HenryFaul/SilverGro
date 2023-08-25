<?php

namespace App\Http\Controllers;

use App\Models\TradeRule;
use App\Models\TradeRuleOpp;
use App\Models\TransportApproval;
use App\Models\TransportTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransportApprovalController extends Controller
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

        $request->validate([
            'is_active' => ['nullable', 'boolean'],
            'is_printed' => ['nullable', 'boolean'],
        ]);

        $user = Auth::user();
        $roles = $user?->getRoleNames();

        $transport_trans = TransportTransaction::find($request->transport_trans_id);
        $transport_finance = $transport_trans->TransportFinance;
        $deal_ticket = $transport_trans->DealTicket;

        $selling_price = $transport_finance != null ? $transport_finance->selling_price : 0;
        $trade_rule = TradeRule::where('max_trade_value', '>=', $selling_price)->where('min_trade_value', '<=', $selling_price)->with('PolyRuleRoles')->first();

        if ($trade_rule == null) {
            $trade_rule = TradeRule::find(1)->first();
        }

        $trade_rules_opp = TradeRuleOpp::with('PolyRuleRoles')->get();

        //request->contract_type_id

        foreach ($roles as $role) {

            $transport_approval = TransportApproval::where('transport_trans_id', $request->transport_trans_id)
                ->where('transport_job_id', $request->transport_job_id)
                ->where('poly_approval_id', $trade_rule->id)
                ->where('poly_approval_type', get_class($trade_rule))
                ->where('role_name', $role)
                ->where('deal_ticket_id', $deal_ticket->id)
                ->where('approved_by_id', $user->id)->first();

            if ($transport_approval == null) {
                $transport_approval = TransportApproval::create([
                    'transport_trans_id' => $request->transport_trans_id,
                    'deal_ticket_id' => $deal_ticket->id,
                    'poly_approval_id' => $trade_rule->id,
                    'poly_approval_type' => get_class($trade_rule),
                    'transport_job_id' => $request->transport_job_id,
                    'approved_by_id' => $user->id,
                    'is_approved' => true,
                    'role_name' => $role
                ]);

            }

            foreach ($trade_rules_opp as $trade_rule_opp) {

                $transport_approval_opp = TransportApproval::where('transport_trans_id', $request->transport_trans_id)
                    ->where('transport_job_id', $request->transport_job_id)
                    ->where('poly_approval_id', $trade_rule_opp->id)
                    ->where('poly_approval_type', get_class($trade_rule_opp))
                    ->where('role_name', $role)
                    ->where('deal_ticket_id', $deal_ticket->id)
                    ->where('approved_by_id', $user->id)->first();

                if ($transport_approval_opp == null) {

                    $transport_approval = TransportApproval::create([
                        'transport_trans_id' => $request->transport_trans_id,
                        'deal_ticket_id' => $deal_ticket->id,
                        'poly_approval_id' => $trade_rule_opp->id,
                        'poly_approval_type' => get_class($trade_rule_opp),
                        'transport_job_id' => $request->transport_job_id,
                        'approved_by_id' => $user->id,
                        'is_approved' => true,
                        'role_name' => $role
                    ]);

                }

            }

        }



        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Approval Updated');

        return redirect()->back();


    }

    /**
     * Display the specified resource.
     */
    public function show(TransportApproval $transportApproval)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransportApproval $transportApproval)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransportApproval $transportApproval)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransportApproval $transportApproval)
    {
        //
    }
}
