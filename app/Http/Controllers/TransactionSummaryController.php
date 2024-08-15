<?php

namespace App\Http\Controllers;

use App\Models\BillingUnits;
use App\Models\ConfirmationTypes;
use App\Models\ContractType;
use App\Models\Customer;
use App\Models\CustomerParent;
use App\Models\InvoiceStatus;
use App\Models\LoadingHourOption;
use App\Models\Packaging;
use App\Models\Product;
use App\Models\ProductSource;
use App\Models\RegularDriver;
use App\Models\RegularVehicle;
use App\Models\Staff;
use App\Models\StatusEntity;
use App\Models\StatusType;
use App\Models\Supplier;
use App\Models\TermsOfPayment;
use App\Models\TransactionSummary;
use App\Models\TransLink;
use App\Models\TransLinkSplit;
use App\Models\Transporter;
use App\Models\TransportRateBasis;
use App\Models\TransportTransaction;
use App\Models\VehicleType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class TransactionSummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        // error_log('Some message here.');

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
            'old_id',
            'a_mq'
        ]);

        $paginate = $request['show'] ?? 25;

        $transactions = TransportTransaction::with('ContractType')->with('Customer')->with('Customer_2')->with('Customer_3')->with('Customer_4')->with('Supplier')->with('Transporter')->with('Product')->with('TransportFinance')
            ->with('TransportLoad')->with('DealTicket')->with('PurchaseOrder')->with('SalesOrder')->with('TransportOrder')->with('TransportInvoice')
            ->index($filters)
            ->orderBy('transport_date_earliest', 'desc')
            ->paginate($paginate)
            ->withQueryString();

        $first_transaction_id = TransportTransaction::with('ContractType')->with('Customer')->with('Customer_2')->with('Customer_3')->with('Customer_4')->with('Supplier')->with('Transporter')->with('Product')
            ->index($filters)->pluck('id')->last();

        $selected_trans_id = $request['selected_trans_id'] ?? $first_transaction_id;

        $transportTransaction = TransportTransaction::where('id', $selected_trans_id)->with('ContractType')->with('TransportInvoice', fn($query) => $query->with('TransportInvoiceDetails'))
            ->with('TransportLoad')->with('DealTicket')->with('Supplier', fn($query) => $query->with('TermsOfPayment')->with('contactable', fn($query) => $query->with('numberable')->with('emailable')))
            ->with('Customer', fn($query) => $query->with('contactable', fn($query) => $query->with('numberable')->with('emailable'))->with('TermsOfPayment')->with('InvoiceBasis'))->with('Product')
            ->with('Customer_2', fn($query) => $query->with('TermsOfPayment')->with('InvoiceBasis'))->with('Product')
            ->with('Customer_3', fn($query) => $query->with('TermsOfPayment')->with('InvoiceBasis'))->with('Product')
            ->with('Customer_4', fn($query) => $query->with('TermsOfPayment')->with('InvoiceBasis'))->with('Product')
            ->with('Customer_5', fn($query) => $query->with('TermsOfPayment')->with('InvoiceBasis'))->with('Product')
            ->with('TransportFinance')->with('Transporter', fn($query) => $query->with('contactable', fn($query) => $query->with('numberable')->with('emailable')))
            ->with('TransportStatus', fn($query) => $query->with('StatusEntity')->with('StatusType'))->with('AssignedUserComm', fn($query) => $query->with('AssignedUserSupplier')->with('AssignedUserCustomer'))
            ->with('TransportJob', fn($query) => $query->with('OffloadingHoursFrom')->with('OffloadingHoursTo')
                ->with('TransportDriverVehicle', fn($query) => $query->with('Driver')->with('Vehicle', fn($query) => $query->with('VehicleType'))))->first();


        $deal_ticket = $transportTransaction?->DealTicket;
        $purchase_order = $transportTransaction?->PurchaseOrder;
        $transport_order = $transportTransaction?->TransportOrder;
        $sales_order = $transportTransaction?->SalesOrder;
        $model_activity = Activity::where('subject_type', 'App\Models\TransportTransaction')->where('subject_id', $transportTransaction?->id)->get();

        $rules_with_approvals = null;

        if ($deal_ticket != null) {
            $deal_ticket->calculateRules();
            $rules_with_approvals = $deal_ticket->getAppliedRules();
        }


        $start_date = (Carbon::now()->tz('Africa/Johannesburg')->startOfMonth())->toDateString();
        $end_date = (Carbon::now()->tz('Africa/Johannesburg'))->toDateString();

        $customers = Customer::with('staff')->with('addressable')->with('contactable')->orderby('last_legal_name', 'asc')->get();
        $customer_parents = CustomerParent::with('staff')->with('addressable')->with('contactable')->orderby('last_legal_name', 'asc')->get();

        $suppliers = Supplier::with('addressable')->orderby('last_legal_name', 'asc')->get();
        $transporters = Transporter::orderby('last_legal_name', 'asc')->get();
        $contract_types = ContractType::all();
        $products = Product::all();
        $staff = Staff::all();
        $confirmation_types = ConfirmationTypes::all();
        $product_sources = ProductSource::all();
        $packaging = Packaging::all();
        $billing_units = BillingUnits::all();
        $loading_hour_options = LoadingHourOption::all();
        $all_drivers = RegularDriver::all();
        $all_vehicles = RegularVehicle::with('VehicleType')->get();
        $all_transport_rates = TransportRateBasis::all();
        $all_status_entities = StatusEntity::all();
        $all_status_types = StatusType::all();
        $all_invoice_statuses = InvoiceStatus::all();

        $all_terms_of_payments = TermsOfPayment::all();
        $all_vehicle_types = VehicleType::all();


        $linked_trans = null;

        if ($transportTransaction != null) {


            /*   1     •	sc_to_pc
            •	2 pc_to_sc
            • 3	mq_to_pc
            • 4	mq_to_sc
*/

            $linked_trans_sc = TransLink::where('linked_transport_trans_id', '=', $transportTransaction->id)->where('trans_link_type_id', '=', 4)->with('TransportTransactionPc', fn($query) => $query->with('Customer')->with('Supplier')->with('Transporter')
                ->with('Product')->with('TransportFinance')->with('TransportLoad'))->get();
            $linked_trans_pc = TransLink::where('linked_transport_trans_id', '=', $transportTransaction->id)->where('trans_link_type_id', '=', 3)->with('TransportTransactionPc', fn($query) => $query->with('Customer')->with('Supplier')->with('Transporter')
                ->with('Product')->with('TransportFinance')->with('TransportLoad'))->get();
            $linked_trans_other = TransLink::where('transport_trans_id', '=', $transportTransaction->id)->with('TransportTransaction', fn($query) => $query->with('Customer')->with('Supplier')->with('Transporter')
                ->with('Product')->with('TransportFinance')->with('TransportLoad'))->get();

            //linked_trans_split


            $is_primary_split = $transportTransaction->is_split_load_primary;

            $primary_linked_trans_split = TransLinkSplit::where('linked_transport_trans_id', '=', $transportTransaction->id)->with('TransportTransaction', fn($query) => $query->with('Customer')->with('Supplier')->with('Transporter')
                ->with('Product')->with('TransportFinance')->with('TransportLoad'))->first();


            $linked_trans_split = null;
            $primary_trans = null;
            $split_totals = [];

            if (isset($primary_linked_trans_split->transport_trans_id)) {

                $primary_trans = TransportTransaction::find($primary_linked_trans_split->transport_trans_id);

                // $linked_trans_split = TransLinkSplit::where('transport_trans_id','=',$primary_linked_trans_split->transport_trans_id)->orWhere('linked_transport_trans_id','=',$primary_linked_trans_split->transport_trans_id)->with('TransportTransaction',fn($query) => $query->with('Customer')->with('Supplier')->with('Transporter')
                //    ->with('Product')->with('TransportFinance')->with('TransportLoad'))->get();

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
                        ])->orderBy('sl_global_id', 'asc');  // Ordering by sl_global_id
                    }])
                    ->get();

                $transport_load_no_units_incoming = 0;
                $transport_load_no_units_outgoing = 0;
                $transport_finance_weight_ton_incoming_actual = 0;
                $transport_finance_weight_ton_outgoing_actual = 0;
                $transport_finance_gross_profit_per_ton = 0;

                foreach ($linked_trans_split as $trans) {

                    $transport_load_no_units_incoming +=  $trans->TransportTransaction->TransportLoad->no_units_incoming;
                    $transport_load_no_units_outgoing += $trans->TransportTransaction->TransportLoad->no_units_outgoing;
                    $transport_finance_weight_ton_incoming_actual += $trans->TransportTransaction->TransportFinance->weight_ton_incoming_actual;
                    $transport_finance_weight_ton_outgoing_actual += $trans->TransportTransaction->TransportFinance->weight_ton_outgoing_actual;
                    $transport_finance_gross_profit_per_ton += $trans->TransportTransaction->TransportFinance->gross_profit_per_ton;
                }


                $split_totals = ['transport_load_no_units_incoming' => round($transport_load_no_units_incoming,2),
                    'transport_load_no_units_outgoing' => round($transport_load_no_units_outgoing,2),
                    'transport_finance_weight_ton_incoming_actual' => round($transport_finance_weight_ton_incoming_actual,2),
                    'transport_finance_weight_ton_outgoing_actual'=>round($transport_finance_weight_ton_outgoing_actual,2),
                    'transport_finance_gross_profit_per_ton' => round($transport_finance_gross_profit_per_ton,2)];

            }


        }


        return inertia(
            'TransactionSummary/Index',
            [
                'filters' => $filters,
                'transactions' => $transactions,
                'selected_transaction' => $transportTransaction,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'all_customers' => $customers,
                'all_products' => $products,
                'contract_types' => $contract_types,
                'all_suppliers' => $suppliers,
                'all_transporters' => $transporters,
                'all_staff' => $staff,
                'confirmation_types' => $confirmation_types,
                'all_product_sources' => $product_sources,
                'all_packaging' => $packaging,
                'all_billing_units' => $billing_units,
                'loading_hour_options' => $loading_hour_options,
                'all_drivers' => $all_drivers,
                'all_vehicles' => $all_vehicles,
                'all_vehicle_types' => $all_vehicle_types,
                'all_transport_rates' => $all_transport_rates,
                'all_status_entities' => $all_status_entities,
                'all_status_types' => $all_status_types,
                'all_invoice_statuses' => $all_invoice_statuses,
                'rules_with_approvals' => $rules_with_approvals,
                'deal_ticket' => $deal_ticket,
                'transport_order' => $transport_order,
                'purchase_order' => $purchase_order,
                'sales_order' => $sales_order,
                'all_terms_of_payments' => $all_terms_of_payments,
                'linked_trans_sc' => $linked_trans_sc,
                'linked_trans_pc' => $linked_trans_pc,
                'linked_trans_other' => $linked_trans_other,
                'model_activity' => $model_activity,
                'linked_trans_split' => $linked_trans_split,
                'primary_linked_trans_split' => $primary_trans,
                'split_totals' => $split_totals


            ]
        );


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

    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionSummary $transactionSummary)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionSummary $transactionSummary)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $update_related_models = 1;

        if (isset($request->update_related_models)) {
            $update_related_models = $request->update_related_models;
        }

        $request->validate([
            'contract_type_id.id' => ['required', 'integer'],
            'supplier_id.id' => ['required', 'integer'],
            'customer_id.id' => ['required', 'integer'],
            'transporter_id.id' => ['required', 'integer'],
            'product_id.id' => ['required', 'integer'],
            'include_in_calculations' => ['nullable', 'boolean'],
            'transport_date_earliest' => ['required', 'date'],
            'transport_date_latest' => ['required', 'date'],
            'delivery_notes' => ['nullable'],
            'product_notes' => ['nullable'],
            'customer_notes' => ['nullable'],
            'suppliers_notes' => ['nullable'],
            'traders_notes' => ['nullable'],
            'transport_notes' => ['nullable'],
            'pricing_notes' => ['nullable'],
            'process_notes' => ['nullable'],
            'document_notes' => ['nullable'],
            'transaction_notes' => ['nullable'],
            'traders_notes_supplier' => ['nullable'],
            'traders_notes_customer' => ['nullable'],
            'traders_notes_transport' => ['nullable'],
            'is_transaction_done' => ['nullable', 'boolean'],
        ]);

        $transportTransaction = TransportTransaction::find($request->transport_trans_id);


        $is_updated = $transportTransaction->update(
            [
                'contract_type_id' => $request->contract_type_id['id'],
                'supplier_id' => $request->supplier_id['id'],
                'customer_id' =>  $request->customer_id['id'],
                'customer_id_2' => isset($request->customer_id_2) ? $request->customer_id_2['id'] : null,
                'customer_id_3' => isset($request->customer_id_3) ? $request->customer_id_3['id'] : null,
                'customer_id_4' => isset($request->customer_id_4) ? $request->customer_id_4['id'] : null,
                'customer_id_5' => isset($request->customer_id_5) ? $request->customer_id_5['id'] : null,
                'transporter_id' => $request->transporter_id['id'],
                'product_id' => $request->product_id['id'],
                'include_in_calculations' => $request->include_in_calculations,
                'transport_date_earliest' => Carbon::parse($request->transport_date_earliest)->tz('Africa/Johannesburg'),
                'transport_date_latest' => Carbon::parse($request->transport_date_latest)->tz('Africa/Johannesburg'),
                'delivery_notes' => $request->delivery_notes,
                'product_notes' => $request->product_notes,
                'customer_notes' => $request->customer_notes,
                'suppliers_notes' => $request->suppliers_notes,
                'traders_notes' => $request->traders_notes,
                'transport_notes' => $request->transport_notes,
                'pricing_notes' => $request->pricing_notes,
                'process_notes' => $request->process_notes,
                'document_notes' => $request->document_notes,
                'transaction_notes' => $request->transaction_notes,
                'traders_notes_supplier' => $request->traders_notes_supplier,
                'traders_notes_customer' => $request->traders_notes_customer,
                'traders_notes_transport' => $request->traders_notes_transport,
                'is_transaction_done' => $request->is_transaction_done,
                'is_split_load' => $request->is_split_load_primary,
                'is_split_load_primary' => $request->is_split_load_primary,
                'is_split_load_member' => $request->is_split_load_member,
            ]
        );

        if (!$request->is_split_load_primary) {

            $linked_trans = TransLinkSplit::where('transport_trans_id', '=', $request->transport_trans_id)->get();


            if (count($linked_trans) > 0) {
                foreach ($linked_trans as $trans) {

                    $trade = $trans->linked_transport_trans_id;

                    $trans_link_split = TransLinkSplit::where('linked_transport_trans_id', '=', trim($trade))->where('trans_link_type_id', '=', 5)->withTrashed()->get();

                    //remove old links
                    if (count($trans_link_split) >= 1) {
                        foreach ($trans_link_split as $link) {
                            $link->forceDelete();
                        }
                    }
                    $trans_link = TransportTransaction::where('id', $trade)->first();

                    $trans_link->is_split_load = false;
                    $trans_link->is_split_load_member = false;
                    $trans_link->is_split_load_primary = false;
                    $trans_link->save();

                }
            }

        }


        //'no_units' => ['required', 'numeric', 'gt:0'],

        $request->validate([
            'confirmed_by_id.id' => ['required', 'integer'],
            'confirmed_by_type_id.id' => ['required', 'integer'],
            'packaging_incoming_id.id' => ['required', 'integer'],
            'packaging_outgoing_id.id' => ['required', 'integer'],
            'product_source_id.id' => ['required', 'integer'],
            'product_grade_perc' => ['nullable'],
            'no_units_incoming' => ['nullable', 'numeric'],
            'billing_units_incoming_id.id' => ['required', 'integer'],
            'no_units_outgoing' => ['nullable', 'numeric'],
            'no_units_outgoing_2' => ['nullable', 'numeric'],
            'no_units_outgoing_3' => ['nullable', 'numeric'],
            'no_units_outgoing_4' => ['nullable', 'numeric'],
            'no_units_outgoing_5' => ['nullable', 'numeric'],
            'billing_units_outgoing_id.id' => ['required'],
            'is_weighbridge_certificate_received' => ['nullable', 'boolean'],
            'delivery_note' => ['nullable'],
            'calculated_route_distance' => ['nullable', 'numeric'],
            'collection_address_id.id' => ['required', 'integer'],
            'delivery_address_id.id' => ['required', 'integer'],
            'delivery_address_id_2.id' => ['nullable', 'integer'],
            'delivery_address_id_3.id' => ['nullable', 'integer'],
            'delivery_address_id_4.id' => ['nullable', 'integer']
        ],
            [
                'packaging_incoming_id.id' => 'Need to select a valid incoming package option!',
                'packaging_outgoing_id.id' => 'Need to select a valid outgoing package option!',
                'product_source_id.id' => 'Need to select a valid product source option!',
                'billing_units_incoming_id.id' => 'Need to select a valid billing units incoming option!',
                'billing_units_outgoing_id.id' => 'Need to select a valid billing units outgoing option!',
                'collection_address_id.id' => 'Need to select a valid collection address option!',
                'delivery_address_id.id' => 'Need to select a valid delivery address option!',
                'delivery_address_id_2.id' => 'Need to select a valid delivery address option!',
                'delivery_address_id_3.id' => 'Need to select a valid delivery address option!',
                'delivery_address_id_4.id' => 'Need to select a valid delivery address option!',
            ]
        );


        $transportLoad = $transportTransaction->TransportLoad;

        $no_units_outgoing_total = $request->no_units_outgoing + $request->no_units_outgoing_2 + $request->no_units_outgoing_3 + $request->no_units_outgoing_4;

        $is_updated = $transportLoad->update(
            [
                'confirmed_by_id' => $request->confirmed_by_id['id'],
                'confirmed_by_type_id' => $request->confirmed_by_type_id['id'],
                'packaging_incoming_id' => $request->packaging_incoming_id['id'],
                'packaging_outgoing_id' => $request->packaging_outgoing_id['id'],
                'product_source_id' => $request->product_source_id['id'],
                'product_grade_perc' => $request->product_grade_perc,
                'no_units_incoming' => $request->no_units_incoming,
                'billing_units_incoming_id' => $request->billing_units_incoming_id['id'],
                'no_units_outgoing' => $request->no_units_outgoing,
                'no_units_outgoing_2' => $request->no_units_outgoing_2,
                'no_units_outgoing_3' => $request->no_units_outgoing_3,
                'no_units_outgoing_4' => $request->no_units_outgoing_4,
                'no_units_outgoing_5' => $request->no_units_outgoing_5,
                'billing_units_outgoing_id' => $request->billing_units_outgoing_id['id'],
                'is_weighbridge_certificate_received' => $request->is_weighbridge_certificate_received,
                'delivery_note' => $request->delivery_note,
                'calculated_route_distance' => $request->calculated_route_distance,
                'collection_address_id' => $request->collection_address_id['id'],
                'delivery_address_id' => $request->delivery_address_id['id'],
                'delivery_address_id_2' => $request->delivery_address_id_2 === null ? null : $request->delivery_address_id_2['id'],
                'delivery_address_id_3' => $request->delivery_address_id_3 === null ? null : $request->delivery_address_id_3['id'],
                'delivery_address_id_4' => $request->delivery_address_id_4 === null ? null : $request->delivery_address_id_4['id'],
                'delivery_address_id_5' => $request->delivery_address_id_4 === null ? null : $request->delivery_address_id_5['id'],
            ]
        );

        $transportJob = $transportTransaction->TransportJob;

        $transportJob->update(
            $request->validate([
                'supplier_loading_number' => ['nullable', 'string'],
                'customer_order_number' => ['nullable', 'string'],
                'supplier_loading_number_2' => ['nullable', 'string'],
                'customer_order_number_2' => ['nullable', 'string'],
                'supplier_loading_number_3' => ['nullable', 'string'],
                'customer_order_number_3' => ['nullable', 'string'],
                'supplier_loading_number_4' => ['nullable', 'string'],
                'customer_order_number_4' => ['nullable', 'string'],
                'supplier_loading_number_5' => ['nullable', 'string'],
                'customer_order_number_5' => ['nullable', 'string'],
                'number_loads' => ['nullable', 'numeric'],
                'is_multi_loads' => ['nullable', 'boolean'],
                'is_approved' => ['nullable', 'boolean'],
                'is_transport_costs_inc_price' => ['nullable', 'boolean'],
                'is_product_zero_rated' => ['nullable', 'boolean'],
                'loading_hours_from_id' => ['required', 'integer', 'exists:loading_hour_options,id'],
                'loading_hours_to_id' => ['required', 'integer', 'exists:loading_hour_options,id'],
                'offloading_hours_from_id' => ['required', 'integer', 'exists:loading_hour_options,id'],
                'offloading_hours_to_id' => ['required', 'integer', 'exists:loading_hour_options,id'],
                'loading_instructions' => ['nullable', 'string'],
                'offloading_instructions' => ['nullable', 'string'],
                'loading_contact' => ['nullable', 'string'],
                'loading_contact_no' => ['nullable', 'string'],
                'offloading_contact' => ['nullable', 'string'],
                'offloading_contact_no' => ['nullable', 'string']
            ])

        );


        //transport driver vehicle update

        $transportDriverVehicle = $transportJob->TransportDriverVehicle[0];

        $request->validate([
            'regular_driver_id.id' => ['required', 'integer', 'exists:regular_drivers,id'],
            'regular_vehicle_id.id' => ['required', 'integer', 'exists:regular_vehicles,id'],
            'weighbridge_upload_weight' => ['required', 'numeric'],
            'weighbridge_offload_weight' => ['required', 'numeric'],
            'is_cancelled' => ['required', 'boolean'],
            'is_loaded' => ['required', 'boolean'],
            'is_onroad' => ['required', 'boolean'],
            'is_delivered' => ['required', 'boolean'],
            'is_transport_scheduled' => ['required', 'boolean'],
            'is_paid' => ['required', 'boolean'],
            'is_payment_overdue' => ['required', 'boolean'],
            'traders_notes' => ['nullable', 'string'],
            'operations_alert_notes' => ['nullable', 'string'],
            'driver_vehicle_loading_number' => ['nullable', 'string'],
            'trailer_reg_1' => ['nullable', 'string'],
            'trailer_reg_2' => ['nullable', 'string']
        ]);

        $cur_date = (Carbon::now())->toDateString();

        //if variance in weight

        $is_weighbridge_variance = !(($request->weighbridge_upload_weight == $request->weighbridge_offload_weight));
        //Stamps if changed
        $date_cancelled = $request->is_cancelled && (!($transportDriverVehicle->is_cancelled)) ? $cur_date : $transportDriverVehicle->date_cancelled;
        $date_loaded = $request->is_loaded && (!($transportDriverVehicle->is_loaded)) ? $cur_date : $transportDriverVehicle->date_loaded;
        $date_onroad = $request->is_onroad && (!($transportDriverVehicle->is_onroad)) ? $cur_date : $transportDriverVehicle->date_onroad;
        $date_delivered = $request->is_delivered && (!($transportDriverVehicle->is_delivered)) ? $cur_date : $transportDriverVehicle->date_delivered;
        $date_scheduled = $request->is_transport_scheduled && (!($transportDriverVehicle->is_transport_scheduled)) ? $cur_date : $transportDriverVehicle->date_scheduled;
        $date_paid = $request->is_paid && (!($transportDriverVehicle->is_paid)) ? $cur_date : $transportDriverVehicle->date_paid;
        $date_payment_overdue = $request->is_payment_overdue && (!($transportDriverVehicle->is_payment_overdue)) ? $cur_date : $transportDriverVehicle->date_payment_overdue;

        $is_updated = $transportDriverVehicle->update(
            [
                'regular_driver_id' => $request->regular_driver_id['id'],
                'regular_vehicle_id' => $request->regular_vehicle_id['id'],
                'weighbridge_upload_weight' => $request->weighbridge_upload_weight,
                'weighbridge_offload_weight' => $request->weighbridge_offload_weight,
                'is_cancelled' => $request->is_cancelled,
                'is_loaded' => $request->is_loaded,
                'is_onroad' => $request->is_onroad,
                'is_delivered' => $request->is_delivered,
                'is_transport_scheduled' => $request->is_transport_scheduled,
                'is_paid' => $request->is_paid,
                'is_payment_overdue' => $request->is_payment_overdue,
                'traders_notes' => $request->traders_notes,
                'operations_alert_notes' => $request->operations_alert_notes,
                'is_weighbridge_variance' => $is_weighbridge_variance,
                'date_cancelled' => $date_cancelled,
                'date_loaded' => $date_loaded,
                'date_onroad' => $date_onroad,
                'date_delivered' => $date_delivered,
                'date_scheduled' => $date_scheduled,
                'date_paid' => $date_paid,
                'date_payment_overdue' => $date_payment_overdue,
                'driver_vehicle_loading_number' => $request->driver_vehicle_loading_number,
                'trailer_reg_1' => $request->trailer_reg_1,
                'trailer_reg_2' => $request->trailer_reg_2
            ]
        );


        //Transport Finance
        $transportFinance = $transportTransaction->TransportFinance;

        $user = auth()->user();

        $can_edit_adjusted_gp = $user->can('edit_adjusted_gp');


        if ($can_edit_adjusted_gp) {

            $is_updated = $transportFinance->update(
                $request->validate([
                    'transport_rate_basis_id' => ['required', 'integer', 'exists:transport_rate_bases,id'],
                    'cost_price_per_unit' => ['required', 'numeric'],
                    'selling_price_per_unit' => ['required', 'numeric'],
                    'selling_price_2' => ['nullable', 'numeric'],
                    'selling_price_3' => ['nullable', 'numeric'],
                    'selling_price_4' => ['nullable', 'numeric'],
                    'selling_price_5' => ['nullable', 'numeric'],
                    'adjusted_gp' => ['required', 'numeric'],
                    'adjusted_gp_notes' => ['nullable', 'string'],
                    'transport_rate' => ['nullable', 'numeric'],
                    'transport_cost_2' => ['nullable', 'numeric'],
                    'transport_cost_3' => ['nullable', 'numeric'],
                    'transport_cost_4' => ['nullable', 'numeric'],
                    'transport_cost_5' => ['nullable', 'numeric'],
                    'additional_cost_1' => ['nullable', 'numeric'],
                    'additional_cost_2' => ['nullable', 'numeric'],
                    'additional_cost_3' => ['nullable', 'numeric'],
                    'additional_cost_desc_1' => ['nullable', 'string'],
                    'additional_cost_desc_2' => ['nullable', 'string'],
                    'additional_cost_desc_3' => ['nullable', 'string']
                ])
            );

        } else {
            $is_updated = $transportFinance->update(
                $request->validate([
                    'transport_rate_basis_id' => ['required', 'integer', 'exists:transport_rate_bases,id'],
                    'cost_price_per_unit' => ['required', 'numeric'],
                    'selling_price_per_unit' => ['required', 'numeric'],
                    'selling_price_2' => ['nullable', 'numeric'],
                    'selling_price_3' => ['nullable', 'numeric'],
                    'selling_price_4' => ['nullable', 'numeric'],
                    'selling_price_5' => ['nullable', 'numeric'],
                    'transport_rate' => ['nullable', 'numeric'],
                    'transport_cost_2' => ['nullable', 'numeric'],
                    'transport_cost_3' => ['nullable', 'numeric'],
                    'transport_cost_4' => ['nullable', 'numeric'],
                    'transport_cost_5' => ['nullable', 'numeric'],
                    'additional_cost_1' => ['nullable', 'numeric'],
                    'additional_cost_2' => ['nullable', 'numeric'],
                    'additional_cost_3' => ['nullable', 'numeric'],
                    'additional_cost_desc_1' => ['nullable', 'string'],
                    'additional_cost_desc_2' => ['nullable', 'string'],
                    'additional_cost_desc_3' => ['nullable', 'string']
                ])
            );
        }

        $transportFinance->CalculateFields();
        $transportInvoice = $transportTransaction->TransportInvoice;
        $transportInvoiceDetails = $transportInvoice->TransportInvoiceDetails;
        $transportTransaction = $transportInvoice->TransportTransaction;
        $customer_id = $transportTransaction->customer_id;


        $request->validate([
                'is_active' => ['nullable', 'boolean'],
                'old_id' => ['nullable'],
                'is_printed' => ['nullable', 'boolean'],
                'is_invoiced' => ['nullable', 'boolean'],
                'is_invoice_paid' => ['nullable', 'boolean'],
                'invoice_id' => ['required', 'integer', 'exists:transport_invoices,id'],
                'invoice_no' => ['nullable', 'string'],
                'invoice_paid_date' => ['nullable', 'date'],
                'invoice_pay_by_date' => ['nullable', 'date'],
                'invoice_date' => ['nullable', 'date'],
                'invoice_amount' => ['required', 'numeric'],
                'invoice_amount_paid' => ['required', 'numeric'],
                'status_id' => ['required', 'integer', 'exists:invoice_statuses,id'],
                'notes' => ['nullable', 'string'],
            ]
        );

        $is_updated = $transportInvoice->update(
            [
                'is_active' => $request->is_active,
                'is_printed' => $request->is_printed,
            ]);

        $found_customer = Customer::where('id', $customer_id)->first();

        if ($found_customer->exists()) {
            $terms_of_payment = $found_customer->TermsOfPayment->days;
            $terms_of_payment_days = is_numeric($terms_of_payment) ? $terms_of_payment : 0;
            $invoice_date = Carbon::parse($request->invoice_date)->tz('Africa/Johannesburg');
            $invoice_pay_by_date = $invoice_date->addDays($terms_of_payment_days);
        }


        $invoice_balance = 0;
        $invoice_overdue = 0;

        if ($request->invoice_amount_paid < $request->invoice_amount) {

            $invoice_balance = ($request->invoice_amount - $request->invoice_amount_paid);

            $customer = Customer::where('id', $request->customer_id['id'])->first();

            $day_to_add = $customer->TermsOfPayment->days;
            $invoice_date = Carbon::create($transportInvoiceDetails->invoice_date);
            $adjusted_date = $invoice_date->addDays($day_to_add);

            if ($adjusted_date < $cur_date) {
                $invoice_overdue = $invoice_balance;
            }


        }


        $transportInvoiceDetails->update(
            [
                'is_invoiced' => $request->is_invoiced,
                'is_printed' => $request->is_printed,
                'is_invoice_paid' => $request->is_invoice_paid,
                'invoice_no' => $request->invoice_no,
                'invoice_paid_date' => Carbon::parse($request->invoice_paid_date)->tz('Africa/Johannesburg'),
                'invoice_pay_by_date' => $invoice_pay_by_date ?? Carbon::parse($request->invoice_date)->tz('Africa/Johannesburg'),
                'invoice_date' => Carbon::parse($request->invoice_date)->tz('Africa/Johannesburg'),
                'invoice_amount' => $request->invoice_amount,
                'invoice_amount_paid' => $request->invoice_amount_paid,
                'status_id' => $request->status_id,
                'notes' => $request->notes,
                'overdue' => $invoice_overdue,
                'outstanding' => $invoice_balance
            ]);


        if ($is_updated) {
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Updated');
        } else {
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'NOT updated');
        }


        return redirect()->back();


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionSummary $transactionSummary)
    {
        //
    }
}
