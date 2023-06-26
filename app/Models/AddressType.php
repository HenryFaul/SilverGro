<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AddressType extends Model
{
    use HasFactory;


    public $fillable = ['type'];


    public function Address(): BelongsTo
    {
        return $this->belongsToMany(Address::class);
    }
}
