<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class NumberContactDetail extends Model
{
    use HasFactory;

    use SoftDeletes;

    public $fillable = ['value','comment','country_code', 'contact_detail_type_id','poly_number_type','poly_number_id'];

    public function ContactType(): BelongsTo
    {
        return $this->belongsTo(ContactType::class);
    }

    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }
}
