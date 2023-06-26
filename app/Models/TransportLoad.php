<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportLoad extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $fillable = ['transport_trans_id','confirmed_by_id','confirmed_by_type_id',
     'product_id','packaging_incoming_id','packaging_outgoing_id','product_source_id','product_grade_perc','no_units_incoming'
    ,'billing_units_incoming_id','no_units_outgoing','billing_units_outgoing_id','is_weighbridge_certificate_received'
      ,'delivery_note','calculated_route_distance','collection_address_id','delivery_address_id'];

    public function BillingUnitsIncoming(): BelongsTo
    {
        return $this->belongsTo(BillingUnits::class,'billing_units_incoming_id');
    }

    public function BillingUnitsOutgoing(): BelongsTo
    {
        return $this->belongsTo(BillingUnits::class,'billing_units_outgoing_id');
    }

    public function TransportFinance(): HasOne
    {
        return $this->hasOne(TransportFinance::class,'transport_load_id');
    }


}
