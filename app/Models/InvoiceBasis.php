<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InvoiceBasis extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $fillable = ['value','comment'];
}
