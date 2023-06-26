<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BillingUnits extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $fillable = ['old_id','name','kgs','comment','is_active'];
}
