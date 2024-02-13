<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomReport extends Model
{
    use HasFactory;

    public $fillable = ['id','created_by_id','name','comment','is_active'];

    public function CustomReportModels(): HasMany
    {
        return $this->hasMany(CustomReportModel::class,'report_id');
    }

}
