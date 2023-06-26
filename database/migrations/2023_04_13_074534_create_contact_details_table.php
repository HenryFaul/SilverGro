<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

//['value','comment','country_code','contact_id','contact_detail_type_id'];

    public function up(): void
    {
        Schema::create('contact_details', function (Blueprint $table) {

            $table->id();
            $table->bigInteger('contact_detail_type_id')->unsigned();
            $table->foreign('contact_detail_type_id')
                ->references('id')->on('contact_types')->onDelete('cascade');

            $table->Morphs('poly_contact');

            $table->string('value');
            $table->string('comment')->nullable();
            $table->string('country_code')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contact_details');
    }
};
