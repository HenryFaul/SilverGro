<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\BillingUnits;
use App\Models\ConfirmationTypes;
use App\Models\ContractType;
use App\Models\Customer;
use App\Models\CustomerParent;
use App\Models\CustomReport;
use App\Models\CustomReportModel;
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
use App\Models\TermsOfPayment;
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
use Illuminate\Support\Facades\Storage;
use Inertia\Response;
use Inertia\ResponseFactory;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Writer\Exception;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TransportTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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
        $custom_reports = CustomReport::with('CustomReportModels')->get();



        //dd($end_date);

        return inertia(
            'Transaction/Index',
            [
                'filters' => $filters,
                'transactions' => $transactions,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'contract_types' => $contract_types,
                'download_url' => null,
                'custom_reports'=>$custom_reports

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
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {

        $request->validate([
            'no_units' => ['required', 'numeric', 'gt:0'],
            'contract_type_id.id' => ['required', 'integer', 'exists:contract_types,id'],
            'supplier_id.id' => ['required', 'integer', 'exists:suppliers,id'],
            'customer_id.id' => ['required', 'integer', 'exists:customers,id'],
            'transporter_id.id' => ['required', 'integer', 'exists:transporters,id'],
            'product_id.id' => ['required', 'integer', 'exists:products,id'],
            'billing_units_id.id' => ['required', 'integer', 'exists:billing_units,id'],
            'transport_date_earliest' => ['required', 'date'],
            'traders_notes' => ['nullable'],
            'transport_rate_basis_id' => ['required', 'integer', 'exists:transport_rate_bases,id', 'not_in:1']
        ], [

            'contract_type_id.id' => 'You need to select a valid option!',
            'product_id.id' => 'You need to select a valid option!',
            'transport_rate_basis_id' => 'You need to select a valid option!',
            'billing_units_id.id' => 'You need to select a valid option!'

        ]);

        //Transport Transaction

        $transport_trans = TransportTransaction::create([
            'contract_type_id' => $request->contract_type_id['id'],
            'supplier_id' => $request->supplier_id['id'],
            'customer_id' => $request->customer_id['id'],
            'transporter_id' => $request->transporter_id['id'],
            'product_id' => $request->product_id['id'],
            'transport_date_earliest' => Carbon::parse($request->transport_date_earliest)->tz('Africa/Johannesburg'),
            'transport_date_latest' => Carbon::parse($request->transport_date_earliest)->tz('Africa/Johannesburg'),
            'transport_rate_basis_id' => $request->transport_rate_basis_id,
            'traders_notes' => $request->traders_notes,
            'old_id' => null,
            'include_in_calculations' => false
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
            'delivery_address_id' => $found_del_address == null ? 1 : $found_del_address->id,
            'collection_address_id' => $found_col_address == null ? 1 : $found_col_address->id,
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
            'transport_rate_basis_id' => $request->transport_rate_basis_id,
            'cost_price_per_unit' => 0,
            'cost_price_per_ton' => 0,
            'total_cost_price' => 0,
            'cost_price' => 0,
            'selling_price' => 0,
            'selling_price_per_unit' => 0,
            'selling_price_per_ton' => 0,
            'is_transport_costs_inc_price' => 0,
            'transport_cost' => 0,
            'transport_rate_per_ton' => 0,
            'transport_rate' => 0,
            'transport_price' => 0,
            'load_insurance_per_ton' => 0,
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
            'customer_id' => $found_customer->id
        ]);

        $terms_of_payment = $found_customer->TermsOfPayment->days;
        $terms_of_payment_days = is_numeric($terms_of_payment) ? $terms_of_payment : 0;
        $invoice_date = Carbon::parse($request->transport_date_earliest)->tz('Africa/Johannesburg');
        $invoice_pay_by_date = $invoice_date->addDays($terms_of_payment_days);



        $transport_invoice_details = TransportInvoiceDetails::create([
            'transport_trans_id' => $transport_trans->id,
            'invoice_id' => $transport_invoice->id,
            'is_invoiced' => false,
            'is_invoice_paid' => false,
            'invoice_paid_date' => null,
            'invoice_pay_by_date' => $invoice_pay_by_date,
            'invoice_date' => $invoice_date,
            'invoice_amount' => 0,
            'cost_price' => 0,
            'selling_price' => 0,
            'status_id' => 1,
        ]);

        //Orders

        $transport_order = TransportOrder::create([
            'transport_trans_id' => $transport_trans->id,
            'confirmed_by_id' => 1,
            'confirmed_by_type_id' => 1,
            'is_printed' => false,
            'is_active' => false
        ]);

        $sales_order = SalesOrder::create([
            'transport_trans_id' => $transport_trans->id,
            'confirmed_by_id' => 1,
            'confirmed_by_type_id' => 1,
            'is_printed' => false,
            'is_active' => false
        ]);

        $purchase_order = PurchaseOrder::create([
            'transport_trans_id' => $transport_trans->id,
            'confirmed_by_id' => 1,
            'confirmed_by_type_id' => 1,
            'is_printed' => false,
            'is_active' => false
        ]);


        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Created:'.$transport_trans->id);
        return redirect()->back();
    }

    /**
     * Clone a trade
     */
    public function clone(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'transport_trans_id' => ['required', 'integer'],
        ]);

        $id_trans_to_clone = $request->transport_trans_id;
        $trans_to_clone = TransportTransaction::where('id', $id_trans_to_clone)->first();


        $transport_trans = TransportTransaction::create([
            'contract_type_id' => $trans_to_clone->contract_type_id,
            'supplier_id' => $trans_to_clone->supplier_id,
            'customer_id' => $trans_to_clone->customer_id,
            'customer_id_2' => $trans_to_clone->customer_id_2,
            'customer_id_3' => $trans_to_clone->customer_id_3,
            'customer_id_4' => $trans_to_clone->customer_id_4,
            'transporter_id' => $trans_to_clone->transporter_id,
            'product_id' => $trans_to_clone->product_id,
            'transport_date_earliest' => $trans_to_clone->transport_date_earliest,
            'transport_date_latest' => $trans_to_clone->transport_date_latest,
            'transport_rate_basis_id' => $trans_to_clone->transport_rate_basis_id,
            'traders_notes' => $trans_to_clone->traders_notes,
            'old_id' => $trans_to_clone->old_id,
            'include_in_calculations' => $trans_to_clone->include_in_calculations
        ]);

        //Deal Ticket

        $deal_ticket = DealTicket::create([
            'transport_trans_id' => $transport_trans->id,
            'is_printed' => false,
            'is_active' => false
        ]);


        $transport_load_to_clone = $trans_to_clone->TransportLoad;



        $transport_load = TransportLoad::create([
            'transport_trans_id' => $transport_trans->id,
            'confirmed_by_id' => 1,
            'confirmed_by_type_id' => 1,
            'product_id' => $transport_load_to_clone->product_id,
            'packaging_incoming_id' => 1,
            'packaging_outgoing_id' => 1,
            'product_source_id' => 1,
            'no_units_incoming' => $transport_load_to_clone->no_units_incoming,
            'billing_units_incoming_id' => $transport_load_to_clone->billing_units_incoming_id,
            'no_units_outgoing' => $transport_load_to_clone->no_units_outgoing,
            'billing_units_outgoing_id' => $transport_load_to_clone->billing_units_outgoing_id,
            'is_weighbridge_certificate_received' => false,
            'delivery_address_id' => $transport_load_to_clone->delivery_address_id,
            'collection_address_id' => $transport_load_to_clone->collection_address_id,
        ]);

        $transport_job_to_clone = $trans_to_clone->TransportJob;

        $transport_job = TransportJob::create([
            'transport_trans_id' => $transport_trans->id,
            'transport_rate_basis_id' => $transport_job_to_clone->transport_rate_basis_id,
            'is_multi_loads' => $transport_job_to_clone->is_multi_loads,
            'is_approved' => $transport_job_to_clone->is_approved,
            'transport_date_earliest' => $transport_job_to_clone->transport_date_earliest,
            'transport_date_latest' => $transport_job_to_clone->transport_date_latest,
            'is_transport_costs_inc_price' => $transport_job_to_clone->is_transport_costs_inc_price,
            'is_product_zero_rated' => $transport_job_to_clone->is_product_zero_rated,
            'loading_hours_from_id' => $transport_job_to_clone->loading_hours_from_id,
            'loading_hours_to_id' => $transport_job_to_clone->loading_hours_to_id,
            'offloading_hours_from_id' => $transport_job_to_clone->offloading_hours_from_id,
            'offloading_hours_to_id' => $transport_job_to_clone->offloading_hours_to_id,
            'load_insurance_per_ton' => $transport_job_to_clone->load_insurance_per_ton,
            'total_load_insurance' => $transport_job_to_clone->total_load_insurance,
            'number_loads' => $transport_job_to_clone->number_loads,
        ]);

        $transport_finance_to_clone = $trans_to_clone->TransportFinance;

        $transport_finance = TransportFinance::create([
            'transport_trans_id' => $transport_trans->id,
            'transport_load_id' => $transport_load->id,
            'transport_rate_basis_id' => $transport_finance_to_clone->transport_rate_basis_id,
            'cost_price_per_unit' => 0,
            'cost_price_per_ton' => 0,
            'total_cost_price' => 0,
            'cost_price' => 0,
            'selling_price' => 0,
            'selling_price_per_unit' => 0,
            'selling_price_per_ton' => 0,
            'is_transport_costs_inc_price' => 0,
            'transport_cost' => 0,
            'transport_rate_per_ton' => 0,
            'transport_rate' => 0,
            'transport_price' => 0,
            'load_insurance_per_ton' => 0,
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

        $transport_invoice_to_clone = $trans_to_clone->TransportInvoice;

        $transport_invoice = TransportInvoice::create([
            'transport_trans_id' => $transport_trans->id,
            'is_active' => false,
            'is_printed' => false,
            'customer_id' => $transport_invoice_to_clone->customer_id
        ]);

        $transport_invoice_details_to_clone = $transport_invoice_to_clone->TransportInvoiceDetails;

        $transport_invoice_details = TransportInvoiceDetails::create([
            'transport_trans_id' => $transport_trans->id,
            'invoice_id' => $transport_invoice->id,
            'is_invoiced' => $transport_invoice_details_to_clone->is_invoiced,
            'is_invoice_paid' => false,
            'invoice_paid_date' => $transport_invoice_details_to_clone->invoice_paid_date,
            'invoice_pay_by_date' => $transport_invoice_details_to_clone->invoice_pay_by_date,
            'invoice_date' => $transport_invoice_details_to_clone->invoice_date,
            'invoice_amount' => $transport_invoice_details_to_clone->invoice_amount,
            'cost_price' => $transport_invoice_details_to_clone->cost_price,
            'selling_price' => $transport_invoice_details_to_clone->selling_price,
            'status_id' => $transport_invoice_details_to_clone->status_id,
        ]);

        //Orders

        $transport_order = TransportOrder::create([
            'transport_trans_id' => $transport_trans->id,
            'confirmed_by_id' => 1,
            'confirmed_by_type_id' => 1,
            'is_printed' => false,
            'is_active' => false
        ]);

        $sales_order = SalesOrder::create([
            'transport_trans_id' => $transport_trans->id,
            'confirmed_by_id' => 1,
            'confirmed_by_type_id' => 1,
            'is_printed' => false,
            'is_active' => false
        ]);

        $purchase_order = PurchaseOrder::create([
            'transport_trans_id' => $transport_trans->id,
            'confirmed_by_id' => 1,
            'confirmed_by_type_id' => 1,
            'is_printed' => false,
            'is_active' => false
        ]);


        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Cloned:'.$transport_trans->id);
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

        return array("customers" => $customers, "suppliers" => $suppliers, 'transporters' => $transporters,
            'contract_types' => $contract_types, 'products' => $products, 'staff' => $staff, 'confirmation_types' => $confirmation_types,
            'product_sources' => $product_sources, 'packaging' => $packaging, 'all_billing_units' => $billing_units, 'loading_hour_options' => $loading_hour_options,
            'all_drivers' => $all_drivers, 'all_vehicles' => $all_vehicles, 'all_transport_rates' => $all_transport_rates, 'all_status_entities' => $all_status_entities,
            'all_status_types' => $all_status_types, 'all_invoice_statuses' => $all_invoice_statuses
        );

    }

    public function getPcs(): array
    {
        $transport_trans = TransportTransaction::where('contract_type_id', 2)->with('Product')->with('TransportLoad')->with('Customer')->with('Supplier')
            ->orderBy('transport_date_earliest', 'desc')->get();
        $contract_types = ContractType::all();

        return array("transport_trans" => $transport_trans, "contract_types" => $contract_types);

    }

    public function getScs(): array
    {
        $transport_trans = TransportTransaction::where('contract_type_id', 3)->with('Product')->with('TransportLoad')->with('Customer')->with('Supplier')
            ->orderBy('transport_date_earliest', 'desc')->get();
        $contract_types = ContractType::all();

        return array("transport_trans" => $transport_trans, "contract_types" => $contract_types);

    }



    /**
     * Display the specified resource.
     */
    public function show(TransportTransaction $transportTransaction)
    {

        $transportTransaction = TransportTransaction::where('id', $transportTransaction->id)->with('ContractType')->with('TransportInvoice', fn($query) => $query->with('TransportInvoiceDetails'))
            ->with('TransportLoad')->with('DealTicket')->with('Customer_2')
            ->with('TransportFinance')->with('TransportStatus', fn($query) => $query->with('StatusEntity')->with('StatusType'))->with('AssignedUserComm', fn($query) => $query->with('AssignedUserSupplier')->with('AssignedUserCustomer'))
            ->with('TransportJob', fn($query) => $query->with('OffloadingHoursFrom')->with('OffloadingHoursTo')
                ->with('TransportDriverVehicle', fn($query) => $query->with('Driver')->with('Vehicle', fn($query) => $query->with('VehicleType'))))->first();


        if ($transportTransaction->contract_type_id === 4) {
            $linked_trans = TransLink::where('linked_transport_trans_id', '=', $transportTransaction->id)->where('trans_link_type_id', '=', 3)->with('TransportTransactionPc', fn($query) => $query->with('Customer')->with('Supplier')->with('Transporter')
                ->with('Product')->with('TransportFinance'))->get();

        } else if ($transportTransaction->contract_type_id === 3) {
            $linked_trans = TransLink::where('linked_transport_trans_id', '=', $transportTransaction->id)->where('trans_link_type_id', '=', 4)->with('TransportTransactionPc', fn($query) => $query->with('Customer')->with('Supplier')->with('Transporter')
                ->with('Product')->with('TransportFinance'))->get();
        } else {
            $linked_trans = TransLink::where('transport_trans_id', '=', $transportTransaction->id)->with('TransportTransaction', fn($query) => $query->with('Customer')->with('Supplier')->with('Transporter')
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
        $all_terms_of_payments = TermsOfPayment::all();


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
                'all_invoice_statuses' => $all_invoice_statuses,
                'linked_trans' => $linked_trans,
                'rules_with_approvals' => $rules_with_approvals,
                'all_terms_of_payments'=>$all_terms_of_payments
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

        //dd($request->process_notes);

        $is_updated = $transportTransaction->update(
            [
                'contract_type_id' => $request->contract_type_id['id'],
                'supplier_id' => $request->supplier_id['id'],
                'customer_id' => $request->is_split_load? 2 : $request->customer_id['id'],
                'customer_id_2' => isset($request->customer_id_2)? $request->customer_id_2['id']: null,
                'customer_id_3' => isset($request->customer_id_3)? $request->customer_id_3['id']: null,
                'customer_id_4' => isset($request->customer_id_4)? $request->customer_id_4['id']: null,
                'customer_id_5' => isset($request->customer_id_5)? $request->customer_id_5['id']: null,
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
                'is_split_load' => $request->is_split_load,
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

    public function updatePlayers(Request $request, TransportTransaction $transportTransaction): \Illuminate\Http\RedirectResponse
    {

        $request->validate([
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'transporter_id' => ['required', 'integer', 'exists:transporters,id'],
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        $is_updated = $transportTransaction->update(
            [
                'supplier_id' => $request->supplier_id,
                'customer_id' => $request->customer_id,
                'transporter_id' => $request->transporter_id,
                'product_id' => $request->product_id,
            ]
        );

        //update invoice to reflect updated customer

        $transport_invoice = $transportTransaction->TransportInvoice;
        $transport_invoice->customer_id = $request->customer_id;
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

    public function generate(Request $request)
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
            'custom_report_id'
        ]);


        $transactions = TransportTransaction::with('ContractType')->with('Customer')->with('Supplier')->with('Transporter')->with('Product')
            ->index($filters)
            ->orderBy('transport_date_earliest', 'desc')
            ->get();

        if ($transactions != null) {

            $customer_report= CustomReport::where('id',$request->custom_report_id)->first();

            $datestamp = time();
            $file_name = ':nam_silvergrow_:dat.xlsx';
            $file_name = str_ireplace(':nam', $customer_report->name, $file_name);
            $file_name = str_ireplace(':dat', $datestamp, $file_name);

            try {
                $spreadsheet = $this->makeExcelDynamic($transactions,$request->custom_report_id);

                if ($spreadsheet != null) {
                    $spreadsheet->getProperties()
                        ->setCreator("silvergrow")
                        ->setLastModifiedBy("silvergrow")
                        ->setTitle("silvergrow")
                        ->setSubject("silvergrow");

                    $resource = tmpfile();
                    $writer = new Xlsx($spreadsheet);
                    $writer->save($resource);

                    $filePdf = Storage::put('reports/excel/' . $file_name, $resource);


                    return inertia(
                        'Transaction/Index',
                        [
                            'download_url' => $file_name
                        ]
                    );

                }


            } catch (\Error $e) {

                return "Error with your spreadsheet";

            } catch (Exception $e) {
            }
        }


    }

    public function makeExcel($transactions): ?Spreadsheet
    {

        try {

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet()->setTitle('report');

            $styleArray = array(
                'font' => array(
                    'bold' => true
                )
            );
            $styleArray1 = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ]
                ],
                'font' => array(
                    'bold' => true
                )
            ];

            $styleArray2 = array(
                'font' => array(
                    'bold' => true,
                    'size' => '13'
                ),
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            );

            //ID	TYPE	DATE	SUPPLIER	CUSTOMER	TRANSPORTER
            $sheet->setCellValue('A1', 'ID');
            $sheet->setCellValue('B1', 'TYPE');
            $sheet->setCellValue('C1', 'DATE_EARLIEST');
            $sheet->setCellValue('D1', 'DATE_LATEST');
            $sheet->setCellValue('E1', 'SUPPLIER');
            $sheet->setCellValue('F1', 'CUSTOMER');
            $sheet->setCellValue('G1', 'TRANSPORTER');
            $sheet->setCellValue('H1', 'PRODUCT');
            $sheet->setCellValue('I1', 'PLANNED_IN');
            $sheet->setCellValue('J1', 'PLANNED_OUT');
            $sheet->setCellValue('K1', 'GP');

            //Set style of headings

            $sheet->getStyle('A1' . ':' . 'k1')->applyFromArray($styleArray1);

            //loop over trans

            $row_count = 2;

            if ($transactions != null) {

                $pos = 0;
                for ($r = $row_count; $r < count($transactions) + $row_count; $r++) {

                    $trans = $transactions[$pos];
                    $sheet->setCellValue([1, $r], $trans->id);
                    $sheet->setCellValue([2, $r], $trans->ContractType->name);
                    $sheet->setCellValue([3, $r], $trans->transport_date_earliest);
                    $sheet->setCellValue([4, $r], $trans->transport_date_latest);
                    $sheet->setCellValue([5, $r], $trans->Supplier->last_legal_name);
                    $sheet->setCellValue([6, $r], $trans->Customer->last_legal_name);
                    $sheet->setCellValue([7, $r], $trans->Transporter->last_legal_name);
                    $sheet->setCellValue([8, $r], $trans->Product->name);
                    $sheet->setCellValue([9, $r], $trans->TransportLoad->no_units_incoming);
                    $sheet->setCellValue([10, $r], $trans->TransportLoad->no_units_outgoing);
                    $sheet->setCellValue([11, $r], $trans->TransportFinance->gross_profit);

                    $sheet
                        ->getStyle([11, $r])
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_ACCOUNTING_USD);

                    // $sheet->setCellValueByColumnAndRow(1,$r,$investor->acc_num);

                    $pos++;
                }
                $row_count += count($transactions) + 1;

            }


            foreach ($sheet->getColumnIterator() as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }

            $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
            $sheet->getPageSetup()->setFitToPage(true);
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);


            return $spreadsheet;


        } catch (\Error $e) {

            return null;
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
        }


        return null;
    }

    public function makeExcelDynamic($transactions,$custom_report_id): ?Spreadsheet
    {

        try {

            $custom_report = CustomReport::where('id',$custom_report_id)->first();
            $custom_report_model = $custom_report->CustomReportModels;


            $transport_transaction = TransportTransaction::first();
            $customer = Customer::first();
            $customer_parent = CustomerParent::first();
            $deal_ticket = DealTicket::first();
            $product = Product::first();
            $regular_driver = RegularDriver::first();
            $regular_vehicle = RegularVehicle::first();
            $supplier = Supplier::first();
            $transporter = Transporter::first();
            $transport_finance = TransportFinance::first();
            $transport_invoice = TransportInvoice::first();
            $transport_invoice_details = TransportInvoiceDetails::first();
            $transport_job = TransportJob::first();
            $transport_load = TransportLoad::first();


            $model_transport_transactions = CustomReportModel::where('report_id',$custom_report->id)->where('class_name',get_class($transport_transaction))->get();
            $model_deal_ticket = CustomReportModel::where('report_id',$custom_report->id)->where('class_name',get_class($deal_ticket))->get();
            $model_transport_finance = CustomReportModel::where('report_id',$custom_report->id)->where('class_name',get_class($transport_finance))->get();
            $model_transport_invoice = CustomReportModel::where('report_id',$custom_report->id)->where('class_name',get_class($transport_invoice))->get();
            $model_transport_invoice_details = CustomReportModel::where('report_id',$custom_report->id)->where('class_name',get_class($transport_invoice_details))->get();
            $model_transport_job= CustomReportModel::where('report_id',$custom_report->id)->where('class_name',get_class($transport_job))->get();
            $transport_load = CustomReportModel::where('report_id',$custom_report->id)->where('class_name',get_class($transport_load))->get();
            $model_customer = CustomReportModel::where('report_id',$custom_report->id)->where('class_name',get_class($customer))->get();
            $model_customer_parent = CustomReportModel::where('report_id',$custom_report->id)->where('class_name',get_class($customer_parent))->get();
            $model_product = CustomReportModel::where('report_id',$custom_report->id)->where('class_name',get_class($product))->get();
            $model_regular_driver = CustomReportModel::where('report_id',$custom_report->id)->where('class_name',get_class($regular_driver))->get();
            $model_regular_vehicle = CustomReportModel::where('report_id',$custom_report->id)->where('class_name',get_class($regular_vehicle))->get();
            $model_supplier = CustomReportModel::where('report_id',$custom_report->id)->where('class_name',get_class($supplier))->get();
            $model_transporter = CustomReportModel::where('report_id',$custom_report->id)->where('class_name',get_class($transporter))->get();



            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet()->setTitle('report');

            $styleArray = array(
                'font' => array(
                    'bold' => true
                )
            );
            $styleArray1 = [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN
                    ]
                ],
                'font' => array(
                    'bold' => true
                )
            ];

            $styleArray2 = array(
                'font' => array(
                    'bold' => true,
                    'size' => '13'
                ),
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            );

            $row_for_heading = 1;

            foreach ($custom_report_model as $model){
                $sheet->setCellValue([$row_for_heading,1], $model->attribute_name);
                $row_for_heading++;
            }


            for ($x = 1; $x < $row_for_heading; $x++) {
                $sheet->getStyleByColumnAndRow($x, 1)->applyFromArray($styleArray1);
            }

            //loop over trans

            $row_count = 2;

           if ($transactions != null) {

                $pos = 0;
                $col = 1;

                for ($r = $row_count; $r < count($transactions) + $row_count; $r++) {

                    $trans = $transactions[$pos];

                    //trans
                    foreach ($model_transport_transactions as $model){
                        $attribute = $model->attribute_name;
                        $sheet->setCellValue([$col, $r], $trans->$attribute);
                        $col++;
                    }

                    //deal ticket
                    foreach ($model_deal_ticket as $model){
                        $attribute = $model->attribute_name;
                        $sheet->setCellValue([$col, $r], $trans->DealTicket->$attribute);
                        $col++;
                    }

                    //transport finance
                    foreach ($model_transport_finance as $model){
                        $attribute = $model->attribute_name;
                        $sheet->setCellValue([$col, $r], $trans->TransportFinance->$attribute);
                        $col++;
                    }

                    //$model_transport_invoice
                    foreach ($model_transport_invoice as $model){
                        $attribute = $model->attribute_name;
                        $sheet->setCellValue([$col, $r], $trans->TransportInvoice->$attribute);
                        $col++;
                    }

                    //$model_transport_invoice_details
                    foreach ($model_transport_invoice_details as $model){
                        $attribute = $model->attribute_name;
                        $sheet->setCellValue([$col, $r], $trans->TransportInvoice->TransportInvoiceDetails->$attribute);
                        $col++;
                    }

                    //$model_transport_job
                    foreach ($model_transport_job as $model){
                        $attribute = $model->attribute_name;
                        $sheet->setCellValue([$col, $r], $trans->TransportJob->$attribute);
                        $col++;
                    }

                    //$transport_load
                    foreach ($transport_load as $model){
                        $attribute = $model->attribute_name;
                        $sheet->setCellValue([$col, $r], $trans->TransportLoad->$attribute);
                        $col++;
                    }

                    //$model_customer
                    foreach ($model_customer as $model){
                        $attribute = $model->attribute_name;
                        $sheet->setCellValue([$col, $r], $trans->Customer->$attribute);
                        $col++;
                    }

                    //$model_customer
                    foreach ($model_customer_parent as $model){
                        $attribute = $model->attribute_name;
                        $sheet->setCellValue([$col, $r], $trans->CustomerParent->$attribute);
                        $col++;
                    }


                    //$model_product
                    foreach ($model_product as $model){
                        $attribute = $model->attribute_name;
                        $sheet->setCellValue([$col, $r], $trans->Product->$attribute);
                        $col++;
                    }

                   /* //$model_regular_driver
                    foreach ($model_regular_driver as $model){
                        $attribute = $model->attribute_name;
                        $sheet->setCellValue([$col, $r], $trans->TransportFinance->$attribute);
                        $col++;
                    }

                    //$model_regular_vehicle
                    foreach ($model_regular_vehicle as $model){
                        $attribute = $model->attribute_name;
                        $sheet->setCellValue([$col, $r], $trans->TransportFinance->$attribute);
                        $col++;
                    }*/


                    //$model_supplier
                    foreach ($model_supplier as $model){
                        $attribute = $model->attribute_name;
                        $sheet->setCellValue([$col, $r], $trans->Supplier->$attribute);
                        $col++;
                    }

                    //$model_transporter
                    foreach ($model_transporter as $model){
                        $attribute = $model->attribute_name;
                        $sheet->setCellValue([$col, $r], $trans->Transporter->$attribute);
                        $col++;
                    }





                    $col = 1;


                    $sheet
                        ->getStyle([11, $r])
                        ->getNumberFormat()
                        ->setFormatCode(NumberFormat::FORMAT_ACCOUNTING_USD);

                    // $sheet->setCellValueByColumnAndRow(1,$r,$investor->acc_num);

                    $pos++;
                }
                $row_count += count($transactions) + 1;

            }


            foreach ($sheet->getColumnIterator() as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }

            $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
            $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
            $sheet->getPageSetup()->setFitToPage(true);
            $sheet->getPageSetup()->setFitToWidth(1);
            $sheet->getPageSetup()->setFitToHeight(0);


            return $spreadsheet;


        } catch (\Error $e) {

            return null;
        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
        }


        return null;
    }


    public function download($file_name): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return Storage::download('/reports/excel/' . $file_name);
    }


}
