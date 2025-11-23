<?php

namespace App\Http\Controllers;

use App\Models\RegularDriver;
use App\Models\RegularVehicle;
use App\Models\TransportDriverVehicle;
use App\Models\Transporter;
use App\Models\TransportTransaction;
use App\Models\VehicleType;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\ResponseFactory;

class RegularVehicleController extends Controller
{

    public function getProps(): array
    {

        $vehicle_types = VehicleType::all();
        $transporters = Transporter::where('is_active', 1)
            ->select('id', 'first_name', 'last_legal_name')
            ->orderBy('first_name')
            ->get();

        return array(
            "vehicle_types" => $vehicle_types,
            "transporters" => $transporters
        );

    }

    /**
     * Get drivers for a specific transporter
     */
    public function getDriversForTransporter(Transporter $transporter)
    {
        // Get unique driver IDs that have worked for this transporter
        $driverIds = TransportTransaction::where('transporter_id', $transporter->id)
            ->whereHas('TransportDriverVehicle')
            ->with('TransportDriverVehicle')
            ->get()
            ->pluck('TransportDriverVehicle')
            ->flatten()
            ->pluck('regular_driver_id')
            ->unique();

        $drivers = RegularDriver::whereIn('id', $driverIds)
            ->where('is_active', 1)
            ->select('id', 'first_name', 'last_name')
            ->orderBy('first_name')
            ->get();

        return response()->json($drivers);
    }


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

        $regular_vehicles = RegularVehicle::filter($filters)
            ->with(['TransportDriverVehicles.Driver', 'TransportDriverVehicles' => function($query) {
                $query->select('regular_vehicle_id', 'transport_trans_id')
                    ->distinct();
            }])
            ->paginate($paginate)
            ->withQueryString();

        // Get transporters and last driver for each vehicle
        $regular_vehicles->getCollection()->transform(function ($vehicle) {
            $transporterIds = TransportTransaction::whereHas('TransportDriverVehicle', function($query) use ($vehicle) {
                $query->where('regular_vehicle_id', $vehicle->id);
            })->pluck('transporter_id')->unique();

            $vehicle->transporters = Transporter::whereIn('id', $transporterIds)
                ->select('id', 'first_name', 'last_legal_name')
                ->get();

            // Get the last driver associated with this vehicle based on actual transport activity
            $lastDriverVehicle = TransportDriverVehicle::where('regular_vehicle_id', $vehicle->id)
                ->whereNotNull('regular_driver_id')
                ->with('Driver:id,first_name,last_name')
                ->orderByRaw('COALESCE(date_delivered, date_onroad, date_loaded, date_scheduled, created_at) DESC')
                ->first();

            $vehicle->last_driver = $lastDriverVehicle ? $lastDriverVehicle->Driver : null;

            return $vehicle;
        });

        return inertia(
            'Vehicle/Index',
            [
                'filters' => $filters,
                'vehicles' => $regular_vehicles,
            ]
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'reg_no' => ['required','string','unique:regular_vehicles,reg_no'],
            'comment' => ['nullable','string'],
            'vehicle_type_id' => ['required','integer'],
            'transporter_id' => ['nullable','integer','exists:transporters,id'],
            'regular_driver_id' => ['nullable','integer','exists:regular_drivers,id'],
        ]);

        $vehicle = RegularVehicle::create([
            'reg_no' => $request->reg_no,
            'comment' => $request->comment,
            'vehicle_type_id'=>$request->vehicle_type_id
        ]);

        if ($vehicle->exists()) {
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Vehicle Created');
        }
        else{
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Vehicle NOT Created');
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
    public function show(RegularVehicle $regularVehicle)
    {

        $vehicle_types = VehicleType::all();

        // Get transporters for this vehicle
        $transporterIds = TransportTransaction::whereHas('TransportDriverVehicle', function($query) use ($regularVehicle) {
            $query->where('regular_vehicle_id', $regularVehicle->id);
        })->pluck('transporter_id')->unique();

        $transporters = Transporter::whereIn('id', $transporterIds)
            ->select('id', 'first_name', 'last_legal_name')
            ->get();

        // Get the last driver associated with this vehicle based on actual transport activity
        $lastDriverVehicle = TransportDriverVehicle::where('regular_vehicle_id', $regularVehicle->id)
            ->whereNotNull('regular_driver_id')
            ->with('Driver:id,first_name,last_name')
            ->orderByRaw('COALESCE(date_delivered, date_onroad, date_loaded, date_scheduled, created_at) DESC')
            ->first();

        $lastDriver = $lastDriverVehicle ? $lastDriverVehicle->Driver : null;

        return inertia(
            'Vehicle/Show',
            [
                'vehicle' => $regularVehicle,
                'vehicle_types'=>$vehicle_types,
                'transporters' => $transporters,
                'last_driver' => $lastDriver
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(RegularVehicle $regularVehicle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, RegularVehicle $regularVehicle)
    {

        $regularVehicle->update(
            $request->validate([
                'vehicle_type_id' => ['required','integer'],
                'reg_no' => ['nullable','string'],
                'is_active' => ['nullable','boolean'],
                'comment' => ['nullable','string'],
            ])
        );

        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Vehicle updated');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RegularVehicle $regularVehicle)
    {
        //
    }
}
