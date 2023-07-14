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
        Schema::create('trans_links', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('trans_link_type_id')->unsigned();
            $table->foreign('trans_link_type_id')
                ->references('id')->on('trans_link_types')->onDelete('cascade');

            $table->bigInteger('transport_trans_id')->unsigned();
            $table->foreign('transport_trans_id')
                ->references('id')->on('transport_transactions')->onDelete('cascade');

            $table->bigInteger('linked_transport_trans_id')->unsigned();
            $table->foreign('linked_transport_trans_id')
                ->references('id')->on('transport_transactions')->onDelete('cascade');


            $table->softDeletes();
            $table->timestamps();

            $table->unique(['trans_link_type_id','transport_trans_id','linked_transport_trans_id'], 'unique_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trans_links');
    }
};
