<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransLink extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $fillable = ['trans_link_type_id','transport_trans_id','linked_transport_trans_id'];

    public function TransportTransaction(): BelongsTo
    {
        return $this->belongsTo(TransportTransaction::class,'linked_transport_trans_id');
    }

    public function TransportTransactionPc(): BelongsTo
    {
        return $this->belongsTo(TransportTransaction::class,'transport_trans_id');
    }

}
