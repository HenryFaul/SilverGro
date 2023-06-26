<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $fillable = ['transport_trans_id','confirmed_by_type_id','confirmed_by_id','type','comment','is_active','is_printed','is_to_sent','is_to_received'];

}
