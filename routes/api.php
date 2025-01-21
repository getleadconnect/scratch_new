<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\GetleadScratchController;

//Route::get('/user', function (Request $request) {
  //  return $request->user();
//})->middleware('auth:sanctum');
   
Route::controller(GetleadScratchController::class)->group(function(){
    Route::post('login', 'login')->name('login');
	
});

Route::middleware('auth:sanctum')->group( function () {
    	
	Route::controller(GetleadScratchController::class)->group(function(){
	Route::post('get-offers', 'getOffers')->name('get-offers');
	Route::post('send-otp', 'sendOtp')->name('send-otp');
	Route::post('verify-otp', 'verifyOtp')->name('verify-otp');
	Route::post('get-branches', 'getBranches')->name('get-branches');
	Route::post('scratch-customer', 'scratchCustomer')->name('scratch-cuatomer');
	
});
		
	
});