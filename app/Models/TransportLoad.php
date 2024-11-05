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
    ,'billing_units_incoming_id','no_units_outgoing','no_units_outgoing_2','no_units_outgoing_3','no_units_outgoing_4','no_units_outgoing_5','billing_units_outgoing_id','is_weighbridge_certificate_received'
      ,'delivery_note','calculated_route_distance','collection_address_id','delivery_address_id','delivery_address_id_2','delivery_address_id_3','delivery_address_id_4','delivery_address_id_5'];

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

    public function ProductSource(): BelongsTo
    {
        return $this->belongsTo(ProductSource::class);
    }

    public function PackagingOutgoing(): BelongsTo
    {
        return $this->belongsTo(Packaging::class,'packaging_outgoing_id');
    }
    public function PackagingIncoming(): BelongsTo
    {
        return $this->belongsTo(Packaging::class,'packaging_incoming_id');
    }

    public function CollectionAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function DeliveryAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function DeliveryAddress_2(): BelongsTo
    {
        return $this->belongsTo(Address::class,'delivery_address_id_2');
    }

    public function DeliveryAddress_3(): BelongsTo
    {
        return $this->belongsTo(Address::class,'delivery_address_id_3');
    }

    public function DeliveryAddress_4(): BelongsTo
    {
        return $this->belongsTo(Address::class,'delivery_address_id_4');
    }

    public function DeliveryAddress_5(): BelongsTo
    {
        return $this->belongsTo(Address::class,'delivery_address_id_5');
    }


    public function ConfirmedByType(): BelongsTo
    {
        return $this->belongsTo(ConfirmationTypes::class);
    }


}
