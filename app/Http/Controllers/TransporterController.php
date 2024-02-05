<?php

namespace App\Http\Controllers;

use App\Models\ContactType;
use App\Models\Customer;
use App\Models\Supplier;
use App\Models\TermsOfPayment;
use App\Models\Transporter;
use Illuminate\Http\Request;

class TransporterController extends Controller
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

        $customers = Transporter::with('TermsOfPayment')->filter($filters)
            ->paginate($paginate)
            ->withQueryString();

        return inertia(
            'Transporter/Index',
            [
                'filters' => $filters,
                'customers' => $customers,
                'transporters'=>$customers

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
        $request->validate([
            'first_name' => ['required','string'],
            'last_legal_name' => ['required','string','string','unique:transporters,last_legal_name'],
            'nickname' => ['nullable','string'],
            'title' => ['nullable','string'],
            'id_reg_no' => ['nullable','string','unique:customers,id_reg_no'],
            'comment' => ['nullable','string'],
        ]);

        $transporter = Transporter::create([
            'first_name' => $request->first_name,
            'last_legal_name' => $request->last_legal_name,
            'nickname' => $request->nickname,
            'title' => $request->title,
            'id_reg_no' => $request->id_reg_no,
            'is_active' => 1,
            'terms_of_payment_id' => 1,
            'is_git' => 0,
            'comment' => $request->comment,
        ]);

        if ($transporter->exists()) {
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Transporter Created');
        }
        else{
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Transporter NOT Created');
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Transporter $transporter)
    {
        $transporter->load('TermsOfPayment')->load('contactable');

        $terms_of_payments = TermsOfPayment::all();
        $contact_type = ContactType::all();


        return inertia(
            'Transporter/Show',
            [
                'transporter' => $transporter,
                'terms_of_payments'=>$terms_of_payments,
                'contact_type'=>$contact_type
        ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transporter $transporter)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Transporter $transporter)
    {

     /*   'id','first_name','last_legal_name','nickname','title','job_description','id_reg_no','is_active',
        'terms_of_payment_id','account_number','comment','is_git'*/


        $transporter->update(
            $request->validate([
                'first_name' => ['nullable', 'string'],
                'last_legal_name' => ['required', 'string'],
                'nickname' => ['nullable', 'string'],
                'title' => ['nullable', 'string'],
                'job_description' => ['nullable', 'string'],
                'is_active' => ['nullable', 'boolean'],
                'terms_of_payment_id' => ['required', 'integer','exists:terms_of_payments,id'],
                'account_number' => ['nullable', 'string'],
                'is_git' => ['nullable', 'boolean'],
                'comment' => ['nullable', 'string'],
            ])
        );

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Transporter updated');

        return redirect()->back();
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Transporter $transporter)
    {
        //
    }
}
