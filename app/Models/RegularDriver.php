<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegularDriver extends Model
{
    use HasFactory;

    use SoftDeletes;

    public $fillable = ['first_name','last_name','cell_no','comment','is_active'];
}
