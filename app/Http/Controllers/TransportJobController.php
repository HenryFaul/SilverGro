<?php

namespace App\Http\Controllers;

use App\Models\TransportJob;
use Illuminate\Http\Request;

class TransportJobController extends Controller
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
    public function show(TransportJob $transportJob)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransportJob $transportJob)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransportJob $transportJob)
    {
/*
        'transport_trans_id','transport_rate_basis_id','customer_order_number','is_multi_loads',
        'is_approved','transport_date_earliest','transport_date_latest','is_transport_costs_inc_price','is_product_zero_rated',
        'loading_hours_from_id','loading_hours_to_id','offloading_hours_from_id','offloading_hours_to_id','load_insurance_per_ton',
        'total_load_insurance','number_loads','loading_instructions','offloading_instructions','comments'*/

        $transportJob->update(
            $request->validate([
                'supplier_loading_number'=>['nullable','string'],
                'customer_order_number'=>['nullable','string'],
                'number_loads' => ['nullable','numeric'],
                'is_multi_loads' => ['nullable','boolean'],
                'is_approved' => ['nullable','boolean'],
                'is_transport_costs_inc_price' => ['nullable','boolean'],
                'is_product_zero_rated' => ['nullable','boolean'],
                'loading_hours_from_id' => ['required', 'integer','exists:loading_hour_options,id'],
                'loading_hours_to_id' => ['required', 'integer','exists:loading_hour_options,id'],
                'offloading_hours_from_id' => ['required', 'integer','exists:loading_hour_options,id'],
                'offloading_hours_to_id' => ['required', 'integer','exists:loading_hour_options,id'],
                'loading_instructions' => ['nullable','string'],
                'offloading_instructions' => ['nullable','string']
            ])
        );


        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Transport Job updated');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransportJob $transportJob)
    {
        //
    }
}
