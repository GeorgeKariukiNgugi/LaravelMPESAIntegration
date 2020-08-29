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

Route::post('v1/access/token', 'PaymentsC2BController@generateAccessToken');
Route::post('v1/hlab/stk/push', 'PaymentsC2BController@customerMpesaSTKPush');
Route::post('v1/hlab/stk/pushCallBack', 'PaymentsC2BController@callBackForTheSTKPush');




