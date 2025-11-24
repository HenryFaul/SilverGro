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
            'reg_no' => ['required','string'],
            'comment' => ['nullable','string'],
            'vehicle_type_id' => ['required','integer'],
            'transporter_id' => ['nullable','integer','exists:transporters,id'],
            'regular_driver_id' => ['nullable','integer','exists:regular_drivers,id'],
            'transport_trans_id' => ['nullable','integer','exists:transport_transactions,id'],
            'transport_job_id' => ['nullable','integer','exists:transport_jobs,id'],
        ]);

        // Normalize input for similarity check (remove spaces, lowercase, remove special chars)
        $normalizedRegNo = strtolower(preg_replace('/[\s\-_]+/', '', $request->reg_no));

        // Check for similar existing vehicles
        $similarVehicle = RegularVehicle::whereRaw('LOWER(REPLACE(REPLACE(REPLACE(reg_no, \' \', \'\'), \'-\', \'\'), \'_\', \'\')) = ?', [$normalizedRegNo])
            ->with('VehicleType')
            ->first();

        // If similar vehicle found
        if ($similarVehicle) {
            // Check if this vehicle has an existing transporter association
            $lastDriverVehicle = TransportDriverVehicle::where('regular_vehicle_id', $similarVehicle->id)
                ->whereNotNull('transport_trans_id')
                ->with('TransportTransaction.Transporter:id,first_name,last_legal_name')
                ->orderByRaw('COALESCE(date_delivered, date_onroad, date_loaded, date_scheduled, created_at) DESC')
                ->first();

            $existingTransporter = $lastDriverVehicle && $lastDriverVehicle->TransportTransaction
                ? $lastDriverVehicle->TransportTransaction->Transporter
                : null;

            // Build appropriate message based on transporter association
            if ($existingTransporter) {
                $message = 'Similar vehicle found: ' . $similarVehicle->reg_no .
                          ' (currently associated with ' . $existingTransporter->last_legal_name . '). ' .
                          'Duplicate prevented. You can select this existing vehicle from the dropdown.';
            } else {
                $message = 'Similar vehicle found: ' . $similarVehicle->reg_no .
                          '. Duplicate prevented. This vehicle is now available in the dropdown for selection.';
            }

            // If transport transaction info provided, link vehicle to transaction
            if ($request->transport_trans_id && $request->transport_job_id) {
                $this->linkVehicleToTransaction($similarVehicle->id, $request->transport_trans_id, $request->transport_job_id, $request->regular_driver_id);
            }

            $request->session()->flash('flash.bannerStyle', 'info');
            $request->session()->flash('flash.banner', $message);
            $request->session()->flash('flash.is_existing', true);
            $request->session()->flash('flash.vehicle_id', $similarVehicle->id); // Pass vehicle ID to frontend

            return redirect()->back();
        }

        // No similar vehicle found, create new one
        $vehicle = RegularVehicle::create([
            'reg_no' => $request->reg_no,
            'comment' => $request->comment,
            'vehicle_type_id'=>$request->vehicle_type_id
        ]);

        if ($vehicle->exists()) {
            // If transport transaction info provided, link vehicle to transaction
            if ($request->transport_trans_id && $request->transport_job_id) {
                $this->linkVehicleToTransaction($vehicle->id, $request->transport_trans_id, $request->transport_job_id, $request->regular_driver_id);
            }

            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Vehicle Created');
            $request->session()->flash('flash.is_existing', false);
            $request->session()->flash('flash.vehicle_id', $vehicle->id); // Pass vehicle ID to frontend
        }
        else{
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Vehicle NOT Created');
        }

        return redirect()->back();
    }

    /**
     * Link vehicle to transport transaction via TransportDriverVehicle
     */
    private function linkVehicleToTransaction($vehicleId, $transportTransId, $transportJobId, $driverId = null)
    {
        // Check if link already exists
        $existingLink = TransportDriverVehicle::where('transport_trans_id', $transportTransId)
            ->where('transport_job_id', $transportJobId)
            ->first();

        if ($existingLink) {
            // Update existing link with new vehicle (and driver if provided)
            $existingLink->regular_vehicle_id = $vehicleId;
            if ($driverId) {
                $existingLink->regular_driver_id = $driverId;
            }
            // Set multiple dates to ensure this is recognized as most recent
            $existingLink->date_scheduled = now();
            $existingLink->date_loaded = now();
            $existingLink->is_loaded = true;
            $existingLink->is_transport_scheduled = true;
            $existingLink->save();
        } else {
            // Create new link - need driver_id (use null/default if not provided)
            $link = TransportDriverVehicle::create([
                'transport_trans_id' => $transportTransId,
                'transport_job_id' => $transportJobId,
                'regular_driver_id' => $driverId ?? 1, // Default to "Unallocated" driver if none provided
                'regular_vehicle_id' => $vehicleId,
                'date_scheduled' => now(),
                'date_loaded' => now(), // Set earlier date in COALESCE priority
                'is_transport_scheduled' => true,
                'is_loaded' => true,
            ]);
        }
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
