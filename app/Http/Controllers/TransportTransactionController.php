<?php

namespace App\Http\Controllers;

use App\Models\BillingUnits;
use App\Models\ConfirmationTypes;
use App\Models\ContractType;
use App\Models\Customer;
use App\Models\InvoiceBasis;
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
use App\Models\Transporter;
use App\Models\TransportJob;
use App\Models\TransportLoad;
use App\Models\TransportRateBasis;
use App\Models\TransportTransaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

class TransportTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, User $user)
    {
        $view_only = $user->cannot('ViewOnly');

        if ($view_only){
            return to_route('no_permission');
        }

        $filters = $request->only([
            'searchName',
            'isActive',
            'field',
            'direction',
            'show'
        ]);

        $paginate = $request['show'] ?? 10;

        $transactions = TransportTransaction::with('Customer')->with('Supplier')->with('Transporter')
            ->paginate($paginate)
            ->withQueryString();

        return inertia(
            'Transaction/Index',
            [
                'filters' => $filters,
                'transactions' => $transactions,

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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TransportTransaction $transportTransaction): Response|ResponseFactory
    {

        $transportTransaction= TransportTransaction::where('id',$transportTransaction->id)->with('TransportLoad')->with('TransportFinance')->with('TransportStatus', fn($query) => $query->with('StatusEntity')->with('StatusType'))->with('AssignedUserComm', fn($query) => $query->with('AssignedUserSupplier')->with('AssignedUserCustomer'))
            ->with('TransportJob', fn($query) => $query->with('OffloadingHoursFrom')->with('OffloadingHoursTo')
                ->with('TransportDriverVehicle', fn($query) => $query->with('Driver')->with('Vehicle', fn($query) => $query->with('VehicleType'))))->first();

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


        return inertia(
            'Transaction/Show',
            [
                'transaction' => $transportTransaction,
                'all_customers' => $customers,
                'all_products' => $products,
                'contract_types' => $contract_types,
                'all_suppliers' => $suppliers,
                'all_transporters' => $transporters,
                'all_staff'=>$staff,
                'confirmation_types'=>$confirmation_types,
                'all_product_sources'=>$product_sources,
                'all_packaging'=>$packaging,
                'all_billing_units'=>$billing_units,
                'loading_hour_options'=>$loading_hour_options,
                'all_drivers'=>$all_drivers,
                'all_vehicles'=>$all_vehicles,
                'all_transport_rates'=>$all_transport_rates,
                'all_status_entities'=>$all_status_entities,
                'all_status_types'=>$all_status_types
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


        if($is_updated){
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Transport Transaction updated');
        }
        else {
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
