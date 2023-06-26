<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DealTicket extends Model
{
    use HasFactory;

    use SoftDeletes;

    public $fillable = ['transport_trans_id','old_id','type','comment','is_active','is_printed','stamp_printed'];
}
