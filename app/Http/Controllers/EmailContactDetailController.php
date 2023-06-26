<?php

namespace App\Http\Controllers;

use App\Models\ContactDetail;
use App\Models\EmailContactDetail;
use App\Models\NumberContactDetail;
use Illuminate\Http\Request;

class EmailContactDetailController extends Controller
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
            'email' => ['required', 'email'],
            'comment' => ['nullable', 'string'],
            'related_class' => ['required', 'string'],
            'related_id' => ['required', 'integer'],
            'contact_detail_type_id'=> ['required', 'integer'],
        ]);

        $contactDetail = EmailContactDetail::create([
            'value' => $request['email'],
            'comment' => $request['comment'],
            'contact_detail_type_id'=>$request['contact_detail_type_id'],
            'poly_email_type' => $request['related_class'],
            'poly_email_id' => $request['related_id'],
        ]);

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Number created');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(EmailContactDetail $emailContactDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EmailContactDetail $emailContactDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EmailContactDetail $emailContactDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EmailContactDetail $emailContactDetail)
    {
        //
    }
}
