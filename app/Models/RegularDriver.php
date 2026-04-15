<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegularDriver extends Model
{
    use HasFactory;

    use SoftDeletes;

    public $fillable = ['first_name','last_name','cell_no','comment','is_active','transporter_id'];

    public function Transporter(): BelongsTo
    {
        return $this->belongsTo(Transporter::class, 'transporter_id');
    }

    public function TransportDriverVehicles(): HasMany
    {
        return $this->hasMany(TransportDriverVehicle::class, 'regular_driver_id');
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query->when(
            $filters['searchName'] ?? false,
            fn ($query, $value) => $query->where(function($q) use ($value) {
                // Search by driver first_name or last_name
                $q->where('first_name', 'like', '%'.$value.'%')
                  ->orWhere('last_name', 'like', '%'.$value.'%')
                  // Or search by transporter name or vehicle reg_no
                  ->orWhereHas('TransportDriverVehicles', function($subQuery) use ($value) {
                      $subQuery->whereHas('TransportTransaction', function($transQuery) use ($value) {
                          $transQuery->whereHas('Transporter', function($transporterQuery) use ($value) {
                              $transporterQuery->where('first_name', 'like', '%'.$value.'%')
                                               ->orWhere('last_legal_name', 'like', '%'.$value.'%');
                          });
                      })
                      // Or search by vehicle
                      ->orWhereHas('Vehicle', function($vehicleQuery) use ($value) {
                          $vehicleQuery->where('reg_no', 'like', '%'.$value.'%');
                      });
                  });
            })
        )->when(
            $filters['isActive'] ?? false,
            fn ($query, $value) => $query->where('is_active', $value == 'active' ? 1:0)
        )->when(
            $filters['field'] ?? false,
            fn ($query, $value) =>
            $query->orderBy($value, $filters['direction'] ?? 'asc')
        );

    }
}
