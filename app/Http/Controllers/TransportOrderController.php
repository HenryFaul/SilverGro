<?php

namespace App\Http\Controllers;

use App\Models\TransLinkSplit;
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

        //check if split load

        $split_data = null;

        if ($transport_trans->is_split_load) {

            $primary_linked_trans_split = TransLinkSplit::where('linked_transport_trans_id', '=', $transport_trans->id)->with('TransportTransaction', fn($query) => $query->with('Customer')->with('Supplier')->with('Transporter')
                ->with('Product')->with('TransportFinance')->with('TransportLoad'))->first();

            $primary_trans = TransportTransaction::find($primary_linked_trans_split->transport_trans_id);

            $linked_trans_split = null;

            if (isset($primary_linked_trans_split->transport_trans_id)) {
                $linked_trans_split = TransLinkSplit::where('transport_trans_id', '=', $primary_linked_trans_split->transport_trans_id)
                    ->with(['TransportTransaction' => function ($query) {
                        $query->with([
                            'Customer',
                            'Supplier',
                            'Transporter',
                            'Product',
                            'TransportFinance',
                            'TransportLoad' => function ($query) {
                                $query->with(['BillingUnitsIncoming', 'BillingUnitsOutgoing']);
                            }
                        ])->orderBy('sl_global_id', 'desc');  // Ordering by sl_global_id
                    }])
                    ->get();
            }

            //One transporter, one supplier, multiple products, multiple customers

            $is_transporter_same = true;
            $is_supplier_same = true;
            $is_customer_same = true;
            $is_product_same = true;
            $is_product_billing_units_outgoing_same = true;
            $is_product_billing_units_incoming_same = true;
            $is_transport_rate_basis_same = true;
            $is_transport_rate_same = true;
            $sum_weight_ton_incoming = 0;
            $sum_weight_ton_outgoing = 0;

            $sum_weight_ton_incoming_planned = 0;
            $sum_weight_ton_outgoing_planned = 0;


            $first = $linked_trans_split->first();
            $first_transporter_id = $first->TransportTransaction->transporter_id;
            $first_supplier_id = $first->TransportTransaction->supplier_id;
            $first_customer_id = $first->TransportTransaction->customer_id;
            $first_product_id = $first->TransportTransaction->product_id;
            $first_product_billing_unit_outgoing_id = $first->TransportTransaction->TransportLoad->BillingUnitsOutgoing->id;
            $first_product_billing_unit_incoming_id = $first->TransportTransaction->TransportLoad->BillingUnitsIncoming->id;
            $first_transport_rate_basis_id = $first->TransportTransaction->TransportJob->transport_rate_basis_id;
            $first_transport_rate = $first->TransportTransaction->TransportFinance->transport_rate;

            foreach ($linked_trans_split as $split) {
                if ($split->TransportTransaction->transporter_id !== $first_transporter_id) {
                    $is_transporter_same = false;
                }
                if ($split->TransportTransaction->supplier_id !== $first_supplier_id) {
                    $is_supplier_same = false;
                }
                if ($split->TransportTransaction->customer_id !== $first_customer_id) {
                    $is_customer_same = false;
                }
                if ($split->TransportTransaction->product_id !== $first_product_id) {
                    $is_product_same = false;
                }

                if ($split->TransportTransaction->TransportLoad->BillingUnitsOutgoing->id !== $first_product_billing_unit_outgoing_id) {
                    $is_product_billing_units_outgoing_same = false;
                }

                if ($split->TransportTransaction->TransportLoad->BillingUnitsIncoming->id !== $first_product_billing_unit_incoming_id) {
                    $is_product_billing_units_incoming_same = false;
                }

                if ($split->TransportTransaction->TransportJob->transport_rate_basis_id !== $first_transport_rate_basis_id) {
                    $is_transport_rate_basis_same = false;
                }

                if ($split->TransportTransaction->TransportFinance->transport_rate !== $first_transport_rate) {
                    $is_transport_rate_same = false;
                }

                // Break early if all flags are already false
                if (!$is_transporter_same && !$is_supplier_same && !$is_customer_same && !$is_product_same && !$is_product_billing_units_outgoing_same && !$is_product_billing_units_incoming_same && !$is_transport_rate_basis_same && !$is_transport_rate_same) {
                    break;
                }
            }

            if (true) {

                foreach ($linked_trans_split as $trans) {

                    $transport_finance = $trans->TransportTransaction->TransportFinance;
                    $sum_weight_ton_incoming += $transport_finance->weight_ton_incoming_actual;
                    $sum_weight_ton_outgoing += $transport_finance->weight_ton_outgoing_actual;

                    $sum_weight_ton_incoming_planned += $transport_finance->weight_ton_incoming;
                    $sum_weight_ton_outgoing_planned += $transport_finance->weight_ton_outgoing;

                }

            }

            $primary_tran = TransportTransaction::find($primary_linked_trans_split->transport_trans_id);

            $split_data = [
                'primary_linked_trans_split' => $primary_trans,
                'linked_trans_split' => $linked_trans_split,
                'primary_trans' => $primary_tran,
                'is_transporter_same' => $is_transporter_same,
                'is_supplier_same' => $is_supplier_same,
                'is_customer_same' => $is_customer_same,
                'is_product_same' => $is_product_same,
                'is_product_billing_units_outgoing_same' => $is_product_billing_units_outgoing_same,
                'is_product_billing_units_incoming_same' => $is_product_billing_units_incoming_same,
                'is_transport_rate_basis_same' => $is_transport_rate_basis_same,
                'is_transport_rate_same' => $is_transport_rate_same,
                'sum_weight_ton_incoming' => $sum_weight_ton_incoming,
                'sum_weight_ton_outgoing' => $sum_weight_ton_outgoing,
                'sum_weight_ton_incoming_planned' => $sum_weight_ton_incoming_planned,
                'sum_weight_ton_outgoing_planned' => $sum_weight_ton_outgoing_planned,
            ];

        }


        $rules_with_approvals = $deal_ticket->getAppliedRules();
        $user_name = Auth::user()->name;
        $now = (Carbon::now()->tz('Africa/Johannesburg'))->toDateTimeLocalString();
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
            'transport_order'=>$transport_order,
            'split_data'=>$split_data
        ];


        // Check if the load is split and set the orientation accordingly
        if ($transport_trans->is_split_load) {

            $pdf = PDF::loadView('pdf_reports.transport_order_confirmation_split_v2',$data);
            $pdf->setPaper('A4', 'landscape');

        } else {
            $pdf = PDF::loadView('pdf_reports.transport_order_confirmation_v3',$data);
            $pdf->setPaper('A4', 'portrait');
        }

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
        ],['is_active'=>'You need permissions to activate a Transport Order']);

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
