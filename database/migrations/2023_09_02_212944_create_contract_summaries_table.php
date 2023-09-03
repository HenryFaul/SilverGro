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
        Schema::create('contract_summaries', function (Blueprint $table) {
            $table->id();
            $table->timestamp('transport_date_earliest')->nullable();
            $table->bigInteger('contract_type_id')->unsigned();
            $table->foreign('contract_type_id')
                ->references('id')->on('contract_types')->onDelete('cascade');

            $table->bigInteger('transport_trans_id')->unsigned();
            $table->foreign('transport_trans_id')
                ->references('id')->on('transport_transactions')->onDelete('cascade');
            $table->string('contract_number');
            $table->double('planned_tons_in')->default(0);
            $table->double('planned_tons_out')->default(0);
            $table->double('actual_tons_in')->default(0);
            $table->double('actual_tons_out')->default(0);
            $table->double('variance_in')->default(0);
            $table->double('variance_out')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_summaries');
    }
};
