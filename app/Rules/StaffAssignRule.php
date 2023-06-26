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

    //customer id
    public int $id;
    public string $class;

    public function __construct($id,$class)
    {
        $this->id = $id;
        $this->class = $class;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $staff_assigned = PivotStaffAssigned::where('staff_id',$value)->where('staff_assigned_type',$this->class)->where('staff_assigned_id',$this->id)->exists();

        if ($staff_assigned){

            $fail('The staff member is already assigned.');
        }


    }


}
