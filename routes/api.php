<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\GetleadScratchController;
use App\Http\Controllers\Api\HyundaiScratchController;

/*Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
*/
//api-prefix = 'api/v1'

Route::controller(GetleadScratchController::class)->group(function() {
	Route::post('user-login', 'login')->name('user-login');
});

Route::controller(HyundaiScratchController::class)->group(function() {
	Route::post('hyundai-login', 'login')->name('hyundai-login');
});


Route::middleware('auth:sanctum')->group( function () {
    	
	Route::controller(GetleadScratchController::class)->group(function()
	{
		
		Route::post('get-offers', 'getOffers')->name('get-offers');
		Route::post('send-otp', 'sendOtp')->name('send-otp');
		Route::post('verify-otp', 'verifyOtp')->name('verify-otp');
		Route::post('scratch-type', 'scratchType')->name('scratch-type');
		Route::post('get-branches', 'getBranches')->name('get-branches');
		Route::post('scratch-customer', 'scratchCustomer')->name('scratch-cuatomer');
		Route::post('scratch-card', 'getScratch')->name('scratch-card');
	});

	Route::controller(HyundaiScratchController::class)->group(function()
	{
		Route::post('hyundai-offers', 'Offers')->name('hyundai-get-offers');
		Route::post('hyundai-send-otp', 'sendOtp')->name('hyundai-send-otp');
		Route::post('hyundai-verify-otp', 'verifyOtp')->name('hyundai-verify-otp');
		Route::post('hyundai-scratch-type', 'scratchType')->name('hyundai-scratch-type');
		Route::post('hyundai-branches', 'getBranches')->name('hyundai-branches');
		Route::post('hyundai-scratch-customer', 'scratchCustomer')->name('hyundai-scratch-customer');
		Route::post('hyundai-scratch-card', 'getScratch')->name('hyundai-scratch-card');
		Route::post('hyundai-slide-images', 'getSlideImages')->name('hyundai-slide-images');
	});


});

