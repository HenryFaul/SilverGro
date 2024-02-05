<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *



     */
    public function up(): void
    {
        Schema::create('transport_loads', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('transport_trans_id')->unsigned();
            $table->foreign('transport_trans_id')
                ->references('id')->on('transport_transactions')->onDelete('cascade');

            $table->bigInteger('product_id')->unsigned();
            $table->foreign('product_id')
                ->references('id')->on('products')->onDelete('cascade');

            $table->bigInteger('confirmed_by_id')->unsigned();
            $table->foreign('confirmed_by_id')
                ->references('id')->on('staff')->onDelete('cascade');

            $table->bigInteger('confirmed_by_type_id')->unsigned();
            $table->foreign('confirmed_by_type_id')
                ->references('id')->on('confirmation_types')->onDelete('cascade');

            $table->bigInteger('packaging_incoming_id')->unsigned();
            $table->foreign('packaging_incoming_id')
                ->references('id')->on('packagings')->onDelete('cascade');

            $table->bigInteger('packaging_outgoing_id')->unsigned();
            $table->foreign('packaging_outgoing_id')
                ->references('id')->on('packagings')->onDelete('cascade');

            $table->bigInteger('product_source_id')->unsigned();
            $table->foreign('product_source_id')
                ->references('id')->on('product_sources')->onDelete('cascade');

            $table->text('product_grade_perc')->nullable();

            $table->double('no_units_incoming')->default(0);
            $table->double('no_units_outgoing')->default(0);
            $table->double('no_units_outgoing_2')->default(0);
            $table->double('no_units_outgoing_3')->default(0);
            $table->double('no_units_outgoing_4')->default(0);
            $table->double('no_units_outgoing_5')->default(0);

            $table->bigInteger('billing_units_incoming_id')->unsigned();
            $table->foreign('billing_units_incoming_id')
                ->references('id')->on('billing_units')->onDelete('cascade');

            $table->bigInteger('billing_units_outgoing_id')->unsigned();
            $table->foreign('billing_units_outgoing_id')
                ->references('id')->on('billing_units')->onDelete('cascade');



            $table->boolean('is_weighbridge_certificate_received')->default(0);
            $table->longText('delivery_note')->nullable();
            $table->double('calculated_route_distance')->default(0);

            $table->bigInteger('collection_address_id')->unsigned()->nullable();
            $table->foreign('collection_address_id')
                ->references('id')->on('addresses')->onDelete('cascade');

            $table->bigInteger('delivery_address_id')->unsigned()->nullable();
            $table->foreign('delivery_address_id')
                ->references('id')->on('addresses')->onDelete('cascade');

            $table->bigInteger('delivery_address_id_2')->unsigned()->nullable();
            $table->foreign('delivery_address_id_2')
                ->references('id')->on('addresses')->onDelete('cascade');

            $table->bigInteger('delivery_address_id_3')->unsigned()->nullable();
            $table->foreign('delivery_address_id_3')
                ->references('id')->on('addresses')->onDelete('cascade');

            $table->bigInteger('delivery_address_id_4')->unsigned()->nullable();
            $table->foreign('delivery_address_id_4')
                ->references('id')->on('addresses')->onDelete('cascade');

            $table->bigInteger('delivery_address_id_5')->unsigned()->nullable();
            $table->foreign('delivery_address_id_5')
                ->references('id')->on('addresses')->onDelete('cascade');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_loads');
    }
};
