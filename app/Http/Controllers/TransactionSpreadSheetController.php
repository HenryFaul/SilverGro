<?php

namespace App\Http\Controllers;

use App\Models\TransactionSpreadSheet;
use Illuminate\Http\Request;

class TransactionSpreadSheetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        return inertia(
            'TransactionSpreadSheet/Index',
            [
                ]);
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TransactionSpreadSheet $transactionSpreadSheet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransactionSpreadSheet $transactionSpreadSheet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransactionSpreadSheet $transactionSpreadSheet)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransactionSpreadSheet $transactionSpreadSheet)
    {
        //
    }
}
