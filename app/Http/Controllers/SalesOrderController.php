<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\TransportTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class SalesOrderController extends Controller
{

    public function viewPDF(Request $request, $id): \Illuminate\Http\Response
    {

        $final_sales_order = false;
        $path = 'images/pdflogo.jpg';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $file_data = file_get_contents($path);
        $logo = 'data:image/' . $type . ';base64,' . base64_encode($file_data);

        $transport_trans = TransportTransaction::where('id', $id)->with('ContractType')->with('Transporter')->with('Supplier',fn($query) => $query->with('TermsOfPayment'))->with('Customer',fn($query) => $query->with('InvoiceBasis')->with('TermsOfPaymentBasis')->with('TermsOfPayment'))->with('TransportInvoice', fn($query) => $query->with('TransportInvoiceDetails'))
            ->with('TransportLoad',fn($query) => $query->with('ProductSource')->with('PackagingOutgoing')->with('CollectionAddress')->with('DeliveryAddress')->with('BillingUnitsOutgoing')->with('ConfirmedByType'))->with('DealTicket')->with('TransportFinance',fn($query) => $query->with('TransportRateBasis'))->first();

        $deal_ticket = $transport_trans->DealTicket;
        $sales_order = $transport_trans->SalesOrder;
        $purchase_order = $transport_trans->PurchaseOrder->load('ConfirmedByType');
        //dd($purchase_order);
        //dd($sales_order);


        $rules_with_approvals = $deal_ticket->getAppliedRules();
        $user_name = Auth::user()->name;
        $now = (Carbon::now()->tz('Africa/Johannesburg'))->toDateString();
        $app_version = env("APP_VERSION_REP", "1");;


        $data = [
            'logo' => $logo,
            'final_sales_order'=>$final_sales_order,
            'transport_trans'=>$transport_trans,
            'deal_ticket'=>$deal_ticket,
            'sales_order'=>$sales_order,
            'purchase_order'=>$purchase_order,
            'rules_with_approvals'=>$rules_with_approvals,
            'user_name'=>$user_name,
            'now'=>$now,
            'app_version'=>$app_version
        ];

        $pdf = PDF::loadView('pdf_reports.sales_order',$data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream();

    }

    public function viewConfirmationPDF(Request $request, $id): \Illuminate\Http\Response
    {

        $final_sales_order = false;
        $path = 'images/pdflogo.jpg';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $file_data = file_get_contents($path);
        $logo = 'data:image/' . $type . ';base64,' . base64_encode($file_data);

        $transport_trans = TransportTransaction::where('id', $id)->with('ContractType')->with('Transporter')->with('Supplier',fn($query) => $query->with('TermsOfPayment'))->with('Customer',fn($query) => $query->with('InvoiceBasis')->with('TermsOfPaymentBasis')->with('TermsOfPayment'))->with('TransportInvoice', fn($query) => $query->with('TransportInvoiceDetails'))
            ->with('TransportLoad',fn($query) => $query->with('ProductSource')->with('PackagingOutgoing')->with('CollectionAddress')->with('DeliveryAddress')->with('BillingUnitsOutgoing')->with('ConfirmedByType'))->with('DealTicket')
            ->with('TransportJob',fn($query) => $query->with('OffloadingHoursFrom')->with('OffloadingHoursTo'))
            ->with('TransportFinance',fn($query) => $query->with('TransportRateBasis'))->first();

        $deal_ticket = $transport_trans->DealTicket;
        $sales_order = $transport_trans->SalesOrder;
        $purchase_order = $transport_trans->PurchaseOrder->load('ConfirmedByType');


        $rules_with_approvals = $deal_ticket->getAppliedRules();
        $user_name = Auth::user()->name;
        $now = (Carbon::now()->tz('Africa/Johannesburg'))->toDateString();
        $app_version = env("APP_VERSION_REP", "1");;


        $data = [
            'logo' => $logo,
            'final_sales_order'=>$final_sales_order,
            'transport_trans'=>$transport_trans,
            'deal_ticket'=>$deal_ticket,
            'sales_order'=>$sales_order,
            'purchase_order'=>$purchase_order,
            'rules_with_approvals'=>$rules_with_approvals,
            'user_name'=>$user_name,
            'now'=>$now,
            'app_version'=>$app_version
        ];

        $pdf = PDF::loadView('pdf_reports.sales_order_confirmation',$data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream();

    }

    public function activate(Request $request): \Illuminate\Http\RedirectResponse
    {

        $user = Auth::user();
        $permissions = $user->getPermissionsViaRoles()->pluck('name');
        $can_approve = $permissions->contains('approve_deal_ticket');
        $transport_trans = TransportTransaction::find($request->transport_trans_id);
        $sales_order = $transport_trans->SalesOrder;


        $request->validate([
            'is_active'=>['nullable','boolean',Rule::prohibitedIf(!$can_approve)],
        ],['is_active'=>'You need permissions to activate a Sales Order']);

        $is_updated = false;

        if ($can_approve){
            $is_updated = $sales_order->update(['is_active' =>1]);
        }

        if ($is_updated){
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Sales Order updated');
        }else{
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Sales Order NOT updated');
        }

        return redirect()->back();

    }

    public function send(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        $permissions = $user->getPermissionsViaRoles()->pluck('name');
        $can_approve = $permissions->contains('approve_deal_ticket');
        $transport_trans = TransportTransaction::find($request->transport_trans_id);
        $sales_order = $transport_trans->SalesOrder;


        $request->validate([
            'is_active'=>['nullable','boolean',Rule::prohibitedIf(!$sales_order->is_active)],
        ],['is_active'=>'Sales Order not Active']);

        $is_updated = false;

        if ($sales_order->is_active){
            $is_updated = $sales_order->update(['is_sa_conf_sent' =>1]);
        }

        if ($is_updated){
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Sales Order updated');
        }else{
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Sales Order NOT updated');
        }

        return redirect()->back();

    }

    public function receive(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        $permissions = $user->getPermissionsViaRoles()->pluck('name');
        $can_approve = $permissions->contains('approve_deal_ticket');
        $transport_trans = TransportTransaction::find($request->transport_trans_id);
        $sales_order = $transport_trans->SalesOrder;


        $request->validate([
            'is_active'=>['nullable','boolean',Rule::prohibitedIf(!$sales_order->is_active)],
        ],['is_active'=>'Sales Order not Active']);

        $is_updated = false;

        if ($sales_order->is_active){
            $is_updated = $sales_order->update(['is_sa_conf_received' =>1]);
        }

        if ($is_updated){
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Sales Order updated');
        }else{
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Sales Order NOT updated');
        }

        return redirect()->back();

    }


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
    public function show(SalesOrder $salesOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SalesOrder $salesOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SalesOrder $salesOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SalesOrder $salesOrder)
    {
        //
    }
}
