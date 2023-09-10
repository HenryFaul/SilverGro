<?php

namespace App\Http\Controllers;

use App\Models\ContractType;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\TransactionSpreadSheet;
use App\Models\Transporter;
use App\Models\TransportTransaction;
use Illuminate\Http\Request;

class TransactionSpreadSheetController extends Controller
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
            ->with('TransportLoad')->with('TransportJob',fn($query) => $query->with('TransportDriverVehicle', fn($query) => $query->with('Driver')->with('Vehicle', fn($query) => $query->with('VehicleType'))))->with('DealTicket')->with('PurchaseOrder')->with('SalesOrder')->with('TransportOrder')->with('TransportInvoice')
            ->index($filters)
            ->orderBy('transport_date_earliest', 'desc')
            ->paginate($paginate)
            ->withQueryString();

        $all_suppliers = Supplier::all();
        $all_customers = Customer::all();
        $all_transporters = Transporter::all();
        $all_products = Product::all();
        $contract_types = ContractType::all();


        return inertia(
            'TransactionSpreadSheet/Index',
            [
                'transactions'=>$transactions,
                'filters'=>$filters,
                'all_suppliers'=>$all_suppliers,
                'all_customers'=>$all_customers,
                'all_transporters'=>$all_transporters,
                'all_products'=>$all_products,
                'contract_types'=>$contract_types
                ]);
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
    public function show(TransactionSpreadSheet $transactionSpreadSheet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionSpreadSheet $transactionSpreadSheet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransactionSpreadSheet $transactionSpreadSheet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionSpreadSheet $transactionSpreadSheet)
    {
        //
    }
}
