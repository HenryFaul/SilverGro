<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportFinance extends Model
{
    use HasFactory;

    use SoftDeletes;

    public $fillable = ['transport_trans_id', 'transport_load_id', 'transport_rate_basis_id', 'cost_price_per_unit', 'cost_price_per_ton', 'selling_price_per_unit', 'cost_price',
        'selling_price', 'selling_price_per_ton', 'cost_price_per_unit', 'selling_price_per_unit', 'transport_rate_per_ton', 'transport_rate', 'transport_price', 'load_insurance_per_ton',
        'comms_due_per_ton', 'weight_ton_incoming', 'weight_ton_outgoing', 'is_transport_costs_inc_price', 'transport_cost', 'total_cost_price', 'additional_cost_1', 'additional_cost_2', 'additional_cost_3',
        'additional_cost_desc_1', 'additional_cost_desc_2', 'additional_cost_desc_3', 'gross_profit', 'gross_profit_percent',
        'gross_profit_per_ton', 'total_supplier_comm', 'total_customer_comm', 'total_comm', 'adjusted_gp', 'adjusted_gp_notes'];


    public function AssignedUserComm(): HasMany
    {
        return $this->hasMany(AssignedUserComm::class, 'transport_finance_id');
    }

    public function TransportTransaction(): BelongsTo
    {
        return $this->belongsTo(TransportTransaction::class);
    }

    public function TransportLoad(): BelongsTo
    {
        return $this->belongsTo(TransportLoad::class, 'transport_load_id');
    }

    public function calculateFields()
    {

        $transport_Finance = $this;
        $transport_Load = ($transport_Finance->TransportLoad);
        $assigned_user_comm = $transport_Finance->AssignedUserComm;

        //selling_price = no_units_outgoing * selling_price_per_unit
        //dd($transport_Finance->selling_price_per_unit);
        $selling_price = $transport_Load->no_units_outgoing * $transport_Finance->selling_price_per_unit;


        //cost_price = no_units_incoming * cost_price_per_unit
        $cost_price = $transport_Load->no_units_incoming * $transport_Finance->cost_price_per_unit;

        //weight_ton_incoming = no_units_incoming * (billing_units_incoming_id -> kgs) /1000

        $billing_units_incoming_id = $transport_Load->BillingUnitsIncoming;
        $billing_units_outgoing_id = $transport_Load->BillingUnitsOutgoing;
        $weight_ton_incoming = $transport_Load->no_units_incoming * ($billing_units_incoming_id->kgs) / 1000;

        //weight_ton_outgoing = no_units_outgoing * (billing_units_outgoing_id -> kgs) /1000
        $weight_ton_outgoing = $transport_Load->no_units_outgoing * ($billing_units_outgoing_id->kgs) / 1000;

        //cost_price_per_ton = cost_price / weight_ton_outgoing
        $cost_price_per_ton = $cost_price / ($weight_ton_outgoing == 0 ? 1 : $weight_ton_outgoing);

        //selling_price_per_ton = selling_price / weight_ton_incoming

        $selling_price_per_ton = $selling_price / ($weight_ton_incoming == 0 ? 1 : $weight_ton_incoming);;

        //load_insurance_per_ton = selling_price_per_ton*1.1
        $load_insurance_per_ton = $selling_price_per_ton * 1.1;

        //If(transport_rate_basis_id = 3 ‘Per Load’)
        //
        //transport_cost = transport_rate
        //else if (weight_ton_incoming >0)
        //transport_cost = transport_rate * weight_ton_incoming
        //else
        //transport_cost = transport_rate * weight_ton_outgoing

        $transport_cost = 0;

        if ($transport_Finance->transport_rate_basis_id === 3) {
            $transport_cost = $transport_Finance->transport_rate;
        } else if ($weight_ton_incoming > 0) {
            $transport_cost = $transport_Finance->transport_rate * $weight_ton_incoming;
        } else {
            $transport_cost = $transport_Finance->transport_rate * $weight_ton_outgoing;
        }

        //If (is_transport_costs_inc_price)
        //
        //total_cost_price =
        //
        //cost_price+
        //additional_cost_1+ additional_cost_2+ additional_cost_3+
        //transport_cost
        //
        //Else
        //
        //cost_price+
        //additional_cost_1+ additional_cost_2+ additional_cost_3+

        $total_cost_price = 0;

        if ($transport_Finance->is_transport_costs_inc_price) {
            $total_cost_price = $cost_price + $transport_Finance->additional_cost_1 + $transport_Finance->additional_cost_2 + $transport_Finance->additional_cost_3 + $transport_cost;
        } else {
            $total_cost_price = $cost_price + $transport_Finance->additional_cost_1 + $transport_Finance->additional_cost_2 + $transport_Finance->additional_cost_3;
        }

        //gross_profit = selling_price – (total_cost_price + adjusted_gp)
        $gross_profit = $selling_price - ($total_cost_price + $transport_Finance->adjusted_gp);

        //gross_profit_percent = gross_profit / selling_price *100

        if ($selling_price>0){
            $gross_profit_percent = round($gross_profit / $selling_price * 100, 4);
        } else {
            $gross_profit_percent=0;
        }



        //gross_profit_per_ton = gross_profit / weight_ton_outgoing

        $gross_profit_per_ton = $gross_profit / ($weight_ton_outgoing == 0 ? 1 : $weight_ton_outgoing);

        //total_supplier_comm = gross_profit /2

        $total_supplier_comm = $gross_profit / 2;

        //total_customer_comm = gross_profit /2

        $total_customer_comm = $gross_profit / 2;


        //UPDATE FIELDS

        $transport_Finance->selling_price = $selling_price;
        $transport_Finance->cost_price = $cost_price;

        $transport_Finance->weight_ton_incoming = $weight_ton_incoming;
        $transport_Finance->weight_ton_outgoing = $weight_ton_outgoing;

        $transport_Finance->cost_price_per_ton = $cost_price_per_ton;
        $transport_Finance->selling_price_per_ton = $selling_price_per_ton;

        $transport_Finance->load_insurance_per_ton = $load_insurance_per_ton;
        $transport_Finance->transport_cost = $transport_cost;
        $transport_Finance->total_cost_price = $total_cost_price;

        $transport_Finance->gross_profit = $gross_profit;
        $transport_Finance->gross_profit_percent = $gross_profit_percent;
        $transport_Finance->gross_profit_per_ton = $gross_profit_per_ton;

        $transport_Finance->total_supplier_comm = $total_supplier_comm;
        $transport_Finance->total_customer_comm = $total_customer_comm;

        //Update comm


        if (isset($assigned_user_comm)) {
            $no_comm_users = count($assigned_user_comm);

            if ($no_comm_users > 0) {

                $per_user_supplier_comm = $total_supplier_comm / $no_comm_users;
                $per_user_customer_comm = $total_customer_comm / $no_comm_users;

                foreach ($assigned_user_comm as $comm) {
                    $comm->supplier_comm = $per_user_supplier_comm;
                    $comm->customer_comm = $per_user_customer_comm;
                    $comm->save();
                }
            }
        }


        $this->save();

    }

    public static function boot()
    {
        parent::boot();

        self::updating(function ($model) {

        });
    }
}
