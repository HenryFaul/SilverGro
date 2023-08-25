<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory;
    use SoftDeletes;

    public $fillable = ['first_name','last_legal_name','user_id','nickname','title','job_description','id_reg_no','is_active'];



    public function customer(): MorphToMany
    {
        return $this->morphedByMany(Customer::class, 'staff_assigned');
    }

    public function User(): belongsTo
    {
        return $this->belongsTo(User::class,'user_id');
    }

}
