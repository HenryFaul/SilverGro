<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportJob extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $fillable = ['transport_trans_id','transport_rate_basis_id','customer_order_number','supplier_loading_number','is_multi_loads',
        'is_approved','transport_date_earliest','transport_date_latest','is_transport_costs_inc_price','is_product_zero_rated',
        'loading_hours_from_id','loading_hours_to_id','offloading_hours_from_id','offloading_hours_to_id','load_insurance_per_ton',
        'total_load_insurance','number_loads','loading_instructions','offloading_instructions','comments','customer_order_number_2','supplier_loading_number_2',
        'customer_order_number_3','supplier_loading_number_3','customer_order_number_4','supplier_loading_number_4','customer_order_number_5','supplier_loading_number_5'];


    public function TransportDriverVehicle(): HasMany
    {
        return $this->hasMany(TransportDriverVehicle::class,'transport_job_id');
    }

    public function OffloadingHoursFrom(): BelongsTo
    {
        return $this->belongsTo(LoadingHourOption::class,'offloading_hours_from_id');
    }

    public function OffloadingHoursTo(): BelongsTo
    {
        return $this->belongsTo(LoadingHourOption::class,'offloading_hours_to_id');
    }

    public function LoadingHoursFrom(): BelongsTo
    {
        return $this->belongsTo(LoadingHourOption::class,'loading_hours_from_id');
    }

    public function LoadingHoursTo(): BelongsTo
    {
        return $this->belongsTo(LoadingHourOption::class,'loading_hours_to_id');
    }
}
