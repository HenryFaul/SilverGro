<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */





/*FK	address_type_id	bigInt
FK	system_player_id	bigInt
line_1	varchar
line_2	varchar
line_3	varchar
country	varchar
code	varchar
is_primary	boolean
longitude	varchar
latitude	varchar
directions	varchar*/


    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('line_1')->nullable();
            $table->string('line_2')->nullable();
            $table->string('line_3')->nullable();
            $table->string('country')->nullable();
            $table->string('code')->nullable();

            $table->boolean('is_primary')->default(false);
            $table->double('longitude',11,8)->nullable();
            $table->double('latitude',10,8)->nullable();
            $table->longText('directions')->nullable();

            $table->bigInteger('address_type_id')->unsigned();
            $table->foreign('address_type_id')
                ->references('id')->on('address_types')->onDelete('cascade');

            $table->nullableMorphs('poly_address');

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
