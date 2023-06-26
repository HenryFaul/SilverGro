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
        Schema::create('old_transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('old_id')->unsigned()->nullable();

            $table->string('contract_type')->nullable();
            $table->string('contract_no')->nullable();
            $table->string('deal_ticket')->nullable();
            $table->boolean('is_deal_ticket_printed')->default(false);
            $table->boolean('is_vat_exempt')->default(false);
            $table->string('created_by')->nullable();


            $table->unsignedBigInteger('customer_id')->unsigned();

            $table->foreign('customer_id')
                ->references('id')->on('customers')->onDelete('cascade');


            $table->bigInteger('supplier_id')->unsigned();

            $table->foreign('supplier_id')
                ->references('id')->on('suppliers')->onDelete('cascade');


            $table->bigInteger('transporter_id')->unsigned();

            $table->foreign('transporter_id')
                ->references('id')->on('transporters')->onDelete('cascade');


            $table->double('no_units_incoming')->default(0);
            $table->string('billing_units_incoming')->nullable();
            $table->string('packaging_incoming')->nullable();
            $table->bigInteger('product_id')->unsigned();
            $table->string('product_source')->nullable();
            $table->double('no_units_outgoing')->default(0);
            $table->string('billing_units_outgoing')->nullable();
            $table->double('cost_price_per_unit')->default(0);
            $table->double('selling_price_per_unit')->default(0);
            $table->double('total_cost_price')->default(0);
            $table->double('total_cost_price_per_ton')->default(0);
            $table->double('selling_price_per_ton')->default(0);


            $table->double('transport_rate')->default(0);
            $table->double('transport_cost_price')->default(0);
            $table->double('comms_due_per_ton')->default(0);
            $table->double('weight_in_tons_incoming')->default(0);
            $table->double('weight_in_tons_outgoing')->default(0);
            $table->double('gross_profit')->default(0);
            $table->double('gross_profit_perc')->default(0);
            $table->double('gross_profit_per_ton')->default(0);
            $table->string('purchase_order_confirmed_by')->nullable();
            $table->string('packaging_outgoing')->nullable();
            $table->boolean('sales_confirmation_sent')->default(false);
            $table->boolean('sales_confirmation_received')->default(false);
            $table->boolean('purchase_order_sent')->default(false);
            $table->boolean('purchase_order_received')->default(false);
            $table->boolean('transport_order_sent')->default(false);
            $table->boolean('transport_order_received')->default(false);
            $table->boolean('weight_bridge_certificate_received')->default(false);
            $table->boolean('invoice_paid')->default(false);



            $table->double('invoice_amount')->default(0);
            $table->double('invoice_amount_paid')->default(0);
            $table->double('invoice_balance')->default(0);
            $table->double('cost_price')->default(0);
            $table->double('selling_price')->default(0);

            $table->longText('supplier_notes')->nullable();
            $table->longText('product_notes')->nullable();
            $table->longText('customer_notes')->nullable();
            $table->longText('transport_notes')->nullable();
            $table->longText('pricing_notes')->nullable();
            $table->longText('process_notes')->nullable();
            $table->longText('transaction_notes')->nullable();


            $table->boolean('approved')->default(false);
            $table->boolean('transaction_done')->default(false);
            $table->double('total_load_insurance')->default(0);

            $table->longText('load_instructions')->nullable();
            $table->longText('offload_instructions')->nullable();

            $table->boolean('transport_included_in_price')->default(false);
            $table->boolean('loaded')->default(false);
            $table->boolean('on_road')->default(false);
            $table->boolean('paid')->default(false);
            $table->boolean('payment_overdue')->default(false);
            $table->boolean('delivered')->default(false);

            $table->longText('traders_notes')->nullable();

            $table->boolean('weight_bridge_variance')->default(false);
            $table->boolean('cancelled')->default(false);
            $table->boolean('transport_delayed')->default(false);
            $table->boolean('transport_breakdown')->default(false);
            $table->boolean('transport_cancelled')->default(false);
            $table->boolean('transport_load_slipped')->default(false);
            $table->boolean('transport_weight_control')->default(false);
            $table->boolean('transport_driver')->default(false);
            $table->boolean('mill_slow')->default(false);
            $table->boolean('mill_breakdown')->default(false);
            $table->boolean('mill_stopped_demand_side')->default(false);
            $table->boolean('quality_wet')->default(false);
            $table->boolean('quality_moisture_content')->default(false);
            $table->boolean('quality_contamination')->default(false);
            $table->boolean('quality_grade_specification')->default(false);
            $table->boolean('quality_visual')->default(false);
            $table->boolean('general_variance_detected')->default(false);
            $table->boolean('include_in_calculations')->default(false);



            $table->softDeletes();

            $table->dateTime('original_date')->nullable();
            $table->timestamps();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('old_transactions');
    }
};
