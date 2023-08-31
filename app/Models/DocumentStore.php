<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DocumentStore extends Model
{
    use HasFactory;

    use SoftDeletes;

    public $fillable = ['transport_trans_id','report_type','file_name','file_path'];
}
