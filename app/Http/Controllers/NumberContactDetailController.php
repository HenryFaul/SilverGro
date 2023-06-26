<?php

namespace App\Http\Controllers;

use App\Models\ContactDetail;
use App\Models\NumberContactDetail;
use Illuminate\Http\Request;

class NumberContactDetailController extends Controller
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
            'number' => ['required', 'numeric', 'digits:10'],
            'comment' => ['nullable', 'string'],
            'country_code' => ['required', 'string'],
            'related_class' => ['required', 'string'],
            'related_id' => ['required', 'integer'],
            'contact_detail_type_id'=> ['required', 'integer'],
        ]);

        $contactDetail = NumberContactDetail::create([
            'value' => $request['number'],
            'comment' => $request['comment'],
            'country_code' => $request['country_code'],
            'contact_detail_type_id'=>$request['contact_detail_type_id'],
            'poly_number_type' => $request['related_class'],
            'poly_number_id' => $request['related_id'],
        ]);

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Number created');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(NumberContactDetail $numberContactDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(NumberContactDetail $numberContactDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, NumberContactDetail $numberContactDetail)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NumberContactDetail $numberContactDetail)
    {
        //
    }
}
