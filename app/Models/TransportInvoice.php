<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportInvoice extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $fillable = ['old_id','transport_trans_id','type','comment','is_active','is_printed'];

    public function TransportInvoiceDetails(): HasOne
    {
        return $this->hasOne(TransportInvoiceDetails::class,'invoice_id');
    }

}
