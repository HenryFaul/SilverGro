<?php

namespace App\Http\Controllers;

use App\Models\TransLink;
use App\Models\TransportTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeOverviewController extends Controller
{
    public function index(Request $request)
    {
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

        $filters['contract_type_id'] = 4;

        $paginate = $request->get('show', 25);

        $start_date = Carbon::now()->tz('Africa/Johannesburg')->startOfMonth()->toDateString();
        $end_date = Carbon::now()->tz('Africa/Johannesburg')->toDateString();

        $transactions = TransportTransaction::with([
            'ContractType:id,name',
            'Customer:id,name',
            'Supplier:id,name',
            'Transporter:id,name',
            'Product:id,name',
        ])
            ->index($filters)
            ->orderBy('transport_date_earliest', 'desc')
            ->paginate($paginate)
            ->withQueryString();

        $first_transaction_id = TransportTransaction::index($filters)->pluck('id')->first();
        $selected_trans_id = $request->get('selected_trans_id', $first_transaction_id);

        if ($selected_trans_id != null) {
            $transportTransaction = TransportTransaction::where('id', $selected_trans_id)
                ->with([
                    'ContractType',
                    'TransportInvoice.TransportInvoiceDetails',
                    'TransportLoad',
                    'DealTicket',
                    'Supplier.TermsOfPayment',
                    'Customer.TermsOfPayment.InvoiceBasis',
                    'Product',
                    'Customer_2.TermsOfPayment.InvoiceBasis',
                    'Customer_3.TermsOfPayment.InvoiceBasis',
                    'Customer_4.TermsOfPayment.InvoiceBasis',
                    'Customer_5.TermsOfPayment.InvoiceBasis',
                    'TransportFinance',
                    'TransportStatus.StatusEntity.StatusType',
                    'AssignedUserComm.AssignedUserSupplier.AssignedUserCustomer',
                    'TransportJob.OffloadingHoursFrom.OffloadingHoursTo.TransportDriverVehicle.Driver.Vehicle.VehicleType',
                ])->first();

            $deal_ticket = $transportTransaction?->DealTicket;
            $purchase_order = $transportTransaction?->PurchaseOrder;
            $transport_order = $transportTransaction?->TransportOrder;
            $sales_order = $transportTransaction?->SalesOrder;

            $rules_with_approvals = null;

            if ($deal_ticket != null) {
                $deal_ticket->calculateRules();
                $rules_with_approvals = $deal_ticket->getAppliedRules();
            }

            $linked_trans_other = TransLink::where('transport_trans_id', $transportTransaction->id)
                ->where('trans_link_type_id', 3)
                ->with('TransportTransaction.Customer.Supplier.Transporter.Product.TransportFinance.TransportLoad')
                ->get();

            // Overview Stats
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

            $trans_data = TransportTransaction::where('include_in_calculations', 1)
                ->where('contract_type_id', 4)
                ->index($filters)
                ->with('TransportFinance:id,weight_ton_incoming_actual,weight_ton_outgoing_actual,cost_price,transport_cost,selling_price,additional_cost_1,additional_cost_2,additional_cost_3,adjusted_gp')
                ->get();

            foreach ($trans_data as $trans) {
                $finance = $trans->TransportFinance;

                $weight_uploaded += $finance->weight_ton_incoming_actual;
                $weight_offloaded += $finance->weight_ton_outgoing_actual;
                $planned_tons_in += $finance->weight_ton_incoming;
                $planned_tons_out += $finance->weight_ton_outgoing;
                $cost_price += $finance->cost_price;
                $trans_cost += $finance->transport_cost;
                $selling_price += $finance->selling_price;
                $other_costs += $finance->additional_cost_1 + $finance->additional_cost_2 + $finance->additional_cost_3 + $finance->adjusted_gp;
            }

            $gp = $selling_price - $cost_price - $trans_cost - $other_costs;
            $gp_perc = $selling_price > 0 ? (round($gp) / round($selling_price)) * 100 : 0;

            return inertia('HomeOverview/Index', [
                'filters' => $filters,
                'transactions' => $transactions,
                'selected_transaction' => $transportTransaction,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'rules_with_approvals' => $rules_with_approvals,
                'deal_ticket' => $deal_ticket,
                'transport_order' => $transport_order,
                'purchase_order' => $purchase_order,
                'sales_order' => $sales_order,
                'linked_trans_other' => $linked_trans_other,
                'planned_tons_in' => round($planned_tons_in, 4),
                'planned_tons_out' => round($planned_tons_out, 4),
                'weight_uploaded' => round($weight_uploaded, 4),
                'weight_offloaded' => round($weight_offloaded, 4),
                'cost_price' => round($cost_price, 2),
                'trans_cost' => round($trans_cost, 2),
                'other_costs' => round($other_costs, 2),
                'selling_price' => round($selling_price, 2),
                'gp' => round($gp, 2),
                'gp_perc' => round($gp_perc),
            ]);
        }

        return inertia('HomeOverview/Index', [
            'filters' => null,
            'transactions' => null,
            'selected_transaction' => null,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'rules_with_approvals' => null,
            'deal_ticket' => null,
            'transport_order' => null,
            'purchase_order' => null,
            'sales_order' => null,
            'linked_trans_other' => null,
            'planned_tons_in' => 0,
            'planned_tons_out' => 0,
            'weight_uploaded' => 0,
            'weight_offloaded' => 0,
            'cost_price' => 0,
            'trans_cost' => 0,
            'other_costs' => 0,
            'selling_price' => 0,
            'gp' => 0,
            'gp_perc' => 0,
        ]);
    }
}
