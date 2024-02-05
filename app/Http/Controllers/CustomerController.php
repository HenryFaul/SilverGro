<?php

namespace App\Http\Controllers;

use App\Models\ContactType;
use App\Models\Customer;
use App\Models\CustomerParent;
use App\Models\CustomerRating;
use App\Models\FlexType;
use App\Models\InvoiceBasis;
use App\Models\ProductionModel;
use App\Models\Staff;
use App\Models\TermsOfPayment;
use App\Models\TermsOfPaymentBasis;
use App\Models\TransportInvoice;
use App\Rules\StaffAssignRule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerController extends Controller
{

    public function getDebtor(Request $request): \Inertia\Response|\Inertia\ResponseFactory
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

        $customers = Customer::with('CustomerRating')->with('TermsOfPayment')->with('TermsOfPaymentBasis')
            ->with('InvoiceBasis')->where('is_active',true)
            ->filter($filters)
            ->paginate($paginate)
            ->withQueryString();



        $all_total_outstanding = 0;
        $all_total_overdue = 0;
        $cur_date = (Carbon::now()->tz('Africa/Johannesburg'));



        foreach ($customers as $customer){

            $customer_total_outstanding = 0;
            $customer_total_overdue = 0;

            //Get invoices for the customer
            $invoices = TransportInvoice::where('customer_id',$customer->id)->get();

            //Loop over the invoices

            foreach ($invoices as $invoice){

               $invoice_detail = $invoice->TransportInvoiceDetails;
               //if amount paid less than invoice amount

                if ($invoice_detail->invoice_amount_paid < $invoice_detail->invoice_amount){
                    $customer_total_outstanding += $invoice_detail->invoice_amount;
                    $all_total_outstanding += $invoice_detail->invoice_amount;

                    $day_to_add = $customer->TermsOfPayment->days;
                    $invoice_date = Carbon::create($invoice_detail->invoice_date);
                    $adjusted_date = $invoice_date->addDays($day_to_add);

                    if($adjusted_date < $cur_date){

                        $customer_total_overdue += $invoice_detail->invoice_amount;
                        $customer_total_overdue += $invoice_detail->invoice_amount;
                    }

                }

            }

            $customer['customer_total_outstanding']=round($customer_total_outstanding,2);
            $customer['customer_total_overdue']= round($customer_total_overdue,2);


        }

        $filtered = $customers->filter(function ($value, $key) {
            return $value->customer_total_outstanding > 0;
        });

        return inertia(
            'Customer/DebtorStanding',
            [
                'filters' => $filters,
                'customers' => $customers,

            ]
        );
    }


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


        $customer = Customer::create([
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
                'customer_parent_id'=>1,
                'is_vat_exempt' => 0,
                'is_vat_cert_received' => 0,
                'credit_limit' => 0,
                'credit_limit_hard' => 0,
                'comment' => $request->comment,
            ]);

        if ($customer->exists()) {
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Customer Created');
        }
        else{
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Customer NOT Created');
        }

        return redirect()->back();
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
        $terms_of_payment_basis = TermsOfPaymentBasis::all();
        $terms_of_payment = TermsOfPayment::all();
        $staff = Staff::all();
        $contact_type = ContactType::all();
        $all_customer_parents = CustomerParent::all();


        return inertia(
            'Customer/Show',
            [
                'customer' => $customer,
                'invoice_basis'=>$invoice_basis,
                'customer_rating'=>$customer_rating,
                'terms_of_payment_basis'=>$terms_of_payment_basis,
                'terms_of_payment'=>$terms_of_payment,
                'staff'=>$staff,
                'contact_type'=>$contact_type,
                'all_customer_parents'=>$all_customer_parents
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
                'is_vat_exempt' => ['nullable','boolean'],
                'is_vat_cert_received' => ['nullable','boolean'],
                'credit_limit' => ['nullable','integer'],
                'customer_parent_id'=> ['nullable','integer'],
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
