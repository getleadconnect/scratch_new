<?php

namespace App\Common;

use App\Models\Designation;
use App\Models\EnquiryFollowup;
use App\Models\LeadType;
use App\Models\Permission;
use App\Models\Settings;
use App\Models\Smsroute;
use App\Models\BillingSubscription;

use App\Country;
use App\Models\User;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Auth;

class Variables
{
    const ROLE_ADMIN = 1;
    const USER = 2;
    const STAFF = 3;

    const ACTIVE = 1;
    const DEACTIVE = 0;

    const SERVICE_GLS = 'GL Scratch';

    const DEF_COUNTRY_INDIA = 29;

    const MESSAGE_API = 1;
    const BALANCE_API = 2;
    const STATUS_API = 3;

    const OTP_PRIORITY = 4;
    const INTL_PRIORITY = 5;


    const ALERT_BOX_ER_01 = 'Wrong Username or password.';
    const ALERT_BOX_ER_02 = 'Sorry! Insufficient balance!';
    const ALERT_BOX_ER_03 = 'Sorry! Insufficient balance! Trackid=Sorry! Insufficient balance!';
    const ALERT_BOX_ER_04 = 'Sorry, No valid numbers found!';
    const ALERT_BOX_ER_05 = 'Sorry, No valid numbers found! Trackid=Sorry, No valid numbers found!';
    const ALERT_BOX_ER_06 = 'Sorry, senderid not valid';
    const ALERT_BOX_ER_07 = 'this msg_id is not valid';
    const ALERT_BOX_ER_08 = 'No user found!';

    const  ALERT_BOX_SENT_MESSAGE = 'Your message has been sent';


    /*const GETLEAD_SERVICES = [self::SERVICE_CRM, self::SERVICE_GLP, self::SERVICE_GLS, self::SERVICE_GLV, self::SERVICE_MISSEDCALL, self::SERVICE_SMS, self::SERVICE_IVR, self::SERVICE_EVENTS, self::SERVICE_SALES, self::SERVICE_CAMPAIGNS];

    const APOLO_USER_ID = 765;
    //const APOLO_USER_ID = 2;
    const BAZANI_USER_ID = 1119;
    const FORTUNE_USER_ID = 1213;
    const NIKSHAN_USER_ID = 1346;
    const EZZAT_USER_ID = 3636;
    

    const BONVOICE_SERVER_2 = [2544,3290,3289,3288,3287,3268,3204,2417,2554];

    const IVR_NUMBER_RESTRICT = [3119];

    const SCARTCH_BYPASS = [3286,3316,1870];
*/
    /**
     * @return mixed
     */
    public function getPermissions()
    {
        return $permissions = Permission::paginate(15);
    }

    /**
     * @param $url
     * @return mixed
     */
    public static function sendData($url)
    {

        $client = new Client();
        $res = $client->get($url);
        return json_decode($res->getBody(), true);
    }

    /**
     * @return mixed
     *
     */
    public static function getCountryList()
    {
        $countries = Country::select('id', 'name')->get();
        return $countries;
    }

    /**
     * @param $date
     * @return string
     * Chnage Date format
     */
    public static function changeDateFormat($date)
    {
        return Carbon::parse($date)->format('M d');
    }

    /**
     * @param $date
     * @return string
     * Change Time format
     */
    public static function changeTimeFormat($date)
    {
        return Carbon::parse($date)->format('g:i A ');
    }

    /**
     * @param $date
     * @return string
     * Get with Year
     */
    public static function dateFormatWithYear($date)
    {
        return Carbon::parse($date)->format('Y M d');
    }

    public static function dateFormatWithYears($date)
    {
        return Carbon::parse($date)->format('M d,Y');
    }

    /**
     * @param $date
     * @return string
     * Get Date String
     */
    public static function dateFormat($date)
    {
        return Carbon::parse($date)->toDateString();

    }


    public static function getDesignations()
    {
        return Designation::where('vendor_id', User::getVendorId())->select('pk_int_designation_id', 'vchr_designation')->get();
    }

   /* public static function getScratchBypass()
    {
        $settings = Settings::where('vchr_settings_type','scratch-bypass')
        ->select('pk_int_settings_id','vchr_settings_value')->first();
        
		if($settings){
            return json_decode($settings->vchr_settings_value);
        }else{
            return [];
        }
    }
	*/
		
	/*public static function getScratchBypass()
    {
        $settings = Settings::where('vchr_settings_type','scratch-bypass')
        ->pluck('vchr_settings_value');
		if($settings){
            return implode(",",$settings->toArray());
        }else{
            return [];
        }
    }*/
	
	public static function getScratchBypass($user_id)
    {
        $settings = Settings::where('vchr_settings_type','scratch_otp_enabled')->where('fk_int_user_id',$user_id)
        ->pluck('vchr_settings_value')->first();
		return $settings;
    } 

    public static function checkEnableSettings($label,$vendor_id =null)
    {
        /**
         * Global search = 'global-search'
         * Scratch Bypass = 'scratch-bypass'
         * Branch vise filter = 'branch-filter'
         */
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
    }
}
