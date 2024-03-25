<?php

namespace App\Http\Controllers\DataImports;

use App\Http\Controllers\Controller;
use App\Imports\CustomerImport;
use App\Imports\ImportCustomers;
use App\Imports\ImportProducts;
use App\Imports\NewTransaction;
use App\Imports\OldTransactionImport;
use App\Imports\SupplierImport;
use App\Imports\TransporterImport;
use App\Models\Customer;
use App\Models\OldTransaction;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\SystemPlayer;
use App\Models\Transporter;
use App\Models\TransportTransaction;
use Barryvdh\Debugbar\Facades\Debugbar;
use Exception;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

ini_set('memory_limit', '-1');
ini_set('max_execution_time', 10000);

class DataImportController extends Controller
{
    //

    public function index(): \Inertia\Response|\Inertia\ResponseFactory
    {


        return inertia(
            'DataImport/Index',
            [
                'Imports' => 'Import Data',

            ]
        );

    }

    public function import(Request $request): \Illuminate\Http\RedirectResponse
    {

        $file_name = $request->file('file')->getClientOriginalName();


        switch ($file_name) {
            case "Silver Customer Import.xlsx":

                $count_before = Customer::all()->count();

                Excel::import(new CustomerImport(),
                    $request->file('file')->store('files'));

                $count_after = Customer::all()->count();

                $message = "Customer Count before: ".$count_before." count after: ".$count_after;

                break;
            case "Silver Supplier Import.xlsx":

                $count_before = Supplier::all()->count();

                Excel::import(new SupplierImport(),
                    $request->file('file')->store('files'));

                $count_after = Supplier::all()->count();

                $message = "Supplier Count before: ".$count_before." count after: ".$count_after;

                break;

            case "Silver Transporter Import.xlsx":

                $count_before = Transporter::all()->count();

                Excel::import(new TransporterImport(),
                    $request->file('file')->store('files'));

                $count_after = Transporter::all()->count();

                $message = "Transporter Count before: ".$count_before." count after: ".$count_after;

                break;

            case "Silver Transaction Import.xlsx":

                $count_before = OldTransaction::all()->count();

                Excel::import(new OldTransactionImport(),
                    $request->file('file')->store('files'));

                $count_after = OldTransaction::all()->count();

                $message = "Transactions Count before: ".$count_before." count after: ".$count_after;

                break;

            case "20230606 Silvergro Products.csv":

                $count_before = Product::all()->count();

                Excel::import(new ImportProducts(),
                    $request->file('file')->store('files'));

                $count_after = Product::all()->count();

                $message = "Products Count before: ".$count_before." products after: ".$count_after;

                break;
            case "raw trans 1.csv":


                $count_before = TransportTransaction::all()->count();

                Excel::import(new NewTransaction(),
                    $request->file('file')->store('files'));

                $count_after = TransportTransaction::all()->count();

                $message = "New trans Count before: ".$count_before." trans after: ".$count_after;

                break;

            case "raw trans 2.csv":

                $count_before = TransportTransaction::all()->count();

                Excel::import(new NewTransaction(),
                    $request->file('file')->store('files'));

                $count_after = TransportTransaction::all()->count();

                $message = "New trans Count before: ".$count_before." trans after: ".$count_after;

                break;

            case "raw trans 3.csv":

                $count_before = TransportTransaction::all()->count();

                Excel::import(new NewTransaction(),
                    $request->file('file')->store('files'));

                $count_after = TransportTransaction::all()->count();

                $message = "New trans Count before: ".$count_before." trans after: ".$count_after;

                break;

            default:
                $message = "No acceptable file name found.";
        }



        $request->session()->flash('flash.bannerStyle', 'success');
        $request->session()->flash('flash.banner', $message);

        return redirect()->back();

    }
}
