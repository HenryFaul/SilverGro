<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\BillingUnits;
use App\Models\ConfirmationTypes;
use App\Models\ContractType;
use App\Models\Customer;
use App\Models\DealTicket;
use App\Models\InvoiceBasis;
use App\Models\InvoiceStatus;
use App\Models\LoadingHourOption;
use App\Models\Packaging;
use App\Models\Product;
use App\Models\ProductSource;
use App\Models\PurchaseOrder;
use App\Models\RegularDriver;
use App\Models\RegularVehicle;
use App\Models\SalesOrder;
use App\Models\Staff;
use App\Models\StatusEntity;
use App\Models\StatusType;
use App\Models\Supplier;
use App\Models\TradeRule;
use App\Models\TransLink;
use App\Models\TransportApproval;
use App\Models\Transporter;
use App\Models\TransportFinance;
use App\Models\TransportInvoice;
use App\Models\TransportInvoiceDetails;
use App\Models\TransportJob;
use App\Models\TransportLoad;
use App\Models\TransportOrder;
use App\Models\TransportRateBasis;
use App\Models\TransportTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Response;
use Inertia\ResponseFactory;

class TransportTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request,)
    {
        $user = auth()->user();

        $view_only = $user->can('view-only');

        /*if ($view_only) {
            return to_route('no_permission');
        }*/

        $filters = $request->only([
            'supplier_name',
            'customer_name',
            'transporter_name',
            'product_name',
            'show',
            'start_date',
            'end_date',
            'contract_type_id',
            'id'
        ]);

        $paginate = $request['show'] ?? 25;

        $transactions = TransportTransaction::with('ContractType')->with('Customer')->with('Supplier')->with('Transporter')->with('Product')
            ->index($filters)
            ->orderBy('transport_date_earliest', 'desc')
            ->paginate($paginate)
            ->withQueryString();

        $start_date = (Carbon::now()->tz('Africa/Johannesburg')->startOfMonth())->toDateString();
        $end_date = (Carbon::now()->tz('Africa/Johannesburg'))->toDateString();
        $contract_types = ContractType::all();


        //dd($end_date);

        return inertia(
            'Transaction/Index',
            [
                'filters' => $filters,
                'transactions' => $transactions,
                'start_date'=>$start_date,
                'end_date'=>$end_date,
                'contract_types'=>$contract_types

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

        /*        contract_type_id:null,
            transport_date_earliest: new Date(),
            traders_notes: null,
            product_id:null,
            supplier_id:null,
            customer_id:null,
            transporter_id:null,
            billing_units_id:null*/


        $request->validate([
            'no_units'=> ['required', 'numeric','gt:0'],
            'contract_type_id.id' => ['required', 'integer', 'exists:contract_types,id'],
            'supplier_id.id' => ['required', 'integer', 'exists:suppliers,id'],
            'customer_id.id' => ['required', 'integer', 'exists:customers,id'],
            'transporter_id.id' => ['required', 'integer', 'exists:transporters,id'],
            'product_id.id' => ['required', 'integer', 'exists:products,id'],
            'billing_units_id.id' => ['required', 'integer','exists:billing_units,id'],
            'transport_date_earliest' => ['required', 'date'],
            'traders_notes' => ['nullable'],
            'transport_rate_basis_id' => ['required', 'integer','exists:transport_rate_bases,id','not_in:1']
        ],[

            'contract_type_id.id'=>'You need to select a valid option!',
            'product_id.id'=>'You need to select a valid option!',
            'transport_rate_basis_id'=>'You need to select a valid option!',
            'billing_units_id.id'=>'You need to select a valid option!'

        ]);

        //Transport Transaction

        $transport_trans = TransportTransaction::create([
            'contract_type_id' => $request->contract_type_id['id'],
            'supplier_id' => $request->supplier_id['id'],
            'customer_id' => $request->customer_id['id'],
            'transporter_id' => $request->transporter_id['id'],
            'product_id' => $request->product_id['id'],
            'transport_date_earliest'=>Carbon::parse($request->transport_date_earliest)->tz('Africa/Johannesburg'),
            'transport_date_latest'=>Carbon::parse($request->transport_date_earliest)->tz('Africa/Johannesburg'),
            'transport_rate_basis_id'=>$request->transport_rate_basis_id,
            'traders_notes'=>$request->traders_notes,
            'old_id'=>null,
            'include_in_calculations'=>false
        ]);

        //Deal Ticket

        $deal_ticket = DealTicket::create([
            'transport_trans_id' => $transport_trans->id,
            'is_printed' => false,
            'is_active' => false
        ]);


        $found_customer = Customer::where('id', $request->customer_id['id'])->first();
        $found_del_address = Address::where('address_type_id', 1)->where('poly_address_id', $found_customer->id)->where('poly_address_type', get_class($found_customer))->first();

        $found_supplier = Supplier::where('id', $request->supplier_id['id'])->first();
        $found_col_address = Address::where('address_type_id', 1)->where('poly_address_id', $found_supplier->id)->where('poly_address_type', get_class($found_supplier))->first();


        $transport_load = TransportLoad::create([
            'transport_trans_id' => $transport_trans->id,
            'confirmed_by_id' => 1,
            'confirmed_by_type_id' => 1,
            'product_id' => $request->product_id['id'],
            'packaging_incoming_id' => 1,
            'packaging_outgoing_id' => 1,
            'product_source_id' => 1,
            'no_units_incoming' => $request->no_units,
            'billing_units_incoming_id' => $request->billing_units_id['id'],
            'no_units_outgoing' => $request->no_units,
            'billing_units_outgoing_id' => $request->billing_units_id['id'],
            'is_weighbridge_certificate_received' => false,
            'delivery_address_id' =>$found_del_address == null ? 1: $found_del_address->id,
            'collection_address_id'=>$found_col_address == null ? 1: $found_col_address->id,
        ]);

        $transport_job = TransportJob::create([
            'transport_trans_id' => $transport_trans->id,
            'transport_rate_basis_id' => $request->transport_rate_basis_id,
            'is_multi_loads' => false,
            'is_approved' => false,
            'transport_date_earliest' => Carbon::parse($request->transport_date_earliest)->tz('Africa/Johannesburg'),
            'transport_date_latest' => Carbon::parse($request->transport_date_earliest)->tz('Africa/Johannesburg'),
            'is_transport_costs_inc_price' => false,
            'is_product_zero_rated' => false,
            'loading_hours_from_id' => 1,
            'loading_hours_to_id' => 1,
            'offloading_hours_from_id' => 1,
            'offloading_hours_to_id' => 1,
            'load_insurance_per_ton' => 0,
            'total_load_insurance' => 0,
            'number_loads' => 1,
        ]);

        $transport_finance = TransportFinance::create([
            'transport_trans_id' => $transport_trans->id,
            'transport_load_id' => $transport_load->id,
            'transport_rate_basis_id'=>$request->transport_rate_basis_id,
            'cost_price_per_unit' => 0,
            'cost_price_per_ton'=>0,
            'total_cost_price'=>0,
            'cost_price'=>0,
            'selling_price'=>0,
            'selling_price_per_unit' => 0,
            'selling_price_per_ton'=> 0,
            'is_transport_costs_inc_price'=>0,
            'transport_cost'=>0,
            'transport_rate_per_ton' => 0,
            'transport_rate' => 0,
            'transport_price' => 0,
            'load_insurance_per_ton'=>0,
            'comms_due_per_ton' => 0,
            'weight_ton_incoming' => 0,
            'weight_ton_outgoing' => 0,
            'additional_cost_1' => 0,
            'additional_cost_2' => 0,
            'additional_cost_3' => 0,
            'gross_profit' => 0,
            'gross_profit_percent' => 0,
            'gross_profit_per_ton' => 0,
            'total_supplier_comm' => 0,
            'total_customer_comm' => 0,
            'total_comm' => 0,
            'adjusted_gp' => 0,
        ]);

        $transport_finance->CalculateFields();

        $transport_invoice = TransportInvoice::create([
            'transport_trans_id' => $transport_trans->id,
            'is_active' => false,
            'is_printed' => false,
            'customer_id'=>$found_customer->id
        ]);

        $transport_invoice_details = TransportInvoiceDetails::create([
            'transport_trans_id' => $transport_trans->id,
            'invoice_id' => $transport_invoice->id,
            'is_invoiced' => false,
            'is_invoice_paid' => false,
            'invoice_paid_date' => Carbon::parse($request->transport_date_earliest)->tz('Africa/Johannesburg'),
            'invoice_pay_by_date' => Carbon::parse($request->transport_date_earliest)->tz('Africa/Johannesburg'),
            'invoice_date' => Carbon::parse($request->transport_date_earliest)->tz('Africa/Johannesburg'),
            'invoice_amount' => 0,
            'cost_price' => 0,
            'selling_price' => 0,
            'status_id' => 1,
        ]);

        //Orders

        $transport_order = TransportOrder::create([
            'transport_trans_id' => $transport_trans->id,
            'confirmed_by_id'=>1,
            'confirmed_by_type_id'=>1,
            'is_printed' => false,
            'is_active' => false
        ]);

        $sales_order = SalesOrder::create([
            'transport_trans_id' => $transport_trans->id,
            'confirmed_by_id'=>1,
            'confirmed_by_type_id'=>1,
            'is_printed' => false,
            'is_active' => false
        ]);

        $purchase_order = PurchaseOrder::create([
            'transport_trans_id' => $transport_trans->id,
            'confirmed_by_id'=>1,
            'confirmed_by_type_id'=>1,
            'is_printed' => false,
            'is_active' => false
        ]);



        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Transport Transaction created');
        return redirect()->back();
    }

    public function getProps(): array
    {
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

        return array("customers"=>$customers,"suppliers"=>$suppliers,'transporters'=>$transporters,
            'contract_types'=>$contract_types,'products'=>$products,'staff'=>$staff, 'confirmation_types'=>$confirmation_types,
            'product_sources'=>$product_sources,'packaging'=>$packaging, 'all_billing_units'=>$billing_units, 'loading_hour_options'=>$loading_hour_options,
            'all_drivers'=>$all_drivers, 'all_vehicles'=>$all_vehicles, 'all_transport_rates'=>$all_transport_rates, 'all_status_entities'=>$all_status_entities,
            'all_status_types'=>$all_status_types,'all_invoice_statuses'=>$all_invoice_statuses
            );

    }

    public function getPcs(): array
    {
        $transport_trans = TransportTransaction::where('contract_type_id',2)->with('Product')->with('TransportLoad')->orderBy('transport_date_earliest', 'desc')->get();
        $contract_types = ContractType::all();

        return array("transport_trans"=>$transport_trans,"contract_types"=>$contract_types);

    }


    /**
     * Display the specified resource.
     */
    public function show(TransportTransaction $transportTransaction)
    {

        $transportTransaction = TransportTransaction::where('id', $transportTransaction->id)->with('ContractType')->with('TransportInvoice', fn($query) => $query->with('TransportInvoiceDetails'))
            ->with('TransportLoad')->with('DealTicket')
            ->with('TransportFinance')->with('TransportStatus', fn($query) => $query->with('StatusEntity')->with('StatusType'))->with('AssignedUserComm', fn($query) => $query->with('AssignedUserSupplier')->with('AssignedUserCustomer'))
            ->with('TransportJob', fn($query) => $query->with('OffloadingHoursFrom')->with('OffloadingHoursTo')
                ->with('TransportDriverVehicle', fn($query) => $query->with('Driver')->with('Vehicle', fn($query) => $query->with('VehicleType'))))->first();





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


            //->with('Customer')->with('Supplier')->with('Transporter')->with('Product')

        $deal_ticket = $transportTransaction->DealTicket;

        $deal_ticket->calculateRules();
        $rules_with_approvals = $deal_ticket->getAppliedRules();

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

       // dd($transportTransaction);

        return inertia(
            'Transaction/Show',
            [
                'transaction' => $transportTransaction,
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
                'linked_trans'=>$linked_trans,
                'rules_with_approvals'=>$rules_with_approvals
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransportTransaction $transportTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransportTransaction $transportTransaction)
    {

        /*  ['id','old_id','contract_type_id','contract_no','supplier_id','customer_id','transporter_id','product_id','include_in_calculations','transport_date_earliest','transport_date_latest','delivery_notes',
              'product_notes','supplier_notes','customer_notes','suppliers_notes','traders_notes','transport_notes','pricing_notes','process_notes','document_notes','transaction_notes',
              'traders_notes_supplier','traders_notes_customer','traders_notes_transport','is_transaction_done','created_at'];*/

        // dd($request->transport_date_earliest);
        //dd(Carbon::parse($request->transport_date_earliest)->tz('Africa/Johannesburg'));


        $request->validate([
            'contract_type_id.id' => ['required', 'integer', 'exists:contract_types,id'],
            'supplier_id.id' => ['required', 'integer', 'exists:suppliers,id'],
            'customer_id.id' => ['required', 'integer', 'exists:customers,id'],
            'transporter_id.id' => ['required', 'integer', 'exists:transporters,id'],
            'product_id.id' => ['required', 'integer', 'exists:products,id'],
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

        //->toDateTimeString()
        $is_updated = $transportTransaction->update(
            [
                'contract_type_id' => $request->contract_type_id['id'],
                'supplier_id' => $request->supplier_id['id'],
                'customer_id' => $request->customer_id['id'],
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
            ]
        );

        //update invoice to reflect updated customer

        $transport_invoice = $transportTransaction->TransportInvoice;
        $transport_invoice->customer_id = $request->customer_id['id'];
        $transport_invoice->save();


        $transport_finance = ($transportTransaction->TransportFinance);
        $transport_finance->CalculateFields();

        if ($is_updated) {
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Transport Transaction updated');
        } else {
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Transport NOT updated');
        }

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransportTransaction $transportTransaction)
    {
        //
    }
}
