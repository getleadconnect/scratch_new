<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CampaignGiftController;

use App\Http\Controllers\Admin\CommonController;


Route::group(['prefix'=>'admin','as'=>'admin.','middleware' => 'authware'], function()
{

Route::controller(DashboardController::class)->group(function() {
	Route::get('/dashboard', 'index')->name('dashboard');
});

Route::controller(CommonController::class)->group(function() {
	Route::get('/get-user-offers/{id}', 'getUserCampaigns')->name('get-user-offers');
});

Route::controller(UserController::class)->group(function() {
	Route::get('/users-list', 'index')->name('users-list');
	Route::post('/save-user', 'store')->name('save-user');
	Route::get('/view-users', 'viewUsers')->name('view-users');
	Route::get('/delete-user/{id}', 'destroy')->name('delete-user');
	Route::get('/edit-user/{id}', 'edit')->name('edit-user');
	Route::post('/update-user', 'updateUser')->name('update-user');
	Route::get('/act-deact-user/{op}/{id}', 'activateDeactivate')->name('act-deact-user');
	
	Route::get('/user-profile/{id}', 'userProfile')->name('user-profile');
	Route::get('/view-scratch-history/{id}', 'viewScratchHistory')->name('view-scratch-history');
	Route::post('/add-scratch-count', 'addScratchCount')->name('add-scratch-count');
	//Route::get('/delete-scratch-count/{id}/{uid}', 'deleteScratchCount')->name('delete-scratch-count');
	Route::get('/delete-scratch-count', 'deleteScratchCount')->name('delete-scratch-count');
	Route::post('/add-subscription', 'addSubscription')->name('add-subscription');
		
});

Route::controller(CampaignGiftController::class)->group(function() {
	Route::get('/scratch-gifts-list', 'index')->name('scratch-gifts-list');
	Route::get('/view-campaign-gifts-list', 'viewCampaignGiftListings')->name('view-campaign-gifts-list');
	Route::get('/deleted-gifts-list', 'deletedGiftsList')->name('deleted-gifts-list');
	Route::get('/view-deleted-gifts-list', 'viewDeletedGiftListings')->name('view-deleted-gifts-list');
});


});
