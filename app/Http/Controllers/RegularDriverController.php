<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\RegularDriver;
use App\Models\TransportDriverVehicle;
use App\Models\Transporter;
use App\Models\TransportTransaction;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;
use SebastianBergmann\CodeCoverage\Driver\Driver;

class RegularDriverController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response|ResponseFactory
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

        // Get transporters and last vehicle for each driver
        $regular_drivers->getCollection()->transform(function ($driver) {
            // Get unique transporters for this driver
            $transporterIds = TransportTransaction::whereHas('TransportDriverVehicle', function($query) use ($driver) {
                $query->where('regular_driver_id', $driver->id);
            })->pluck('transporter_id')->unique();

            $driver->transporters = Transporter::whereIn('id', $transporterIds)
                ->select('id', 'first_name', 'last_legal_name')
                ->get();

            // Get the last vehicle associated with this driver based on actual transport activity
            $lastDriverVehicle = TransportDriverVehicle::where('regular_driver_id', $driver->id)
                ->whereNotNull('regular_vehicle_id')
                ->with('Vehicle:id,reg_no')
                ->orderByRaw('COALESCE(date_delivered, date_onroad, date_loaded, date_scheduled, created_at) DESC')
                ->first();

            $driver->last_vehicle = $lastDriverVehicle ? $lastDriverVehicle->Vehicle : null;

            return $driver;
        });

        return inertia(
            'Driver/Index',
            [
                'filters' => $filters,
                'drivers' => $regular_drivers,
            ]
        );
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(RegularDriver $regular_driver): Response|ResponseFactory
    {
        // Get transporters for this driver
        $transporterIds = TransportTransaction::whereHas('TransportDriverVehicle', function($query) use ($regular_driver) {
            $query->where('regular_driver_id', $regular_driver->id);
        })->pluck('transporter_id')->unique();

        $transporters = Transporter::whereIn('id', $transporterIds)
            ->select('id', 'first_name', 'last_legal_name')
            ->get();

        // Get the last vehicle associated with this driver based on actual transport activity
        $lastDriverVehicle = TransportDriverVehicle::where('regular_driver_id', $regular_driver->id)
            ->whereNotNull('regular_vehicle_id')
            ->with('Vehicle:id,reg_no')
            ->orderByRaw('COALESCE(date_delivered, date_onroad, date_loaded, date_scheduled, created_at) DESC')
            ->first();

        $lastVehicle = $lastDriverVehicle ? $lastDriverVehicle->Vehicle : null;

        return inertia(
            'Driver/Show',
            [
                'driver' => $regular_driver,
                'transporters' => $transporters,
                'last_vehicle' => $lastVehicle
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
