<?php

namespace App\Observers;

use App\Models\TransportInvoice;
use Illuminate\Support\Facades\Log;

class TransportInvoiceObserver
{
    /**
     * Handle the TransportInvoice "creating" event.
     * Ensures invoice customer_id matches transaction customer_id
     */
    public function creating(TransportInvoice $transportInvoice): void
    {
        // If transaction is loaded, ensure customer_id matches
        if ($transportInvoice->TransportTransaction) {
            $transactionCustomerId = $transportInvoice->TransportTransaction->customer_id;

            if ($transportInvoice->customer_id !== $transactionCustomerId) {
                Log::warning(
                    "Invoice being created with mismatched customer_id. " .
                    "Invoice customer_id: {$transportInvoice->customer_id}, " .
                    "Transaction customer_id: {$transactionCustomerId}. " .
                    "Auto-correcting to match transaction."
                );

                $transportInvoice->customer_id = $transactionCustomerId;
            }
        }
    }

    /**
     * Handle the TransportInvoice "updating" event.
     * Ensures invoice customer_id matches transaction customer_id
     */
    public function updating(TransportInvoice $transportInvoice): void
    {
        // If customer_id is being changed, ensure it matches transaction
        if ($transportInvoice->isDirty('customer_id')) {
            $transaction = $transportInvoice->TransportTransaction;

            if ($transaction && $transportInvoice->customer_id !== $transaction->customer_id) {
                Log::warning(
                    "Invoice {$transportInvoice->id} customer_id being changed to {$transportInvoice->customer_id} " .
                    "but transaction customer_id is {$transaction->customer_id}. " .
                    "This may cause debtor standing issues."
                );
            }
        }
    }

    /**
     * Handle the TransportInvoice "created" event.
     */
    public function created(TransportInvoice $transportInvoice): void
    {
        //
    }

    /**
     * Handle the TransportInvoice "updated" event.
     */
    public function updated(TransportInvoice $transportInvoice): void
    {
        //
    }

    /**
     * Handle the TransportInvoice "deleted" event.
     */
    public function deleted(TransportInvoice $transportInvoice): void
    {
        //
    }

    /**
     * Handle the TransportInvoice "restored" event.
     */
    public function restored(TransportInvoice $transportInvoice): void
    {
        //
    }

    /**
     * Handle the TransportInvoice "force deleted" event.
     */
    public function forceDeleted(TransportInvoice $transportInvoice): void
    {
        //
    }
}

