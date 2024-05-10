<?php

namespace App\Http\Controllers;

use App\Models\TradeRule;
use App\Models\TradeRuleOpp;
use App\Models\TransportApproval;
use App\Models\TransportTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TransportApprovalController extends Controller
{


    /**
     * Store a newly created resource in storage.
     */
    public function approve(Request $request): \Illuminate\Http\RedirectResponse
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


        //activate if all approvals are done
        if (true){
            $is_updated = false;
            if (true){
                $is_updated = $deal_ticket->update(['is_active' =>1]);

                //update tranport order & sales order

                $transport_order = $transport_trans->TransportOrder;
                $transport_order->update(['is_active' =>1]);
                $sales_order = $transport_trans->SalesOrder;
                $sales_order->update(['is_active' =>1]);

                if ($deal_ticket->is_active){
                    $transport_transaction = $deal_ticket->TransportTransaction;
                    if ($transport_transaction->a_mq == null){

                        $max_a_mq = TransportTransaction::max("a_mq");

                        if($max_a_mq == null){
                            $max_a_mq = TransportTransaction::max("id");
                        }
                        if (is_numeric($max_a_mq)){
                            $transport_transaction->a_mq=($max_a_mq+1);
                            $transport_transaction->save();
                        }


                    }
                }
            }

        }


        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Approval Updated');

        return redirect()->back();


    }

    public function activate(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        $permissions = $user->getPermissionsViaRoles()->pluck('name');
        $can_approve = $permissions->contains('approve_deal_ticket');
        $transport_trans = TransportTransaction::find($request->transport_trans_id);
        $deal_ticket = $transport_trans->DealTicket;
        $deal_ticket->calculateRules();
        $is_approved = $deal_ticket->is_approved;


   /*     $request->validate([
            'is_active'=>['nullable','boolean',Rule::prohibitedIf(!$can_approve),Rule::prohibitedIf(!$is_approved)],
        ],['is_active'=>'You need permissions to activate a deal ticket & must be approved!']);*/

        $is_updated = false;

        if ($can_approve && $is_approved){
            $is_updated = $deal_ticket->update(['is_active' =>1]);

            if ($deal_ticket->is_active){

                $transport_transaction = $deal_ticket->TransportTransaction;
                if ($transport_transaction->a_mq == null){

                    $max_a_mq = TransportTransaction::max("a_mq");

                    if($max_a_mq == null){
                        $max_a_mq = TransportTransaction::max("id");
                    }

                    if (is_numeric($max_a_mq)){
                        $transport_transaction->a_mq=($max_a_mq+1);
                        $transport_transaction->save();
                    }

                }
            }
        }

        if ($is_updated){
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Deal Ticket updated');
        }else{
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Deal Ticket NOT updated');
        }

        return redirect()->back();

    }

}
