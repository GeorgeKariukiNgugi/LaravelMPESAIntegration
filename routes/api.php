<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('v1/register/url', 'PaymentsC2BController@mpesaRegisterUrls');
Route::post('v1/validation', 'PaymentsC2BController@mpesaValidation');
Route::post('v1/transaction/confirmation', 'PaymentsC2BController@mpesaConfirmation');
Route::post('makePayment','PaymentsC2BController@makePayment');
