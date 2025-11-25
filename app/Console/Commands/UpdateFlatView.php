<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateFlatView extends Command
{
    protected $signature = 'view:update-flat';
    protected $description = 'Update transaction_summary_flat_view with a_sc and a_pc fields';

    public function handle()
    {
        $this->info('Updating transaction_summary_flat_view...');

        try {
            $migration = require base_path('database/migrations/2025_11_25_063559_create_transaction_summary_flat_view.php');
            $migration->up();

            $this->info('✓ View updated successfully!');

            $count = DB::table('transaction_summary_flat_view')->count();
            $this->info("✓ View has {$count} records");

            return 0;
        } catch (Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}

