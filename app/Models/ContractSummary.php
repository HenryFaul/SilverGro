<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractSummary extends Model
{
    use HasFactory;

    public $fillable = ['transport_trans_id','transport_date_earliest','contract_type_id'
        ,'contract_number','planned_tons_in','planned_tons_out','actual_tons_in','actual_tons_out',
        'variance_in','variance_out'];

    public function scopeMonth(Builder $query, array $filters): Builder
    {
        return $query->when(
            $filters['date'] ?? false,
            fn ($query, $value) =>
            $query->whereBetween('transport_date_earliest', [Carbon::parse($value)->startOfMonth(),Carbon::parse($value)->endOfMonth()])
        );

    }
}
