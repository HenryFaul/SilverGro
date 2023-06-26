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
        Schema::create('transporters', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_legal_name')->nullable();
            $table->string('nickname')->nullable();
            $table->string('id_reg_no')->nullable();
            $table->string('title')->nullable();
            $table->string('job_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->bigInteger('terms_of_payment_id')->unsigned();
            $table->foreign('terms_of_payment_id')
                ->references('id')->on('terms_of_payments')->onDelete('cascade');
            $table->string('account_number')->nullable();
            $table->longText('comment')->nullable();
            $table->boolean('is_git')->nullable();
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
        Schema::dropIfExists('transporters');
    }
};
