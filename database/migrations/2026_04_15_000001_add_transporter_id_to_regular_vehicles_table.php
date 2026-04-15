<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('regular_vehicles', function (Blueprint $table) {
            $table->unsignedBigInteger('transporter_id')->nullable()->after('vehicle_type_id');
            $table->foreign('transporter_id')->references('id')->on('transporters')->onDelete('set null');

            $table->unsignedBigInteger('regular_driver_id')->nullable()->after('transporter_id');
            $table->foreign('regular_driver_id')->references('id')->on('regular_drivers')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('regular_vehicles', function (Blueprint $table) {
            $table->dropForeign(['transporter_id']);
            $table->dropForeign(['regular_driver_id']);
            $table->dropColumn(['transporter_id', 'regular_driver_id']);
        });
    }
};
