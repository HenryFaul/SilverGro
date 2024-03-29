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
        Schema::create('email_contact_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('contact_detail_type_id')->unsigned();
            $table->foreign('contact_detail_type_id')
                ->references('id')->on('contact_types')->onDelete('cascade');
            $table->Morphs('poly_email');
            $table->string('value');
            $table->string('comment')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_contact_details');
    }
};
