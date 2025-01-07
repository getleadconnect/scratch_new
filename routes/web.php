<?php

require __DIR__ . '/admin.php';

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\CampaignController;
use App\Http\Controllers\User\CampaignDetailController;
use App\Http\Controllers\User\CampaignGiftController;
use App\Http\Controllers\User\ScratchHistoryController;
use App\Http\Controllers\User\GlShortLinksController;
use App\Http\Controllers\User\ScratchWebController;
use App\Http\Controllers\User\ScratchAdImageController;
use App\Http\Controllers\User\ScratchBillController;
use App\Http\Controllers\User\ScratchOfferBranchController;
use App\Http\Controllers\User\ScratchStaffUserController;
use App\Http\Controllers\User\UserProfileController;

use App\Http\Controllers\Shortener\ShortenerController;
use App\Http\Controllers\Shortener\GlScratchWebController;
use App\Http\Controllers\Shortener\WhatsappLinkController;


Route::get('/', function () {
    return redirect('login');
});


Route::controller(LoginController::class)->group(function() {
	Route::get('/login', 'showLoginForm')->name('login');
	Route::post('/login', 'userLogin')->name('user-login');
	Route::post('/logout', 'logout')->name('logout');
});


Route::group(['prefix'=>'users','as'=>'users.','middleware' => 'authware'], function()
{

Route::controller(DashboardController::class)->group(function() {
	
	//Route::group(['prefix'=>'users','as'=>'users.'], function()
	//{
		Route::get('/dashboard', 'index')->name('dashboard');
	//});
});
Route::controller(CampaignController::class)->group(function() {
	
	Route::get('/campaigns', 'index')->name('campaigns');
	Route::get('/add-campaign', 'addCampaign')->name('add-campaign');
	Route::post('/save-campaign', 'store')->name('save-campaign');
	Route::get('/edit-campaign/{id}', 'edit')->name('edit-campaign');
	Route::post('/update-campaign', 'update')->name('update-campaign');
	Route::get('/delete-campaign/{id}', 'destroy')->name('delete-campaign');
	Route::get('/view-offers', 'viewOffers')->name('view-offers');
	
	//Route::get('/view-campaign-details/{id}', 'getCampaignDetails')->name('view-campaign-details');
	Route::get('/view-campaign-gifts-listings', 'viewCampaignGiftListings')->name('view-campaign-gifts-listings');
	Route::get('/deleted-gifts-list', 'deleteGiftsList')->name('deleted-gifts-listings');
	Route::get('/view-deleted-gifts-listings', 'viewDeletedGiftListings')->name('view-deleted-gifts-listings');
	
	Route::get('/delete-gift/{id}', 'deleteGift')->name('delete-gift');
	Route::get('/offer-activate-deactivate/{opt}/{id}', 'offerActivateDeactivate')->name('offer-activate-deactivate');
	Route::post('/update-image', 'uploadOfferGiftImage')->name('update-image');

	//Route::get('/add-gifts/{id}', 'addGifts')->name('add-gifts');
	//Route::post('/save-gifts', 'saveGifts')->name('save-gifts');
	
	//Route::get('/check-user-scratch-count', 'checkUserScratchCount')->name('check-user-scratch-count');
});



Route::controller(CampaignDetailController::class)->group(function() {
	
	Route::get('//get-campaign/{id}', 'getCampaign')->name('get-campaign');
	Route::get('/view-campaign-customers', 'getCustomers')->name('view-campaign-customers');
	
	
	/*Route::get('/add-campaign', 'addCampaign')->name('add-campaign');
	Route::post('/save-campaign', 'store')->name('save-campaign');
	Route::get('/edit-campaign/{id}', 'edit')->name('edit-campaign');
	Route::post('/update-campaign', 'update')->name('update-campaign');
	Route::get('/delete-campaign/{id}', 'destroy')->name('delete-campaign');
	Route::get('/view-offers', 'viewOffers')->name('view-offers');
	
	Route::get('/view-campaign-details/{id}', 'getCampaignDetails')->name('view-campaign-details');
	Route::get('/view-campaign-gifts-listings', 'viewCampaignGiftListings')->name('view-campaign-gifts-listings');
	Route::get('/deleted-gifts-list', 'deleteGiftsList')->name('deleted-gifts-listings');
	Route::get('/view-deleted-gifts-listings', 'viewDeletedGiftListings')->name('view-deleted-gifts-listings');
	
	Route::get('/delete-gift/{id}', 'deleteGift')->name('delete-gift');
	Route::get('/offer-activate-deactivate/{opt}/{id}', 'offerActivateDeactivate')->name('offer-activate-deactivate');
	Route::post('/update-image', 'uploadOfferGiftImage')->name('update-image');

	Route::get('/add-gifts/{id}', 'addGifts')->name('add-gifts');
	Route::post('/save-gifts', 'saveGifts')->name('save-gifts');
	
	//Route::get('/check-user-scratch-count', 'checkUserScratchCount')->name('check-user-scratch-count');*/
	
});

Route::controller(CampaignGiftController::class)->group(function() {
	
	Route::get('/add-gifts/{id}', 'addGifts')->name('add-gifts');
	Route::post('/save-gift', 'saveGifts')->name('save-gift');
	Route::get('/view-campaign-gifts', 'viewCampaignGifts')->name('view-campaign-gifts');
	
});


Route::controller(ScratchHistoryController::class)->group(function() {

	Route::get('/redeem-history', 'index')->name('redeem-history');
	Route::get('/view-redeem-history', 'viewRedeemHistory')->name('view-redeem-history');
	Route::post('/export-customers-list', 'exportCustomersList')->name('export-customers-list');
	
	Route::get('scratch-offer-redeem/{id}', 'redeemOffers')->name('scratch-offer-redeem');
	
});


Route::controller(GlShortLinksController::class)->group(function() {

	Route::get('/gl-links', 'index')->name('gl-links');
	Route::get('/add-link', 'addLink')->name('add-link');
	Route::post('/save-link', 'store')->name('save-link');
	Route::get('/view-short-links', 'getShortLinks')->name('view-short-links');
	Route::get('/edit-link/{id}', 'edit')->name('edit-link');
	Route::post('/update-link', 'updateLink')->name('update-link');
	Route::get('/link-activate-deactivate/{opt}/{id}', 'linkActivateDeactivate')->name('link-activate-deactivate');
	Route::get('/web-click-link-history/{id}', 'webClickLinkHistory')->name('web-click-link-history');
	Route::get('/view-click-link-history/{id}', 'viewWebClickLinkHistory')->name('view-click-link-history');
});

Route::controller(ScratchWebController::class)->group(function() {

	Route::get('/scratch-web-customers', 'index')->name('scratch-web-customers');
	Route::get('/get-scratch-web-customers', 'getCustomers')->name('get-scratch-web-customers');
	Route::get('/scratch-web-redeem/{id}', 'redeem')->name('scratch-web-redeem');
	Route::post('/sractch-web-history-download', 'downloadHistory')->name('sractch-web-history-download');	
	//Route::get('/export-web-customers-list/{sdate}/{edate}', 'export_web_customers_list')->name('export-web-customers-list');
	
	Route::post('/export-web-customers-list', 'exportWebCustomersList')->name('export-web-customers-list');
});


Route::controller(ScratchAdImageController::class)->group(function() {

	Route::get('/scratch-ads-image', 'index')->name('scratch-ads-image');
	Route::post('/save-ads-image', 'store')->name('save-ads-image');
	Route::get('/get-scratch-ads-images', 'getScratchAds')->name('get-scratch-ads-images');
	Route::get('/delete-ad-image/{id}', 'destroy')->name('delete-ad-image');
	Route::get('/act-deact-ads-image/{op}/{id}', 'activateDeactivate')->name('act-deact-ads-image');	
	
});


Route::controller(ScratchBillController::class)->group(function() {

	Route::get('/scratch-bills', 'index')->name('scratch-bill');
	Route::post('/save-bill', 'store')->name('save-bill');
	Route::get('/view-bills', 'viewBills')->name('view-bills');
	Route::get('/edit-bill/{id}', 'edit')->name('edit-bill');
	Route::post('/update-bill', 'updateBill')->name('update-bill');
	Route::get('/delete-bill/{id}', 'destroy')->name('delete-bill');
	Route::get('/act-deact-bill/{op}/{id}', 'activateDeactivate')->name('act-deact-bill');	
	
});

Route::controller(ScratchOfferBranchController::class)->group(function() {

	Route::get('/scratch-branches', 'index')->name('scratch-branches');
	Route::post('/save-branch', 'store')->name('save-branch');
	Route::get('/view-branches', 'viewBranches')->name('view-branches');
	Route::get('/edit-branch/{id}', 'edit')->name('edit-branch');
	Route::post('/update-branch', 'updateBranch')->name('update-branch');
	Route::get('/delete-branch/{id}', 'destroy')->name('delete-branch');
	Route::get('/act-deact-branch/{op}/{id}', 'activateDeactivate')->name('act-deact-branch');	
	
});

Route::controller(ScratchStaffUserController::class)->group(function() {

	Route::get('/staff-users', 'index')->name('staff-users');
	Route::post('/save-staff-user', 'store')->name('save-staff-user');
	Route::get('/view-staff-users', 'viewStaffUsers')->name('view-staff-users');
	Route::get('/edit-staff-user/{id}', 'edit')->name('edit-staff-user');
	Route::post('/update-staff-user', 'updateStaffUser')->name('update-staff-user');
	Route::get('/delete-staff-user/{id}', 'destroy')->name('delete-staff-user');
	Route::get('/act-deact-staff-user/{op}/{id}', 'activateDeactivate')->name('act-deact-staff-user');	
	
});

Route::controller(UserProfileController::class)->group(function() {

	Route::get('/user-profile', 'index')->name('user-profile');
	Route::post('/update-user-profile', 'updateUserProfile')->name('update-user-profile');
	Route::post('/update-profile-image', 'uploadProfileImage')->name('update-profile-image');
	Route::post('/change-password', 'changePassword')->name('change-password');
});

});

// Scrtach web routes -------------------------------------------------------------------------------------------->

//Route::domain(env('SHORT_LINK_DOMAIN'))->namespace('Shortener')->group(function () 
//{

    Route::get('scratch-form', 'App\Http\Controllers\Shortener\ShortenerController@form');
    Route::get('scratch/terms', 'App\Http\Controllers\Shortener\ShortenerController@terms')->name('shorter-link.terms');
    Route::get('scratch/thank-you', 'App\Http\Controllers\Shortener\ShortenerController@thankyou')->name('shorter-link.thank-you');
	
    Route::get('{id}/{code}', 'App\Http\Controllers\Shortener\ShortenerController@index')->name('shorter-link');
	Route::get('sc/{code}', 'App\Http\Controllers\Shortener\GlScratchWebController@shortenLink')->name('shorter-link-2');
    Route::post('scr/gl-verify-mobile', 'App\Http\Controllers\Shortener\GlScratchWebController@verifyMobile')->name('/scr/gl-verify-mobile');
    Route::post('scr/gl-verify-otp', 'App\Http\Controllers\Shortener\GlScratchWebController@verifyOtp')->name('gl-verify-otp');
    Route::post('scr/scratch-web-customer', 'App\Http\Controllers\Shortener\GlScratchWebController@scratchCustomer')->name('scratch-web-user');
    Route::post('scr/gl-scratched/{id}/{web_api?}', 'App\Http\Controllers\Shortener\GlScratchWebController@glScratched')->name('scratch-scratched');
    Route::get('w/{code}', 'App\Http\Controllers\Shortener\WhatsappLinkController@index')->name('shorter-wap-link');
    Route::get('wa/{code}', 'App\Http\Controllers\Shortener\GlScratchWebController@gotoApiScratch')->name('go-to-api-scratch');


	Route::get('sc/get-branch-autocomplete/{user_id}', 'App\Http\Controllers\Shortener\GlScratchWebController@getBranchAutocomplete')->name('get-branch-autocomplete');

//});


