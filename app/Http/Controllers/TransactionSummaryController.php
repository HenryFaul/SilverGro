<?php

namespace App\Http\Controllers;

use App\Models\BillingUnits;
use App\Models\ConfirmationTypes;
use App\Models\ContractType;
use App\Models\Customer;
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
use App\Models\TransactionSummary;
use App\Models\TransLink;
use App\Models\Transporter;
use App\Models\TransportRateBasis;
use App\Models\TransportTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransactionSummaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
            'selected_trans_id'
        ]);

        $paginate = $request['show'] ?? 5;

        $transactions = TransportTransaction::with('ContractType')->with('Customer')->with('Supplier')->with('Transporter')->with('Product')->with('TransportFinance')
            ->with('TransportLoad')
            ->index($filters)
            ->orderBy('transport_date_earliest', 'desc')
            ->paginate($paginate)
            ->withQueryString();

        $first_transaction_id = TransportTransaction::with('ContractType')->with('Customer')->with('Supplier')->with('Transporter')->with('Product')
            ->index($filters)
            ->orderBy('transport_date_earliest', 'desc')->pluck('id')->first();


        $selected_trans_id = $request['selected_trans_id'] ?? $first_transaction_id;

        $transportTransaction = TransportTransaction::where('id', $selected_trans_id)->with('ContractType')->with('TransportInvoice', fn($query) => $query->with('TransportInvoiceDetails'))
            ->with('TransportLoad')->with('DealTicket')->with('Supplier',fn($query) => $query->with('TermsOfPayment'))->with('Customer',fn($query) => $query->with('TermsOfPayment')->with('InvoiceBasis'))->with('Product')
            ->with('TransportFinance')->with('TransportStatus', fn($query) => $query->with('StatusEntity')->with('StatusType'))->with('AssignedUserComm', fn($query) => $query->with('AssignedUserSupplier')->with('AssignedUserCustomer'))
            ->with('TransportJob', fn($query) => $query->with('OffloadingHoursFrom')->with('OffloadingHoursTo')
                ->with('TransportDriverVehicle', fn($query) => $query->with('Driver')->with('Vehicle', fn($query) => $query->with('VehicleType'))))->first();



        $deal_ticket = $transportTransaction->DealTicket;

        $deal_ticket->calculateRules();
        $rules_with_approvals = $deal_ticket->getAppliedRules();

        $start_date = (Carbon::now()->tz('Africa/Johannesburg')->startOfMonth())->toDateString();
        $end_date = (Carbon::now()->tz('Africa/Johannesburg'))->toDateString();

        $customers = Customer::with('staff')->with('addressable')->with('contactable')->orderby('last_legal_name', 'asc')->get();
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

        if ($transportTransaction->contract_type_id === 4){
            $linked_trans = TransLink::where('linked_transport_trans_id','=',$transportTransaction->id)->where('trans_link_type_id','=',3)->with('TransportTransactionPc',fn($query) => $query->with('Customer')->with('Supplier')->with('Transporter')
                ->with('Product')->with('TransportFinance'))->get();

        } else if ($transportTransaction->contract_type_id === 3){
            $linked_trans = TransLink::where('linked_transport_trans_id','=',$transportTransaction->id)->where('trans_link_type_id','=',4)->with('TransportTransactionPc',fn($query) => $query->with('Customer')->with('Supplier')->with('Transporter')
                ->with('Product')->with('TransportFinance'))->get();
        } else {
            $linked_trans = TransLink::where('transport_trans_id','=',$transportTransaction->id)->with('TransportTransaction',fn($query) => $query->with('Customer')->with('Supplier')->with('Transporter')
                ->with('Product')->with('TransportFinance'))->get();
        }


        return inertia(
            'TransactionSummary/Index',
            [
                'filters' => $filters,
                'transactions' => $transactions,
                'selected_transaction'=>$transportTransaction,
                'start_date'=>$start_date,
                'end_date'=>$end_date,
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
                'all_transport_rates' => $all_transport_rates,
                'all_status_entities' => $all_status_entities,
                'all_status_types' => $all_status_types,
                'all_invoice_statuses'=>$all_invoice_statuses,
                'rules_with_approvals'=>$rules_with_approvals,
                'linked_trans'=>$linked_trans

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
    public function update(Request $request, TransactionSummary $transactionSummary)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionSummary $transactionSummary)
    {
        //
    }
}
