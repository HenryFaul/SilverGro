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
}
