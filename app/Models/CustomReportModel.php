<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomReportModel extends Model
{
    use HasFactory;

    public $fillable = ['id','created_by_id','report_id','class_name','attribute_name','comment','is_active'];

    public function CustomReport(): HasOne
    {
        return $this->hasOne(CustomReport::class,'report_id');
    }

}
