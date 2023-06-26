<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactDetail extends Model
{
    use HasFactory;

    use SoftDeletes;

    public $fillable = ['value','comment','country_code','contact_detail_type_id','system_player_id'];


    public function ContactType(): BelongsTo
    {
        return $this->belongsTo(ContactType::class);
    }
}
