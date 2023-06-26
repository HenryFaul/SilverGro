<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportStatus extends Model
{
    use HasFactory;

    use SoftDeletes;

    public $fillable = ['transport_trans_id','status_entity_id','status_type_id'];


    public function StatusEntity(): BelongsTo
    {
        return $this->belongsTo(StatusEntity::class,'status_entity_id');
    }

    public function StatusType(): BelongsTo
    {
        return $this->belongsTo(StatusType::class,'status_type_id');
    }
}
