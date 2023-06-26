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
        Schema::create('assigned_user_comms', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('transport_trans_id')->unsigned();
            $table->foreign('transport_trans_id')
                ->references('id')->on('transport_transactions')->onDelete('cascade');

            $table->bigInteger('transport_finance_id')->unsigned();
            $table->foreign('transport_finance_id')
                ->references('id')->on('transport_finances')->onDelete('cascade');

            $table->bigInteger('assigned_user_supplier_id')->unsigned();
            $table->foreign('assigned_user_supplier_id')
                ->references('id')->on('staff')->onDelete('cascade');

            $table->bigInteger('assigned_user_customer_id')->unsigned();
            $table->foreign('assigned_user_customer_id')
                ->references('id')->on('staff')->onDelete('cascade');

            $table->double('supplier_comm')->default(0);
            $table->double('customer_comm')->default(0);

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
        Schema::dropIfExists('assigned_user_comms');
    }
};
