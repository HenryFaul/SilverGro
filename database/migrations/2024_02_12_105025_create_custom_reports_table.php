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






        Schema::create('custom_reports', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('created_by_id')->unsigned();
            $table->foreign('created_by_id')
                ->references('id')->on('users')->onDelete('cascade');
            $table->string('name');
            $table->string('comment')->nullable();
            $table->softDeletes();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['name'], 'unique_report');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_reports');
    }
};
