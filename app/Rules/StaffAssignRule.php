<?php

namespace App\Rules;

use App\Models\Customer;
use App\Models\PivotStaffAssigned;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StaffAssignRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */


    public int $related_id;
    public string $related_class;

    public function __construct($related_id,$related_class)
    {
        $this->related_id = $related_id;
        $this->related_class = $related_class;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void

    {
        //poly_assigned_type
        $staff_assigned = PivotStaffAssigned::where('staff_id',$value)->where('staff_assigned_type',$this->related_class)->where('staff_assigned_id',$this->related_id)->exists();

        if ($staff_assigned){

            $fail('The staff member is already assigned.');
        }


    }


}
