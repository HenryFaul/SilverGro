<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use JetBrains\PhpStorm\NoReturn;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class DealTicket extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;
    use SoftDeletes;

    public $fillable = ['transport_trans_id', 'old_id', 'trade_value', 'type', 'comment', 'is_active', 'is_approved', 'is_printed', 'stamp_printed','report_path','report_path_old'];

    public function TransportTransaction(): BelongsTo
    {
        return $this->belongsTo(TransportTransaction::class,'transport_trans_id');
    }


    #[NoReturn] public function calculateRules()
    {
        $deal_ticket = $this;
        $transport_trans = TransportTransaction::where('id', $deal_ticket->transport_trans_id)->first();

        if ($transport_trans != null) {

            $transport_finance = $transport_trans->TransportFinance;
            $selling_price = $transport_finance != null ? $transport_finance->selling_price : 0;

            $trade_rule = TradeRule::where('max_trade_value', '>=', $selling_price)->where('min_trade_value', '<=', $selling_price)->with('PolyRuleRoles')->first();

            if ($trade_rule == null) {
                $trade_rule = TradeRule::find(1)->with('PolyRuleRoles')->first();
            }

            $trade_rules_opp = TradeRuleOpp::with('PolyRuleRoles')->where('is_active', true)->get();

            //check if approved according to applied rule

            $trade_rule_roles = $trade_rule->PolyRuleRoles;
            $is_approved = true;

            if (count($trade_rule_roles) > 0) {
                foreach ($trade_rule_roles as $trade_rule_role) {
                    //Find approval for the role
                    $transport_approval = TransportApproval::where('transport_trans_id', $transport_trans->id)
                        ->where('transport_job_id', $transport_trans->TransportJob->id)
                        ->where('poly_approval_id', $trade_rule->id)
                        ->where('poly_approval_type', get_class($trade_rule))
                        ->where('role_name', $trade_rule_role->role)
                        ->where('deal_ticket_id', $deal_ticket->id)
                        ->first();
                    if ($transport_approval == null) {
                        $is_approved = false;
                    }
                }
            } else {
                $is_approved = false;
            }

            if (count($trade_rules_opp) > 0) {

                foreach ($trade_rules_opp as $trade_rule_opp) {

                    switch ($trade_rule_opp->id) {
                        case 1:
                            //COD
                            $supplier = $transport_trans->Supplier;
                            //$supplier_terms_of_payment = $supplier->TermsOfPayment();
                            if ($supplier->terms_of_payment_id == 1) {
                                foreach ($trade_rule_opp->PolyRuleRoles as $rule) {
                                    //dd($trade_rule_opp);
                                    $transport_approval_opp = TransportApproval::where('transport_trans_id', $transport_trans->id)
                                        ->where('transport_job_id', $transport_trans->TransportJob->id)
                                        ->where('poly_approval_id', $trade_rule_opp->id)
                                        ->where('poly_approval_type', get_class($trade_rule_opp))
                                        ->where('role_name', $rule->role)
                                        ->where('deal_ticket_id', $deal_ticket->id)
                                        ->first();
                                    if ($transport_approval_opp == null) {
                                        $is_approved = false;
                                    }
                                }
                            }
                            break;
                        case 2:

                            //Suspended
                            $Customer = $transport_trans->Customer;
                            if ($Customer->is_active != 1) {
                                foreach ($trade_rule_opp->PolyRuleRoles as $rule) {
                                    //dd($trade_rule_opp);
                                    $transport_approval_opp = TransportApproval::where('transport_trans_id', $transport_trans->id)
                                        ->where('transport_job_id', $transport_trans->TransportJob->id)
                                        ->where('poly_approval_id', $trade_rule_opp->id)
                                        ->where('poly_approval_type', get_class($trade_rule_opp))
                                        ->where('role_name', $rule->role)
                                        ->where('deal_ticket_id', $deal_ticket->id)
                                        ->first();
                                    if ($transport_approval_opp == null) {
                                        $is_approved = false;
                                    }
                                }
                            }
                            break;
                        default:
                    }
                }

            }

            if ($is_approved == false) {
                $deal_ticket->update(
                    [
                        'trade_value' => $selling_price,
                        'is_approved' => false,
                        'is_active' => false
                    ]
                );
            } else {

                $deal_ticket->update([
                        'trade_value' => $selling_price,
                        'is_approved' => true,
                    ]);
            }
        }


    }

    public function getAppliedRules(): array
    {

        $rules_with_approvals = [];
        $deal_ticket = $this;
        $transport_trans = TransportTransaction::where('id', $deal_ticket->transport_trans_id)->first();

        if ($transport_trans != null) {

            $transport_finance = $transport_trans->TransportFinance;
            $selling_price = $transport_finance != null ? $transport_finance->selling_price : 0;
            $trade_rule = TradeRule::where('max_trade_value', '>=', $selling_price)->where('min_trade_value', '<=', $selling_price)
                ->with('PolyRuleRoles')->first();

            if ($trade_rule == null) {
                $trade_rule = TradeRule::find(1)->with('PolyRuleRoles')->first();
            }

            $trade_rules_opp = TradeRuleOpp::with('PolyRuleRoles')->where('is_active', true)->get();
            //check if approved according to applied rule
            $trade_rule_roles = $trade_rule->PolyRuleRoles;
            if (count($trade_rule_roles) > 0) {

                foreach ($trade_rule_roles as $trade_rule_role) {
                    //Find approval for the role
                    $transport_approval = TransportApproval::where('transport_trans_id', $transport_trans->id)
                        ->where('transport_job_id', $transport_trans->TransportJob->id)
                        ->where('poly_approval_id', $trade_rule->id)
                        ->where('poly_approval_type', get_class($trade_rule))
                        ->where('role_name', $trade_rule_role->role)
                        ->where('deal_ticket_id', $deal_ticket->id)
                        ->with('User')
                        ->get();
                    $rules_with_approvals['TradeRule'][] = array('rule' => $trade_rule->name, 'role' => $trade_rule_role->role, 'approvals' => $transport_approval);
                }
            }

            if (count($trade_rules_opp) > 0) {
                foreach ($trade_rules_opp as $trade_rule_opp) {
                    switch ($trade_rule_opp->id) {
                        case 1:
                            //COD
                            $supplier = $transport_trans->Supplier;
                            //$supplier_terms_of_payment = $supplier->TermsOfPayment();
                            if ($supplier->terms_of_payment_id == 1) {
                                foreach ($trade_rule_opp->PolyRuleRoles as $rule) {
                                    //dd($trade_rule_opp);
                                    $transport_approval_opp = TransportApproval::where('transport_trans_id', $transport_trans->id)
                                        ->where('transport_job_id', $transport_trans->TransportJob->id)
                                        ->where('poly_approval_id', $trade_rule_opp->id)
                                        ->where('poly_approval_type', get_class($trade_rule_opp))
                                        ->where('role_name', $rule->role)
                                        ->where('deal_ticket_id', $deal_ticket->id)
                                        ->with('User')
                                        ->get();
                                    $rules_with_approvals['TradeRuleOpp'][] = array('rule' => $trade_rule_opp->name, 'role' => $rule->role, 'approvals' => $transport_approval_opp);
                                }

                            }
                            break;
                        case 2:
                            //Suspended
                            $Customer = $transport_trans->Customer;
                            if ($Customer->is_active != 1) {
                                foreach ($trade_rule_opp->PolyRuleRoles as $rule) {
                                    //dd($trade_rule_opp);
                                    $transport_approval_opp = TransportApproval::where('transport_trans_id', $transport_trans->id)
                                        ->where('transport_job_id', $transport_trans->TransportJob->id)
                                        ->where('poly_approval_id', $trade_rule_opp->id)
                                        ->where('poly_approval_type', get_class($trade_rule_opp))
                                        ->where('role_name', $rule->role)
                                        ->where('deal_ticket_id', $deal_ticket->id)
                                        ->with('User')
                                        ->get();
                                    $rules_with_approvals['TradeRuleOpp'][] = array('rule' => $trade_rule_opp->name, 'role' => $rule->role, 'approvals' => $transport_approval_opp);
                                }
                            }
                            break;
                        default:
                    }
                }
            }
        }
        return $rules_with_approvals;
    }

}
