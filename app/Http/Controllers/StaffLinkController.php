<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Staff;
use App\Models\Supplier;
use App\Rules\StaffAssignRule;
use Illuminate\Http\Request;

class StaffLinkController extends Controller
{

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {


          $request->validate([
              'related_id' => ['required','integer'],
              'staff_id' => ['required','integer',new StaffAssignRule($request->related_id,$request->related_class)],

        ]);

        $staff_id = $request->staff_id;
        $related_class = $request->related_class;
        $staff = Staff::find($staff_id);

        $related_entity = match ($related_class) {
            'App\Models\Customer' => Customer::find($request->related_id),
            'App\Models\Supplier' => Supplier::find($request->related_id),
        };



        if ($staff != null && $related_entity != null){

            $related_entity->staff()->attach($staff);
            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Staff added');
            return redirect()->back();
        }


        $request->session()->flash('flash.bannerStyle', 'danger');
        $request->session()->flash('flash.banner', 'Staff not added');

        return redirect()->back();

    }

    public function update(Request $request): \Illuminate\Http\RedirectResponse
    {

        $staff = Staff::find($request->staff_id);
        $related_class = $request->related_class;

        $related_entity = match ($related_class) {
            'App\Models\Customer' => Customer::find($request->related_id),
            'App\Models\Supplier' => Supplier::find($request->related_id),
        };

        $res = $related_entity->staff()->detach($staff);

        if ($res){

            $request->session()->flash('flash.bannerStyle', 'success');
            $request->session()->flash('flash.banner', 'Staff unlinked');
            return redirect()->back();
        }

        $request->session()->flash('flash.bannerStyle', 'danger');
        $request->session()->flash('flash.banner', 'Staff not unlinked');

        return redirect()->back();

    }
}
