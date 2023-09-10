<?php

namespace App\Http\Controllers;

use App\Models\TransportOrder;
use App\Models\TransportTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class TransportOrderController extends Controller
{


    public function viewConfirmationPDF(Request $request, $id): \Illuminate\Http\Response
    {

        $final_transport_order = false;
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
        $transport_order = $transport_trans->TransportOrder->load('ConfirmedByType');


        $rules_with_approvals = $deal_ticket->getAppliedRules();
        $user_name = Auth::user()->name;
        $now = (Carbon::now()->tz('Africa/Johannesburg'))->toDateString();
        $app_version = env("APP_VERSION_REP", "1");;


        $data = [
            'logo' => $logo,
            'final_transport_order'=>$final_transport_order,
            'transport_trans'=>$transport_trans,
            'deal_ticket'=>$deal_ticket,
            'sales_order'=>$sales_order,
            'purchase_order'=>$purchase_order,
            'rules_with_approvals'=>$rules_with_approvals,
            'user_name'=>$user_name,
            'now'=>$now,
            'app_version'=>$app_version,
            'transport_order'=>$transport_order
        ];

        $pdf = PDF::loadView('pdf_reports.transport_order_confirmation',$data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream();

    }

    public function activate(Request $request): \Illuminate\Http\RedirectResponse
    {

        $user = Auth::user();
        $permissions = $user->getPermissionsViaRoles()->pluck('name');
        $can_approve = $permissions->contains('approve_deal_ticket');
        $transport_trans = TransportTransaction::find($request->transport_trans_id);
        $transport_order = $transport_trans->TransportOrder;


        $request->validate([
            'is_active'=>['nullable','boolean',Rule::prohibitedIf(!$can_approve)],
        ],['is_active'=>'You need permissions to activate a Sales Order']);

        $is_updated = false;

        if ($can_approve){
            $is_updated = $transport_order->update(['is_active' =>1]);
        }

        if ($is_updated){
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Transport Order updated');
        }else{
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Transport Order NOT updated');
        }

        return redirect()->back();

    }

    public function send(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        $permissions = $user->getPermissionsViaRoles()->pluck('name');
        $can_approve = $permissions->contains('approve_deal_ticket');
        $transport_trans = TransportTransaction::find($request->transport_trans_id);
        $transport_order = $transport_trans->TransportOrder;


        $request->validate([
            'is_active'=>['nullable','boolean',Rule::prohibitedIf(!$transport_order->is_active)],
        ],['is_active'=>'Sales Order not Active']);

        $is_updated = false;

        if ($transport_order->is_active){
            $is_updated = $transport_order->update(['is_to_sent' =>1]);
        }

        if ($is_updated){
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Purchase Order updated');
        }else{
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Purchase Order NOT updated');
        }

        return redirect()->back();

    }

    public function receive(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = Auth::user();
        $permissions = $user->getPermissionsViaRoles()->pluck('name');
        $can_approve = $permissions->contains('approve_deal_ticket');
        $transport_trans = TransportTransaction::find($request->transport_trans_id);
        $transport_order = $transport_trans->TransportOrder;


        $request->validate([
            'is_active'=>['nullable','boolean',Rule::prohibitedIf(!$transport_order->is_active)],
        ],['is_active'=>'Sales Order not Active']);

        $is_updated = false;

        if ($transport_order->is_active){
            $is_updated = $transport_order->update(['is_to_received' =>1]);
        }

        if ($is_updated){
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Transport Order updated');
        }else{
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Transport Order NOT updated');
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
    public function show(TransportOrder $transportOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransportOrder $transportOrder)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransportOrder $transportOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransportOrder $transportOrder)
    {
        //
    }
}
