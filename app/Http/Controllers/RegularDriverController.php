<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RegularDriver;
use Illuminate\Http\Request;
use SebastianBergmann\CodeCoverage\Driver\Driver;

class RegularDriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): \Inertia\Response|\Inertia\ResponseFactory
    {

        $filters = $request->only([
            'searchName',
            'isActive',
            'field',
            'direction',
            'show'
        ]);

        $paginate = $request['show'] ?? 10;

        $regular_drivers = RegularDriver::filter($filters)
            ->paginate($paginate)
            ->withQueryString();



        return inertia(
            'Driver/Index',
            [
                'filters' => $filters,
                'drivers' => $regular_drivers,
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
            'last_name' => ['required','string'],
            'comment' => ['nullable','string'],
            'cell_no' => ['required','string'],
        ]);

        $driver = RegularDriver::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'comment'=>$request->comment,
            'cell_no'=>$request->cell_no
        ]);

        if ($driver->exists()) {
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Driver Created');
        }
        else{
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Driver NOT Created');
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(RegularDriver $regular_driver): \Inertia\Response|\Inertia\ResponseFactory
    {
        return inertia(
            'Driver/Show',
            [
                'driver' => $regular_driver,
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RegularDriver $regularDriver)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RegularDriver $regularDriver)
    {
        $regularDriver->update(
            $request->validate([
                'first_name' => ['nullable','string'],
                'last_name' => ['nullable','string'],
                'cell_no' => ['nullable','string'],
                'is_active' => ['nullable','boolean'],
                'comment' => ['nullable','string'],
            ])
        );

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Driver updated');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RegularDriver $regularDriver)
    {
        //
    }
}
