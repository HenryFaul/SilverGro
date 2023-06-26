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
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('old_id')->nullable();
            $table->bigInteger('transport_trans_id')->unsigned();
            $table->foreign('transport_trans_id')
                ->references('id')->on('transport_transactions')->onDelete('cascade');

            $table->bigInteger('confirmed_by_id')->unsigned();
            $table->foreign('confirmed_by_id')
                ->references('id')->on('staff')->onDelete('cascade');

            $table->bigInteger('confirmed_by_type_id')->unsigned();
            $table->foreign('confirmed_by_type_id')
                ->references('id')->on('confirmation_types')->onDelete('cascade');


            $table->string('type')->nullable();
            $table->longText('comment')->nullable();
            $table->boolean('is_active')->default(1);
            $table->boolean('is_printed')->default(0);

            $table->boolean('is_po_sent')->default(0);
            $table->boolean('is_po_received')->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
