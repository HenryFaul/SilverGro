<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The part of a customer's balance on invoices that are late: past invoice
     * date + payment terms + days overdue allowed. Drives the debtor flags;
     * total_overdue stays the plain accounting figure.
     */
    public function up(): void
    {
        Schema::table('debtor_standings', function (Blueprint $table) {
            $table->double('total_late')->default(0)->after('total_overdue');
        });
    }

    public function down(): void
    {
        Schema::table('debtor_standings', function (Blueprint $table) {
            $table->dropColumn('total_late');
        });
    }
};
