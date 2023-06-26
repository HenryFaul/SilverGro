<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Rules\StaffAssignRule;
use Illuminate\Http\Request;

class AddressController extends Controller
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
            'line_1' => ['required', 'string'],
            'line_2' => ['nullable', 'string'],
            'line_3' => ['nullable', 'string'],
            'country' => ['required', 'string'],
            'code' => ['required', 'integer'],
            'address_type_id' => ['required', 'integer','exists:address_types,id'],
            'directions' => ['nullable', 'string'],
            'is_primary' => ['nullable', 'boolean'],
            'related_class' => ['required', 'string'],
            'related_id' => ['required', 'integer'],

        ]);

        $address = Address::create([
            'line_1' => $request['line_1'],
            'line_2' => $request['line_2'],
            'line_3' => $request['line_3'],
            'country' => $request['country'],
            'code' => $request['code'],
            'address_type_id' => $request['address_type_id'],
            'poly_address_type' => $request['related_class'],
            'poly_address_id' => $request['related_id'],
            'is_primary' => $request['is_primary'],
            'longitude' => $request['longitude'],
            'latitude' => $request['latitude'],
            'directions' => $request['directions'],
        ]);

        //if current address is primary, set others to false
        if ($request['is_primary']) {
            Address::where('poly_address_type', $address->poly_address_type)->where('poly_address_id', $address->poly_address_id)
                ->where('id', '<>', $address->id)->update(['is_primary' => 0]);
        }

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Address created');
        return redirect()->back();


    }

    /**
     * Display the specified resource.
     */
    public function show(Address $address)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Address $address)
    {
        //edit
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Address $address): \Illuminate\Http\RedirectResponse
    {

        //['line_1','line_2','line_3','country','code','is_primary','longitude','latitude','directions','address_type_id','poly_address_type','poly_address_id'];

        $address->update(
            $request->validate([
                'address_type_id'=>['required', 'integer','exists:address_types,id'],
                'line_1' => ['required', 'string'],
                'line_2' => ['nullable', 'string'],
                'line_3' => ['nullable', 'string'],
                'country' => ['required', 'string'],
                'code' => ['required', 'integer'],
                'longitude' => ['nullable', 'numeric'],
                'latitude' => ['nullable', 'numeric'],
                'directions' => ['nullable', 'string'],
                'is_primary' => ['nullable', 'boolean'],
            ])
        );

        //if current address is primary, set others to false
        if ($request['is_primary']) {
            Address::where('poly_address_type', $address->poly_address_type)->where('poly_address_id', $address->poly_address_id)
                ->where('id', '<>', $address->id)->update(['is_primary' => 0]);
        }

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Address updated');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request,Address $address)
    {
        $address->delete();
        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Address deleted');

        return redirect()->back();
    }
}
