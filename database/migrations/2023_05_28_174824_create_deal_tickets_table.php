<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.

    FK	transport_trans_id	bigInt
    type	varchar
    comment	varchar
    is_active	boolean
    is_printed	boolean
     *
     * public $fillable = ['transport_trans_id','type','comment','is_active','is_printed'];

     */
    public function up(): void
    {
        Schema::create('deal_tickets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('old_id')->nullable();
            $table->bigInteger('transport_trans_id')->unsigned();
            $table->foreign('transport_trans_id')
                ->references('id')->on('transport_transactions')->onDelete('cascade');
            $table->string('type')->nullable();
            $table->double('trade_value')->default(0);

            $table->longText('comment')->nullable();
            $table->boolean('is_active')->default(1);
            $table->boolean('is_approved')->default(0);
            $table->boolean('is_printed')->default(0);
            $table->timestamp('stamp_printed')->nullable();

            $table->string('report_path')->nullable();
            $table->string('report_path_old')->nullable();
            $table->string('report_path_confirmation')->nullable();
            $table->string('report_path_confirmation_old')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deal_tickets');
    }
};
