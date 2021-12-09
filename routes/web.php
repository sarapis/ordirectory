<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SrvController;

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

Route::get('/', [SearchController::class, 'index'])->name('index');

Route::get('/services', [SearchController::class, 'services'])->name('services');
Route::get('/services.csv', [SearchController::class, 'servicescsv'])->name('servicescsv');
Route::get('/services.pdf', [SearchController::class, 'servicespdf'])->name('servicespdf');

Route::get('/service', function () { return back(); });
Route::get('/service.csv/{id}', [SearchController::class, 'servicecsv'])->name('servicecsv');
Route::get('/service.pdf/{id}', [SearchController::class, 'servicepdf'])->name('servicepdf');
Route::get('/service/{id}', [SearchController::class, 'service'])->name('service');

Route::get('/organization', [SearchController::class, 'organization'])->name('organization');
Route::get('/organization.csv', [SearchController::class, 'organizationcsv'])->name('organizationcsv');
Route::get('/organization.pdf', [SearchController::class, 'organizationpdf'])->name('organizationpdf');

Route::get('/bigmap', [SearchController::class, 'bigmap'])->name('bigmap');


###################### cron ################################################################################

Route::get('/cron/updateAutocompleteFiles', [SrvController::class, 'autocompleteFiles'])->name('cronAutocomplete');
