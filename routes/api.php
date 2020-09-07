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

// ! THIS ARE THE NEW ROUTES FROM THE DOCUMENTATION. 

Route::post('/accessToken','newImplementation@generateAccessTokens');
Route::post('/validationURL','newImplementation@validationMethod');
Route::post('/confirmationURL','newImplementation@confirmationMethod');
Route::post('/registerURLS','newImplementation@registerURLS');
Route::post('/simulateTransaction','newImplementation@simulateTransaction');
Route::post('/stkPush', 'newImplementation@customerMpesaSTKPush');






