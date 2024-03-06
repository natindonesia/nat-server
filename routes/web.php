<?php

use App\Http\Controllers\ChangePasswordController;
use App\Http\Controllers\DataApiController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SensorDataController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\WaterpoolController;
use Illuminate\Support\Facades\Route;

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

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', function () {
        return redirect('main-dashboard');
    });

    Route::redirect('/dashboard-default', '/main-dashboard');

    Route::get('/detail', [SensorDataController::class, 'index'])->name('detail');
    Route::get('/main-dashboard', [\App\Http\Controllers\Pool\MainDashboardController::class, 'index']);
    Route::get('/fetch-latest-data', [SensorDataController::class, 'fetchLatestData'])->name('fetch.latest.data');


    Route::get('/logout', [SessionController::class, 'destroy']);
    Route::view('/login', 'dashboards/default')->name('sign-up');


    // show data kolam from db all
    Route::get('/sensor-data', [SensorDataController::class, 'index'])->name('sensor-data');

    // fetch api insert to db
    Route::get('/data-pool', [DataApiController::class, 'index']);

    Route::get('/tabel-data', [WaterpoolController::class, 'index'])->name('waterpool-index');

    Route::get('/app-settings', [\App\Http\Controllers\AppSettingsController::class, 'index'])->name('app-settings');
    Route::post('/app-settings', [\App\Http\Controllers\AppSettingsController::class, 'store'])->name('app-settings');

    Route::get('/settings-parameter', [\App\Http\Controllers\ParameterController::class, 'index'])->name('settings.parameter');
    Route::post('/settings-parameter', [\App\Http\Controllers\ParameterController::class, 'store'])->name('settings.parameter');
    Route::get('/settings-parameter-livewire', \App\Livewire\SettingsParameter::class)->name('settings.parameter.livewire');
    Route::get('/detail/export', [SensorDataController::class, 'export'])->name('detailed-dashboard.export');
    Route::get('/score-simulation', [\App\Http\Controllers\ScoreSimulationController::class, 'index'])->name('score-simulation');
});

Route::group(['middleware' => 'guest'], function () {
    Route::get('/register', [RegisterController::class, 'create']);
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [SessionController::class, 'create'])->name('login');
    Route::post('/login', [SessionController::class, 'store']);
    Route::get('/login/forgot-password', [ChangePasswordController::class, 'create']);
    Route::post('/forgot-password', [ChangePasswordController::class, 'sendEmail']);
    Route::get('/reset-password/{token}', [ChangePasswordController::class, 'resetPass'])->name('password.reset');
    Route::post('/reset-password', [ChangePasswordController::class, 'changePassword'])->name('password.update');
    Route::get('/', function () {
        return redirect('/login');
    });
});
