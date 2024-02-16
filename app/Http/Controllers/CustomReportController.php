<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerParent;
use App\Models\CustomReport;
use App\Models\DealTicket;
use App\Models\Product;
use App\Models\RegularDriver;
use App\Models\RegularVehicle;
use App\Models\Supplier;
use App\Models\TransportDriverVehicle;
use App\Models\Transporter;
use App\Models\TransportFinance;
use App\Models\TransportInvoice;
use App\Models\TransportInvoiceDetails;
use App\Models\TransportJob;
use App\Models\TransportLoad;
use App\Models\TransportTransaction;
use Illuminate\Http\Request;

class CustomReportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $custom_reports = CustomReport::with('CustomReportModels')->get();

  /*      dd(get_class($transportTransaction));
        dd($transportTransaction->getAttributes());*/



        //Customer
        //CustomerParent
        //Deal Ticket
        //Product
        //RegularDriver
        //RegularVehicle
        //Supplier
        //Transporter
        //TransportFinance
        //TransportInvoice
        //TransportInvoiceDetails
        //TransportJob
        //TransportLoad
        //TransportTransaction


        $transport_transaction = TransportTransaction::first();
        $customer = Customer::first();
        $customer_parent = CustomerParent::first();
        $deal_ticket = DealTicket::first();
        $product = Product::first();
        $regular_driver = RegularDriver::first();
        $regular_vehicle = RegularVehicle::first();
        $transport_driver_vehicle = TransportDriverVehicle::first();
        $supplier = Supplier::first();
        $transporter = Transporter::first();
        $transport_finance = TransportFinance::first();
        $transport_invoice = TransportInvoice::first();
        $transport_invoice_details = TransportInvoiceDetails::first();
        $transport_job = TransportJob::first();
        $transport_load = TransportLoad::first();


        $model_data = array(
           'data' => array(
                ['name'=>get_class($transport_transaction),'attributes'=> array_keys($transport_transaction->getAttributes())],
                ['name'=>get_class($customer),'attributes'=> array_keys($customer->getAttributes())],
                ['name'=>get_class($customer_parent),'attributes'=> array_keys($customer_parent->getAttributes())],
               ['name'=>get_class($deal_ticket),'attributes'=> array_keys($deal_ticket->getAttributes())],
               ['name'=>get_class($product),'attributes'=> array_keys($product->getAttributes())],
               ['name'=>get_class($regular_driver),'attributes'=> array_keys($regular_driver->getAttributes())],
               ['name'=>get_class($regular_vehicle),'attributes'=> array_keys($regular_vehicle->getAttributes())],
               ['name'=>get_class($transport_driver_vehicle),'attributes'=> array_keys($transport_driver_vehicle->getAttributes())],
               ['name'=>get_class($supplier),'attributes'=> array_keys($supplier->getAttributes())],
               ['name'=>get_class($transporter),'attributes'=> array_keys($transporter->getAttributes())],
               ['name'=>get_class($transport_finance),'attributes'=> array_keys($transport_finance->getAttributes())],
               ['name'=>get_class($transport_invoice),'attributes'=> array_keys($transport_invoice->getAttributes())],
               ['name'=>get_class($transport_invoice_details),'attributes'=> array_keys($transport_invoice_details->getAttributes())],
               ['name'=>get_class($transport_job),'attributes'=> array_keys($transport_job->getAttributes())],
               ['name'=>get_class($transport_load),'attributes'=> array_keys($transport_load->getAttributes())],
           )
        );


        return inertia(
            'ReportGenerator/Index',
            [
                'custom_reports'=>$custom_reports,
                'model_data'=>$model_data
            ]
        );
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
        // ['id','created_by_id','name','comment','is_active'];

        $request->validate([
            'name' => ['string','unique:custom_reports,name'],
            'comment' => ['nullable','string'],
        ]);

        $custom_report = CustomReport::create([
            'created_by_id' => $request->user()->id,
            'name' => $request->name,
            'comment' => $request->comment,
        ]);

        if ($custom_report->exists()) {
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Report Created');
        }
        else{
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Report NOT Created');
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomReport $customReport)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomReport $customReport)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomReport $customReport)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomReport $customReport)
    {
        //
    }
}
