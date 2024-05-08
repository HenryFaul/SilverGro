<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportDriverVehicle extends Model
{
    use HasFactory;

    use SoftDeletes;

    public $fillable = ['transport_trans_id','transport_job_id','regular_driver_id','regular_vehicle_id','weighbridge_upload_weight','weighbridge_offload_weight',
        'is_weighbridge_variance','is_cancelled','date_cancelled','is_loaded','date_loaded','is_onroad','date_onroad',
        'is_delivered','date_delivered','is_transport_scheduled','date_scheduled','is_paid','date_paid','is_payment_overdue',
        'traders_notes','operations_alert_notes','date_payment_overdue','driver_vehicle_loading_number','trailer_reg_1','trailer_reg_2'];



    public function Vehicle(): BelongsTo
    {
        return $this->belongsTo(RegularVehicle::class,'regular_vehicle_id');
    }

    public function Driver(): BelongsTo
    {
        return $this->belongsTo(RegularDriver::class,'regular_driver_id');
    }

}
