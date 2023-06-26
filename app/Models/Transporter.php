<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transporter extends Model
{
    use HasFactory;

    use SoftDeletes;

    public $fillable = ['id','first_name','last_legal_name','nickname','title','job_description','id_reg_no','is_active',
        'terms_of_payment_id','account_number','comment','is_git'];

    public function TermsOfPayment(): BelongsTo
    {
        return $this->belongsTo(TermsOfPayment::class);
    }
}
