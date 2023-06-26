<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\AssignedUserCommController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ContactDetailController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataImports\DataImportController;
use App\Http\Controllers\EmailContactDetailController;
use App\Http\Controllers\NumberContactDetailController;
use App\Http\Controllers\PlanningDiaryController;
use App\Http\Controllers\PlanningWeekController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TransportDriverVehicleController;
use App\Http\Controllers\TransportFinanceController;
use App\Http\Controllers\TransportJobController;
use App\Http\Controllers\TransportLoadController;
use App\Http\Controllers\TransportStatusController;
use App\Http\Controllers\TransportTransactionController;
use App\Models\Customer;
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
});


//Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

//Data import route

Route::get('/import', [DataImportController::class, 'index'])->middleware('auth')->name('import.index');
Route::post('/import', [DataImportController::class, 'import'])->middleware('auth')->name('import.process');

//Planning

//Diary

Route::get('/planning/diary', [PlanningDiaryController::class, 'index'])->middleware('auth')->name('planning.diary');

//Weekly

Route::get('/planning/weekly', [PlanningWeekController::class, 'index'])->middleware('auth')->name('planning.week');

//Transport Transaction

Route::resource('transport_transaction', TransportTransactionController::class)->middleware('auth')
    ->only(['index', 'show','update','destroy']);

//Transport Load

Route::resource('transport_load', TransportLoadController::class)->middleware('auth')
    ->only([ 'update']);

//Transport Load

Route::resource('transport_job', TransportJobController::class)->middleware('auth')
    ->only([ 'update']);

//Transport Finance

Route::resource('transport_finance', TransportFinanceController::class)->middleware('auth')
    ->only([ 'update']);

//Transport Driver Vehicle

Route::resource('transport_driver_vehicle', TransportDriverVehicleController::class)->middleware('auth')
    ->only([ 'destroy','update','store']);

//Assigned User Comm

Route::resource('assigned_user_comm', AssignedUserCommController::class)->middleware('auth')
    ->only([ 'destroy','update','store']);

//Transport status

Route::resource('transport_status', TransportStatusController::class)->middleware('auth')
    ->only([ 'destroy','update','store']);


//Customer

Route::resource('customer', CustomerController::class)->middleware('auth')
    ->only(['index', 'show','update','destroy']);

//Address

Route::resource('address', AddressController::class)->middleware('auth')
    ->only(['store','update','destroy']);

//Staff

Route::resource('staff', StaffController::class)->middleware('auth')
    ->only(['store','update']);

//Contact

Route::resource('contact', ContactController::class)->middleware('auth')
    ->only(['store','update','show']);

//Number detail

Route::resource('number_contact_detail', NumberContactDetailController::class)->middleware('auth')
    ->only(['store','update','show']);

//Email detail
Route::resource('email_contact_detail', EmailContactDetailController::class)->middleware('auth')
    ->only(['store','update','show']);
