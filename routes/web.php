<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AssignedUserCommController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactDetailController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataImports\DataImportController;
use App\Http\Controllers\DealTicketController;
use App\Http\Controllers\DebtorStandingController;
use App\Http\Controllers\EmailContactDetailController;
use App\Http\Controllers\NumberContactDetailController;
use App\Http\Controllers\PlanningDiaryController;
use App\Http\Controllers\PlanningWeekController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RegularDriverController;
use App\Http\Controllers\RegularVehicleController;
use App\Http\Controllers\RoleModifyController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StaffLinkController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TransactionSpreadSheetController;
use App\Http\Controllers\TransactionSummaryController;
use App\Http\Controllers\TransLinkController;
use App\Http\Controllers\TransportApprovalController;
use App\Http\Controllers\TransportDriverVehicleController;
use App\Http\Controllers\TransporterController;
use App\Http\Controllers\TransportFinanceController;
use App\Http\Controllers\TransportInvoiceController;
use App\Http\Controllers\TransportJobController;
use App\Http\Controllers\TransportLoadController;
use App\Http\Controllers\TransportStatusController;
use App\Http\Controllers\TransportTransactionController;
use App\Models\Customer;
use App\Models\RegularDriver;
use App\Models\TransportDriverVehicle;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

/*Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});*/


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/test', function () {
        return Inertia::render('Test');
    })->name('test');

    Route::get('/no_permission', function () {
        return Inertia::render('NoPermission');
    })->name('no_permission');


    //Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

    //Data import route

    Route::get('/import', [DataImportController::class, 'index'])->middleware('auth')->name('import.index');
    Route::post('/import', [DataImportController::class, 'import'])->middleware('auth')->name('import.process');

    //Modify User roles

    Route::post('/roles/modify', [RoleModifyController::class, 'store'])->middleware('auth')->name('roles.modify.store');
    Route::put('/roles/modify', [RoleModifyController::class, 'destroy'])->middleware('auth')->name('roles.modify.destroy');


    //Planning

    //Diary

    Route::get('/planning/diary', [PlanningDiaryController::class, 'index'])->middleware('auth')->name('planning.diary');

    //Weekly

    Route::get('/planning/weekly', [PlanningWeekController::class, 'index'])->middleware('auth')->name('planning.week');

    //Transport Transaction (detail)

    Route::resource('transport_transaction', TransportTransactionController::class)->middleware('auth')
        ->only(['index', 'show', 'update', 'destroy', 'store']);

    //Transaction Summary

    Route::resource('transaction_summary', TransactionSummaryController::class)->middleware('auth')
        ->only(['index']);

    //Transaction Spreadhseet

    Route::resource('transaction_spreadsheet', TransactionSpreadSheetController::class)->middleware('auth')
        ->only(['index']);

    //Transport Trans SlideOver Props

    Route::get('/props/trade_slide_over', [TransportTransactionController::class, 'getProps'])->middleware('auth')->name('props.trade_slide_over');

    //Transport Trans SlideOver Props

    Route::get('/props/vehicle_slide_over', [RegularVehicleController::class, 'getProps'])->middleware('auth')->name('props.vehicle_slide_over');

    //Transport PC trans Modal Props

    Route::get('/props/contract_link_modal', [TransportTransactionController::class, 'getPcs'])->middleware('auth')->name('props.contract_link_modal');


    //Transport Load

    Route::resource('transport_load', TransportLoadController::class)->middleware('auth')
        ->only(['update']);

    //TransLink

    Route::resource('trans_link', TransLinkController::class)->middleware('auth')
        ->only(['store']);

    //Trans Approval
   // Route::resource('trans_approval', TransportApprovalController::class)->middleware('auth')->only(['store']);
    Route::post('trans_approval/approve', [TransportApprovalController::class, 'approve'])->middleware('auth')->name('trans_approval.approve');
    Route::post('trans_approval/activate', [TransportApprovalController::class, 'activate'])->middleware('auth')->name('trans_approval.activate');


    //Deal Ticket

    Route::resource('deal_ticket', DealTicketController::class)->middleware('auth')
        ->only(['update']);

    //Transport Load

    Route::resource('transport_job', TransportJobController::class)->middleware('auth')
        ->only(['update']);

    //Transport Finance

    Route::resource('transport_finance', TransportFinanceController::class)->middleware('auth')
        ->only(['update']);

    //Transport Driver Vehicle

    Route::resource('transport_driver_vehicle', TransportDriverVehicleController::class)->middleware('auth')
        ->only(['destroy', 'update', 'store']);

    //Assigned User Comm

    Route::resource('assigned_user_comm', AssignedUserCommController::class)->middleware('auth')
        ->only(['destroy', 'update', 'store']);

    //Transport status

    Route::resource('transport_status', TransportStatusController::class)->middleware('auth')
        ->only(['destroy', 'update', 'store']);

    //Transport invoice

    Route::resource('transport_invoice', TransportInvoiceController::class)->middleware('auth')
        ->only(['update']);


    //Staff

    Route::resource('staff', StaffController::class)->middleware('auth')
        ->only(['index', 'show','store','update']);

    //Staff Link

    Route::resource('staff_link', StaffLinkController::class)->middleware('auth')
        ->only(['store', 'update']);



/*    Route::post('/staff/link', [StaffController::class, 'link'])->middleware('auth')->name('staff.link');

    Route::put('/staff/link/remove', [StaffController::class, 'removeLink'])->middleware('auth')->name('staff.link.remove');*/


    //Customer

    Route::resource('customer', CustomerController::class)->middleware('auth')
        ->only(['index', 'show', 'update', 'destroy','store']);

    //Debtor standing (customer pivot)

    Route::get('/debtor/standing', [DebtorStandingController::class, 'index'])->middleware('auth')->name('debtor.index');
    Route::get('/debtor/calculating', [DebtorStandingController::class, 'calculateDebtors'])->middleware('auth')->name('debtor.calculating');

    //Supplier

    Route::resource('supplier', SupplierController::class)->middleware('auth')
        ->only(['index', 'show', 'update', 'destroy','store']);

    //Products

    Route::resource('product', ProductController::class)->middleware('auth')
        ->only(['index', 'show', 'update','store']);

    //Driver

    Route::resource('regular_driver', RegularDriverController::class)->middleware('auth')
        ->only(['index', 'show','update','store']);

    //Vehicle

    Route::resource('regular_vehicle', RegularVehicleController::class)->middleware('auth')
        ->only(['index', 'show','update','store']);


    //Transporter

    Route::resource('transporter', TransporterController::class)->middleware('auth')
        ->only(['index', 'show', 'update', 'destroy','store']);

    //Address

    Route::resource('address', AddressController::class)->middleware('auth')
        ->only(['store', 'update', 'destroy']);



    //Contact

    Route::resource('contact', ContactController::class)->middleware('auth')
        ->only(['index', 'store', 'update', 'show']);

    //Number detail

    Route::resource('number_contact_detail', NumberContactDetailController::class)->middleware('auth')
        ->only(['store', 'update', 'show']);

    //Email detail
    Route::resource('email_contact_detail', EmailContactDetailController::class)->middleware('auth')
        ->only(['store', 'update', 'show']);

    //PDF Reports
    //DealTicket
    Route::get('/pdf_report/deal_ticket_view/{id}', [DealTicketController::class, 'viewPDF'])->middleware('auth')->name('pdf_report.deal_ticket_view');
    Route::post('/pdf_report/deal_ticket_final/', [DealTicketController::class, 'generatePDF'])->middleware('auth')->name('pdf_report.deal_ticket_final');
    Route::get('/pdf_report/deal_ticket_final/download/{file_name}', [DealTicketController::class, 'downloadPDF'])->middleware('auth')->name('pdf_report.deal_ticket_final.download');

    //SalesOrder

    Route::get('/pdf_report/sales_order_view/{id}', [SalesOrderController::class, 'viewPDF'])->middleware('auth')->name('pdf_report.sales_order_view');
    Route::post('/sales_order/activate', [SalesOrderController::class, 'activate'])->middleware('auth')->name('sales_order.activate');
    Route::post('/sales_order/send', [SalesOrderController::class, 'send'])->middleware('auth')->name('sales_order.send');
    Route::post('/sales_order/receive', [SalesOrderController::class, 'receive'])->middleware('auth')->name('sales_order.received');

    //SalesOrder Confirmation
    Route::get('/pdf_report/sales_order_confirmation_view/{id}', [SalesOrderController::class, 'viewConfirmationPDF'])->middleware('auth')->name('pdf_report.sales_order_confirmation_view');


});


