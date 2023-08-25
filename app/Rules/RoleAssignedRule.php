<?php

namespace App\Rules;

use App\Models\PivotStaffAssigned;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RoleAssignedRule implements ValidationRule
{

    //customer id
    public int $user_id;
    public string $role_name;

    public function __construct($user_id,$role_name)
    {
        $this->user_id = $user_id;
        $this->role_name = $role_name;
    }


    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //$staff_assigned = PivotStaffAssigned::where('staff_id',$value)->where('staff_assigned_type',$this->class)->where('staff_assigned_id',$this->id)->exists();

        $user = User::where('id','=',$this->user_id)->first();

        if ($user != null){

            $roles = $user->getRoleNames();

            $has_role = $roles->contains($this->role_name);

            if ($has_role){

                $fail('The staff member is already assigned.');
            }

        } else{

            $fail('User does not exist.');
        }

    }
}
