<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Address;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Create a generic "No Address" record with ID 1
        Address::updateOrCreate(
            ['id' => 1],
            [
                'line_1' => 'No Address Specified',
                'line_2' => null,
                'line_3' => null,
                'country' => null,
                'code' => null,
                'address_type_id' => 1, // Assuming 1 is a valid address type
                'poly_address_type' => null,
                'poly_address_id' => null,
                'is_primary' => false,
                'longitude' => null,
                'latitude' => null,
                'directions' => 'Generic placeholder address for when no address is specified',
            ]
        );

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the generic address
        Address::where('id', 1)->delete();
    }
};

