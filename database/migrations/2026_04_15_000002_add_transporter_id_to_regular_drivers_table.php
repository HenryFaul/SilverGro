<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('regular_drivers', function (Blueprint $table) {
            $table->unsignedBigInteger('transporter_id')->nullable()->after('is_active');
            $table->foreign('transporter_id')->references('id')->on('transporters')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('regular_drivers', function (Blueprint $table) {
            $table->dropForeign(['transporter_id']);
            $table->dropColumn('transporter_id');
        });
    }
};
