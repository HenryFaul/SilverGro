<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactType extends Model
{
    use HasFactory;


    public $fillable = ['type'];

   // public $fillable = ['value','comment','country_code','contact_detail_type_id','system_player_id'];


    public function ContactDetail(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(ContactDetail::class);
    }
}
