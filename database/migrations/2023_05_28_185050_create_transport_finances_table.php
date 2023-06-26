<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Key	Attribute	Type
    PK	id	bigInt
    FK	transport_trans_id	bigInt
    FK	transport_load_id
    cost_price_per_unit
    selling_price_per_unit
    transport_rate_per_ton
    transport_rate
    transport_price
    comms_due_per_ton
    weight_ton_incoming
    weight_ton_outgoing
    additional_cost_1	double
    additional_cost_2	double
    additional_cost_3	double
    additional_cost_desc_1	varchar
    additional_cost_desc_2	varchar
    additional_cost_desc_3	varchar
    gross_profit
    gross_profit_percent
    gross_profit_per_ton
    total_supplier_comm
    total_customer_comm
    total_comm
    adjusted_gp
    adjusted_gp_notes


     */



    public function up(): void
    {

        Schema::create('transport_finances', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('transport_trans_id')->unsigned();
            $table->foreign('transport_trans_id')
                ->references('id')->on('transport_transactions')->onDelete('cascade');

            $table->bigInteger('transport_load_id')->unsigned();
            $table->foreign('transport_load_id')
                ->references('id')->on('transport_loads')->onDelete('cascade');

            $table->bigInteger('transport_rate_basis_id')->unsigned();
            $table->foreign('transport_rate_basis_id')
                ->references('id')->on('transport_rate_bases')->onDelete('cascade');

            $table->double('cost_price')->default(0);
            $table->double('selling_price')->default(0);
            $table->double('load_insurance_per_ton')->default(0);
            $table->double('cost_price_per_unit')->default(0);
            $table->double('cost_price_per_ton')->default(0);
            $table->double('selling_price_per_unit')->default(0);
            $table->double('selling_price_per_ton')->default(0);
            $table->boolean('is_transport_costs_inc_price')->default(false);
            $table->double('transport_rate_per_ton')->default(0);
            $table->double('transport_rate')->default(0);
            $table->double('transport_price')->default(0);
            $table->double('transport_cost')->default(0);
            $table->double('comms_due_per_ton')->default(0);
            $table->double('weight_ton_incoming')->default(0);
            $table->double('weight_ton_outgoing')->default(0);
            $table->double('total_cost_price')->default(0);
            $table->double('additional_cost_1')->default(0);
            $table->double('additional_cost_2')->default(0);
            $table->double('additional_cost_3')->default(0);

            $table->string('additional_cost_desc_1')->nullable();
            $table->string('additional_cost_desc_2')->nullable();
            $table->string('additional_cost_desc_3')->nullable();

            $table->double('gross_profit')->default(0);
            $table->double('gross_profit_percent')->default(0);
            $table->double('gross_profit_per_ton')->default(0);

            $table->double('total_supplier_comm')->default(0);
            $table->double('total_customer_comm')->default(0);
            $table->double('total_comm')->default(0);
            $table->double('adjusted_gp')->default(0);
            $table->longText('adjusted_gp_notes')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_finances');
    }
};
