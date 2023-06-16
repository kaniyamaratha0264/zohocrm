<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZohoController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::get('/', [ZohoController::class, 'index'])->name('login.zoho');

Route::get('zoho/callback', [ZohoController::class, 'callback']);
Route::group(['prefix' => 'zoho'], function () {
    Route::get('callback', [ZohoController::class, 'callback'])->name('zoho.callback');
    Route::get('create-form', [ZohoController::class, 'createAccount'])->name('zoho.createForm');
    Route::get('check-connection', [ZohoController::class, 'checkConnection'])->name('zoho.check-connection');

    Route::group(['middleware' => ['zoho.token']], function () {
        Route::post('submit-account-form', [ZohoController::class, 'submitAccountForm'])->name('zoho.submit-account-form');
        Route::post('submit-deal-form', [ZohoController::class, 'submitDealForm'])->name('zoho.submit-deal-form');
        Route::get('get-accounts', [ZohoController::class, 'getAccounts'])->name('zoho.get-accounts');
    });
});