<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Key	Attribute	Type


     */
    public function up(): void
    {
        Schema::create('transport_invoice_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('transport_trans_id')->unsigned();
            $table->foreign('transport_trans_id')
                ->references('id')->on('transport_transactions')->onDelete('cascade');

            $table->bigInteger('invoice_id')->unsigned();
            $table->foreign('invoice_id')
                ->references('id')->on('transport_invoices')->onDelete('cascade');


            $table->boolean('is_invoiced')->default(0);
            $table->boolean('is_invoice_paid')->default(0);
            $table->string('invoice_no')->nullable();
            $table->date('invoice_paid_date')->nullable();
            $table->date('invoice_pay_by_date')->nullable();
            $table->date('invoice_date')->nullable();
            $table->double('invoice_amount')->default(0);
            $table->double('cost_price')->default(0);
            $table->double('invoice_amount_paid')->default(0);
            $table->double('outstanding')->default(0);
            $table->double('overdue')->default(0);
            $table->double('selling_price')->default(0);
            $table->bigInteger('status_id')->unsigned();
            $table->foreign('status_id')
                ->references('id')->on('invoice_statuses')->onDelete('cascade');
            $table->longText('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_invoice_details');
    }
};
