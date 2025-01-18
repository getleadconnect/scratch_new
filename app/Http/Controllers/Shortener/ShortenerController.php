<?php

namespace App\Http\Controllers\Shortener;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;

use Carbon\Carbon;
use App\Common\Common;
use App\Common\Variables;
use Jenssegers\Agent\Agent;

use App\Models\ShortLink;
use App\Models\ScratchBranch;
use App\Models\ScratchOffer;
use App\Models\ScratchOffersListing;

use App\Models\ShortLinkHistory;
use App\Models\Settings;
use App\Models\User;

use App\Traits\GeneralTrait;


use Auth;
use Flash;

use Session;

class ShortenerController extends Controller
{
	
	use GeneralTrait;
	
    public function index($id,$code)
    {
		$user_id=$id;
		
		$result=$this->checkUserStatus($id);
		if($result==false)
		{
			$messageText = "Oops!! This link is expired!!!.";
			return view('gl-scratch-web.short-link.invalid',compact('messageText'));
		}
		
		$messageText = "Oops this link is Invalid";
		$shortlink=ShortLink::where('vendor_id',$user_id)->where('code',$code);
        if((clone $shortlink)->first()){

                $shortlink = $shortlink->where('vendor_id',$user_id)->where('status',ShortLink::ACTIVE)->first();
		
				if(!$shortlink){
                    $messageText = "Oops!! This link is inactive.";
                    return view('gl-scratch-web.short-link.invalid',compact('messageText'));
                }
				
				$offerListing = ScratchOffersListing::where('fk_int_scratch_offers_id', $shortlink->offer_id)
								->where('int_scratch_offers_balance', '>', '0')->where('int_status',1)->first();
				if(!$offerListing)
				{
					$messageText = "Oops!! This offer is closed.";
					return view('gl-scratch-web.short-link.invalid',compact('messageText')); 
				}

				
				$offer=ScratchOffer::where('pk_int_scratch_offers_id',$shortlink->offer_id)->first();	
				if($offer)
				{
					if( $offer->int_status!=1)
					{
						//$offer=ScratchOffer::where('pk_int_scratch_offers_id',$shortlink->offer_id)->where('int_status',1)->first();
						$messageText = "Oops!! This campaign is inactive.";
						return view('gl-scratch-web.short-link.invalid',compact('messageText')); 
					}
					else
					{
						$result=$this->checkCampaignExpired($offer->pk_int_scratch_offers_id);
						if($result==false)
						{
							$messageText = "Oops!! This link is expired!!!.";
							return view('gl-scratch-web.short-link.invalid',compact('messageText'));
						}
					}
				}
				else
				{
					$messageText = "Oops!! This link is Invalid.";
					return view('gl-scratch-web.short-link.invalid',compact('messageText')); 
				}
				
                $agent = new Agent();        
                $device = $agent->device();
                $os = $agent->platform();
                $browser = $agent->browser();
				
                if($agent->isMobile()){
                    $device_type = ShortLinkHistory::MOBILE;
                }
                elseif($agent->isTablet()){
                    $device_type = ShortLinkHistory::TABLET;
                }
            
                elseif($agent->isPhone()){
                    $device_type = ShortLinkHistory::PHONE;
                }
                elseif($agent->isDesktop()){
                    $device_type = ShortLinkHistory::DESKTOP;
                }
                elseif($agent->isRobot()){
                    $device_type = ShortLinkHistory::ROBOT;
                }
				
                $ip =Common::getClientIp();
                $shortlink_history = new ShortLinkHistory();
                $shortlink_history->short_link_id = $shortlink->id;
                $shortlink_history->date = Carbon::now();
                $shortlink_history->ip_address = $ip;
                $shortlink_history->device = $device;
                $shortlink_history->os = $os;
                $shortlink_history->browser = $browser; 
                $shortlink_history->device_type = $device_type;
				
                // $ip = '2409:4073:296:6513:d1d8:abed:535c:c4d9';
                /***Start ip-api.com */
				
                try{
                    $response = file_get_contents('http://ip-api.com/json/'.$ip);
                    $response = json_decode($response);
                  
                    if($response->status == 'success')
                    {
                        $shortlink_history->country =$response->country ;
                        $shortlink_history->city = $response->city;
                        $shortlink_history->region = $response->regionName;
                        $shortlink_history->area_code = $response->zip;
                        $shortlink_history->country_code = $response->countryCode;
                        $shortlink_history->latitude = $response->lat;
                        $shortlink_history->logitude = $response->lon;
                        $shortlink_history->timezone = $response->timezone; 
                        $shortlink_history->ip_address = $response->query;
    
                        /***end ip-api.com */
    
                    }else{
                         /*** Geo Location */
    
                         $ipdat = @json_decode(file_get_contents( 
                            "http://www.geoplugin.net/json.gp?ip=".$ip ));                
                            $shortlink_history->country = $ipdat->geoplugin_countryName ;
                            $shortlink_history->city = $ipdat->geoplugin_city;
                            $shortlink_history->region = $ipdat->geoplugin_region;
                            $shortlink_history->area_code = $ipdat->geoplugin_areaCode;
                            $shortlink_history->country_code = $ipdat->geoplugin_countryCode;
                            $shortlink_history->continent = $ipdat->geoplugin_continentName;
                            $shortlink_history->latitude = $ipdat->geoplugin_latitude;
                            $shortlink_history->logitude = $ipdat->geoplugin_longitude;
                            $shortlink_history->currency = $ipdat->geoplugin_currencyCode;
                            $shortlink_history->timezone = $ipdat->geoplugin_timezone; 

                        /***end Geo Location */
                    }
                }catch(\Exception $e){
                   \Log::info($e->getMessage()); 
                }
            $shortlink_history->save();
            $shortlink->click_count++;
            $shortlink->save();
            $branches=ScratchBranch::where('vendor_id',$shortlink->vendor_id)->get();        
            $expired = false;

            $offerList =[];
            $user=User::where('pk_int_user_id',$shortlink->vendor_id)->where('int_status', Variables::ACTIVE)->first();    
			
			$set=Settings::where('vchr_settings_type','scratch_otp_enabled')->where('fk_int_user_id',$shortlink->vendor_id)->first();
			if($set)
				$scratch_otp_enabled=$set->vchr_settings_value;
			else
				$scratch_otp_enabled="Disabled";
						
            if($user){       
                $offer=ScratchOffer::where('fk_int_user_id',$user_id)->where('int_status',ScratchOffer::ACTIVATE)->where('pk_int_scratch_offers_id',$shortlink->offer_id)->first(); 
                if($offer){
					
                    return view('gl-scratch-web.scratch.index', compact(['user','offer','shortlink','branches','scratch_otp_enabled']));
                    // return view('gl-scratch-web.short-link.scratch-new',compact(['user','offer','shortlink','branches','title']));
                }else{
                    $messageText = "Oops!! There is no offers added.";
                    return view('gl-scratch-web.short-link.invalid',compact('messageText'));
                }
            }
			
        }else{
            $messageText = "Oops!! This is invalid offer.";
            return view('gl-scratch-web.short-link.invalid',compact('messageText','user_id'));
        }
        return view('gl-scratch-web.short-link.invalid',compact('messageText','user_id'));
    }
	
    public function form()
    {
        return view('gl-scratch-web.short-link.terms');
    }
	
    public function terms(Request $request){
        return view('gl-scratch-web.short-link.terms');
    }
	
    public function thankyou(Request $request){
        return view('gl-scratch-web.short-link.thankyou1');
    }
   
}
