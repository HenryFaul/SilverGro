<?php

namespace App\Http\Controllers;

use App\Models\DealTicket;
use App\Models\TradeRule;
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

        $transport_trans = TransportTransaction::where('id', $id)->with('ContractType')->with('Transporter')->with('Supplier',fn($query) => $query->with('TermsOfPayment'))->with('Customer',fn($query) => $query->with('InvoiceBasis')->with('TermsOfPaymentBasis')->with('TermsOfPayment'))->with('TransportInvoice', fn($query) => $query->with('TransportInvoiceDetails'))
            ->with('TransportLoad',fn($query) => $query->with('ProductSource')->with('PackagingOutgoing')->with('CollectionAddress')->with('DeliveryAddress')->with('BillingUnitsOutgoing')->with('ConfirmedByType'))->with('DealTicket')->with('TransportFinance',fn($query) => $query->with('TransportRateBasis'))->first();

        $deal_ticket = $transport_trans->DealTicket;

        if (!($deal_ticket->is_active)){
            abort(403);
        }

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
            'app_version'=>$app_version
        ];

        $pdf = PDF::loadView('pdf_reports.deal_ticket',$data);
        $pdf->setPaper('A4', 'portrait');

       return $pdf->stream();

    }

    public function generatePDF(Request $request)
    {

        $final_deal_ticket = true;
        $path = 'images/pdflogo.jpg';
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $file_data = file_get_contents($path);
        $logo = 'data:image/' . $type . ';base64,' . base64_encode($file_data);

        $transport_trans = TransportTransaction::where('id', $request->transport_trans_id)->with('ContractType')->with('Transporter')->with('Supplier',fn($query) => $query->with('TermsOfPayment'))->with('Customer',fn($query) => $query->with('InvoiceBasis')->with('TermsOfPaymentBasis')->with('TermsOfPayment'))->with('TransportInvoice', fn($query) => $query->with('TransportInvoiceDetails'))
            ->with('TransportLoad',fn($query) => $query->with('ProductSource')->with('PackagingOutgoing')->with('CollectionAddress')->with('DeliveryAddress')->with('BillingUnitsOutgoing')->with('ConfirmedByType'))->with('DealTicket')->with('TransportFinance',fn($query) => $query->with('TransportRateBasis'))->first();

        $deal_ticket = $transport_trans->DealTicket;

        if (!($deal_ticket->is_active)){
            abort(403);
        }

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
            'app_version'=>$app_version
        ];

        $pdf = PDF::loadView('pdf_reports.deal_ticket',$data);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setEncryption($transport_trans->id);

        $date_stamp = time();
        $file_name = ':nam_MQ_Deal_Ticket_:dat.pdf';
        $file_name = str_ireplace(':nam',$transport_trans->id,$file_name);
        $file_name = str_ireplace(':dat',$date_stamp,$file_name);
        $filePdf = Storage::put('reports/mq/'.$file_name, $pdf->output());
        $url = asset($file_name);
        dd($url);

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
