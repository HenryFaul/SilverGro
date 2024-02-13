<?php

namespace App\Http\Controllers;

use App\Models\TransLink;
use App\Models\TransportTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScOverviewController extends Controller
{
    //

    public function index(Request $request)
    {

        //pc = 2
        //sc = 3

        $filters = $request->only([
            'supplier_name',
            'customer_name',
            'transporter_name',
            'product_name',
            'show',
            'start_date',
            'end_date',
            'contract_type_id',
            'id',
            'selected_trans_id',
            'old_id'
        ]);

        $filters['contract_type_id'] = 3;

        $paginate = $request['show'] ?? 5;

        $transactions = TransportTransaction::with('ContractType')->with('Customer')->with('Customer_2')->with('Customer_3')->with('Customer_4')->with('Supplier')->with('Transporter')->with('Product')->with('TransportFinance')
            ->with('TransportLoad')->with('DealTicket')->with('PurchaseOrder')->with('SalesOrder')->with('TransportOrder')->with('TransportInvoice')
            ->index($filters)
            ->orderBy('transport_date_earliest', 'desc')
            ->paginate($paginate)
            ->withQueryString();

        $first_transaction_id = TransportTransaction::with('ContractType')->with('Customer')->with('Customer_2')->with('Customer_3')->with('Customer_4')->with('Supplier')->with('Transporter')->with('Product')
            ->index($filters)->pluck('id')->first();

        $selected_trans_id = $request['selected_trans_id'] ?? $first_transaction_id;

        $transportTransaction = TransportTransaction::where('id', $selected_trans_id)->with('ContractType')->with('TransportInvoice', fn($query) => $query->with('TransportInvoiceDetails'))
            ->with('TransportLoad')->with('DealTicket')->with('Supplier',fn($query) => $query->with('TermsOfPayment'))
            ->with('Customer',fn($query) => $query->with('TermsOfPayment')->with('InvoiceBasis'))->with('Product')
            ->with('Customer_2',fn($query) => $query->with('TermsOfPayment')->with('InvoiceBasis'))->with('Product')
            ->with('Customer_3',fn($query) => $query->with('TermsOfPayment')->with('InvoiceBasis'))->with('Product')
            ->with('Customer_4',fn($query) => $query->with('TermsOfPayment')->with('InvoiceBasis'))->with('Product')
            ->with('Customer_5',fn($query) => $query->with('TermsOfPayment')->with('InvoiceBasis'))->with('Product')
            ->with('TransportFinance')->with('TransportStatus', fn($query) => $query->with('StatusEntity')->with('StatusType'))->with('AssignedUserComm', fn($query) => $query->with('AssignedUserSupplier')->with('AssignedUserCustomer'))
            ->with('TransportJob', fn($query) => $query->with('OffloadingHoursFrom')->with('OffloadingHoursTo')
                ->with('TransportDriverVehicle', fn($query) => $query->with('Driver')->with('Vehicle', fn($query) => $query->with('VehicleType'))))->first();



        $deal_ticket = $transportTransaction?->DealTicket;
        $purchase_order = $transportTransaction?->PurchaseOrder;
        $transport_order = $transportTransaction?->TransportOrder;
        $sales_order = $transportTransaction?->SalesOrder;

        $rules_with_approvals = null;

        if ($deal_ticket != null){
            $deal_ticket->calculateRules();
            $rules_with_approvals = $deal_ticket->getAppliedRules();
        }


        $start_date = (Carbon::now()->tz('Africa/Johannesburg')->startOfMonth())->toDateString();
        $end_date = (Carbon::now()->tz('Africa/Johannesburg'))->toDateString();



        $linked_trans = null;

        if ($transportTransaction != null){


            /*   1     •	sc_to_pc
            •	2 pc_to_sc
            • 3	mq_to_pc
            • 4	mq_to_sc
*/
            $linked_trans_other = TransLink::where('transport_trans_id','=',$transportTransaction->id)->where('trans_link_type_id',3)->with('TransportTransaction',fn($query) => $query->with('Customer')->with('Supplier')->with('Transporter')
                ->with('Product')->with('TransportFinance')->with('TransportLoad'))->get();

            // dd($linked_trans_other);

        }


        return inertia(
            'ScOverview/Index',
            [
                'filters' => $filters,
                'transactions' => $transactions,
                'selected_transaction'=>$transportTransaction,
                'start_date'=>$start_date,
                'end_date'=>$end_date,
                'rules_with_approvals'=>$rules_with_approvals,
                'deal_ticket'=>$deal_ticket,
                'transport_order'=>$transport_order,
                'purchase_order'=>$purchase_order,
                'sales_order'=>$sales_order,
                'linked_trans_other'=>$linked_trans_other

            ]
        );


    }
}
