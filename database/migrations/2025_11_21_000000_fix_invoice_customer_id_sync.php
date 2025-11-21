<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * This migration fixes a critical data integrity bug where TransportInvoice.customer_id
     * does not match TransportTransaction.customer_id. This happens when:
     * 1. A transaction is updated and customer changes
     * 2. Split load flag is set, transaction goes to Unallocated but invoice doesn't
     * 3. Import created invoices with wrong customer_id
     */
    public function up(): void
    {
        // Log before state
        $mismatched_count = DB::select("
            SELECT COUNT(*) as count
            FROM transport_invoices ti
            INNER JOIN transport_transactions tt ON ti.transport_trans_id = tt.id
            WHERE ti.customer_id != tt.customer_id
        ")[0]->count;

        if ($mismatched_count > 0) {
            echo "\nFound {$mismatched_count} invoices with mismatched customer_id\n";
            echo "Syncing invoice customer_id to match transaction customer_id...\n";

            // Fix the mismatched customer_ids - PostgreSQL syntax
            DB::statement("
                UPDATE transport_invoices
                SET customer_id = tt.customer_id
                FROM transport_transactions tt
                WHERE transport_invoices.transport_trans_id = tt.id
                AND transport_invoices.customer_id != tt.customer_id
            ");

            echo "Successfully synced {$mismatched_count} invoices\n";
        } else {
            echo "\nNo mismatched invoice customer_ids found. Database is clean.\n";
        }

        // Log summary
        $unallocated_invoices = DB::select("
            SELECT COUNT(*) as count
            FROM transport_invoices
            WHERE customer_id = 2
        ")[0]->count;

        echo "Total invoices with Unallocated customer: {$unallocated_invoices}\n";
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Cannot reverse - we don't know the original wrong values
        echo "Cannot reverse this migration - original wrong values are lost\n";
    }
};

