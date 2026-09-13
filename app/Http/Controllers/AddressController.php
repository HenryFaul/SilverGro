<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\TransportLoad;
use App\Rules\StaffAssignRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'code' => ['required', 'string', 'max:20'],
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

        // Editing an existing address has to work for every row already in the
        // book, including the ones the migration left half-filled: 1,120 have no
        // street line, 205 have no country and one has an over-length code. With
        // those fields required, opening any of them and pressing Save failed
        // validation - and because the modal only logged the error, it looked as
        // though editing had been removed altogether.
        //
        // So the update rules describe what we can store, not what we would like
        // to have been captured. Creating a new address still demands the lot.
        $address->update(
            $request->validate([
                'address_type_id'=>['required', 'integer','exists:address_types,id'],
                'line_1' => ['nullable', 'string'],
                'line_2' => ['nullable', 'string'],
                'line_3' => ['nullable', 'string'],
                'country' => ['nullable', 'string'],
                'code' => ['nullable', 'string', 'max:50'],
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
        // Trades reference addresses by id. Deleting one that is still in use
        // orphans every trade pointing at it, which blanks the address on their
        // documents (and used to 500 the PDF outright). Refuse instead.
        $inUse = TransportLoad::where('delivery_address_id', $address->id)
            ->orWhere('collection_address_id', $address->id)
            ->count();

        if ($inUse > 0) {
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner',
                'This address cannot be deleted: it is used by ' . $inUse . ' trade' . ($inUse == 1 ? '' : 's') .
                '. Hide it instead - it will disappear from the address lists and dropdowns, ' .
                'while those trades and their documents keep working.');

            return redirect()->back();
        }

        $address->delete();
        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Address deleted');

        return redirect()->back();
    }

    /**
     * Take an address out of the pickers without touching the trades that use it.
     *
     * Most of the surplus addresses are referenced by a trade, so they cannot be
     * deleted - doing that blanks the address on documents that have already been
     * issued. Hiding leaves the row exactly where it is and only removes it from
     * the lists and dropdowns where somebody is choosing an address.
     */
    public function hide(Request $request, Address $address)
    {
        $address->hidden_at = now();
        $address->hidden_by_id = Auth::id();
        $address->save();

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Address hidden. It stays on the trades that already use it.');

        return redirect()->back();
    }

    public function unhide(Request $request, Address $address)
    {
        $address->hidden_at = null;
        $address->hidden_by_id = null;
        $address->save();

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Address restored');

        return redirect()->back();
    }
}
