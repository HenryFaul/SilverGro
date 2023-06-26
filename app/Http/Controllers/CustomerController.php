<?php

namespace App\Http\Controllers;

use App\Models\ContactType;
use App\Models\Customer;
use App\Models\CustomerRating;
use App\Models\FlexType;
use App\Models\InvoiceBasis;
use App\Models\ProductionModel;
use App\Models\Staff;
use App\Models\TermsOfPayment;
use App\Rules\StaffAssignRule;
use Illuminate\Http\Request;

class CustomerController extends Controller
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

        $customers = Customer::with('CustomerRating')->filter($filters)
            ->paginate($paginate)
            ->withQueryString();

        return inertia(
            'Customer/Index',
            [
                'filters' => $filters,
                'customers' => $customers,

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
    public function show(Customer $customer)
    {
        //

        $customer->load('staff')->load('addressable')->load('contactable');
        $invoice_basis = InvoiceBasis::all();
        $customer_rating = CustomerRating::all();
        $terms_of_payment = TermsOfPayment::all();
        $staff = Staff::all();
        $contact_type = ContactType::all();


        return inertia(
            'Customer/Show',
            [
                'customer' => $customer,
                'invoice_basis'=>$invoice_basis,
                'customer_rating'=>$customer_rating,
                'terms_of_payment'=>$terms_of_payment,
                'staff'=>$staff,
                'contact_type'=>$contact_type
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Customer $customer)
    {
        //['first_name','last_legal_name','nickname','title','id_reg_no','is_active'
        //        ,'terms_of_payment_id','invoice_basis_id','customer_rating_id','days_overdue_allowed_id','is_vat_exempt','is_vat_cert_received',
        //        'credit_limit','credit_limit_hard','comment'];
        $customer->update(
            $request->validate([
                'first_name' => ['nullable','string'],
                'last_legal_name' => ['nullable','string'],
                'nickname' => ['nullable','string'],
                'title' => ['nullable','string'],
                'id_reg_no' => ['nullable','string'],
                'is_active' => ['nullable','boolean'],
                'terms_of_payment_id' => ['required', 'integer','exists:terms_of_payments,id'],
                'invoice_basis_id' => ['required', 'integer','exists:invoice_bases,id'],
                'customer_rating_id' => ['required', 'integer','exists:customer_ratings,id'],
                'days_overdue_allowed_id' => ['required', 'integer','exists:terms_of_payments,id'],
                'is_vat_exempt' => ['nullable','boolean'],
                'is_vat_cert_received' => ['nullable','boolean'],
                'credit_limit' => ['nullable','integer'],
                'credit_limit_hard' => ['nullable','integer'],
                'comment' => ['nullable','string'],
            ])
        );

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Customer updated');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
