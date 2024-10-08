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

     */

//sl_id, is_split_load_primary, is_split_load_member

    public function up(): void
    {
        Schema::create('transport_transactions', function (Blueprint $table) {
            $table->id()->from(13000);

            $table->bigInteger('old_id')->nullable();
            $table->bigInteger('a_mq')->nullable();
            $table->bigInteger('sl_id')->nullable();
            $table->bigInteger('sl_global_id')->nullable();

            $table->string('contract_no')->nullable();
            $table->string('old_deal_ticket')->nullable();

            $table->bigInteger('contract_type_id')->unsigned();
            $table->foreign('contract_type_id')
                ->references('id')->on('contract_types')->onDelete('cascade');

            $table->bigInteger('supplier_id')->unsigned();
            $table->foreign('supplier_id')
                ->references('id')->on('suppliers')->onDelete('cascade');

            $table->bigInteger('customer_id')->unsigned();
            $table->foreign('customer_id')
                ->references('id')->on('customers')->onDelete('cascade');

            $table->bigInteger('customer_id_2')->unsigned()->default(1);
            $table->foreign('customer_id_2')
                ->references('id')->on('customers')->onDelete('cascade');

            $table->bigInteger('customer_id_3')->unsigned()->default(1);
            $table->foreign('customer_id_3')
                ->references('id')->on('customers')->onDelete('cascade');

            $table->bigInteger('customer_id_4')->unsigned()->default(1);
            $table->foreign('customer_id_4')
                ->references('id')->on('customers')->onDelete('cascade');

            $table->bigInteger('customer_id_5')->unsigned()->default(1);
            $table->foreign('customer_id_5')
                ->references('id')->on('customers')->onDelete('cascade');

            $table->bigInteger('transporter_id')->unsigned();
            $table->foreign('transporter_id')
                ->references('id')->on('transporters')->onDelete('cascade');

            $table->bigInteger('product_id')->unsigned();
            $table->foreign('product_id')
                ->references('id')->on('products')->onDelete('cascade');
            $table->timestamp('transport_date_earliest')->nullable();
            $table->timestamp('transport_date_latest')->nullable();
            $table->longText('suppliers_notes')->nullable();
            $table->longText('traders_notes')->nullable();
            $table->longText('delivery_notes')->nullable();
            $table->longText('product_notes')->nullable();
            $table->longText('customer_notes')->nullable();
            $table->longText('transport_notes')->nullable();
            $table->longText('pricing_notes')->nullable();

            $table->longText('process_notes')->nullable();
            $table->longText('document_notes')->nullable();
            $table->longText('transaction_notes')->nullable();

            $table->longText('traders_notes_supplier')->nullable();
            $table->longText('traders_notes_customer')->nullable();
            $table->longText('traders_notes_transport')->nullable();

            $table->boolean('include_in_calculations')->default(1);
            $table->boolean('is_transaction_done')->default(0);
            $table->boolean('is_split_load')->default(0);
            $table->boolean('is_split_load_primary')->default(0);
            $table->boolean('is_split_load_member')->default(0);

            $table->softDeletes();
            $table->timestamps();
        ;
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_transactions');
    }
};
