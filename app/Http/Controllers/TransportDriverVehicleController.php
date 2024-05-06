<?php

namespace App\Http\Controllers;

use App\Models\TransportDriverVehicle;
use App\Models\TransportFinance;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransportDriverVehicleController extends Controller
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
            'transport_trans_id'=> ['required', 'integer','exists:transport_transactions,id'],
            'transport_job_id'=> ['required', 'integer','exists:transport_jobs,id'],
            'regular_driver_id' => ['required', 'integer','exists:regular_drivers,id'],
            'regular_vehicle_id' => ['required', 'integer','exists:regular_vehicles,id'],
            'weighbridge_upload_weight' => ['required','numeric'],
            'weighbridge_offload_weight' => ['required','numeric'],
            'is_cancelled' => ['required','boolean'],
            'is_loaded' => ['required','boolean'],
            'is_onroad' => ['required','boolean'],
            'is_delivered' => ['required','boolean'],
            'is_transport_scheduled' => ['required','boolean'],
            'is_paid' => ['required','boolean'],
            'is_payment_overdue' => ['required','boolean'],
            'traders_notes' => ['nullable','string'],
            'operations_alert_notes' => ['nullable','string'],
            'driver_vehicle_loading_number' => ['nullable','string']
        ]);

        $cur_date = (Carbon::now())->toDateString();

        //if variance in weight

        $is_weighbridge_variance = !(($request->weighbridge_upload_weight == $request->weighbridge_offload_weight));

        //Stamps if changed
        $date_cancelled = $request->is_cancelled  ? $cur_date : null;
        $date_loaded = $request->is_loaded  ? $cur_date : null;
        $date_onroad = $request->is_onroad  ? $cur_date : null;
        $date_delivered = $request->is_delivered  ? $cur_date : null;
        $date_scheduled = $request->is_transport_scheduled  ? $cur_date : null;
        $date_paid = $request->is_paid  ? $cur_date : null ;
        $date_payment_overdue = $request->is_payment_overdue  ? $cur_date : null;

        $is_created = TransportDriverVehicle::create(
            [
                'transport_trans_id'=> $request->transport_trans_id,
                'transport_job_id'=> $request->transport_job_id,
                'regular_driver_id' => $request->regular_driver_id,
                'regular_vehicle_id' => $request->regular_vehicle_id,
                'weighbridge_upload_weight' => $request->weighbridge_upload_weight,
                'weighbridge_offload_weight' => $request->weighbridge_offload_weight,
                'is_cancelled' => $request->is_cancelled,
                'is_loaded' => $request->is_loaded,
                'is_onroad' => $request->is_onroad,
                'is_delivered' => $request->is_delivered,
                'is_transport_scheduled' => $request->is_transport_scheduled,
                'is_paid' => $request->is_paid,
                'is_payment_overdue' => $request->is_payment_overdue,
                'traders_notes' => $request->traders_notes,
                'operations_alert_notes' => $request->operations_alert_notes,
                'is_weighbridge_variance' => $is_weighbridge_variance,
                'date_cancelled' => $date_cancelled,
                'date_loaded' =>  $date_loaded,
                'date_onroad' => $date_onroad,
                'date_delivered' => $date_delivered,
                'date_scheduled' => $date_scheduled,
                'date_paid' => $date_paid,
                'date_payment_overdue' => $date_payment_overdue,
                'driver_vehicle_loading_number' => $request->driver_vehicle_loading_number,
            ]
        );




        if($is_created){
            $transport_finance = TransportFinance::where('transport_trans_id',$request->transport_trans_id)->first();
            $transport_finance?->calculateFields();
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Driver Vehicle created');
        }
        else {
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Driver Vehicle NOT created');
        }

        return redirect()->back();

    }

    /**
     * Display the specified resource.
     */
    public function show(TransportDriverVehicle $transportDriverVehicle)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransportDriverVehicle $transportDriverVehicle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransportDriverVehicle $transportDriverVehicle)
    {

       /* 'transport_trans_id','transport_job_id','regular_driver_id','regular_vehicle_id','weighbridge_upload_weight','weighbridge_offload_weight',
        'is_weighbridge_variance','is_cancelled','date_cancelled','is_loaded','date_loaded','is_onroad','date_onroad',
        'is_delivered','date_delivered','is_transport_scheduled','date_scheduled','is_paid','date_paid','is_payment_overdue',
        'traders_notes','operations_alert_notes','date_payment_overdue'*/


        $request->validate([
            'regular_driver_id' => ['required', 'integer','exists:regular_drivers,id'],
            'regular_vehicle_id' => ['required', 'integer','exists:regular_vehicles,id'],
            'weighbridge_upload_weight' => ['required','numeric'],
            'weighbridge_offload_weight' => ['required','numeric'],
            'is_cancelled' => ['required','boolean'],
            'is_loaded' => ['required','boolean'],
            'is_onroad' => ['required','boolean'],
            'is_delivered' => ['required','boolean'],
            'is_transport_scheduled' => ['required','boolean'],
            'is_paid' => ['required','boolean'],
            'is_payment_overdue' => ['required','boolean'],
            'traders_notes' => ['nullable','string'],
            'operations_alert_notes' => ['nullable','string'],
            'driver_vehicle_loading_number' => ['nullable','string']
        ]);

        $cur_date = (Carbon::now())->toDateString();

        //if variance in weight

        $is_weighbridge_variance = !(($request->weighbridge_upload_weight == $request->weighbridge_offload_weight));
        //Stamps if changed
        $date_cancelled = $request->is_cancelled && (!($transportDriverVehicle->is_cancelled)) ? $cur_date : $transportDriverVehicle->date_cancelled;
        $date_loaded = $request->is_loaded && (!($transportDriverVehicle->is_loaded)) ? $cur_date : $transportDriverVehicle->date_loaded;
        $date_onroad = $request->is_onroad && (!($transportDriverVehicle->is_onroad)) ? $cur_date : $transportDriverVehicle->date_onroad;
        $date_delivered = $request->is_delivered && (!($transportDriverVehicle->is_delivered)) ? $cur_date : $transportDriverVehicle->date_delivered;
        $date_scheduled = $request->is_transport_scheduled && (!($transportDriverVehicle->is_transport_scheduled)) ? $cur_date : $transportDriverVehicle->date_scheduled;
        $date_paid = $request->is_paid && (!($transportDriverVehicle->is_paid)) ? $cur_date : $transportDriverVehicle->date_paid ;
        $date_payment_overdue = $request->is_payment_overdue && (!($transportDriverVehicle->is_payment_overdue)) ? $cur_date : $transportDriverVehicle->date_payment_overdue;

        $is_updated = $transportDriverVehicle->update(
            [
                'regular_driver_id' => $request->regular_driver_id,
                'regular_vehicle_id' => $request->regular_vehicle_id,
                'weighbridge_upload_weight' => $request->weighbridge_upload_weight,
                'weighbridge_offload_weight' => $request->weighbridge_offload_weight,
                'is_cancelled' => $request->is_cancelled,
                'is_loaded' => $request->is_loaded,
                'is_onroad' => $request->is_onroad,
                'is_delivered' => $request->is_delivered,
                'is_transport_scheduled' => $request->is_transport_scheduled,
                'is_paid' => $request->is_paid,
                'is_payment_overdue' => $request->is_payment_overdue,
                'traders_notes' => $request->traders_notes,
                'operations_alert_notes' => $request->operations_alert_notes,
                'is_weighbridge_variance' => $is_weighbridge_variance,
                'date_cancelled' => $date_cancelled,
                'date_loaded' =>  $date_loaded,
                'date_onroad' => $date_onroad,
                'date_delivered' => $date_delivered,
                'date_scheduled' => $date_scheduled,
                'date_paid' => $date_paid,
                'date_payment_overdue' => $date_payment_overdue,
                'driver_vehicle_loading_number'=>$request->driver_vehicle_loading_number,
            ]
        );


        if($is_updated){
            $transport_finance = TransportFinance::where('transport_trans_id',$request->transport_trans_id)->first();
            $transport_finance?->calculateFields();
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Driver Vehicle updated');
        }
        else {
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Driver Vehicle NOT updated');
        }

    }

    public function updateLoad(Request $request, TransportDriverVehicle $transportDriverVehicle)
    {

        /* 'transport_trans_id','transport_job_id','regular_driver_id','regular_vehicle_id','weighbridge_upload_weight','weighbridge_offload_weight',
         'is_weighbridge_variance','is_cancelled','date_cancelled','is_loaded','date_loaded','is_onroad','date_onroad',
         'is_delivered','date_delivered','is_transport_scheduled','date_scheduled','is_paid','date_paid','is_payment_overdue',
         'traders_notes','operations_alert_notes','date_payment_overdue'*/


        $request->validate([

            'weighbridge_upload_weight' => ['required','numeric'],
            'weighbridge_offload_weight' => ['required','numeric'],
            'driver_vehicle_loading_number' => ['nullable','string']
        ]);

        $cur_date = (Carbon::now())->toDateString();

        //if variance in weight

        $is_weighbridge_variance = !(($request->weighbridge_upload_weight == $request->weighbridge_offload_weight));

        $is_updated = $transportDriverVehicle->update(
            [

                'weighbridge_upload_weight' => $request->weighbridge_upload_weight,
                'weighbridge_offload_weight' => $request->weighbridge_offload_weight,
                'driver_vehicle_loading_number'=>$request->driver_vehicle_loading_number,
            ]
        );

        if($is_updated){
            $transport_finance = TransportFinance::where('transport_trans_id',$request->transport_trans_id)->first();
            $transport_finance?->calculateFields();
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Driver Vehicle updated');
        }
        else {
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Driver Vehicle NOT updated');
        }

    }

    public function updateState(Request $request, TransportDriverVehicle $transportDriverVehicle)
    {

        /* 'transport_trans_id','transport_job_id','regular_driver_id','regular_vehicle_id','weighbridge_upload_weight','weighbridge_offload_weight',
         'is_weighbridge_variance','is_cancelled','date_cancelled','is_loaded','date_loaded','is_onroad','date_onroad',
         'is_delivered','date_delivered','is_transport_scheduled','date_scheduled','is_paid','date_paid','is_payment_overdue',
         'traders_notes','operations_alert_notes','date_payment_overdue'*/

        $request->validate([
            'is_cancelled' => ['required','boolean'],
            'is_loaded' => ['required','boolean'],
            'is_onroad' => ['required','boolean'],
            'is_delivered' => ['required','boolean'],
            'is_transport_scheduled' => ['required','boolean'],
            'is_paid' => ['required','boolean'],
            'is_payment_overdue' => ['required','boolean'],
        ]);

        $cur_date = (Carbon::now())->toDateString();

        //if variance in weight

        $is_weighbridge_variance = !(($transportDriverVehicle->weighbridge_upload_weight == $transportDriverVehicle->weighbridge_offload_weight));

        //Stamps if changed
        $date_cancelled = $request->is_cancelled && (!($transportDriverVehicle->is_cancelled)) ? $cur_date : $transportDriverVehicle->date_cancelled;
        $date_loaded = $request->is_loaded && (!($transportDriverVehicle->is_loaded)) ? $cur_date : $transportDriverVehicle->date_loaded;
        $date_onroad = $request->is_onroad && (!($transportDriverVehicle->is_onroad)) ? $cur_date : $transportDriverVehicle->date_onroad;
        $date_delivered = $request->is_delivered && (!($transportDriverVehicle->is_delivered)) ? $cur_date : $transportDriverVehicle->date_delivered;
        $date_scheduled = $request->is_transport_scheduled && (!($transportDriverVehicle->is_transport_scheduled)) ? $cur_date : $transportDriverVehicle->date_scheduled;
        $date_paid = $request->is_paid && (!($transportDriverVehicle->is_paid)) ? $cur_date : $transportDriverVehicle->date_paid ;
        $date_payment_overdue = $request->is_payment_overdue && (!($transportDriverVehicle->is_payment_overdue)) ? $cur_date : $transportDriverVehicle->date_payment_overdue;

        $is_updated = $transportDriverVehicle->update(
            [
                'is_cancelled' => $request->is_cancelled,
                'is_loaded' => $request->is_loaded,
                'is_onroad' => $request->is_onroad,
                'is_delivered' => $request->is_delivered,
                'is_transport_scheduled' => $request->is_transport_scheduled,
                'is_paid' => $request->is_paid,
                'is_payment_overdue' => $request->is_payment_overdue,
                'is_weighbridge_variance' => $is_weighbridge_variance,
                'date_cancelled' => $date_cancelled,
                'date_loaded' =>  $date_loaded,
                'date_onroad' => $date_onroad,
                'date_delivered' => $date_delivered,
                'date_scheduled' => $date_scheduled,
                'date_paid' => $date_paid,
                'date_payment_overdue' => $date_payment_overdue,
            ]
        );


        /*if($is_updated){
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Driver Vehicle updated');
        }
        else {
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Driver Vehicle NOT updated');
        }*/

    }

    public function updateDriverVehicle(Request $request, TransportDriverVehicle $transportDriverVehicle)
    {

        /* 'transport_trans_id','transport_job_id','regular_driver_id','regular_vehicle_id','weighbridge_upload_weight','weighbridge_offload_weight',
         'is_weighbridge_variance','is_cancelled','date_cancelled','is_loaded','date_loaded','is_onroad','date_onroad',
         'is_delivered','date_delivered','is_transport_scheduled','date_scheduled','is_paid','date_paid','is_payment_overdue',
         'traders_notes','operations_alert_notes','date_payment_overdue'*/

        $request->validate([
            'regular_driver_id' => ['required', 'integer','exists:regular_drivers,id'],
            'regular_vehicle_id' => ['required', 'integer','exists:regular_vehicles,id'],

        ]);

        $is_updated = $transportDriverVehicle->update(
            [
                'regular_driver_id' => $request->regular_driver_id,
                'regular_vehicle_id' => $request->regular_vehicle_id,
            ]
        );

        if($is_updated){
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Driver Vehicle updated');
        }
        else {
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Driver Vehicle NOT updated');
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,TransportDriverVehicle $transportDriverVehicle)
    {
        $transportDriverVehicle->delete();

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Driver Vehicle deleted');

        return redirect()->back();
    }
}
