<?php

namespace App\Http\Controllers;

use App\Models\ContactType;
use App\Models\Customer;
use App\Models\CustomerRating;
use App\Models\InvoiceBasis;
use App\Models\Staff;
use App\Models\Supplier;
use App\Models\TermsOfPayment;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'searchName',
            'isActive',
            'field',
            'direction',
            'show'
        ]);

        $paginate = $request['show'] ?? 10;

        $suppliers = Supplier::with('TermsOfPayment')->filter($filters)
            ->paginate($paginate)
            ->withQueryString();

        return inertia(
            'Supplier/Index',
            [
                'filters' => $filters,
                'suppliers' => $suppliers,

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
    public function show(Supplier $supplier)
    {
        $supplier->load('staff')->load('addressable')->load('contactable');
        $invoice_basis = InvoiceBasis::all();
        $terms_of_payment = TermsOfPayment::all();
        $staff = Staff::all();
        $contact_type = ContactType::all();


        return inertia(
            'Supplier/Show',
            [
                'supplier' => $supplier,
                'invoice_basis'=>$invoice_basis,
                'terms_of_payment'=>$terms_of_payment,
                'staff'=>$staff,
                'contact_type'=>$contact_type
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {

       /* 'id','first_name','last_legal_name','nickname','title','job_description','id_reg_no','is_active',
        'terms_of_payment_id','account_number','comment*/

        $supplier->update(
            $request->validate([
                'first_name' => ['nullable','string'],
                'last_legal_name' => ['nullable','string'],
                'nickname' => ['nullable','string'],
                'title' => ['nullable','string'],
                'id_reg_no' => ['nullable','string'],
                'is_active' => ['nullable','boolean'],
                'terms_of_payment_id' => ['required', 'integer','exists:terms_of_payments,id'],
                'account_number' => ['nullable','string'],
                'comment' => ['nullable','string'],
            ])
        );

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Supplier updated');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        //
    }
}
