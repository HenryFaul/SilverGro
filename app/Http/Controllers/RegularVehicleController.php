<?php

namespace App\Http\Controllers;

use App\Models\RegularDriver;
use App\Models\RegularVehicle;
use App\Models\VehicleType;
use Illuminate\Http\Request;

class RegularVehicleController extends Controller
{

    public function getProps(): array
    {

        $vehicle_types = VehicleType::all();

        return array("vehicle_types"=>$vehicle_types);

    }


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

        $regular_vehicles = RegularVehicle::filter($filters)
            ->paginate($paginate)
            ->withQueryString();



        return inertia(
            'Vehicle/Index',
            [
                'filters' => $filters,
                'vehicles' => $regular_vehicles,
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
            'reg_no' => ['required','string','unique:regular_vehicles,reg_no'],
            'comment' => ['nullable','string'],
            'vehicle_type_id' => ['required','integer'],
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
     * Display the specified resource.
     */
    public function show(RegularVehicle $regularVehicle)
    {

        $vehicle_types = VehicleType::all();

        return inertia(
            'Vehicle/Show',
            [
                'vehicle' => $regularVehicle,
                'vehicle_types'=>$vehicle_types
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
