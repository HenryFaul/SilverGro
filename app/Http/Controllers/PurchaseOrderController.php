<?php

namespace App\Http\Controllers;

use App\Models\PdfSetting;
use App\Models\PurchaseOrder;
use App\Models\TransLinkSplit;
use App\Models\TransportTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class PurchaseOrderController extends Controller
{


    public function viewPDF(Request $request, $id): Response
    {

        $final_sales_order = false;
        // Get PDF settings
        $pdfSettings = PdfSetting::getActive();
        $logo = $pdfSettings ? $pdfSettings->logo_full_path : public_path('images/pdflogo.jpg');

        $transport_trans = TransportTransaction::where('id', $id)->with('ContractType')->with('Transporter')->with('Supplier',fn($query) => $query->with('TermsOfPayment'))->with('Customer',fn($query) => $query->with('InvoiceBasis')->with('TermsOfPaymentBasis')->with('TermsOfPayment'))->with('TransportInvoice', fn($query) => $query->with('TransportInvoiceDetails'))
            ->with('TransportLoad',fn($query) => $query->with('ProductSource')->with('PackagingOutgoing')->with('CollectionAddress')->with('DeliveryAddress')->with('BillingUnitsOutgoing')->with('ConfirmedByType'))->with('DealTicket')->with('TransportFinance',fn($query) => $query->with('TransportRateBasis'))->first();

        abort_if(is_null($transport_trans), 404, 'Transaction #'.$id.' not found.');

        $deal_ticket = $transport_trans->DealTicket;
        $sales_order = $transport_trans->SalesOrder;
        $purchase_order = $transport_trans->PurchaseOrder?->load('ConfirmedByType');


        $rules_with_approvals = $deal_ticket->getAppliedRules();
        $user_name = Auth::user()->name;
        $now = (Carbon::now()->tz('Africa/Johannesburg'))->toDateString();
        $app_version = env("APP_VERSION_REP", "1");


        $data = [
            'logo' => $logo,
            'pdfSettings' => $pdfSettings,
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

        $pdf = PDF::loadView('pdf_reports.purchase_order',$data);
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream();

    }

    public function viewConfirmationPDF(Request $request, $id): Response
    {

        $final_purchase_order = false;
        // Get PDF settings
        $pdfSettings = PdfSetting::getActive();
        $logo = $pdfSettings ? $pdfSettings->logo_full_path : public_path('images/pdflogo.jpg');

        $transport_trans = TransportTransaction::where('id', $id)->with('ContractType')->with('Transporter')->with('Supplier',fn($query) => $query->with('TermsOfPayment')->with('addressablePhysical')->with('contactable.numberable')->with('contactable.emailable'))->with('Customer',fn($query) => $query->with('InvoiceBasis')->with('TermsOfPaymentBasis')->with('TermsOfPayment'))->with('TransportInvoice', fn($query) => $query->with('TransportInvoiceDetails'))
            ->with('TransportLoad',fn($query) => $query->with('ProductSource')->with('PackagingIncoming')->with('PackagingOutgoing')->with('CollectionAddress')->with('DeliveryAddress')->with('BillingUnitsIncoming')->with('BillingUnitsOutgoing')->with('ConfirmedByType'))->with('DealTicket')
            ->with('TransportJob',fn($query) => $query->with('LoadingHoursFrom')->with('LoadingHoursTo')->with('OffloadingHoursFrom')->with('OffloadingHoursTo'))
            ->with('TransportFinance',fn($query) => $query->with('TransportRateBasis'))->first();

        abort_if(is_null($transport_trans), 404, 'Transaction #'.$id.' not found.');

        $deal_ticket = $transport_trans->DealTicket;
        $sales_order = $transport_trans->SalesOrder;
        $purchase_order = $transport_trans->PurchaseOrder?->load('ConfirmedByType');

        // Get split data if it's a split load
        $split_data = null;
        if ($transport_trans->is_split_load) {
            $primary_linked_trans_split = TransLinkSplit::where('linked_transport_trans_id', '=', $transport_trans->id)->with('TransportTransaction', fn($query) => $query->with('Customer')->with('Supplier')->with('Transporter')
                ->with('Product')->with('TransportFinance')->with('TransportLoad'))->first();

            $primary_trans = null;
            if ($primary_linked_trans_split && isset($primary_linked_trans_split->transport_trans_id)) {
                $primary_trans = TransportTransaction::find($primary_linked_trans_split->transport_trans_id);
                $linked_trans_split = TransLinkSplit::where('transport_trans_id', '=', $primary_linked_trans_split->transport_trans_id)
                    ->with(['TransportTransaction' => function ($query) {
                        $query->with([
                            'Customer',
                            'Supplier' => function ($query) {
                                $query->with(['TermsOfPayment', 'addressablePhysical', 'contactable.numberable', 'contactable.emailable']);
                            },
                            'Transporter',
                            'Product',
                            'TransportFinance',
                            'TransportLoad' => function ($query) {
                                $query->with(['BillingUnitsIncoming', 'BillingUnitsOutgoing', 'PackagingIncoming', 'ProductSource', 'CollectionAddress']);
                            },
                            'TransportJob' => function ($query) {
                                $query->with(['LoadingHoursFrom', 'LoadingHoursTo']);
                            }
                        ])->orderBy('sl_global_id', 'desc');
                    }])
                    ->get();

                $split_data = [
                    'primary_linked_trans_split' => $primary_trans,
                    'linked_trans_split' => $linked_trans_split,
                ];
            }
        }

        $rules_with_approvals = $deal_ticket->getAppliedRules();
        $user_name = Auth::user()->name;
        $now = (Carbon::now()->tz('Africa/Johannesburg'))->toDateString();
        $app_version = env("APP_VERSION_REP", "1");


        $data = [
            'logo' => $logo,
            'pdfSettings' => $pdfSettings,
            'final_purchase_order'=>$final_purchase_order,
            'transport_trans'=>$transport_trans,
            'deal_ticket'=>$deal_ticket,
            'sales_order'=>$sales_order,
            'purchase_order'=>$purchase_order,
            'rules_with_approvals'=>$rules_with_approvals,
            'user_name'=>$user_name,
            'now'=>$now,
            'app_version'=>$app_version,
            'split_data'=>$split_data
        ];

        // Check if the load is split and set the orientation and view accordingly
        if ($transport_trans->is_split_load) {
            $pdf = PDF::loadView('pdf_reports.purchase_order_confirmation_split_v4', $data);
            $pdf->setPaper('A4','landscape');
        } else {
            $pdf = PDF::loadView('pdf_reports.purchase_order_confirmation_v3',$data);
            $pdf->setPaper('A4', 'portrait');
        }

        return $pdf->stream();

    }

    public function activate(Request $request): RedirectResponse
    {

        $user = Auth::user();
        $permissions = $user->getPermissionsViaRoles()->pluck('name');
        $can_approve = $permissions->contains('approve_deal_ticket');
        $transport_trans = TransportTransaction::find($request->transport_trans_id);
        $purchase_order = $transport_trans->PurchaseOrder;


        $request->validate([
            'is_active'=>['nullable','boolean',Rule::prohibitedIf(!$can_approve)],
        ],['is_active'=>'You need permissions to activate a Sales Order']);

        $is_updated = false;

        if ($can_approve){
            $is_updated = $purchase_order->update(['is_active' =>1]);
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        //
    }

    public function send(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $permissions = $user->getPermissionsViaRoles()->pluck('name');
        $can_approve = $permissions->contains('approve_deal_ticket');
        $transport_trans = TransportTransaction::find($request->transport_trans_id);
        $purchase_order = $transport_trans->PurchaseOrder;


        $request->validate([
            'is_active'=>['nullable','boolean',Rule::prohibitedIf(!$purchase_order->is_active)],
        ],['is_active'=>'Sales Order not Active']);

        $is_updated = false;

        if ($purchase_order->is_active){
            $is_updated = $purchase_order->update(['is_po_sent' =>1]);
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

    public function receive(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $permissions = $user->getPermissionsViaRoles()->pluck('name');
        $can_approve = $permissions->contains('approve_deal_ticket');
        $transport_trans = TransportTransaction::find($request->transport_trans_id);
        $purchase_order = $transport_trans->PurchaseOrder;


        $request->validate([
            'is_active'=>['nullable','boolean',Rule::prohibitedIf(!$purchase_order->is_active)],
        ],['is_active'=>'Sales Order not Active']);

        $is_updated = false;

        if ($purchase_order->is_active){
            $is_updated = $purchase_order->update(['is_po_received' =>1]);
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
    public function show(PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $purchaseOrder)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        //
    }
}
