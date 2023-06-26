<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SystemPlayer;
use App\Models\TransportTransaction;
use Illuminate\Http\Request;

class TestController extends Controller
{
    //

    public function test(){

        //with('FlexType')

        $customers = Customer::with('staff')->find(8);

        dd($customers);


    }
}
