<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignedUserComm extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $fillable = ['transport_trans_id','transport_finance_id','assigned_user_supplier_id','assigned_user_customer_id',
        'supplier_comm','customer_comm','notes'];


    public function AssignedUserSupplier(): BelongsTo
    {
        return $this->belongsTo(Staff::class,'assigned_user_supplier_id');
    }

    public function AssignedUserCustomer(): BelongsTo
    {
        return $this->belongsTo(Staff::class,'assigned_user_customer_id');
    }

    public function TransportTransaction(): BelongsTo
    {
        return $this->belongsTo(TransportTransaction::class, 'transport_trans_id');
    }

    /**
     * Auto-populate AssignedUserComm records from the linked staff of the
     * given supplier and customer. Zips the two lists (shorter side repeats
     * its last entry). Skips pairs that already exist.
     */
    public static function syncDefaultCommUsers(int $transactionId, int $supplierId, int $customerId): void
    {
        $financeId = TransportFinance::where('transport_trans_id', $transactionId)->value('id');
        if (!$financeId) {
            return;
        }

        $supplierIds = Supplier::find($supplierId)?->staff->pluck('id')->toArray() ?? [];
        $customerIds = Customer::find($customerId)?->staff->pluck('id')->toArray() ?? [];

        // Both sides must have at least one staff member — can't create a valid pair otherwise
        if (empty($supplierIds) || empty($customerIds)) {
            return;
        }

        $maxCount = max(count($supplierIds), count($customerIds));

        for ($i = 0; $i < $maxCount; $i++) {
            $suppId = $supplierIds[$i] ?? $supplierIds[array_key_last($supplierIds)];
            $custId = $customerIds[$i] ?? $customerIds[array_key_last($customerIds)];

            $exists = self::where('transport_trans_id', $transactionId)
                ->where('assigned_user_supplier_id', $suppId)
                ->where('assigned_user_customer_id', $custId)
                ->exists();

            if (!$exists) {
                self::create([
                    'transport_trans_id'        => $transactionId,
                    'transport_finance_id'      => $financeId,
                    'assigned_user_supplier_id' => $suppId,
                    'assigned_user_customer_id' => $custId,
                    'supplier_comm'             => 0,
                    'customer_comm'             => 0,
                ]);
            }
        }
    }

    /**
     * Remove AssignedUserComm records whose staff is no longer linked to the
     * new supplier or customer after a change. Only removes records where the
     * staff was linked to the OLD entity but not the new one.
     */
    public static function removeStaleCommUsers(int $transactionId, int $oldSupplierId, int $newSupplierId, int $oldCustomerId, int $newCustomerId): void
    {
        if ($oldSupplierId !== $newSupplierId) {
            $oldIds = Supplier::find($oldSupplierId)?->staff->pluck('id')->toArray() ?? [];
            $newIds = Supplier::find($newSupplierId)?->staff->pluck('id')->toArray() ?? [];
            $stale  = array_diff($oldIds, $newIds);
            if (!empty($stale)) {
                self::where('transport_trans_id', $transactionId)
                    ->whereIn('assigned_user_supplier_id', $stale)
                    ->delete();
            }
        }

        if ($oldCustomerId !== $newCustomerId) {
            $oldIds = Customer::find($oldCustomerId)?->staff->pluck('id')->toArray() ?? [];
            $newIds = Customer::find($newCustomerId)?->staff->pluck('id')->toArray() ?? [];
            $stale  = array_diff($oldIds, $newIds);
            if (!empty($stale)) {
                self::where('transport_trans_id', $transactionId)
                    ->whereIn('assigned_user_customer_id', $stale)
                    ->delete();
            }
        }
    }
}
