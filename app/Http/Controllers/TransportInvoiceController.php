<?php

namespace App\Http\Controllers;

use App\Models\TransportInvoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TransportInvoiceController extends Controller
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(TransportInvoice $transportInvoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TransportInvoice $transportInvoice)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TransportInvoice $transportInvoice)
    {

        $transportInvoiceDetails = $transportInvoice->TransportInvoiceDetails;

        //dd($transportInvoiceDetails);



        /*        transport_trans_id: props.transaction.id,
            old_id:props.transaction.transport_invoice.old_id,
            is_active:props.transaction.transport_invoice.is_active,
            is_printed:props.transaction.transport_invoice.is_printed,
            invoice_id:props.transaction.transport_invoice.transport_invoice_details.invoice_id,
            is_invoiced:props.transaction.transport_invoice.transport_invoice_details.is_invoiced,
            is_invoice_paid:props.transaction.transport_invoice.transport_invoice_details.is_invoice_paid,
            invoice_no:props.transaction.transport_invoice.transport_invoice_details.invoice_no,
            invoice_paid_date:props.transaction.transport_invoice.transport_invoice_details.invoice_paid_date,
            invoice_pay_by_date:props.transaction.transport_invoice.transport_invoice_details.invoice_pay_by_date,
            invoice_date:props.transaction.transport_invoice.transport_invoice_details.invoice_date,
            invoice_amount:props.transaction.transport_invoice.transport_invoice_details.invoice_amount,
            invoice_amount_paid:props.transaction.transport_invoice.transport_invoice_details.invoice_amount_paid,
            status_id:props.transaction.transport_invoice.transport_invoice_details.status_id,
            notes:props.transaction.transport_invoice.transport_invoice_details.notes
                */

        $request->validate([
                'is_active' => ['nullable', 'boolean'],
                'old_id' => ['nullable'],
                'is_printed' => ['nullable', 'boolean'],
                'is_invoiced' => ['nullable', 'boolean'],
                'is_invoice_paid' => ['nullable', 'boolean'],
                'invoice_id' => ['required', 'integer', 'exists:transport_invoices,id'],
                'invoice_no' => ['nullable', 'string'],
                'invoice_paid_date' => ['nullable', 'date'],
                'invoice_pay_by_date' => ['nullable', 'date'],
                'invoice_date' => ['nullable', 'date'],
                'invoice_amount' => ['required', 'numeric'],
                'invoice_amount_paid' => ['required', 'numeric'],
                'status_id' => ['required', 'integer', 'exists:invoice_statuses,id'],
                'notes' => ['nullable', 'string'],
            ]
        );

        $is_updated = $transportInvoice->update(
            [
                'is_active' => $request->is_active,
                'is_printed' => $request->is_printed,
            ]);


        $transportInvoiceDetails->update(
            [
                'is_invoiced' => $request->is_invoiced,
                'is_printed' => $request->is_printed,
                'is_invoice_paid' => $request->is_invoice_paid,
                'invoice_no' => $request->invoice_no,
                'invoice_paid_date' => Carbon::parse($request->invoice_paid_date)->tz('Africa/Johannesburg'),
                'invoice_pay_by_date' => Carbon::parse($request->invoice_pay_by_date)->tz('Africa/Johannesburg'),
                'invoice_date' => Carbon::parse($request->invoice_date)->tz('Africa/Johannesburg'),
                'invoice_amount' => $request->invoice_amount,
                'invoice_amount_paid' => $request->invoice_amount_paid,
                'status_id' => $request->status_id,
                'notes' => $request->notes,

            ]);



        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', 'Transport Invoice updated');

        return redirect()->back();

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransportInvoice $transportInvoice)
    {
        //
    }
}
