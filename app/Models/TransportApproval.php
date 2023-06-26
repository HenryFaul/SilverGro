<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportApproval extends Model
{
    use HasFactory;

    use SoftDeletes;

    public $fillable = ['transport_trans_id','transport_job_id','user_id'];



}
