<?php

namespace App\Http\Controllers;

use App\Models\ContactType;
use App\Models\CustomerParent;
use App\Models\CustomerRating;
use App\Models\InvoiceBasis;
use App\Models\Staff;
use App\Models\TermsOfPayment;
use App\Models\TermsOfPaymentBasis;
use Illuminate\Http\Request;

class CustomerParentController extends Controller
{

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response|\Inertia\ResponseFactory
    {
        //abort(403);

        $filters = $request->only([
            'searchName',
            'isActive',
            'field',
            'direction',
            'show'
        ]);

        $paginate = $request['show'] ?? 10;

        $customer_parent = CustomerParent::with('CustomerRating')->filter($filters)
            ->paginate($paginate)
            ->withQueryString();

        return inertia(
            'CustomerParents/Index',
            [
                'filters' => $filters,
                'customer_parents' => $customer_parent,

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
            'first_name' => ['required','string'],
            'last_legal_name' => ['nullable','string','string','unique:customers,last_legal_name'],
            'nickname' => ['nullable','string'],
            'title' => ['nullable','string'],
            'id_reg_no' => ['nullable','string','unique:customers,id_reg_no'],
            'comment' => ['nullable','string'],
        ]);


        $customer_parent = CustomerParent::create([
            'first_name' => $request->first_name,
            'last_legal_name' => $request->last_legal_name,
            'nickname' => $request->nickname,
            'title' => $request->title,
            'id_reg_no' => $request->id_reg_no,
            'is_active' => 1,
            'terms_of_payment_basis_id'=>1,
            'terms_of_payment_id' => 1,
            'invoice_basis_id' => 1,
            'customer_rating_id' => 1,
            'is_vat_exempt' => 0,
            'is_vat_cert_received' => 0,
            'credit_limit' => 0,
            'credit_limit_hard' => 0,
            'comment' => $request->comment,
        ]);

        if ($customer_parent->exists()) {
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Customer Parent Created');
        }
        else{
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Customer Parent NOT Created');
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomerParent $customerParent)
    {
        //

        $customerParent->load('staff')->load('addressable')->load('contactable')->load('Customer');
        $invoice_basis = InvoiceBasis::all();
        $customer_rating = CustomerRating::all();
        $terms_of_payment_basis = TermsOfPaymentBasis::all();
        $terms_of_payment = TermsOfPayment::all();
        $staff = Staff::all();
        $contact_type = ContactType::all();


        return inertia(
            'CustomerParents/Show',
            [
                'customer_parent' => $customerParent,
                'invoice_basis'=>$invoice_basis,
                'customer_rating'=>$customer_rating,
                'terms_of_payment_basis'=>$terms_of_payment_basis,
                'terms_of_payment'=>$terms_of_payment,
                'staff'=>$staff,
                'contact_type'=>$contact_type
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomerParent $customerParent)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomerParent $customerParent)
    {
        //['first_name','last_legal_name','nickname','title','id_reg_no','is_active'
        //        ,'terms_of_payment_id','invoice_basis_id','customer_rating_id','days_overdue_allowed_id','is_vat_exempt','is_vat_cert_received',
        //        'credit_limit','credit_limit_hard','comment'];
        $customerParent->update(
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
                'is_vat_exempt' => ['nullable','boolean'],
                'is_vat_cert_received' => ['nullable','boolean'],
                'credit_limit' => ['nullable','integer'],
                'credit_limit_hard' => ['nullable','integer'],
                'comment' => ['nullable','string'],
            ])
        );

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Customer Parent updated');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerParent $customerParent)
    {
        //
    }
}
