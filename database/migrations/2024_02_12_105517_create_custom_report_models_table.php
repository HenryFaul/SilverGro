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


        Schema::create('custom_report_models', function (Blueprint $table) {

            $table->id();
            $table->bigInteger('created_by_id')->unsigned();
            $table->foreign('created_by_id')
                ->references('id')->on('users')->onDelete('cascade');

            $table->bigInteger('report_id')->unsigned();
            $table->foreign('report_id')
                ->references('id')->on('custom_reports')->onDelete('cascade');

            $table->string('class_name');
            $table->string('attribute_name');

            $table->string('comment')->nullable();
            $table->softDeletes();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['report_id','class_name','attribute_name'], 'unique_report_attribute');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_report_models');
    }
};
