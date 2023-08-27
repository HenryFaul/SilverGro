<?php

namespace App\Http\Controllers;

use App\Models\TransportFinance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransportFinanceController extends Controller
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
    public function show(TransportFinance $transportFinance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransportFinance $transportFinance)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransportFinance $transportFinance)
    {
        //  'transport_rate_basis_id'=>['required', 'integer','exists:transport_rate_bases,id'],

 /*       'transport_trans_id','transport_load_id','transport_rate_basis_id','cost_price_per_unit','cost_price_per_ton','selling_price_per_unit','cost_price',
        'selling_price','selling_price_per_ton','cost_price_per_unit','selling_price_per_unit','transport_rate_per_ton','transport_rate','transport_price','load_insurance_per_ton',
        'comms_due_per_ton','weight_ton_incoming','weight_ton_outgoing','is_transport_costs_inc_price','transport_cost','total_cost_price','additional_cost_1','additional_cost_2','additional_cost_3',
        'additional_cost_desc_1','additional_cost_desc_2','additional_cost_desc_3','gross_profit','gross_profit_percent',
        'gross_profit_per_ton','total_supplier_comm','total_customer_comm','total_comm','adjusted_gp','adjusted_gp_notes'*/

        //dd($request->selling_price_per_unit);

        $user = auth()->user();

        $can_edit_adjusted_gp = $user->can('edit_adjusted_gp');


        if($can_edit_adjusted_gp){

            $is_updated = $transportFinance->update(
                $request->validate([
                    'transport_rate_basis_id'=>['required', 'integer','exists:transport_rate_bases,id'],
                    'cost_price_per_unit' => ['required','numeric'],
                    'selling_price_per_unit' => ['required','numeric'],
                    'adjusted_gp'=> ['required','numeric'],
                    'adjusted_gp_notes'=> ['nullable','string'],
                    'transport_rate' => ['nullable','numeric'],
                    'additional_cost_1' => ['nullable','numeric'],
                    'additional_cost_2' => ['nullable','numeric'],
                    'additional_cost_3' => ['nullable','numeric'],
                    'additional_cost_desc_1' => ['nullable','string'],
                    'additional_cost_desc_2' => ['nullable','string'],
                    'additional_cost_desc_3' => ['nullable','string']
                ])
            );

        }else{
            $is_updated = $transportFinance->update(
                $request->validate([
                    'transport_rate_basis_id'=>['required', 'integer','exists:transport_rate_bases,id'],
                    'cost_price_per_unit' => ['required','numeric'],
                    'selling_price_per_unit' => ['required','numeric'],
                    'transport_rate' => ['nullable','numeric'],
                    'additional_cost_1' => ['nullable','numeric'],
                    'additional_cost_2' => ['nullable','numeric'],
                    'additional_cost_3' => ['nullable','numeric'],
                    'additional_cost_desc_1' => ['nullable','string'],
                    'additional_cost_desc_2' => ['nullable','string'],
                    'additional_cost_desc_3' => ['nullable','string']
                ])
            );
        }


        $transportFinance->CalculateFields();

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Transport Finance updated');

        return redirect()->back();
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransportFinance $transportFinance)
    {
        //
    }
}
