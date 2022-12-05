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
Route::get('test_code_deployment', function ()
{
    return "yes code is deploying";
});

Route::post('register', [\App\Http\Controllers\Api\AuthController::class,'register']);
Route::post('login', [\App\Http\Controllers\Api\AuthController::class,'login']);


/**********  Verify via link **********/
// Route::get('email/verify/{id}', [\App\Http\Controllers\Api\AuthController::class,'verify'])->name('verification.verify');
// Route::get('email/resend', [\App\Http\Controllers\Api\AuthController::class,'resend'])->name('verification.resend');


/**********  Verify via Code **********/
//submit email & code fields for verfication
Route::post('email/verify_code', [\App\Http\Controllers\Api\AuthController::class,'verifyCode']);


//If user accidently delete email
//send email only
Route::post('email/resend/verify_code', [\App\Http\Controllers\Api\AuthController::class,'resendVerificationCode']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return auth()->user();
});

Route::group(['middleware' => 'auth:sanctum','namespace' => 'Api'], function () {
    Route::middleware([isVerified::class])->group(function(){
        //Only verified email accounts can access these routes
        
        Route::resource('todos', UserTodoController::class);
        Route::post('logout', 'AuthController@logout');

        /**for todo crud use api routes like this
            for read
            http://127.0.0.1:8000/api/todos (method: GET)
            
            for create
            http://127.0.0.1:8000/api/todos (method: POST)
            
            for update
            http://127.0.0.1:8000/api/todos (method: POST) // pass id input

            for show
            http://127.0.0.1:8000/api/todos/3 (method: GET)

            for delete
            http://127.0.0.1:8000/api/todos/3 (method: DELETE)
        **/
    });
});