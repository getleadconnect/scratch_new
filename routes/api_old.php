<?php

use App\Http\Controllers\Api\GetleadScratchController;
use App\Http\Controllers\Api\HyundaiScratchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\User;
use GuzzleHttp\Client;

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

/* ----------- Gl Scratch api for custom --------- */

Route::controller(GetleadScratchController::class)->group(function() {
    Route::post('login', 'login');
    Route::post('offers', 'offers'); 
    Route::post('type', 'type');
    Route::post('send-otp', 'sendOtp');
    Route::post('verify-otp', 'verifyOtp');
    Route::post('scratch-customers', 'scratchCustomer');
    Route::post('scratch-branches', 'scratchBranches');    
});


// ---------end website contact api's------------------- //
Route::get('/gl-scratch-login', 'User\ScratchApiController@loginApi');
Route::get('/gl-scratch-offers', 'User\ScratchApiController@offersListingApi');
Route::get('/gl-scratch-offer', 'User\ScratchApiController@offerApi');
Route::get('/gl-scratch-branches', 'User\ScratchApiController@branchApi');
Route::get('/gl-scratch-customers', 'User\ScratchApiController@addCustomerApi');
Route::post('/gl-scratch-customers', 'User\ScratchApiController@addCustomerApi');
Route::get('/gl-scratch-ads', 'User\ScratchApiController@adApi');
Route::get('/gl-scratch-footer', 'User\ScratchApiController@footerApi');
Route::get('/gl-scratch-form-customisation', 'User\ScratchApiController@formCustomisation');
Route::get('/scratch-model', 'User\ScratchApiController@scratchModel');
Route::get('/scratch-type', 'User\ScratchApiController@typeApi');


/* ----------- Hyundai scratch api for custom --------- */
Route::prefix('hyundai-scratch')->controller(HyundaiScratchController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('offers', 'offers'); 
    Route::post('type', 'type');
    Route::post('send-otp', 'sendOtp');
    Route::post('verify-otp', 'verifyOtp');
    Route::post('scratch-customers', 'scratchCustomer');
    Route::post('scratch-branches', 'scratchBranches');    
});



//require 'api_agentapp.php';
//require 'api_userapp.php';
