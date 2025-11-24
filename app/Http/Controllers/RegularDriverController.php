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
use Log;
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
            'transporter_id' => ['nullable','integer','exists:transporters,id'],
            'transport_trans_id' => ['nullable','integer','exists:transport_transactions,id'],
            'transport_job_id' => ['nullable','integer','exists:transport_jobs,id'],
            'regular_vehicle_id' => ['nullable','integer','exists:regular_vehicles,id'],
        ]);

        // Normalize input for similarity check (remove spaces, lowercase)
        $normalizedFirstName = strtolower(preg_replace('/\s+/', '', $request->first_name));
        $normalizedLastName = strtolower(preg_replace('/\s+/', '', $request->last_name));
        $normalizedCell = preg_replace('/\s+/', '', $request->cell_no);

        // Check for similar existing drivers
        $similarDriver = RegularDriver::whereRaw('LOWER(REPLACE(first_name, \' \', \'\')) = ?', [$normalizedFirstName])
            ->whereRaw('LOWER(REPLACE(last_name, \' \', \'\')) = ?', [$normalizedLastName])
            ->first();

        // If similar driver found
        if ($similarDriver) {
            // Check if this driver has an existing transporter association
            $lastDriverVehicle = TransportDriverVehicle::where('regular_driver_id', $similarDriver->id)
                ->whereNotNull('transport_trans_id')
                ->with('TransportTransaction.Transporter:id,first_name,last_legal_name')
                ->orderByRaw('COALESCE(date_delivered, date_onroad, date_loaded, date_scheduled, created_at) DESC')
                ->first();

            $existingTransporter = $lastDriverVehicle && $lastDriverVehicle->TransportTransaction
                ? $lastDriverVehicle->TransportTransaction->Transporter
                : null;


            // Build appropriate message based on transporter association
            if ($existingTransporter) {
                $message = 'Similar driver found: ' . $similarDriver->first_name . ' ' . $similarDriver->last_name .
                          ' (currently associated with ' . $existingTransporter->last_legal_name . '). ' .
                          'Duplicate prevented. You can select this existing driver from the dropdown.';
            } else {
                $message = 'Similar driver found: ' . $similarDriver->first_name . ' ' . $similarDriver->last_name .
                          '. Duplicate prevented. This driver is now available in the dropdown for selection.';
            }

            // If transport transaction info provided, link driver to transaction
            if ($request->transport_trans_id && $request->transport_job_id) {
                $this->linkDriverToTransaction($similarDriver->id, $request->transport_trans_id, $request->transport_job_id, $request->regular_vehicle_id);
                Log::info('Driver linked to transaction');
            }

            $request->session()->flash('flash.bannerStyle', 'info');
            $request->session()->flash('flash.banner', $message);
            $request->session()->flash('flash.is_existing', true);
            $request->session()->flash('flash.driver_id', $similarDriver->id); // Pass driver ID to frontend

            return redirect()->back();
        }

        // No similar driver found, create new one
        $driver = RegularDriver::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'comment'=>$request->comment,
            'cell_no'=>$request->cell_no
        ]);

        if ($driver->exists()) {
            // If transport transaction info provided, link driver to transaction
            if ($request->transport_trans_id && $request->transport_job_id) {
                $this->linkDriverToTransaction($driver->id, $request->transport_trans_id, $request->transport_job_id, $request->regular_vehicle_id);
            }

            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Driver Created');
            $request->session()->flash('flash.is_existing', false);
            $request->session()->flash('flash.driver_id', $driver->id); // Pass driver ID to frontend
        }
        else{
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Driver NOT Created');
        }

        return redirect()->back();
    }

    /**
     * Link driver to transport transaction via TransportDriverVehicle
     */
    private function linkDriverToTransaction($driverId, $transportTransId, $transportJobId, $vehicleId = null)
    {
        // Check if link already exists
        $existingLink = TransportDriverVehicle::where('transport_trans_id', $transportTransId)
            ->where('transport_job_id', $transportJobId)
            ->first();

        if ($existingLink) {
            // Update existing link with new driver (and vehicle if provided)
            $existingLink->regular_driver_id = $driverId;
            if ($vehicleId) {
                $existingLink->regular_vehicle_id = $vehicleId;
            }
            // Set multiple dates to ensure this is recognized as most recent
            // Use date_loaded as it's earlier in COALESCE than date_scheduled
            $existingLink->date_scheduled = now();
            $existingLink->date_loaded = now();
            $existingLink->is_loaded = true;
            $existingLink->is_transport_scheduled = true;
            $existingLink->save();
        } else {
            // Create new link - need vehicle_id (use null/default if not provided)
            $link = TransportDriverVehicle::create([
                'transport_trans_id' => $transportTransId,
                'transport_job_id' => $transportJobId,
                'regular_driver_id' => $driverId,
                'regular_vehicle_id' => $vehicleId ?? 1, // Default to "N/A" vehicle if none provided
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
