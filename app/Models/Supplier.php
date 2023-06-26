<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory;

    use SoftDeletes;

    public $fillable = ['id','first_name','last_legal_name','nickname','title','job_description','id_reg_no','is_active',
        'terms_of_payment_id','account_number','comment'];


    public function TermsOfPayment(): BelongsTo
    {
        return $this->belongsTo(TermsOfPayment::class);
    }

    public function addressable(): MorphMany
    {
        return $this->morphMany(Address::class, 'poly_address');
    }

    public function contactable(): MorphMany
    {
        return $this->morphMany(Contact::class, 'poly_contact');
    }

    public function staff(): MorphToMany
    {
        return $this->morphToMany(Staff::class, 'staff_assigned');
    }
}
