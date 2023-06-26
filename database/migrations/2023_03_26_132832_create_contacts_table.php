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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_legal_name')->nullable();
            $table->string('nickname')->nullable();
            $table->string('id_reg_no')->nullable();
            $table->string('title')->nullable();
            $table->string('job_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('branch')->nullable();
            $table->string('department')->nullable();
            $table->longText('comment')->nullable();
            $table->nullableMorphs('poly_contact');
            $table->softDeletes();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
