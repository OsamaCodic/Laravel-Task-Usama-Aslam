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
Route::post('register', [\App\Http\Controllers\Api\AuthController::class,'register']);
Route::post('login', [\App\Http\Controllers\Api\AuthController::class,'login']);


/**********  Verify via link **********/
// Route::get('email/verify/{id}', [\App\Http\Controllers\Api\AuthController::class,'verify'])->name('verification.verify');
// Route::get('email/resend', [\App\Http\Controllers\Api\AuthController::class,'resend'])->name('verification.resend');


/**********  Verify via Code **********/
//submit email & code fields for verfication
Route::post('email/verify_code', [\App\Http\Controllers\Api\AuthController::class,'verifyCode']);
//If user accidently delete email
Route::post('email/resend/verify_code', [\App\Http\Controllers\Api\AuthController::class,'resendVerificationCode']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return auth()->user();
});

Route::group(['middleware' => 'auth:api','namespace' => 'Api'], function () {
    Route::middleware([isVerified::class])->group(function(){
        //Only verified email accounts can access these routes
        Route::resource('todos', UserTodoController::class);
        Route::post('logout', 'AuthController@logout');
    });
    Route::post('refresh', 'AuthController@refresh');
});