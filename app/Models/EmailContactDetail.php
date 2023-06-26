<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmailContactDetail extends Model
{
    use HasFactory;

    use SoftDeletes;

    public $fillable = ['value','comment','contact_detail_type_id','poly_email_type','poly_email_id'];


    public function ContactType(): BelongsTo
    {
        return $this->belongsTo(ContactType::class);
    }

    public function contactable(): MorphTo
    {
        return $this->morphTo();
    }

}
