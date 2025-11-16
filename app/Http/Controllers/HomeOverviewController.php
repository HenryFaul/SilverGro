<?php

namespace App\Http\Controllers;

use App\Models\ContractType;
use App\Models\TransLink;
use App\Models\TransportTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeOverviewController extends Controller
{
    public function index(Request $request)
    {

        $contract_types = ContractType::all();
        //pc = 2
        //sc = 3
        //mq = 4

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

        //$filters['contract_type_id'] = 4;

        $paginate = $request['show'] ?? 25;

        $start_date = (Carbon::now()->tz('Africa/Johannesburg')->startOfMonth())->toDateString();
        $end_date = (Carbon::now()->tz('Africa/Johannesburg'))->toDateString();

        $linked_trans = null;

        $linked_trans_other = null;

        $transactions = TransportTransaction::with('ContractType')->with('Customer')->with('Customer_2')->with('Customer_3')->with('Customer_4')->with('Supplier')->with('Transporter')->with('Product')->with('TransportFinance')
            ->with('TransportLoad')->with('DealTicket')->with('PurchaseOrder')->with('SalesOrder')->with('TransportOrder')->with('TransportInvoice')
            ->with('TransportStatus', fn($query) => $query->with('StatusEntity')->with('StatusType'))
            ->with('TransportDriverVehicle', fn($query) => $query->with('Vehicle'))
            ->index($filters)
            ->orderBy('transport_date_earliest', 'desc')
            ->paginate($paginate)
            ->withQueryString();

        $first_transaction_id = TransportTransaction::with('ContractType')->with('Customer')->with('Customer_2')->with('Customer_3')->with('Customer_4')->with('Supplier')->with('Transporter')->with('Product')
            ->index($filters)->pluck('id')->first();

        $selected_trans_id = $request['selected_trans_id'] ?? $first_transaction_id;

        if ($selected_trans_id != null){

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

            if ($transportTransaction != null){

                $linked_trans_other = TransLink::where('transport_trans_id','=',$transportTransaction->id)->where('trans_link_type_id',3)->with('TransportTransaction',fn($query) => $query->with('Customer')->with('Supplier')->with('Transporter')
                    ->with('Product')->with('TransportFinance')->with('TransportLoad'))->get();
            }

            //Overview Stats
            $planned_tons_in = 0;
            $planned_tons_out = 0;
            $weight_uploaded = 0;
            $weight_offloaded = 0;
            $cost_price = 0;
            $trans_cost = 0;
            $other_costs = 0;
            $selling_price = 0;
            $gp = 0;
            $gp_perc = 0;
            $no_trades = 0;

            $trans_data = TransportTransaction::where('include_in_calculations', '=', 1)->where('contract_type_id', '=', 4)->index($filters)->with('TransportFinance')->with('TransportDriverVehicle')->with('TransportJob')->with('Customer')->with('Transporter')->get();

            $no_trades = count($trans_data);

            foreach ($trans_data as $trans) {
                $transport_finance = $trans->TransportFinance;

                $cur_weight_uploaded=$transport_finance->weight_ton_incoming_actual;
                $cur_weight_offloaded=$transport_finance->weight_ton_outgoing_actual;

                $weight_uploaded += $cur_weight_uploaded;
                $weight_offloaded += $cur_weight_offloaded;
                $planned_tons_in += $transport_finance->weight_ton_incoming;
                $planned_tons_out += $transport_finance->weight_ton_outgoing;

                $cost_price+=$transport_finance->cost_price;
                $trans_cost+=$transport_finance->transport_cost;
                $selling_price+=$transport_finance->selling_price;

                $other_costs += ($transport_finance->additional_cost_1 + $transport_finance->additional_cost_2 + $transport_finance->additional_cost_3 + $transport_finance->adjusted_gp);

            }

            $gp = $selling_price - $cost_price - $trans_cost - $other_costs;
            if ($selling_price > 0) {
                $gp_perc = ( round($gp) / round($selling_price)) * 100;
            }



            return inertia(
                'HomeOverview/Index',
                [
                    'filters' => $filters,
                    'contract_types' => $contract_types,
                    'transactions' => $transactions,
                    'selected_transaction'=>$transportTransaction,
                    'start_date'=>$start_date,
                    'end_date'=>$end_date,
                    'rules_with_approvals'=>$rules_with_approvals,
                    'deal_ticket'=>$deal_ticket,
                    'transport_order'=>$transport_order,
                    'purchase_order'=>$purchase_order,
                    'sales_order'=>$sales_order,
                    'linked_trans_other'=>$linked_trans_other,
                    'planned_tons_in'=> round($planned_tons_in,4),
                    'planned_tons_out'=> round($planned_tons_out,4),
                    'weight_uploaded'=> round($weight_uploaded,4),
                    'weight_offloaded'=> round($weight_offloaded,4),
                    'cost_price'=> round($cost_price,2),
                    'trans_cost'=> round($trans_cost,2),
                    'other_costs'=> round($other_costs,2),
                    'selling_price'=> round($selling_price,2),
                    'gp'=> round($gp,2),
                    'gp_perc'=> round($gp_perc),
                    'no_trades'=>$no_trades,

                ]
            );

        } else {

            return inertia(
                'HomeOverview/Index',
                [
                    'filters' => null,
                    'transactions' => null,
                    'selected_transaction'=>null,
                    'start_date'=>$start_date,
                    'end_date'=>$end_date,
                    'rules_with_approvals'=>null,
                    'deal_ticket'=>null,
                    'transport_order'=>null,
                    'purchase_order'=>null,
                    'sales_order'=>null,
                    'linked_trans_other'=>null,
                    'planned_tons_in'=> 0,
                    'planned_tons_out'=> 0,
                    'weight_uploaded'=> 0,
                    'weight_offloaded'=> 0,
                    'cost_price'=> 0,
                    'trans_cost'=> 0,
                    'other_costs'=> 0,
                    'selling_price'=> 0,
                    'gp'=> 0,
                    'gp_perc'=> 0,
                    'no_trades'=>0,

                ]
            );

        }





    }
}
