<?php

namespace App\Http\Controllers\Shortener;

use App\Http\Controllers\Controller;

use App\Mail\GlScratchSuccessMail;
use App\Mail\VerifyEmailScratch;
use Illuminate\Http\Request;
use App\Facades\FileUpload;

use GuzzleHttp\Client;

use App\Models\ShortLink;
use App\Models\ScratchOffer;
use App\Models\ScratchOffersListing;
use App\Models\ScratchWebCustomer;
use App\Models\ShortLinkHistory;
use App\Models\ScratchBranch;
use App\Models\User;

use App\Models\GlApiTokens;
use App\Models\UserOtp;

use App\Common\Common;
use App\Common\Variables;
use App\Common\WhatsappSend;
use App\Services\WhatsappService;
use App\Jobs\SendEmailJob;

use Carbon\Carbon;
use Jenssegers\Agent\Agent;
use Flash;
use Log;
use Mail;
use Auth;
use Session;


class GlScratchWebController extends Controller
{

    public function shortenLink($code)
    {
        $shortlink = ShortLink::where('code', $code)->where('status', ShortLink::ACTIVE)->first();
		
		
        if ($shortlink) {
            $agent = new Agent();
            $device = $agent->device();
            $os = $agent->platform();
            $browser = $agent->browser();
            if ($agent->isMobile()) {
                $device_type = History::MOBILE;
            } elseif ($agent->isTablet()) {
                $device_type = History::TABLET;
            } elseif ($agent->isPhone()) {
                $device_type = History::PHONE;
            } elseif ($agent->isDesktop()) {
                $device_type = History::DESKTOP;
            } elseif ($agent->isRobot()) {
                $device_type = History::ROBOT;
            }
            $ip = Common::getClientIp();
            $shortlink_history = new History();
            $shortlink_history->short_link_id = $shortlink->id;
            $shortlink_history->date = Carbon::now();
            $shortlink_history->ip_address = $ip;
            $shortlink_history->device = $device;
            $shortlink_history->os = $os;
            $shortlink_history->browser = $browser;
            $shortlink_history->device_type = $device_type;

            // $ip = '2409:4073:296:6513:d1d8:abed:535c:c4d9';

            /***Start ip-api.com */

            $response = file_get_contents('http://ip-api.com/json/' . $ip);
            $response = json_decode($response);

            if ($response->status == 'success') {
                $shortlink_history->country = $response->country;
                $shortlink_history->city = $response->city;
                $shortlink_history->region = $response->regionName;
                $shortlink_history->area_code = $response->zip;
                $shortlink_history->country_code = $response->countryCode;
                $shortlink_history->latitude = $response->lat;
                $shortlink_history->logitude = $response->lon;
                $shortlink_history->timezone = $response->timezone;
                $shortlink_history->ip_address = $response->query;

                /***end ip-api.com */

            } else {
                /*** Geo Location */

                $ipdat = @json_decode(file_get_contents(
                    "http://www.geoplugin.net/json.gp?ip=" . $ip));
                $shortlink_history->country = $ipdat->geoplugin_countryName;
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

            $shortlink_history->save();
            $shortlink->click_count++;
            $shortlink->save();

            $user = User::where('pk_int_user_id', $shortlink->vendor_id)->where('int_status', Variables::ACTIVE)->first();
            if ($user) {
                $offer = ScratchOffers::where('int_status', ScratchOffers::ACTIVATE)->where('pk_int_scratch_offers_id', $shortlink->offer_id)->first();
                if ($offer) {

                    return view('gl-scratch-web.short-link.scratch', compact(['shortlink', 'user', 'offer']));

                }
            }
        }

        return view('gl-scratch-web.short-link.invalid');
    }


    public function verifyMobile(Request $request)
    {
	
        if(request()->has('bill_no')){
            $check_num = ScratchWebCustomer::where('bill_no',request('bill_no'))->where('user_id',$request->vendor_id)->first();
            if($check_num){
                return response()->json(['msg' => "You already Scratched with this bill number. Please try with other.", 'status' => false]);
            }
        }

           $check_num = ScratchWebCustomer::join('tbl_scratch_offers_listing', 'tbl_scratch_offers_listing.pk_int_scratch_offers_listing_id', '=', 'scratch_web_customers.offer_list_id')
                    ->join('short_links', 'short_links.offer_id', '=', 'tbl_scratch_offers_listing.fk_int_scratch_offers_id')
                    ->where(function($q){
                        if(request()->has('offer_id'))
                            $q->where('tbl_scratch_offers_listing.fk_int_scratch_offers_id', request('offer_id'));
                            $q->where('scratch_web_customers.country_code', request('country_code'))
                                ->where('scratch_web_customers.mobile', request('mobile'));    
                    })
                    ->where('scratch_web_customers.user_id', request('vendor_id'))
					->whereDate('scratch_web_customers.created_at',now())
                    ->first();
	   
 		
		if($check_num)
		{
            return response()->json(['msg' => "Your chance for today is done, Kindly visit us again tomorrow to win exciting prizes", 'status' => false]);
		}
		
		$mobile = $request->country_code . $request->mobile;
		
		$otp_verify_status=Variables::getScratchBypass(request('vendor_id'));

		if($otp_verify_status=="Disabled")
		{
			return response()->json(['msg' => "Enbled otp", 'status' => true]);
		}

		//otp send to whats app --------------------------------------------------
		
        try {

            $otp = rand(1111, 9999);

            $matchThese = ['number' => $request->mobile, 'user_id' => $request->vendor_id,'otp_type' => 'scratch_web'];
            UserOtp::updateOrCreate($matchThese, ['otp' => $otp]);
						
			Session::put('number',$request->mobile);
			
            try {
                $dataSend = [
                    'mobile_no' => $mobile,
                    'otp' => $otp
                ];
				
                (new WhatsappSend(resolve(WhatsappService::class)))->sendWhatsappOtp($dataSend);
				
            } catch (\Exception $e) {
                Log::info($e->getMessage());
            }

             $code = basename(parse_url($request->link, PHP_URL_PATH));
             $link = ShortLink::where('code',$code)->first();
            return response()->json(['msg' => "Please Wait For Your Otp", 'status' => true,'slink'=>$link,'code'=>$code]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $e->getMessage();
        }

    }
	
    public function verifyOTP(Request $request)
    {
        // return response()->json(['msg' => "Token Expired.!! Try again", 'status' => true]);

        $requestOtp = $request->otp;
        $number = Session::get('number');
        if (!empty($number)) {
            if ($number == $request->mobile) {
                $otpOld = Session::get('otp');
                if (!empty($otpOld)) {
                    if ($otpOld == $requestOtp) {
                        return response()->json(['msg' => "OTP Verification successful", 'status' => true]);
                    } else {
                        return response()->json(['msg' => "Invalid OTP", 'status' => false]);
                    }
                } else {
                    Session::flush();
                    return response()->json(['msg' => "Some error occurred!! Try again..", 'status' => false]);
                }

            }

        } else {
            Session::flush();
            return response()->json(['msg' => "Token Expired.!! Try again", 'status' => false]);
        }
    }
	

    public function scratchCustomer(Request $request)
    {
		
        $mobile = $request->country_code . $request->mobile;
		$otp_verify_status=Variables::getScratchBypass($request->vendor_id);
		
		$type_id= ScratchOffer::where('pk_int_scratch_offers_id', $request->offer_id)->where('int_status',1)->pluck('type_id')->first();
        
		if($otp_verify_status=="Disabled")
		{
            $customer = new ScratchWebCustomer();
            //$customer->fill($request->all());
			$customer->user_id = $request->vendor_id;
			$customer->name = $request->name;
			$customer->country_code = $request->country_code;
			$customer->mobile = $request->mobile;
			$customer->vchr_mobile = $mobile;
			$customer->status = ScratchWebCustomer::NOT_SCRATCHED;
            $customer->redeem = ScratchWebCustomer::NOT_REDEEMED;
            $customer->email = $request->email;
            $customer->branch_id = $request->branch;
			$customer->bill_no = $request->bill_no;
			$customer->short_code = $request->short_code;

            $offerListing = ScratchOffersListing::where('fk_int_scratch_offers_id', $request->offer_id)
                          ->where('int_scratch_offers_balance', '>', '0')->where('int_status',1)
                          ->inRandomOrder()->first();
						
            if ($offerListing) 
			{
                do {
                    $uniqueId = 'GW' . strtoupper(substr(uniqid(), 8));
                    $unique_flag = ScratchWebCustomer::where('unique_id', $uniqueId)->exists();
                } while ($unique_flag);
				
				
				if($offerListing->int_winning_status==1)
					$customer->win_status = 1;
				else
					$customer->win_status = 0;
				
                $customer->unique_id = $uniqueId;
				$customer->offer_id = $offerListing->fk_int_scratch_offers_id;
                $customer->offer_list_id = $offerListing->pk_int_scratch_offers_listing_id;
                $customer->offer_text = $offerListing->txt_description;
				$customer->redeem_source='web';
				$customer->type_id=$type_id;
                //$offerListing->int_scratch_offers_balance--;
                //$offerListing->save();
                $customer->save();

                $offerListing->customer_id = $customer->id;
                $offerListing->unique_id = $uniqueId;
                $offerListing->customer_name = $customer->name;
				
				$offerListing['image'] = FileUpload::viewFile($offerListing->image,'local');
				
                return response()->json(['status' => true, 'offerListing' => $offerListing]);

            }
			
            Session::flush();
            return response()->json(['msg' => "Scratch Offer is Completed", 'status' => false]);
        
		}
		else{
			
            $requestOtp = $request->otp;
            $otpOld = UserOtp::where('number',$request->mobile)->where('user_id',$request->user_id)->where('otp_type','scratch_web')->latest()->first();
            
            if (!empty($otpOld)) {
                if ($otpOld->otp == $requestOtp) {
                    $customer = new ScratchWebCustomer();
                    //$customer->fill($request->all());
					$customer->name = $request->name;
					$customer->user_id = $request->vendor_id;
					$customer->country_code = $request->country_code;
					$customer->mobile = $request->mobile;
					$customer->vchr_mobile = $mobile;
					$customer->status = ScratchWebCustomer::NOT_SCRATCHED;
					$customer->redeem = ScratchWebCustomer::NOT_REDEEMED;
					$customer->email = $request->email;
					$customer->branch_id = $request->branch;
					$customer->bill_no = $request->bill_no;
					$customer->short_code = $request->short_code;

                    $offerListing = ScratchOffersListing::where('fk_int_scratch_offers_id', $request->offer_id)
                                    ->where('int_scratch_offers_balance','>', '0')->where('int_status',1)
                                    ->inRandomOrder()->first();
 										
                    if ($offerListing) {
                        do {
                            $uniqueId = 'GW' . strtoupper(substr(uniqid(), 8));
                            $unique_flag = ScratchWebCustomer::where('unique_id', $uniqueId)->exists();
                        } while ($unique_flag);
						
						
						if($offerListing->int_winning_status==1)
							$customer->win_status = 1;
						else
							$customer->win_status = 0;
						
                        $customer->unique_id = $uniqueId;
						$customer->offer_id = $offerListing->fk_int_scratch_offers_id;
                        $customer->offer_list_id = $offerListing->pk_int_scratch_offers_listing_id;
                        $customer->offer_text = $offerListing->txt_description;
						$customer->redeem_source='web';
						$customer->type_id=$type_id;
						
                        //$offerListing->int_scratch_offers_balance--;
                        //$offerListing->save();
                        $customer->save();
                        $offerListing->customer_id = $customer->id;
                        $offerListing->unique_id = $uniqueId;
                        $offerListing->customer_name = $customer->name;
						
                        $offerListing['image'] = FileUpload::viewFile($offerListing->image,'local');

                        return response()->json(['status' => true, 'offerListing' => $offerListing]);
                    }
                    return response()->json(['msg' => "Scratch Offer is Completed", 'status' => false]);
                } else {
                    return response()->json(['msg' => "Invalid OTP", 'status' => false]);
                }
            } else {
                Session::flush();
                return response()->json(['msg' => "Some error occurred!! Try again..", 'status' => false]);
            }
        }

        return response()->json(['msg' => "Token Expired.!! Try again", 'status' => false]);
        /* end SMS verification */
    }


    public function glScratched($id,$web_api=null)
    {

        $customer = ScratchWebCustomer::find($id);
        $vendor_id = User::getVendorIdApi($customer->user_id);
       		
        $offerListing = ScratchOffersListing::where('pk_int_scratch_offers_listing_id', $customer->offer_list_id)->select('int_winning_status')->first();
        $customer->status = ScratchWebCustomer::SCRATCHED;
        $uniqueId = $customer->unique_id;
        $offetText = $customer->offer_text;
					
		
        /** .... Send email ...*/
        if ($offerListing->int_winning_status == ScratchOffersListing::WIN) 
		{

            /*if ($customer->email != NULL) {
                try{
                    $content = $customer->name . ' Congratulations!! You have won ' . $offetText . '.And Your Redeem Id is ' . $uniqueId . '. Getlead';
                    $data = [
                        'email' => $customer->email,
                        'file_name' => 'App\Mail\GlScratchSuccessMail', // This is the class name of the Mailable
                        'content' => $content,
                    ];
					
                    dispatch(new SendEmailJob($data));
					
                }catch(\Exception $e){
                    \Log::info($e->getMessage());
                }
            } 
			*/
			
        }

        $offerListing = ScratchOffersListing::where('pk_int_scratch_offers_listing_id', $customer->offer_list_id)->where('int_scratch_offers_balance','>', '0')->first();
        if($offerListing){
            $offerListing->int_scratch_offers_balance--;
            $offerListing->save();
			
			$sl=ShortLink::where('code',$customer->short_code)->first();
			if($sl->link_type=="Multiple")
			{
				$sl->status=0;
				$sl->save();
			}
        }

        $flag = $customer->save();
        if ($flag) {
            return response()->json(['msg' => "Success", 'status' => true]);
        }
        return response()->json(['msg' => "Sorry Somthing Went Wrong .!! Try again", 'status' => false]);
    }
	

    public function gotoApiScratch($code)
    {
        $scratchObject = ScratchWebCustomer::where('unique_id',$code);
        $expired = false;
        if($scratch = (clone $scratchObject)->first()){
            $offerList = ScratchOffersListing::where('pk_int_scratch_offers_listing_id', $scratch->offer_list_id)->first();
            $user=User::where('pk_int_user_id',$scratch->user_id)->where('int_status', Variables::ACTIVE)->first();            
                $offer=ScratchOffers::where('int_status',ScratchOffers::ACTIVATE)->where('pk_int_scratch_offers_id',$offerList->fk_int_scratch_offers_id)->first(); 
                    $offerList->uniqueId = $code;
                    $offerList->customer_id = $scratch->id;
                    $offerList->customer_name = $scratch->name;

        }else{
            return view('gl-scratch-web.short-link.invalid');
        }

        if((clone $scratchObject)->where('status' , ScratchWebCustomer::NOT_SCRATCHED)->where('redeem' , ScratchWebCustomer::NOT_REDEEMED)->first()){
            return view('gl-scratch-web.short-link.scratch-new-design',compact(['user','offer','offerList','expired']));
        }else{
            $expired = true;
             return view('gl-scratch-web.short-link.scratch-new-design',compact(['user','offer','offerList','expired']));
        }
    }

	
	public function getBranchAutocomplete($user_id)
    {
        $vendor_id = User::getVendorIdApi($user_id);
        if(request()->filled('term'))
            $branches = ScratchBranch::where('vendor_id',$vendor_id)
                            ->select('id','branch_name')
                            ->where(function($q){
                                $q->where('branch_name','LIKE','%'.request('term').'%');
                            })
                            ->get();
        else
            $branches = [];
                
        return response()->json([ 'status' => 'success','data' => $branches]);
    }


    
}
