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

Route::get('/service', function () { return back(); });
Route::get('/service/{id}', [SearchController::class, 'service'])->name('service');

Route::get('/organization', [SearchController::class, 'organization'])->name('organization');


###################### cron ################################################################################

Route::get('/cron/updateAutocompleteFiles', [SrvController::class, 'autocompleteFiles'])->name('cronAutocomplete');
