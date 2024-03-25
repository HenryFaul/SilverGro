<?php

namespace App\Http\Controllers;

use App\Models\TransportLoad;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransportLoadController extends Controller
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
    public function show(TransportLoad $transportLoad)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransportLoad $transportLoad)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransportLoad $transportLoad): \Illuminate\Http\RedirectResponse
    {

       /* 'contract_type_id.id' => ['required', 'integer', 'exists:contract_types,id'],
                'supplier_id.id' => ['required', 'integer', 'exists:suppliers,id'],
                'customer_id.id' => ['required', 'integer', 'exists:customers,id'],
                'transporter_id.id' => ['required', 'integer', 'exists:transporters,id'],
                'product_id.id' => ['required', 'integer', 'exists:products,id'],
                'include_in_calculations' => ['nullable', 'boolean'],
                'transport_date_earliest' => ['required', 'date'],
                'transport_date_latest' => ['required', 'date'],
                'delivery_notes' => ['nullable'],
                'is_transaction_done' => ['nullable', 'boolean']*/


        //dd($request->confirmed_by_id['id']);

        $update_related_models = 1;

        if (isset($request->update_related_models)){
            $update_related_models=$request->update_related_models;
        }

        $request->validate([
            'confirmed_by_id.id'=>['required', 'integer'],
            'confirmed_by_type_id.id'=>['required', 'integer'],
            'packaging_incoming_id.id'=>['required', 'integer'],
            'packaging_outgoing_id.id'=>['required', 'integer'],
            'product_source_id.id'=>['required', 'integer'],
            'product_grade_perc'=>['nullable'],
            'no_units_incoming'=>['nullable', 'integer'],
            'billing_units_incoming_id.id'=>['required', 'integer'],
            'no_units_outgoing'=>['nullable', 'integer'],
            'no_units_outgoing_2'=>['nullable', 'integer'],
            'no_units_outgoing_3'=>['nullable', 'integer'],
            'no_units_outgoing_4'=>['nullable', 'integer'],
            'no_units_outgoing_5'=>['nullable', 'integer'],
            'billing_units_outgoing_id.id'=>['required'],
            'is_weighbridge_certificate_received'=>['nullable', 'boolean'],
            'delivery_note'=>['nullable'],
            'calculated_route_distance'=>['nullable', 'numeric'],
            'collection_address_id.id'=>['required', 'integer'],
            'delivery_address_id.id'=>['required', 'integer'],
            'delivery_address_id_2.id'=>['nullable', 'integer'],
            'delivery_address_id_3.id'=>['nullable', 'integer'],
            'delivery_address_id_4.id'=>['nullable', 'integer']
        ],
            [
                'packaging_incoming_id.id' => 'Need to select a valid incoming package option!',
                'packaging_outgoing_id.id' => 'Need to select a valid outgoing package option!',
                'product_source_id.id' => 'Need to select a valid product source option!',
                'billing_units_incoming_id.id' => 'Need to select a valid billing units incoming option!',
                'billing_units_outgoing_id.id' => 'Need to select a valid billing units outgoing option!',
                'collection_address_id.id' => 'Need to select a valid collection address option!',
                'delivery_address_id.id' => 'Need to select a valid delivery address option!',
                'delivery_address_id_2.id' => 'Need to select a valid delivery address option!',
                'delivery_address_id_3.id' => 'Need to select a valid delivery address option!',
                'delivery_address_id_4.id' => 'Need to select a valid delivery address option!',
            ]
        );

        $no_units_outgoing_total=$request->no_units_outgoing+$request->no_units_outgoing_2+$request->no_units_outgoing_3+$request->no_units_outgoing_4;

        $is_updated = $transportLoad->update(
            [
                'confirmed_by_id' => $request->confirmed_by_id['id'],
                'confirmed_by_type_id' => $request->confirmed_by_type_id['id'],
                'packaging_incoming_id' => $request->packaging_incoming_id['id'],
                'packaging_outgoing_id' => $request->packaging_outgoing_id['id'],
                'product_source_id' => $request->product_source_id['id'],
                'product_grade_perc' => $request->product_grade_perc,
                'no_units_incoming' => $request->no_units_incoming,
                'billing_units_incoming_id' => $request->billing_units_incoming_id['id'],
                'no_units_outgoing' => $request->no_units_outgoing,
                'no_units_outgoing_2' => $request->no_units_outgoing_2,
                'no_units_outgoing_3' => $request->no_units_outgoing_3,
                'no_units_outgoing_4' => $request->no_units_outgoing_4,
                'no_units_outgoing_5' => $request->no_units_outgoing_5,
                'billing_units_outgoing_id' => $request->billing_units_outgoing_id['id'],
                'is_weighbridge_certificate_received' => $request->is_weighbridge_certificate_received,
                'delivery_note' => $request->delivery_note,
                'calculated_route_distance' => $request->calculated_route_distance,
                'collection_address_id' => $request->collection_address_id['id'],
                'delivery_address_id' => $request->delivery_address_id['id'],
                'delivery_address_id_2' =>$request->delivery_address_id_2 === null? null: $request->delivery_address_id_2['id'],
                'delivery_address_id_3' =>$request->delivery_address_id_3 === null? null:  $request->delivery_address_id_3['id'],
                'delivery_address_id_4' => $request->delivery_address_id_4 === null? null: $request->delivery_address_id_4['id'],
                'delivery_address_id_5' => $request->delivery_address_id_4 === null? null: $request->delivery_address_id_5['id'],
            ]
        );

   /*     $request->validate([
            'confirmed_by_id.id'=>['required', 'integer'],
            'confirmed_by_type_id.id'=>['required', 'integer','exists:staff,id'],
            'packaging_incoming_id.id'=>['required', 'integer','exists:packagings,id','not_in:1'],
            'packaging_outgoing_id.id'=>['required', 'integer','exists:packagings,id','not_in:1'],
            'product_source_id.id'=>['required', 'integer','exists:product_sources,id','not_in:1'],
            'product_grade_perc'=>['nullable'],
            'no_units_incoming'=>['nullable', 'integer'],
            'billing_units_incoming_id.id'=>['required', 'integer','exists:billing_units,id','not_in:1'],
            'no_units_outgoing'=>['nullable', 'integer'],
            'no_units_outgoing_2'=>['nullable', 'integer'],
            'no_units_outgoing_3'=>['nullable', 'integer'],
            'no_units_outgoing_4'=>['nullable', 'integer'],
            'no_units_outgoing_5'=>['nullable', 'integer'],
            'billing_units_outgoing_id.id'=>['required', 'integer','exists:billing_units,id','not_in:1'],
            'is_weighbridge_certificate_received'=>['nullable', 'boolean'],
            'delivery_note'=>['nullable'],
            'calculated_route_distance'=>['nullable', 'numeric'],
            'collection_address_id.id'=>['required', 'integer'],
            'delivery_address_id.id'=>['required', 'integer'],
            'delivery_address_id_2.id'=>['nullable', 'integer'],
            'delivery_address_id_3.id'=>['nullable', 'integer'],
            'delivery_address_id_4.id'=>['nullable', 'integer']
        ],
            [
                'packaging_incoming_id.id' => 'Need to select a valid incoming package option!',
                'packaging_outgoing_id.id' => 'Need to select a valid outgoing package option!',
                'product_source_id.id' => 'Need to select a valid product source option!',
                'billing_units_incoming_id.id' => 'Need to select a valid billing units incoming option!',
                'billing_units_outgoing_id.id' => 'Need to select a valid billing units outgoing option!',
                'collection_address_id.id' => 'Need to select a valid collection address option!',
                'delivery_address_id.id' => 'Need to select a valid delivery address option!',
                'delivery_address_id_2.id' => 'Need to select a valid delivery address option!',
                'delivery_address_id_3.id' => 'Need to select a valid delivery address option!',
                'delivery_address_id_4.id' => 'Need to select a valid delivery address option!',
            ]
        );

        $no_units_outgoing_total=$request->no_units_outgoing+$request->no_units_outgoing_2+$request->no_units_outgoing_3+$request->no_units_outgoing_4;

        $is_updated = $transportLoad->update(
            [
                'confirmed_by_id' => $request->confirmed_by_id['id'],
                'confirmed_by_type_id' => $request->confirmed_by_type_id['id'],
                'packaging_incoming_id' => $request->packaging_incoming_id['id'],
                'packaging_outgoing_id' => $request->packaging_outgoing_id['id'],
                'product_source_id' => $request->product_source_id['id'],
                'product_grade_perc' => $request->product_grade_perc,
                'no_units_incoming' => $request->no_units_incoming,
                'billing_units_incoming_id' => $request->billing_units_incoming_id['id'],
                'no_units_outgoing' => $request->no_units_outgoing,
                'no_units_outgoing_2' => $request->no_units_outgoing_2,
                'no_units_outgoing_3' => $request->no_units_outgoing_3,
                'no_units_outgoing_4' => $request->no_units_outgoing_4,
                'no_units_outgoing_5' => $request->no_units_outgoing_5,
                'billing_units_outgoing_id' => $request->billing_units_outgoing_id['id'],
                'is_weighbridge_certificate_received' => $request->is_weighbridge_certificate_received,
                'delivery_note' => $request->delivery_note,
                'calculated_route_distance' => $request->calculated_route_distance,
                'collection_address_id' => $request->collection_address_id['id'],
                'delivery_address_id' => $request->delivery_address_id['id'],
                'delivery_address_id_2' =>$request->delivery_address_id_2 === null? null: $request->delivery_address_id_2['id'],
                'delivery_address_id_3' =>$request->delivery_address_id_3 === null? null:  $request->delivery_address_id_3['id'],
                'delivery_address_id_4' => $request->delivery_address_id_4 === null? null: $request->delivery_address_id_4['id'],
                'delivery_address_id_5' => $request->delivery_address_id_4 === null? null: $request->delivery_address_id_5['id'],
            ]
        );
       */


        if($update_related_models==1){
            $transport_finance = ($transportLoad->TransportFinance);
            $transport_finance->CalculateFields();
            if($is_updated){
                $request->session()->flash('flash.bannerStyle', 'success');
                $request->session()->flash('flash.banner', 'Transport Load updated');
            }
            else {
                $request->session()->flash('flash.bannerStyle', 'danger');
                $request->session()->flash('flash.banner', 'Transport Load NOT updated');
            }
        }



        return redirect()->back();
    }

    public function updateUnits(Request $request, TransportLoad $transportLoad): \Illuminate\Http\RedirectResponse
    {

        $request->validate([
            'selected_trans_id'=>['required', 'integer'],
            'no_units_incoming'=>['required', 'integer'],
            'no_units_outgoing'=>['required', 'integer']

        ]);

        $is_updated = $transportLoad->update(
            [
                'no_units_incoming' => $request->no_units_incoming,
                'no_units_outgoing' => $request->no_units_outgoing,
            ]
        );

        $transport_finance = ($transportLoad->TransportFinance);
        $transport_finance->CalculateFields();

        if($is_updated){
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Transport Load updated');
        }
        else {
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Transport Load NOT updated');
        }

        return redirect()->back();
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransportLoad $transportLoad)
    {
        //
    }
}
