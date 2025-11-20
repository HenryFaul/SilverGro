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
        Schema::table('transport_transactions', function (Blueprint $table) {
            $table->bigInteger('a_pc')->nullable();
            $table->bigInteger('a_sc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transport_transactions', function (Blueprint $table) {
            $table->dropColumn(['a_pc', 'a_sc']);
        });
    }
};

