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

        $paginate = $request->input('show', 25);

        // Fetch paginated transactions with minimal relationships
        $transactions = TransportTransaction::with(['ContractType', 'Customer', 'Supplier', 'Product'])
            ->index($filters)
            ->orderBy('transport_date_earliest', 'desc')
            ->paginate($paginate)
            ->withQueryString();

        // Fetch first transaction ID
        $first_transaction_id = TransportTransaction::index($filters)->pluck('id')->first();
        $selected_trans_id = $request->input('selected_trans_id', $first_transaction_id);

        // Fetch detailed information for the selected transaction
        $transportTransaction = TransportTransaction::where('id', $selected_trans_id)
            ->with([
                'ContractType',
                'TransportInvoice.TransportInvoiceDetails',
                'TransportLoad',
                'Supplier.TermsOfPayment',
                'Customer.TermsOfPayment',
                'Customer.InvoiceBasis',
                'Product',
                'TransportFinance',
                'TransportStatus.StatusEntity',
                'TransportStatus.StatusType',
                'AssignedUserComm.AssignedUserSupplier',
                'AssignedUserComm.AssignedUserCustomer',
                'TransportJob.OffloadingHoursFrom',
                'TransportJob.OffloadingHoursTo',
                'TransportJob.TransportDriverVehicle.Driver',
                'TransportJob.TransportDriverVehicle.Vehicle.VehicleType',
            ])
            ->first();

        $deal_ticket = $transportTransaction?->DealTicket;
        $purchase_order = $transportTransaction?->PurchaseOrder;
        $transport_order = $transportTransaction?->TransportOrder;
        $sales_order = $transportTransaction?->SalesOrder;

        $rules_with_approvals = null;

        if ($deal_ticket) {
            $deal_ticket->calculateRules();
            $rules_with_approvals = $deal_ticket->getAppliedRules();
        }

        $start_date = Carbon::now()->tz('Africa/Johannesburg')->startOfMonth()->toDateString();
        $end_date = Carbon::now()->tz('Africa/Johannesburg')->toDateString();

        $linked_trans_other = $transportTransaction
            ? TransLink::where('transport_trans_id', $transportTransaction->id)
                ->where('trans_link_type_id', 3)
                ->with(['TransportTransaction.Customer', 'TransportTransaction.Supplier', 'TransportTransaction.Transporter', 'TransportTransaction.Product'])
                ->get()
            : null;

        // Overview Stats
        $trans_data = TransportTransaction::where('include_in_calculations', 1)
            ->where('contract_type_id', 4)
            ->index($filters)
            ->with(['TransportFinance'])
            ->get();

        $overviewStats = $this->calculateOverviewStats($trans_data);

        return inertia('HomeOverview/Index', array_merge([
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
        ], $overviewStats));
    }

    private function calculateOverviewStats($trans_data)
    {
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

        foreach ($trans_data as $trans) {
            $transport_finance = $trans->TransportFinance;

            $cur_weight_uploaded = $transport_finance->weight_ton_incoming_actual;
            $cur_weight_offloaded = $transport_finance->weight_ton_outgoing_actual;

            $weight_uploaded += $cur_weight_uploaded;
            $weight_offloaded += $cur_weight_offloaded;
            $planned_tons_in += $transport_finance->weight_ton_incoming;
            $planned_tons_out += $transport_finance->weight_ton_outgoing;

            $cost_price += $transport_finance->cost_price;
            $trans_cost += $transport_finance->transport_cost;
            $selling_price += $transport_finance->selling_price;

            $other_costs += $transport_finance->additional_cost_1 +
                $transport_finance->additional_cost_2 +
                $transport_finance->additional_cost_3 +
                $transport_finance->adjusted_gp;
        }

        $gp = $selling_price - $cost_price - $trans_cost - $other_costs;
        if ($selling_price > 0) {
            $gp_perc = (round($gp) / round($selling_price)) * 100;
        }

        return [
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
            'no_trades' => count($trans_data),
        ];
    }
}
