<?php

use App\Http\Controllers\ExpertController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BaseController;


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
##################################### Auth Api #########################################
Route::post('expert-register', [AuthController::class, 'exp_register']);
Route::post('user-register', [AuthController::class, 'usr_register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
#########################################################################################
Route::middleware('auth:api')->group(function (){
    Route::get('experts/{id}',[ExpertController::class,'index']);
    Route::get('expert/{id}',[ExpertController::class,'show']);
    Route::post('search',[ExpertController::class,'search']);

});
//Route::get('users',[AuthController::class,'index'])->middleware('auth:api');


