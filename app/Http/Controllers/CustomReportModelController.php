<?php

namespace App\Http\Controllers;

use App\Models\CustomReportModel;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class CustomReportModelController extends Controller
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

       // ['id','created_by_id','report_id','class_name','attribute_name','comment','is_active']

        $request->validate([
            'class_name' => ['required','string'],
            'attribute_name' => ['required','string', Rule::unique('custom_report_models')
                ->where('report_id', $request->report_id)->where('class_name',$request->class_name)->where('attribute_name',$request->attribute_name),
            ],
            'report_id' => ['required', 'integer', 'exists:custom_reports,id'],
            'comment' => ['nullable','string'],
        ]);

        $custom_report_model = CustomReportModel::create([
            'created_by_id' => $request->user()->id,
            'class_name' => $request->class_name,
            'attribute_name' => $request->attribute_name,
            'report_id' => $request->report_id,
            'comment' => $request->comment,
        ]);

        if ($custom_report_model->exists()) {
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Report Model Added');
        }
        else{
            $request->session()->flash('flash.bannerStyle', 'danger');
            $request->session()->flash('flash.banner', 'Report Model NOT Added');
        }

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show(CustomReportModel $customReportModel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CustomReportModel $customReportModel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CustomReportModel $customReportModel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomReportModel $customReportModel)
    {
        //
    }
}
