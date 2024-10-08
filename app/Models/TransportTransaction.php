<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class TransportTransaction extends Model
{
    use HasFactory;

    use SoftDeletes;

    use LogsActivity;

    //sl_id, is_split_load_primary, is_split_load_member

    public $fillable = ['id','old_id','old_deal_ticket','a_mq','sl_id','sl_global_id','contract_type_id','contract_no','supplier_id','customer_id','customer_id_2','customer_id_3','customer_id_4','customer_id_5','transporter_id','product_id','include_in_calculations','transport_date_earliest','transport_date_latest','delivery_notes',
        'product_notes','customer_notes','suppliers_notes','traders_notes','transport_notes','pricing_notes','process_notes','document_notes','transaction_notes',
        'traders_notes_supplier','traders_notes_customer','traders_notes_transport','is_transaction_done','created_at','is_split_load','is_split_load_primary','is_split_load_member'];


    protected $casts = [
        'transport_date_earliest' => 'date',
        'transport_date_latest' => 'date',
    ];


    public function TransLinks(): HasMany
    {
        return $this->hasMany(TransLink::class,'transport_trans_id');
    }

    public function TransLinkSplits(): HasMany
    {
        return $this->hasMany(TransLinkSplit::class,'transport_trans_id');
    }


    public function TransportStatus(): HasMany
    {
        return $this->hasMany(TransportStatus::class,'transport_trans_id');
    }

    public function TransportInvoiceDetails(): HasOne
    {
        return $this->hasOne(TransportInvoiceDetails::class,'transport_trans_id');
    }


    public function AssignedUserComm(): HasMany
    {
        return $this->hasMany(AssignedUserComm::class,'transport_trans_id');
    }


    public function TransportDriverVehicle(): HasMany
    {
        return $this->hasMany(TransportDriverVehicle::class,'transport_trans_id');
    }

    public function TransportInvoice(): HasOne
    {
        return $this->hasOne(TransportInvoice::class,'transport_trans_id');
    }

    public function DealTicket(): HasOne
    {
        return $this->hasOne(DealTicket::class,'transport_trans_id');
    }

    public function PurchaseOrder(): HasOne
    {
        return $this->hasOne(PurchaseOrder::class,'transport_trans_id');
    }

    public function TransportOrder(): HasOne
    {
        return $this->hasOne(TransportOrder::class,'transport_trans_id');
    }

    public function SalesOrder(): HasOne
    {
        return $this->hasOne(SalesOrder::class,'transport_trans_id');
    }

    public function TransportJob(): HasOne
    {
        return $this->hasOne(TransportJob::class,'transport_trans_id');
    }

    public function TransportLoad(): HasOne
    {
        return $this->hasOne(TransportLoad::class,'transport_trans_id');
    }

    public function TransportFinance(): HasOne
    {
        return $this->hasOne(TransportFinance::class,'transport_trans_id');
    }


    public function Product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }


    public function ContractType(): BelongsTo
    {
        return $this->belongsTo(ContractType::class);
    }


    public function Transporter(): BelongsTo
    {
        return $this->belongsTo(Transporter::class);
    }

    public function Customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function Customer_2(): BelongsTo
    {
        return $this->belongsTo(Customer::class,'customer_id_2');
    }

    public function Customer_3(): BelongsTo
    {
        return $this->belongsTo(Customer::class,'customer_id_3');
    }

    public function Customer_4(): BelongsTo
    {
        return $this->belongsTo(Customer::class,'customer_id_4');
    }

    public function Customer_5(): BelongsTo
    {
        return $this->belongsTo(Customer::class,'customer_id_5');
    }

    public function Supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }


    public function scopeFilter(Builder $query, array $filters): Builder
    {

        return $query->when(
            $filters['date'] ?? false,
            fn ($query, $value) => $query->whereDate('transport_date_earliest',"=" ,$value)
        )->when(
            $filters['contract_type_id'] ?? false,
            fn ($query, $value) => $query->where('contract_type_id',"=" ,$value)
        );

    }


    public function scopeIndex(Builder $query, array $filters): Builder
    {

        /*->orWhere('Supplier.id_reg_no', 'like', '%'.$value.'%')*/

        return $query->when(
            $filters['supplier_name'] ?? false,
            fn ($query, $value) => $query->whereHas('Supplier', function ($q) use ($value){
                $q->where('first_name', 'like', '%'.$value.'%')
                    ->orWhere('last_legal_name', 'like', '%'.$value.'%')
                    ->orWhere('id_reg_no', 'like', '%'.$value.'%');
            })
        )->when(
            $filters['customer_name'] ?? false,
            fn ($query, $value) => $query->whereHas('Customer', function ($q) use ($value){
                $q->where('first_name', 'like', '%'.$value.'%')
                    ->orWhere('last_legal_name', 'like', '%'.$value.'%')
                    ->orWhere('id_reg_no', 'like', '%'.$value.'%');
            })
        )->when(
            $filters['transporter_name'] ?? false,
            fn ($query, $value) => $query->whereHas('Transporter', function ($q) use ($value){
                $q->where('first_name', 'like', '%'.$value.'%')
                    ->orWhere('last_legal_name', 'like', '%'.$value.'%')
                    ->orWhere('id_reg_no', 'like', '%'.$value.'%');
            })
        )->when(
            $filters['product_name'] ?? false,
            fn ($query, $value) => $query->whereHas('Product', function ($q) use ($value){
                $q->where('name', 'like', '%'.$value.'%');
            })
        )->when(
            $filters['start_date'] ?? false,
            fn ($query, $value) => $query->whereDate('transport_date_earliest',">=" ,$value)
        )->when(
            $filters['end_date'] ?? false,
            fn ($query, $value) => $query->whereDate('transport_date_earliest',"<=" ,$value)
        )->when(
            $filters['contract_type_id'] ?? false,
            fn ($query, $value) => $query->where('contract_type_id',"=" ,$value)
        )->when(
            $filters['id'] ?? false,
            fn ($query, $value) => $query->where('id','like', '%'.$value.'%')
        )->when(
            $filters['old_id'] ?? false,
            fn ($query, $value) => $query->where('old_id','like', '%'.$value.'%')
        )->when(
            $filters['a_mq'] ?? false,
            fn ($query, $value) => $query->where('a_mq','like', '%'.$value.'%')
        );


    }


    public function scopeWeek(Builder $query, array $filters): Builder
    {
        return $query->when(
            $filters['date'] ?? false,
            fn ($query, $value) =>
            $query->whereBetween('transport_date_earliest', [Carbon::parse($value)->startOfWeek(),Carbon::parse($value)->endOfWeek()])
        )->when(
                $filters['contract_type_id'] ?? false,
                fn ($query, $value) => $query->where('contract_type_id',"=" ,$value)
            );

    }

    public function scopeMonth(Builder $query, array $filters): Builder
    {
        return $query->when(
            $filters['date'] ?? false,
            fn ($query, $value) =>
            $query->whereBetween('transport_date_earliest', [Carbon::parse($value)->startOfMonth(),Carbon::parse($value)->endOfMonth()])
        );

    }


    public function getActivitylogOptions(): LogOptions
    {
        // TODO: Implement getActivitylogOptions() method.

        return LogOptions::defaults()
            ->logOnly(['old_id','old_deal_ticket','contract_type_id','contract_no','supplier_id','customer_id','transporter_id','product_id','include_in_calculations','transport_date_earliest','transport_date_latest','delivery_notes',
                'product_notes','customer_notes','suppliers_notes','traders_notes','transport_notes','pricing_notes','process_notes','document_notes','transaction_notes',
                'traders_notes_supplier','traders_notes_customer','traders_notes_transport','is_transaction_done']);
    }
}
