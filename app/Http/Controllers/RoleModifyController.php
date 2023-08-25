<?php

namespace App\Http\Controllers;

use App\Models\Staff;
use App\Models\User;
use App\Rules\RoleAssignedRule;
use App\Rules\StaffAssignRule;
use Illuminate\Http\Request;

class RoleModifyController extends Controller
{
    //

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'user_id' => ['required','integer','exists:users,id'],
            'role_name' => ['required','string',new RoleAssignedRule($request->user_id,$request->role_name)],
        ]);

        $user = User::where('id','=',$request->user_id)->first();

        $user?->assignRole($request->role_name);


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {

        $request->validate([
            'user_id' => ['required','integer','exists:users,id'],
            'role_name' => ['required','string'],
        ]);

        $user = User::where('id','=',$request->user_id)->first();

        $user?->removeRole($request->role_name);

    }
}
