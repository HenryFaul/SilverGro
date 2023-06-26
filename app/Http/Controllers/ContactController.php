<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactType;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
            'first_name' => ['nullable', 'string'],
            'last_legal_name' => ['required', 'string'],
            'nickname' => ['nullable', 'string'],
            'title' => ['nullable', 'string'],
            'job_description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'branch' => ['nullable', 'string'],
            'department' => ['nullable', 'string'],
            'comment' => ['nullable', 'string'],
            'related_class' => ['required', 'string'],
            'related_id' => ['required', 'integer'],
        ]);

        //['first_name','last_legal_name','nickname','title','job_description','id_reg_no','is_active',
        //        'branch','department','comment', 'poly_contact_type','poly_contact_id'];


        $contact = Contact::create([
            'first_name' => $request['first_name'],
            'last_legal_name' => $request['last_legal_name'],
            'nickname' => $request['nickname'],
            'title' => $request['title'],
            'id_reg_no' => $request['id_reg_no'],
            'job_description' => $request['job_description'],
            'is_active' => $request['is_active'],
            'branch' => $request['branch'],
            'department' => $request['department'],
            'comment' => $request['comment'],
            'poly_contact_type' => $request['related_class'],
            'poly_contact_id' => $request['related_id'],

        ]);



        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Contact created');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {

       // $customer->load('staff')->load('addressable')->load('contactable');

        $contact->load('emailable')->load('numberable');

        $contact_type = ContactType::all();

        return inertia(
            'Contact/Show',
            [
                'contact' => $contact,
                'contact_type'=>$contact_type
            ]
        );


    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        //
    }
}
