<?php

use App\Http\Controllers\AppointmentController;
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
Route::get('specializations',[AuthController::class,'specials']);
Route::post('expert-register', [AuthController::class, 'exp_register']);
Route::post('user-register', [AuthController::class, 'usr_register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
#########################################################################################
Route::middleware('auth:api')->group(function (){
    Route::get('experts/{id}',[ExpertController::class,'index']);
    Route::get('expert/{id}',[ExpertController::class,'show']);
    Route::post('search',[ExpertController::class,'search']);
    Route::post('slots',[ExpertController::class,'slots']);
    ############################ Appointment Routes #############################
    Route::post('appointment-store',[AppointmentController::class,'appointment_store']);
    Route::get('user-appointments',[AppointmentController::class,'usr_appoints']);
    Route::get('expert-appointments',[AppointmentController::class,'exp_appoints']);
    Route::get('user-upcoming-appointments',[AppointmentController::class,'usr_upcoming_appoints']);
    Route::get('expert-upcoming-appointments',[AppointmentController::class,'exp_upcoming_appoints']);
    Route::get('cancel-appointment/{id}',[AppointmentController::class,'cancel_appoint']);
    Route::get('confirm-appointment/{id}',[AppointmentController::class,'confirm_appoint']);
    Route::get('today-appointments',[AppointmentController::class,'today_appoints']);
    #############################################################################
});


