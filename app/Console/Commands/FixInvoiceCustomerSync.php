<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixInvoiceCustomerSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:fix-customer-sync {--dry-run : Show what would be fixed without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix invoice customer_id to match transaction customer_id';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('=== Invoice Customer ID Sync Fix ===');
        $this->newLine();

        // Check for mismatches
        $mismatches = DB::select("
            SELECT
                ti.id as invoice_id,
                ti.customer_id as invoice_customer_id,
                tt.id as transaction_id,
                tt.customer_id as transaction_customer_id,
                tt.is_split_load,
                c1.last_legal_name as invoice_customer_name,
                c2.last_legal_name as transaction_customer_name
            FROM transport_invoices ti
            INNER JOIN transport_transactions tt ON ti.transport_trans_id = tt.id
            LEFT JOIN customers c1 ON ti.customer_id = c1.id
            LEFT JOIN customers c2 ON tt.customer_id = c2.id
            WHERE ti.customer_id != tt.customer_id
        ");

        $count = count($mismatches);

        if ($count === 0) {
            $this->info('✅ No mismatches found! All invoice customer_ids are in sync.');
            return 0;
        }

        $this->warn("Found {$count} invoices with mismatched customer_id");
        $this->newLine();

        // Show sample of mismatches
        $sample = array_slice($mismatches, 0, 10);

        $this->table(
            ['Invoice ID', 'Invoice Customer', 'Transaction Customer', 'Split Load'],
            array_map(function($row) {
                return [
                    $row->invoice_id,
                    $row->invoice_customer_name . " (ID: {$row->invoice_customer_id})",
                    $row->transaction_customer_name . " (ID: {$row->transaction_customer_id})",
                    $row->is_split_load ? 'Yes' : 'No'
                ];
            }, $sample)
        );

        if ($count > 10) {
            $this->info("... and " . ($count - 10) . " more");
        }

        $this->newLine();

        if ($this->option('dry-run')) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
            $this->info('Run without --dry-run flag to apply fixes');
            return 0;
        }

        if (!$this->confirm('Do you want to fix these mismatches?', true)) {
            $this->info('Operation cancelled');
            return 0;
        }

        // Apply the fix
        $this->info('Applying fixes...');

        // PostgreSQL syntax for UPDATE with JOIN
        $affected = DB::update("
            UPDATE transport_invoices
            SET customer_id = tt.customer_id
            FROM transport_transactions tt
            WHERE transport_invoices.transport_trans_id = tt.id
            AND transport_invoices.customer_id != tt.customer_id
        ");

        $this->newLine();
        $this->info("✅ Successfully synced {$affected} invoice customer_ids");

        // Show summary
        $unallocated_count = DB::select("
            SELECT COUNT(*) as count
            FROM transport_invoices
            WHERE customer_id = 2
        ")[0]->count;

        $this->newLine();
        $this->info('=== Summary ===');
        $this->info("Total invoices with Unallocated customer: {$unallocated_count}");

        $this->newLine();
        $this->info('🎯 Next steps:');
        $this->info('1. Recalculate debtor standings (visit /debtor/calculating)');
        $this->info('2. Verify "Unallocated" client only shows correct invoices');

        return 0;
    }
}

