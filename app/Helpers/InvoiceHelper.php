<?php

namespace App\Helpers;

use App\Models\TransportInvoice;
use App\Models\TransportTransaction;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class InvoiceHelper
{
    /**
     * Ensure invoice customer_id matches transaction customer_id
     * This should be called whenever transaction customer changes
     *
     * @param TransportTransaction $transaction
     * @return bool True if sync was needed and performed
     */
    public static function syncInvoiceCustomer(TransportTransaction $transaction): bool
    {
        $invoice = $transaction->TransportInvoice;

        if (!$invoice) {
            Log::warning("Transaction {$transaction->id} has no invoice to sync");
            return false;
        }

        if ($invoice->customer_id !== $transaction->customer_id) {
            Log::info("Syncing invoice {$invoice->id} customer_id from {$invoice->customer_id} to {$transaction->customer_id}");
            $invoice->customer_id = $transaction->customer_id;
            $invoice->save();
            return true;
        }

        return false;
    }

    /**
     * Validate that all invoices match their transaction customer_ids
     * Returns array of mismatched invoice IDs
     *
     * @return array
     */
    public static function validateAllInvoices(): array
    {
        $mismatches = DB::select("
            SELECT ti.id as invoice_id
            FROM transport_invoices ti
            INNER JOIN transport_transactions tt ON ti.transport_trans_id = tt.id
            WHERE ti.customer_id != tt.customer_id
        ");

        return array_map(fn($row) => $row->invoice_id, $mismatches);
    }

    /**
     * Get the correct customer_id for an invoice based on transaction logic
     * Handles split loads and other business rules
     *
     * @param TransportTransaction $transaction
     * @param bool $isSplitLoad
     * @return int
     */
    public static function getInvoiceCustomerId(TransportTransaction $transaction, bool $isSplitLoad = false): int
    {
        // Split loads should always use Unallocated (customer_id = 2)
        if ($isSplitLoad || $transaction->is_split_load) {
            return 2;
        }

        // Otherwise use transaction's customer_id
        return $transaction->customer_id;
    }

    /**
     * Fix all mismatched invoice customer_ids to match their transactions
     *
     * @return int Number of invoices fixed
     */
    public static function fixAllMismatches(): int
    {
        $affected = DB::update("
            UPDATE transport_invoices
            SET customer_id = tt.customer_id
            FROM transport_transactions tt
            WHERE transport_invoices.transport_trans_id = tt.id
            AND transport_invoices.customer_id != tt.customer_id
        ");

        if ($affected > 0) {
            Log::info("Fixed {$affected} invoice customer_id mismatches");
        }

        return $affected;
    }

    /**
     * Validate that an invoice customer_id matches its transaction
     * Throws exception if mismatch found
     *
     * @param TransportInvoice $invoice
     * @throws Exception
     */
    public static function validateInvoiceCustomer(TransportInvoice $invoice): void
    {
        $transaction = $invoice->TransportTransaction;

        if (!$transaction) {
            throw new Exception("Invoice {$invoice->id} has no associated transaction");
        }

        if ($invoice->customer_id !== $transaction->customer_id) {
            throw new Exception(
                "Invoice {$invoice->id} customer_id ({$invoice->customer_id}) " .
                "does not match transaction {$transaction->id} customer_id ({$transaction->customer_id})"
            );
        }
    }
}

