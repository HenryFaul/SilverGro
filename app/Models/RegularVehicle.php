<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegularVehicle extends Model
{
    use HasFactory;

    use SoftDeletes;

    public $fillable = ['vehicle_type_id','reg_no','comment','is_active'];

    public function VehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class,'vehicle_type_id');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query->when(
            $filters['searchName'] ?? false,
            fn ($query, $value) => $query->where('reg_no', 'like', '%'.$value.'%')
        )->when(
            $filters['isActive'] ?? false,
            fn ($query, $value) => $query->where('is_active', $value == 'active' ? 1:0)
        );

    }
}
