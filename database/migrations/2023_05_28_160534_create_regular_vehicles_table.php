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
        Schema::create('regular_vehicles', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('vehicle_type_id')->unsigned();
            $table->foreign('vehicle_type_id')
                ->references('id')->on('vehicle_types')->onDelete('cascade');
            $table->string('reg_no')->unique();
            $table->longText('comment')->nullable();
            $table->boolean('is_active')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regular_vehicles');
    }
};
