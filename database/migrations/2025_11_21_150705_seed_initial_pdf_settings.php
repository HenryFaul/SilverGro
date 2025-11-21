<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if pdf_settings table exists and is empty
        if (Schema::hasTable('pdf_settings') && DB::table('pdf_settings')->count() === 0) {
            // Insert initial PDF settings with values from the template
            DB::table('pdf_settings')->insert([
                'company_name' => 'SilverGro Feed & Grain',
                'po_box' => 'P.O. Box 71658, Rink Street',
                'street_address' => 'Port Elizabeth',
                'city' => 'Port Elizabeth',
                'postal_code' => '6001',
                'country' => 'South Africa',
                'phone' => 'Tel : +27 82 897 5966',
                'fax' => '+27 41 582 1952',
                'email' => 'documents@silvergro.co.za',
                'website' => null,
                'logo_path' => 'images/pdflogo.jpg',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the seeded record if it exists with the exact same values
        if (Schema::hasTable('pdf_settings')) {
            DB::table('pdf_settings')
                ->where('company_name', 'SilverGro Feed & Grain')
                ->where('email', 'documents@silvergro.co.za')
                ->delete();
        }
    }
};

