<?php

use App\Http\Controllers\Api\V1\ShopeepayController;
use App\Http\Controllers\Api\V1\GopayController;
use App\Http\Controllers\Api\V1\QrisController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('order/shopeePay', [ShopeepayController::class, 'shopeePay']);
Route::post('order/goPay', [GopayController::class, 'goPay']);
Route::post('order/Qris', [QrisController::class, 'Qris']);

Route::get('transaksi', function () {
    $client = Http::get('http://tubeseaiproject.000webhostapp.com/api/Transaction')->body();

    echo ($client);
});
