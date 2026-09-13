<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory;

    use SoftDeletes;

    public $fillable = ['line_1','line_2','line_3','country','code','is_primary','longitude','latitude','directions','address_type_id','poly_address_type','poly_address_id','hidden_at','hidden_by_id'];

    protected $casts = [
        'hidden_at' => 'datetime',
    ];

    protected $appends = ['is_hidden'];

    public function AddressType(): BelongsTo
    {
        return $this->belongsTo(AddressType::class);
    }

    public function addressable(): MorphTo
    {
        return $this->morphTo();
    }

    public function HiddenBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hidden_by_id');
    }

    /**
     * Addresses a human should be offered.
     *
     * This is deliberately an opt-in scope rather than a global scope. A global
     * scope would also strip hidden addresses out of the lookups that render an
     * existing trade's documents, which would silently blank the address on
     * paperwork that has already gone out - the same failure that took the PDFs
     * down in August, but harder to spot. Hiding is about what we offer for
     * selection, never about what we can still resolve by id.
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->whereNull('hidden_at');
    }

    public function scopeHidden(Builder $query): Builder
    {
        return $query->whereNotNull('hidden_at');
    }

    public function getIsHiddenAttribute(): bool
    {
        return $this->hidden_at !== null;
    }
}
