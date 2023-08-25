<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransportApproval extends Model
{
    use HasFactory;

    use SoftDeletes;

    public $fillable = ['transport_trans_id','deal_ticket_id','poly_approval_type','poly_approval_id','transport_job_id','approved_by_id','role_name','is_approved'];


    public function User(): BelongsTo
    {
        return $this->belongsTo(User::class,'approved_by_id');
    }


}
