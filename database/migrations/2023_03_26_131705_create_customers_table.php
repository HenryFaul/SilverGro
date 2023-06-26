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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_legal_name')->nullable();
            $table->string('nickname')->nullable();
            $table->string('id_reg_no')->nullable();
            $table->string('title')->nullable();
            $table->boolean('is_active')->default(true);
            $table->bigInteger('terms_of_payment_id')->unsigned();
            $table->foreign('terms_of_payment_id')
                ->references('id')->on('terms_of_payments')->onDelete('cascade');
            $table->bigInteger('invoice_basis_id')->unsigned();
            $table->foreign('invoice_basis_id')
                ->references('id')->on('invoice_bases')->onDelete('cascade');
            $table->bigInteger('customer_rating_id')->unsigned();
            $table->foreign('customer_rating_id')
                ->references('id')->on('customer_ratings')->onDelete('cascade');
            $table->bigInteger('days_overdue_allowed_id')->unsigned();
            $table->foreign('days_overdue_allowed_id')
                ->references('id')->on('terms_of_payments')->onDelete('cascade');
            $table->boolean('is_vat_exempt')->default(false);
            $table->boolean('is_vat_cert_received')->default(false);
            $table->double('credit_limit')->default(0);
            $table->double('credit_limit_hard')->default(0);
            $table->longText('comment')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['first_name','last_legal_name','id_reg_no']);


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
