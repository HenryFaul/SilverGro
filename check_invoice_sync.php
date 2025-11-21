<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Invoice Customer ID Sync ===\n\n";

// Check for mismatches
$result = DB::select("
    SELECT COUNT(*) as count
    FROM transport_invoices ti
    INNER JOIN transport_transactions tt ON ti.transport_trans_id = tt.id
    WHERE ti.customer_id != tt.customer_id
");

$count = $result[0]->count;

if ($count == 0) {
    echo "✅ SUCCESS! No mismatched invoices found.\n";
    echo "All invoice customer_ids are now in sync with their transactions.\n\n";
} else {
    echo "⚠️  Found {$count} invoices still mismatched.\n\n";

    // Show examples
    $examples = DB::select("
        SELECT
            ti.id as invoice_id,
            ti.customer_id as invoice_customer_id,
            tt.customer_id as transaction_customer_id,
            c1.last_legal_name as invoice_customer,
            c2.last_legal_name as transaction_customer
        FROM transport_invoices ti
        INNER JOIN transport_transactions tt ON ti.transport_trans_id = tt.id
        LEFT JOIN customers c1 ON ti.customer_id = c1.id
        LEFT JOIN customers c2 ON tt.customer_id = c2.id
        WHERE ti.customer_id != tt.customer_id
        LIMIT 5
    ");

    echo "Examples:\n";
    foreach ($examples as $ex) {
        echo "  Invoice #{$ex->invoice_id}: ";
        echo "Invoice Customer='{$ex->invoice_customer}' ({$ex->invoice_customer_id}), ";
        echo "Transaction Customer='{$ex->transaction_customer}' ({$ex->transaction_customer_id})\n";
    }
}

// Check Unallocated count
$unallocated = DB::select("
    SELECT COUNT(*) as count
    FROM transport_invoices
    WHERE customer_id = 2
");

echo "\nTotal invoices with Unallocated customer (ID=2): {$unallocated[0]->count}\n";

echo "\n=== Next Steps ===\n";
echo "1. Recalculate debtor standings: Visit /debtor/calculating\n";
echo "2. Check 'Unallocated' client in debtor standing page\n";
echo "3. Verify it only shows genuinely unallocated invoices\n";

