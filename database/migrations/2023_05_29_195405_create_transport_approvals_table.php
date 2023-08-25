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
        Schema::create('transport_approvals', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('transport_trans_id')->unsigned();
            $table->foreign('transport_trans_id')
                ->references('id')->on('transport_transactions')->onDelete('cascade');

            $table->bigInteger('transport_job_id')->unsigned();
            $table->foreign('transport_job_id')
                ->references('id')->on('transport_jobs')->onDelete('cascade');

            $table->bigInteger('deal_ticket_id')->unsigned();
            $table->foreign('deal_ticket_id')
                ->references('id')->on('deal_tickets')->onDelete('cascade');

            $table->bigInteger('approved_by_id')->unsigned()->nullable();
            $table->foreign('approved_by_id')
                ->references('id')->on('users')->onDelete('cascade');

            $table->Morphs('poly_approval');

            $table->string('role_name');

            $table->boolean('is_approved')->default(false);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transport_approvals');
    }
};
