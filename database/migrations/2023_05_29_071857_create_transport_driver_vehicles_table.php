<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**

     */

    public function up(): void
    {
        Schema::create('transport_driver_vehicles', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('transport_trans_id')->unsigned();
            $table->foreign('transport_trans_id')
                ->references('id')->on('transport_transactions')->onDelete('cascade');

            $table->bigInteger('transport_job_id')->unsigned();
            $table->foreign('transport_job_id')
                ->references('id')->on('transport_jobs')->onDelete('cascade');



            $table->bigInteger('regular_driver_id')->unsigned();
            $table->foreign('regular_driver_id')
                ->references('id')->on('regular_drivers')->onDelete('cascade');

            $table->bigInteger('regular_vehicle_id')->unsigned();
            $table->foreign('regular_vehicle_id')
                ->references('id')->on('regular_vehicles')->onDelete('cascade');

            $table->string('driver_vehicle_loading_number')->nullable();
            $table->string('trailer_reg_1')->nullable();
            $table->string('trailer_reg_2')->nullable();

            $table->double('weighbridge_upload_weight')->default(0);
            $table->double('weighbridge_offload_weight')->default(0);
            $table->boolean('is_weighbridge_variance')->default(0);
            $table->boolean('is_cancelled')->default(0);
            $table->dateTime('date_cancelled')->nullable();
            $table->boolean('is_loaded')->default(0);
            $table->dateTime('date_loaded')->nullable();
            $table->boolean('is_onroad')->default(0);
            $table->dateTime('date_onroad')->nullable();
            $table->boolean('is_delivered')->default(0);
            $table->dateTime('date_delivered')->nullable();
            $table->boolean('is_transport_scheduled')->default(0);
            $table->dateTime('date_scheduled')->nullable();
            $table->boolean('is_paid')->default(0);
            $table->dateTime('date_paid')->nullable();
            $table->boolean('is_payment_overdue')->default(0);
            $table->dateTime('date_payment_overdue')->nullable();
            $table->longText('traders_notes')->nullable();
            $table->longText('operations_alert_notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_driver_vehicles');
    }
};
