<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Addresses cannot simply be deleted: roughly 70% of them are referenced by
     * a trade, and deleting a referenced address blanks it on that trade's
     * documents. Hiding is the reversible alternative - the row stays exactly
     * where it is for anything that looks it up by id, and only disappears from
     * the pickers and lists where a human is choosing an address.
     */
    public function up(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->timestamp('hidden_at')->nullable()->after('directions');
            $table->foreignId('hidden_by_id')->nullable()->after('hidden_at')
                ->constrained('users')->nullOnDelete();

            // Every picker filters on this, so it is worth an index.
            $table->index(['poly_address_type', 'poly_address_id', 'hidden_at'], 'addresses_party_hidden_index');
        });
    }

    public function down(): void
    {
        Schema::table('addresses', function (Blueprint $table) {
            $table->dropIndex('addresses_party_hidden_index');
            $table->dropConstrainedForeignId('hidden_by_id');
            $table->dropColumn('hidden_at');
        });
    }
};
