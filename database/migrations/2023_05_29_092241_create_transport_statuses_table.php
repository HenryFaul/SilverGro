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
        Schema::create('transport_statuses', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('transport_trans_id')->unsigned();
            $table->foreign('transport_trans_id')
                ->references('id')->on('transport_transactions')->onDelete('cascade');

            $table->bigInteger('status_entity_id')->unsigned();
            $table->foreign('status_entity_id')
                ->references('id')->on('status_entities')->onDelete('cascade');


            $table->bigInteger('status_type_id')->unsigned();
            $table->foreign('status_type_id')
                ->references('id')->on('status_types')->onDelete('cascade');


            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_statuses');
    }
};
