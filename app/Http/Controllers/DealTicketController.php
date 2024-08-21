<?php

namespace App\Http\Controllers;

use App\Models\DealTicket;
use App\Models\DocumentStore;
use App\Models\TradeRule;
use App\Models\TransLinkSplit;
use App\Models\TransportApproval;
use App\Models\TransportTransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use ZipStream\Test\DataDescriptorTest;

class DealTicketController extends Controller
{


    public function viewPDF(Request $request, $id): \Illuminate\Http\Response
    {

        $final_deal_ticket = false;
        $path = 'images/pdflogo.jpg';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $file_data = file_get_contents($path);
        $logo = 'data:image/' . $type . ';base64,' . base64_encode($file_data);

        $transport_trans = TransportTransaction::where('id', $id)->with('ContractType')->with('Transporter')->with('Supplier',fn($query) => $query->with('TermsOfPayment'))
            ->with('Customer',fn($query) => $query->with('InvoiceBasis')->with('TermsOfPaymentBasis')->with('TermsOfPayment')->with('addressablePhysical'))
            ->with('TransportInvoice', fn($query) => $query->with('TransportInvoiceDetails'))
            ->with('TransportLoad',fn($query) => $query->with('ProductSource')->with('PackagingOutgoing')->with('CollectionAddress')
                ->with('DeliveryAddress')->with('DeliveryAddress_2')->with('BillingUnitsOutgoing')->with('ConfirmedByType'))
            ->with('DealTicket')->with('TransportFinance',fn($query) => $query->with('TransportRateBasis'))->first();

        $deal_ticket = $transport_trans->DealTicket;
        $sales_order = $transport_trans->SalesOrder;
        $purchase_order = $transport_trans->PurchaseOrder->load('ConfirmedByType');

        if (!($deal_ticket->is_active)){
            abort(403);
        }

        $rules_with_approvals = $deal_ticket->getAppliedRules();
        $user_name = Auth::user()->name;
        $now = (Carbon::now()->tz('Africa/Johannesburg'))->toDateString();
        $app_version = env("APP_VERSION_REP", "1");

        //check if split load

        $split_data = null;

        if ($transport_trans->is_split_load){

            $primary_linked_trans_split = TransLinkSplit::where('linked_transport_trans_id','=',$transport_trans->id)->with('TransportTransaction',fn($query) => $query->with('Customer')->with('Supplier')->with('Transporter')
                ->with('Product')->with('TransportFinance')->with('TransportLoad'))->first();

            $primary_trans = TransportTransaction::find($primary_linked_trans_split->transport_trans_id);


           // dd($primary_linked_trans_split->TransportTransaction);

            $linked_trans_split = null;

            if (isset($primary_linked_trans_split->transport_trans_id)){
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
            $is_transport_rate_same =true;
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

            if (true){

                foreach ($linked_trans_split as $trans) {

                    $transport_finance= $trans->TransportTransaction->TransportFinance;
                    $sum_weight_ton_incoming += $transport_finance->weight_ton_incoming_actual;
                    $sum_weight_ton_outgoing += $transport_finance->weight_ton_outgoing_actual;

                    $sum_weight_ton_incoming_planned += $transport_finance->weight_ton_incoming;
                    $sum_weight_ton_outgoing_planned += $transport_finance->weight_ton_outgoing;

                }

            }

            $primary_tran = TransportTransaction::find($primary_linked_trans_split->transport_trans_id);

            $split_data = [
                'primary_linked_trans_split'=>$primary_trans,
                'linked_trans_split'=>$linked_trans_split,
                'primary_trans'=>$primary_tran,
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


        $data = [
            'logo' => $logo,
            'final_deal_ticket'=>$final_deal_ticket,
            'transport_trans'=>$transport_trans,
            'deal_ticket'=>$deal_ticket,
            'rules_with_approvals'=>$rules_with_approvals,
            'user_name'=>$user_name,
            'now'=>$now,
            'app_version'=>$app_version,
            'sales_order'=>$sales_order,
            'purchase_order'=>$purchase_order,
            'split_data'=>$split_data
        ];

        $pdf = PDF::loadView('pdf_reports.deal_ticket_v3',$data);
        $pdf->setPaper('A4', 'portrait');

       return $pdf->stream();

    }

    public function generatePDF(Request $request): \Illuminate\Http\RedirectResponse
    {

        $final_deal_ticket = true;
        $path = 'images/pdflogo.jpg';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $file_data = file_get_contents($path);
        $logo = 'data:image/' . $type . ';base64,' . base64_encode($file_data);

        $transport_trans = TransportTransaction::where('id', $request->transport_trans_id)->with('ContractType')->with('Transporter')->with('Supplier',fn($query) => $query->with('TermsOfPayment'))->with('Customer',fn($query) => $query->with('InvoiceBasis')->with('addressablePhysical')->with('TermsOfPaymentBasis')->with('TermsOfPayment'))->with('TransportInvoice', fn($query) => $query->with('TransportInvoiceDetails'))
            ->with('TransportLoad',fn($query) => $query->with('ProductSource')->with('PackagingOutgoing')->with('CollectionAddress')->with('DeliveryAddress')->with('BillingUnitsOutgoing')->with('ConfirmedByType'))->with('DealTicket')->with('TransportFinance',fn($query) => $query->with('TransportRateBasis'))->first();

        $deal_ticket = $transport_trans->DealTicket;

      /*  if (!($deal_ticket->is_active)){
            abort(403);
        }*/

        $sales_order = $transport_trans->SalesOrder;
        $purchase_order = $transport_trans->PurchaseOrder->load('ConfirmedByType');

        if (false){
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Deal Ticket Already exists');
            return redirect()->back();
        } else{

        $rules_with_approvals = $deal_ticket->getAppliedRules();
        $user_name = Auth::user()->name;
        $now = (Carbon::now()->tz('Africa/Johannesburg'))->toDateString();
        $app_version = env("APP_VERSION_REP", "1");;


        $data = [
            'logo' => $logo,
            'final_deal_ticket'=>$final_deal_ticket,
            'transport_trans'=>$transport_trans,
            'deal_ticket'=>$deal_ticket,
            'rules_with_approvals'=>$rules_with_approvals,
            'user_name'=>$user_name,
            'now'=>$now,
            'app_version'=>$app_version,
            'sales_order'=>$sales_order,
            'purchase_order'=>$purchase_order
        ];

        $pdf = PDF::loadView('pdf_reports.deal_ticket_v3',$data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setEncryption($transport_trans->id);

        $date_stamp = time();
        $file_name = ':nam_MQ_Deal_Ticket_:dat.pdf';
        $file_name = str_ireplace(':nam',$transport_trans->id,$file_name);
        $file_name = str_ireplace(':dat',$date_stamp,$file_name);
        $filePdf = Storage::put('reports/mq/'.$file_name, $pdf->output());
        $url = Storage::url($file_name);
        //$url = asset($file_name);


        $document_store = DocumentStore::create([
            'transport_trans_id'=>$transport_trans->id,
            'report_type'=>'deal_ticket',
            'file_name'=>$file_name,
            'file_path'=>'/reports/mq/'.$file_name
        ]);

        $deal_ticket->report_path = $file_name;
        $deal_ticket->save();


        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Deal Ticket Created');

        return redirect()->back();

        }

    }

    public function downloadPDF($file_name): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return Storage::download('/reports/mq/'.$file_name);
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
    public function show(DealTicket $dealTicket)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DealTicket $dealTicket)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DealTicket $dealTicket)
    {

        $user = Auth::user();
        $roles = $user?->getRoleNames();
        $permissions = $user->getPermissionsViaRoles()->pluck('name');
        $dealTicket->calculateRules();

        $can_approve = $permissions->contains('approve_deal_ticket');


        $request->validate([
            'is_printed' => ['nullable', 'boolean'],
            'is_active'=>['nullable','boolean',Rule::prohibitedIf(!$can_approve)],
        ],
        ['is_active'=>'You need permissions to activate a deal ticket & must be approved!']);

        $is_updated = $dealTicket->update(
            ['is_active' =>$request->is_active]);



        if ($dealTicket->is_active){

            $transport_transaction = $dealTicket->TransportTransaction;
            if ($transport_transaction->a_mq == null){

                $max_a_mq = TransportTransaction::max("a_mq");
                $max_a_mq = max(20000,$max_a_mq);

                if (is_numeric($max_a_mq)){
                    $transport_transaction->max_a_mq=($max_a_mq+1);
                    $transport_transaction->save();
                }


            }
        }


        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Deal Ticket updated');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DealTicket $dealTicket)
    {
        //
    }

}
