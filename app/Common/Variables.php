<?php

namespace App\Common;

use App\Models\Settings;
use App\Models\BillingSubscription;

use App\Country;
use App\Models\User;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Auth;

class Variables
{
    const ROLE_SUPERADMIN = 0;
	const ROLE_ADMIN = 1;
    const USER = 2;
    const SHOPS = 3;

    const ACTIVE = 1;
    const DEACTIVE = 0;

    const SERVICE_GLS = 'GL Scratch';

    const DEF_COUNTRY_INDIA = 29;


    const ALERT_BOX_ER_01 = 'Wrong Username or password.';
    const ALERT_BOX_ER_02 = 'Sorry! Insufficient balance!';
    const ALERT_BOX_ER_03 = 'Sorry! Insufficient balance! Trackid=Sorry! Insufficient balance!';
    const ALERT_BOX_ER_04 = 'Sorry, No valid numbers found!';
    const ALERT_BOX_ER_05 = 'Sorry, No valid numbers found! Trackid=Sorry, No valid numbers found!';
    const ALERT_BOX_ER_06 = 'Sorry, senderid not valid';
    const ALERT_BOX_ER_07 = 'this msg_id is not valid';
    const ALERT_BOX_ER_08 = 'No user found!';

    const  ALERT_BOX_SENT_MESSAGE = 'Your message has been sent';


    /**
     * @return mixed
     *
     */
    public static function getCountryList()
    {
        $countries = Country::select('id', 'name')->get();
        return $countries;
    }

	
	public static function getScratchBypass($user_id)
    {
        $settings = Settings::where('vchr_settings_type','scratch_otp_enabled')->where('fk_int_user_id',$user_id)
        ->pluck('vchr_settings_value')->first();
		return $settings;
    } 

    /*public static function checkEnableSettings($label,$vendor_id =null)
    {
	 
        $settings = Settings::where('vchr_settings_type',$label)->whereJsonContains('vchr_settings_value',$vendor_id ?? User::getVendorId())->select('pk_int_settings_id','vchr_settings_value')->first();
        if($settings){
            return true;
        }else{
            return false;
        }
    }

    public static function checkCurrencyEnableSetting($label)
    {
        $settings = Settings::where('vchr_settings_type',$label)->where('fk_int_user_id',User::getVendorId())->select('pk_int_settings_id','vchr_settings_value')->first();
        if($settings){
            return $settings->vchr_settings_value;
        }
        else
        {
            return null;
        }
    }*/
	
	
	
}
