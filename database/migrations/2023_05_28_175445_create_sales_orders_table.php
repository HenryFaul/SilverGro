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
        Schema::create('sales_orders', function (Blueprint $table) {
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

            $table->boolean('is_sa_conf_sent')->default(0);
            $table->boolean('is_sa_conf_received')->default(0);

            $table->string('report_path')->nullable();
            $table->string('report_path_old')->nullable();
            $table->string('report_path_confirmation')->nullable();
            $table->string('report_path_confirmation_old')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_orders');
    }
};
