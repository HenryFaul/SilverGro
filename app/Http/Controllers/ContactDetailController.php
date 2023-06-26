<?php

namespace App\Http\Controllers;

use App\Models\ContactDetail;
use Illuminate\Http\Request;

class ContactDetailController extends Controller
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
            'value' => ['required', 'string'],
            'comment' => ['nullable', 'string'],
            'country_code' => ['nullable', 'number'],
            'related_class' => ['required', 'string'],
            'related_id' => ['required', 'integer'],
        ]);

       // 'value','comment','contact_detail_type_id','poly_number_type','poly_number_id'];
        $contactDetail = ContactDetail::create([
            'value' => $request['value'],
            'comment' => $request['comment'],
            'country_code' => $request['country_code'],
            'poly_contact_type' => $request['related_class'],
            'poly_contact_id' => $request['related_id'],

        ]);



        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Contact Detail created');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(ContactDetail $contactDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContactDetail $contactDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ContactDetail $contactDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ContactDetail $contactDetail)
    {
        //
    }
}
