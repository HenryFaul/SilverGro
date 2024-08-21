<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TransportFinance extends Model
{
    use HasFactory;

    use SoftDeletes;

    use LogsActivity;

    //cost_price_actual,cost_price_per_ton_actual,selling_price_per_ton_actual,transport_cost_actual, weight_ton_incoming_actual,weight_ton_outgoing_actual,total_cost_price_actual,gross_profit_actual
    //gross_profit_percent_actual,gross_profit_per_ton_actual


    public $fillable = ['transport_trans_id', 'transport_load_id', 'transport_rate_basis_id', 'cost_price_per_unit', 'cost_price_per_ton', 'cost_price',
        'selling_price', 'selling_price_2', 'selling_price_3', 'selling_price_4', 'selling_price_5', 'selling_price_per_ton', 'selling_price_per_unit', 'transport_rate_per_ton', 'transport_rate', 'transport_price', 'load_insurance_per_ton',
        'comms_due_per_ton', 'weight_ton_incoming', 'weight_ton_outgoing', 'is_transport_costs_inc_price', 'transport_cost', 'transport_cost_2', 'transport_cost_3',
        'transport_cost_4', 'transport_cost_5', 'total_cost_price', 'additional_cost_1', 'additional_cost_2', 'additional_cost_3',
        'additional_cost_desc_1', 'additional_cost_desc_2', 'additional_cost_desc_3', 'gross_profit', 'gross_profit_percent',
        'gross_profit_per_ton', 'total_supplier_comm', 'total_customer_comm', 'total_comm', 'adjusted_gp', 'adjusted_gp_notes',
        'cost_price_actual', 'selling_price_actual', 'cost_price_per_ton_actual', 'selling_price_per_ton_actual', 'transport_cost_actual', 'weight_ton_incoming_actual', 'weight_ton_outgoing_actual', 'total_cost_price_actual', 'gross_profit_actual',
        'gross_profit_percent_actual', 'gross_profit_per_ton_actual', 'transport_rate_per_ton_actual'
    ];


    public function TransportRateBasis(): BelongsTo
    {
        return $this->belongsTo(TransportRateBasis::class);
    }


    public function AssignedUserComm(): HasMany
    {
        return $this->hasMany(AssignedUserComm::class, 'transport_finance_id');
    }

    public function TransportTransaction(): BelongsTo
    {
        return $this->belongsTo(TransportTransaction::class, 'transport_trans_id');
    }

    public function TransportLoad(): BelongsTo
    {
        return $this->belongsTo(TransportLoad::class, 'transport_load_id');
    }

    public function calculateFields()
    {

        //cost_price_actual,cost_price_per_ton_actual,selling_price_per_ton_actual,transport_cost_actual, weight_ton_incoming_actual,weight_ton_outgoing_actual,total_cost_price_actual,gross_profit_actual
        //gross_profit_percent_actual,gross_profit_per_ton_actual

        $transport_Finance = $this;
        $transport_Load = ($transport_Finance->TransportLoad);

        $assigned_user_comm = $transport_Finance->AssignedUserComm;
        $transport_trans = $transport_Finance->TransportTransaction;
        $transport_job = ($transport_trans->TransportJob);
        $is_split_load = false;
        $deal_ticket = $transport_trans->DealTicket;
        $driver_vehicles = $transport_trans->TransportDriverVehicle;

        //actual weighbridge weights
        $actual_tons_in = 0;
        $actual_tons_out = 0;

        if (isset($driver_vehicles)) {
            foreach ($driver_vehicles as $driver_vehicle) {
                $actual_tons_in += $driver_vehicle->weighbridge_upload_weight;
                $actual_tons_out += $driver_vehicle->weighbridge_offload_weight;
            }
        }

        //selling_price = no_units_outgoing * selling_price_per_unit
        //dd($transport_Finance->selling_price_per_unit);
        $selling_price = $transport_Load->no_units_outgoing * $transport_Finance->selling_price_per_unit;

        //need to convert tons to the billing units
        //$weight_ton_outgoing = $no_units_outgoing_total * ($billing_units_outgoing_id->kgs) / 1000;
        //$selling_price_actual = $actual_tons_out * $transport_Finance->selling_price_per_unit;

        $sp_div = ($transport_Load->BillingUnitsOutgoing->kgs) == 1000 ? 1 : ($transport_Load->BillingUnitsOutgoing->kgs) / 1000;

        if ($transport_Finance->selling_price_per_unit > 0 && $transport_Load->BillingUnitsOutgoing->kgs > 0) {
            $selling_price_actual = ($actual_tons_out / $sp_div * $transport_Finance->selling_price_per_unit);
        } else {
            $selling_price_actual = 0;
        }

        //cost_price = no_units_incoming * cost_price_per_unit
        $cost_price = $transport_Load->no_units_incoming * $transport_Finance->cost_price_per_unit;
        //$cost_price_actual = $actual_tons_in * $transport_Finance->cost_price_per_unit;
        $cp_div = ($transport_Load->BillingUnitsIncoming->kgs) == 1000 ? 1 : ($transport_Load->BillingUnitsIncoming->kgs) / 1000;


        if ($transport_Finance->cost_price_per_unit > 0 && $transport_Load->BillingUnitsIncoming->kgs > 0) {
            $cost_price_actual = ($actual_tons_in / $cp_div * $transport_Finance->cost_price_per_unit);
        } else {
            $cost_price_actual = 0;
        }

        //Update cost price

        //units calc
        if ($is_split_load) {
            $no_units_outgoing_total = $transport_Load->no_units_outgoing_2 + $transport_Load->no_units_outgoing_3 + $transport_Load->no_units_outgoing_4 + $transport_Load->no_units_outgoing_5;
        } else {
            $no_units_outgoing_total = $transport_Load->no_units_outgoing;
        }

        $transport_Load->no_units_outgoing_total = $no_units_outgoing_total;

        //weight_ton_incoming = no_units_incoming * (billing_units_incoming_id -> kgs) /1000

        $billing_units_incoming_id = $transport_Load->BillingUnitsIncoming;
        $billing_units_outgoing_id = $transport_Load->BillingUnitsOutgoing;
        $weight_ton_incoming = $transport_Load->no_units_incoming * ($billing_units_incoming_id->kgs) / 1000;

        //weight_ton_outgoing = no_units_outgoing * (billing_units_outgoing_id -> kgs) /1000
        $weight_ton_outgoing = $no_units_outgoing_total * ($billing_units_outgoing_id->kgs) / 1000;

        //cost_price_per_ton = cost_price / weight_ton_outgoing
        $cost_price_per_ton = $cost_price / ($weight_ton_incoming == 0 ? 1 : $weight_ton_incoming);
        $cost_price_per_ton_actual = $cost_price_actual / ($actual_tons_in == 0 ? 1 : $actual_tons_in);

        //selling_price_per_ton = selling_price / weight_ton_incoming


        $selling_price_per_ton = $selling_price / ($weight_ton_outgoing == 0 ? 1 : $weight_ton_outgoing);
        $selling_price_per_ton_actual = $selling_price_actual / ($actual_tons_out == 0 ? 1 : $actual_tons_out);

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
        $transport_rate_per_ton = 0;


        if ($transport_Finance->transport_rate_basis_id === 3) {
            $transport_cost = $transport_Finance->transport_rate;
            $transport_rate_per_ton = $transport_cost / ($weight_ton_incoming > 0 ? $weight_ton_incoming : 1);
            //actual
            $transport_cost_actual = $transport_Finance->transport_rate;
            $transport_rate_per_ton_actual = $transport_cost_actual / ($actual_tons_in > 0 ? $actual_tons_in : 1);

        } else if ($weight_ton_incoming > 0) {
            $transport_cost = $transport_Finance->transport_rate * $weight_ton_incoming;
            $transport_rate_per_ton = $transport_cost / $weight_ton_incoming;

            //actual
            $transport_cost_actual = $transport_Finance->transport_rate * $actual_tons_in;
            $transport_rate_per_ton_actual = $transport_cost_actual / ($actual_tons_in > 0 ? $actual_tons_in : 1);

        } else {
            $transport_cost = $transport_Finance->transport_rate * $weight_ton_outgoing;
            $transport_rate_per_ton = $transport_cost / ($weight_ton_outgoing > 0 ? $weight_ton_outgoing : 1);

            //actual
            $transport_cost_actual = $transport_Finance->transport_rate * $actual_tons_out;
            $transport_rate_per_ton_actual = $transport_cost_actual / ($actual_tons_out > 0 ? $actual_tons_out : 1);
        }

        //transport_rate_per_ton

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

        if ($transport_job->is_transport_costs_inc_price) {
            $total_cost_price = $cost_price + $transport_Finance->additional_cost_1 + $transport_Finance->additional_cost_2 + $transport_Finance->additional_cost_3;
            $total_cost_price_actual = $cost_price_actual + $transport_Finance->additional_cost_1 + $transport_Finance->additional_cost_2 + $transport_Finance->additional_cost_3;
        } else {
            $total_cost_price = $cost_price + $transport_Finance->additional_cost_1 + $transport_Finance->additional_cost_2 + $transport_Finance->additional_cost_3 + $transport_cost;
            $total_cost_price_actual = $cost_price_actual + $transport_Finance->additional_cost_1 + $transport_Finance->additional_cost_2 + $transport_Finance->additional_cost_3 + $transport_cost_actual;

        }

        //gross_profit = selling_price – (total_cost_price + adjusted_gp)
        $gross_profit = $selling_price - ($total_cost_price + $transport_Finance->adjusted_gp);

        //if actual units > 0
        if ($actual_tons_in > 0) {
            $gross_profit_actual = $selling_price_actual - ($total_cost_price_actual + $transport_Finance->adjusted_gp);
        } else {
            $gross_profit_actual = 0;
        }


        //gross_profit_percent = gross_profit / selling_price *100

        if ($selling_price > 0) {
            $gross_profit_percent = round($gross_profit / $selling_price * 100, 4);

            if ($actual_tons_in > 0) {
                $gross_profit_percent_actual = round($gross_profit_actual / ($selling_price_actual > 0 ? $selling_price_actual : 1) * 100, 4);
            } else {
                $gross_profit_percent_actual = 0;
            }


        } else {
            $gross_profit_percent = 0;
            $gross_profit_percent_actual = 0;
        }


        //gross_profit_per_ton = gross_profit / weight_ton_outgoing

        $gross_profit_per_ton = $gross_profit / ($weight_ton_outgoing == 0 ? 1 : $weight_ton_outgoing);
        $gross_profit_per_ton_actual = $gross_profit_actual / ($actual_tons_out == 0 ? 1 : $actual_tons_out);

        //total_supplier_comm = gross_profit /2

        $total_supplier_comm = $gross_profit / 2;

        //total_customer_comm = gross_profit /2

        $total_customer_comm = $gross_profit / 2;


        //UPDATE FIELDS

        $transport_Finance->weight_ton_incoming_actual = $actual_tons_in;
        $transport_Finance->weight_ton_outgoing_actual = $actual_tons_out;

        $transport_Finance->selling_price = $selling_price;
        $transport_Finance->selling_price_actual = $selling_price_actual;

        $transport_Finance->cost_price = $cost_price;
        $transport_Finance->cost_price_actual = $cost_price_actual;


        $transport_Finance->weight_ton_incoming = $weight_ton_incoming;
        $transport_Finance->weight_ton_outgoing = $weight_ton_outgoing;

        $transport_Finance->transport_rate_per_ton = $transport_rate_per_ton;
        $transport_Finance->transport_rate_per_ton_actual = $transport_rate_per_ton_actual;

        $transport_Finance->cost_price_per_ton = $cost_price_per_ton;
        $transport_Finance->cost_price_per_ton_actual = $cost_price_per_ton_actual;
        $transport_Finance->selling_price_per_ton = $selling_price_per_ton;
        $transport_Finance->selling_price_per_ton_actual = $selling_price_per_ton_actual;

        $transport_Finance->load_insurance_per_ton = $load_insurance_per_ton;
        $transport_Finance->transport_cost = $transport_cost;
        $transport_Finance->transport_cost_actual = $transport_cost_actual;
        $transport_Finance->total_cost_price = $total_cost_price;
        $transport_Finance->total_cost_price_actual = $total_cost_price_actual;

        $transport_Finance->gross_profit = $gross_profit;
        $transport_Finance->gross_profit_actual = $gross_profit_actual;
        $transport_Finance->gross_profit_percent = $gross_profit_percent;
        $transport_Finance->gross_profit_percent_actual = $gross_profit_percent_actual;
        $transport_Finance->gross_profit_per_ton = $gross_profit_per_ton;
        $transport_Finance->gross_profit_per_ton_actual = $gross_profit_per_ton_actual;

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

    public function getActivitylogOptions(): LogOptions
    {
        // TODO: Implement getActivitylogOptions() method.

        return LogOptions::defaults()
            ->logOnly(['transport_trans_id', 'transport_load_id', 'transport_rate_basis_id', 'cost_price_per_unit', 'cost_price_per_ton', 'cost_price',
                'selling_price', 'selling_price_2', 'selling_price_3', 'selling_price_4', 'selling_price_5', 'selling_price_per_ton', 'selling_price_per_unit', 'transport_rate_per_ton', 'transport_rate', 'transport_price', 'load_insurance_per_ton',
                'comms_due_per_ton', 'weight_ton_incoming', 'weight_ton_outgoing', 'is_transport_costs_inc_price', 'transport_cost', 'transport_cost_2', 'transport_cost_3',
                'transport_cost_4', 'transport_cost_5', 'total_cost_price', 'additional_cost_1', 'additional_cost_2', 'additional_cost_3',
                'additional_cost_desc_1', 'additional_cost_desc_2', 'additional_cost_desc_3', 'gross_profit', 'gross_profit_percent',
                'gross_profit_per_ton', 'total_supplier_comm', 'total_customer_comm', 'total_comm', 'adjusted_gp', 'adjusted_gp_notes']);
    }

    public static function boot()
    {
        parent::boot();

        self::updating(function ($model) {

        });
    }
}
