<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Key	Attribute	Type

    loading_contact_id
    offloading_contact

     */


    public function up(): void
    {
        Schema::create('transport_jobs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('transport_trans_id')->unsigned();
            $table->foreign('transport_trans_id')
                ->references('id')->on('transport_transactions')->onDelete('cascade');
            $table->bigInteger('transport_rate_basis_id')->unsigned();
            $table->foreign('transport_rate_basis_id')
                ->references('id')->on('transport_rate_bases')->onDelete('cascade');
            $table->string('customer_order_number')->nullable();
            $table->boolean('is_multi_loads')->default(0);
            $table->boolean('is_approved')->default(0);
            $table->timestamp('transport_date_earliest')->nullable();
            $table->timestamp('transport_date_latest')->nullable();
            $table->boolean('is_transport_costs_inc_price')->default(0);
            $table->boolean('is_product_zero_rated')->default(0);
            $table->bigInteger('loading_hours_from_id')->unsigned();
            $table->foreign('loading_hours_from_id')
                ->references('id')->on('loading_hour_options')->onDelete('cascade');
            $table->bigInteger('loading_hours_to_id')->unsigned();
            $table->foreign('loading_hours_to_id')
                ->references('id')->on('loading_hour_options')->onDelete('cascade');
            $table->bigInteger('offloading_hours_from_id')->unsigned();
            $table->foreign('offloading_hours_from_id')
                ->references('id')->on('loading_hour_options')->onDelete('cascade');
            $table->bigInteger('offloading_hours_to_id')->unsigned();
            $table->foreign('offloading_hours_to_id')
                ->references('id')->on('loading_hour_options')->onDelete('cascade');
            $table->double('load_insurance_per_ton')->default(0);
            $table->double('total_load_insurance')->default(0);
            $table->integer('number_loads')->default(0);
            $table->longText('loading_instructions')->nullable();
            $table->longText('offloading_instructions')->nullable();
            $table->longText('comments')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_jobs');
    }
};
