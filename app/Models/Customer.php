<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory;

    use SoftDeletes;

    public $fillable = ['id','first_name','customer_parent_id','last_legal_name','nickname','title','id_reg_no','is_active','terms_of_payment_basis_id','terms_of_payment_id','invoice_basis_id','customer_rating_id','is_vat_exempt','is_vat_cert_received',
        'credit_limit','credit_limit_hard','comment','days_overdue_allowed_id'];

    protected $appends = [
        'trades_count'
    ];

    protected function tradesCount(): Attribute
    {
        return new Attribute(
            get: fn () => $this->TransportTransaction()->where('is_transaction_done','=',false)->where('include_in_calculations','=',true)->count()
        );
    }

    public function TransportTransaction(): HasMany
    {
        return $this->hasMany(TransportTransaction::class);
    }


    public function CustomerParent(): BelongsTo
    {
        return $this->belongsTo(CustomerParent::class);
    }


    public function TermsOfPayment(): BelongsTo
    {
        return $this->belongsTo(TermsOfPayment::class);
    }

    public function TermsOfPaymentBasis(): BelongsTo
    {
        return $this->belongsTo(TermsOfPaymentBasis::class);
    }

    public function InvoiceBasis(): BelongsTo
    {
        return $this->belongsTo(InvoiceBasis::class);
    }

    public function CustomerRating(): BelongsTo
    {
        return $this->belongsTo(CustomerRating::class);
    }


    public function addressable(): MorphMany
    {
        return $this->morphMany(Address::class, 'poly_address');
    }

    public function addressablePhysical(): MorphMany
    {
        return $this->morphMany(Address::class, 'poly_address')->where('address_type_id',2);
    }

    public function contactable(): MorphMany
    {
        return $this->morphMany(Contact::class, 'poly_contact');
    }

    public function staff(): MorphToMany
    {
        return $this->morphToMany(Staff::class, 'staff_assigned');
    }


    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query->when(
            $filters['searchName'] ?? false,
            fn ($query, $value) => $query->where('first_name', 'like', '%'.$value.'%')
                ->orWhere('last_legal_name', 'like', '%'.$value.'%')
                ->orWhere('id_reg_no', 'like', '%'.$value.'%')
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
