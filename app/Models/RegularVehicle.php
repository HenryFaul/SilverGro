<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegularVehicle extends Model
{
    use HasFactory;

    use SoftDeletes;

    public $fillable = ['vehicle_type_id','reg_no','comment','is_active','transporter_id','regular_driver_id'];

    public function VehicleType(): BelongsTo
    {
        return $this->belongsTo(VehicleType::class,'vehicle_type_id');
    }

    public function Transporter(): BelongsTo
    {
        return $this->belongsTo(Transporter::class, 'transporter_id');
    }

    public function Driver(): BelongsTo
    {
        return $this->belongsTo(RegularDriver::class, 'regular_driver_id');
    }

    public function TransportDriverVehicles(): HasMany
    {
        return $this->hasMany(TransportDriverVehicle::class, 'regular_vehicle_id');
    }

    /**
     * Get all unique transporters for this vehicle — combines transaction history
     * with the directly linked transporter_id (set on standalone vehicle creation).
     */
    public function getTransportersAttribute()
    {
        $historyIds = TransportTransaction::whereHas('TransportDriverVehicle', function ($query) {
            $query->where('regular_vehicle_id', $this->id);
        })->pluck('transporter_id')->unique();

        $transporters = Transporter::whereIn('id', $historyIds)
            ->select('id', 'first_name', 'last_legal_name')
            ->get();

        if ($this->transporter_id && !$historyIds->contains($this->transporter_id)) {
            $direct = Transporter::select('id', 'first_name', 'last_legal_name')
                ->find($this->transporter_id);
            if ($direct) {
                $transporters->push($direct);
            }
        }

        return $transporters;
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query->when(
            $filters['searchName'] ?? false,
            fn ($query, $value) => $query->where(function($q) use ($value) {
                // Search by vehicle reg_no
                $q->where('reg_no', 'like', '%'.$value.'%')
                  // Or search by transporter name
                  ->orWhereHas('TransportDriverVehicles', function($subQuery) use ($value) {
                      $subQuery->whereHas('TransportTransaction', function($transQuery) use ($value) {
                          $transQuery->whereHas('Transporter', function($transporterQuery) use ($value) {
                              $transporterQuery->where('first_name', 'like', '%'.$value.'%')
                                               ->orWhere('last_legal_name', 'like', '%'.$value.'%');
                          });
                      })
                      // Or search by driver name
                      ->orWhereHas('Driver', function($driverQuery) use ($value) {
                          $driverQuery->where('first_name', 'like', '%'.$value.'%')
                                      ->orWhere('last_name', 'like', '%'.$value.'%');
                      });
                  });
            })
        )->when(
            $filters['isActive'] ?? false,
            fn ($query, $value) => $query->where('is_active', $value == 'active' ? 1:0)
        );

    }
}
