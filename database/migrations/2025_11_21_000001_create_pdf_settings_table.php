<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pdf_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('SilverGro');
            $table->string('po_box')->nullable();
            $table->string('street_address')->nullable();
            $table->string('city')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('South Africa');
            $table->string('phone')->nullable();
            $table->string('fax')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo_path')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default settings
        DB::table('pdf_settings')->insert([
            'company_name' => 'SilverGro',
            'po_box' => 'P.O. Box 71658, Rink Street',
            'street_address' => 'Port Elizabeth, 6001',
            'phone' => 'Tel : +27 82 897 5866',
            'fax' => 'Fax : +27 41 582 1952',
            'email' => 'Email : documents@silvergro.co.za',
            'logo_path' => 'images/pdflogo.jpg',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdf_settings');
    }
};

