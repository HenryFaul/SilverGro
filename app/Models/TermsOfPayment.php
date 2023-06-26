<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TermsOfPayment extends Model
{
    use HasFactory;
    public $fillable = ['value','comment'];
}
