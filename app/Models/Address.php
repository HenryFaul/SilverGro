<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory;
    use HasFactory;

    use SoftDeletes;

    public $fillable = ['line_1','line_2','line_3','country','code','is_primary','longitude','latitude','directions','address_type_id','poly_address_type','poly_address_id'];


    public function AddressType(): BelongsTo
    {
        return $this->belongsTo(AddressType::class);
    }

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }
}
