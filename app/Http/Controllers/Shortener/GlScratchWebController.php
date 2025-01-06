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
//use App\Models\EnquiryType;
//use App\Models\Enquiry;
use App\Models\ScratchBranch;
use App\Models\User;
//use App\Models\EnquiryFollowup;
use App\Models\GlApiTokens;
use App\Models\UserOtp;

use App\Common\Common;
use App\Common\Variables;
use App\Common\WhatsappSend;
use App\Common\Notifications;
use App\Common\SingleSMS;
use App\Services\WhatsappService;

//use App\Jobs\SendNotification;
//use App\Jobs\SendEmailJob;

//use App\Core\CustomClass;
use App\SmsPanel;

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
                    // return view('gl-scratch-web.short-link.scratch-common',compact(['shortlink','user','offer']));
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
	   
        /*if($check_num && $request->mobile != '9048333535')
		{
            if($request->user_id == 1815) // for hilite
                return response()->json(['msg' => "Your chance for today is done, Kindly visit us again tomorrow to win exciting prizes", 'status' => false]);
            else
                return response()->json(['msg' => "You have already used up your chance. Please try with a different number", 'status' => false]);
        }
		*/

		
		if($check_num)
		{
            return response()->json(['msg' => "Your chance for today is done, Kindly visit us again tomorrow to win exciting prizes", 'status' => false]);
		}
		
		$mobile = $request->country_code . $request->mobile;
		
		$bypass_ids=explode(",",Variables::getScratchBypass());
        if(in_array($request->vendor_id, $bypass_ids))
		{
			return response()->json(['msg' => "bypass otp", 'status' => true]);
		}
		
	//otp send to whats app --------------------------------------------------
		        
        try {

            $otp = rand(1111, 9999);

            /*if(in_array($request->vendor_id, Variables::getScratchBypass()))
                return response()->json(['msg' => "Please Wait For Your Otp", 'status' => true]);*/

            $matchThese = ['number' => $request->mobile, 'user_id' => $request->vendor_id,'otp_type' => 'scratch_web'];
            UserOtp::updateOrCreate($matchThese, ['otp' => $otp]);
			
			Session::put('number',$request->mobile);
			
            try {
                $dataSend = [
                    'mobile_no' => $mobile,
                    'otp' => $otp
                ];
                //(new WhatsappSend(resolve(WhatsappService::class)))->sendWhatsappOtp($dataSend);
				
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
		
		dd($request->all());
		
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
		
		$bypass_ids=explode(",",Variables::getScratchBypass());
				
        if(in_array($request->vendor_id, $bypass_ids)){
		//if(in_array($request->vendor_id, Variables::getScratchBypass())){
			
            $customer = new ScratchWebCustomer();
            $customer->fill($request->all());
            $customer->status = ScratchWebCustomer::NOT_SCRATCHED;
            $customer->redeem = ScratchWebCustomer::NOT_REDEEMED;
            $customer->email = $request->email;
            $customer->branch_id = $request->branch;

            $offerListing = ScratchOffersListing::where('fk_int_scratch_offers_id', $request->offer_id)
                          ->where('int_scratch_offers_balance', '>', '0')->where('int_status',1)
                          ->inRandomOrder()->first();
						
            if ($offerListing) 
			{
                do {
                    $uniqueId = 'GW' . strtoupper(substr(uniqid(), 8));
                    $unique_flag = ScratchWebCustomer::where('unique_id', $uniqueId)->exists();
                } while ($unique_flag);
				
                $customer->unique_id = $uniqueId;
				$customer->offer_id = $offerListing->fk_int_scratch_offers_id;
                $customer->offer_list_id = $offerListing->pk_int_scratch_offers_listing_id;
                $customer->offer_text = $offerListing->txt_description;
                //$offerListing->int_scratch_offers_balance--;
                $offerListing->save();
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
            
            // Check if an OTP was found and if it has expired by 2 minutes
            /*if ($otpOld) {
                $now = Carbon::now();
                // Check if the OTP is expired by 3 minutes
                if ($now->diffInMinutes($otpOld->updated_at) > 3) {
                    return response()->json(['message' => "OTP Expired!! Try again", 'status' => false]);
                }
				
            } else {
                // No OTP found
                return response()->json(['message' => "OTP Expired!! Try again", 'status' => false]);
            }*/
						

            if (!empty($otpOld)) {
                if ($otpOld->otp == $requestOtp) {
                    $customer = new ScratchWebCustomer();
                    $customer->fill($request->all());
                    $customer->status = ScratchWebCustomer::NOT_SCRATCHED;
                    $customer->redeem = ScratchWebCustomer::NOT_REDEEMED;
                    $customer->email = $request->email;
                    $customer->branch_id = $request->branch;

                    $offerListing = ScratchOffersListing::where('fk_int_scratch_offers_id', $request->offer_id)
                                    ->where('int_scratch_offers_balance','>', '0')->where('int_status',1)
                                    ->inRandomOrder()->first();
 										
                    if ($offerListing) {
                        do {
                            $uniqueId = 'GW' . strtoupper(substr(uniqid(), 8));
                            $unique_flag = ScratchWebCustomer::where('unique_id', $uniqueId)->exists();
                        } while ($unique_flag);
						
                        $customer->unique_id = $uniqueId;
						$customer->offer_id = $offerListing->fk_int_scratch_offers_id;
                        $customer->offer_list_id = $offerListing->pk_int_scratch_offers_listing_id;
                        $customer->offer_text = $offerListing->txt_description;
                        //$offerListing->int_scratch_offers_balance--;
                        $offerListing->save();
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
        if($vendor_id == 3286){
            $company = request('company_name');
        }else{
            $company = '';
        }
        $offerListing = ScratchOffersListing::where('pk_int_scratch_offers_listing_id', $customer->offer_list_id)->select('int_winning_status')->first();
        $customer->status = ScratchWebCustomer::SCRATCHED;
        $uniqueId = $customer->unique_id;
		
        /**.....Add Leads ......*/
        //if (!request()->has('status') && !request()->filled('status')) {
         //   request()->merge([
           //     'status' => 'New'
           // ]);
       // }
        //$enq_id = Enquiry::getCRMWebsiteUsers(EnquiryType::GLSCRATCH_WEB, $customer->mobile, $vendor_id, $customer->name, '', '', $customer->country_code,$company ,request());
        /** ......End Leads .... */

        $offetText = $customer->offer_text;
        /** .... Send SMS ...*/
        if ($offerListing->int_winning_status == ScratchOffersListing::WIN) 
		{

            try {
                $message = "Congratulations $customer->name !! You have won $offetText And Your Redeem Id is $uniqueId";
                //event(new CreateFollowup($message,EnquiryFollowup::TYPE_NOTE,$enq_id,$vendor_id));
            } catch (\Exception $e) {
                 \Log::info($e->getMessage());
            } 

            if ($customer->email != NULL) {
                try{
                    $content = $customer->name . ' Congratulations!! You have won ' . $offetText . '.And Your Redeem Id is ' . $uniqueId . '. Getlead';
                    $data = [
                        'email' => $customer->email,
                        'file_name' => 'App\Mail\GlScratchSuccessMail', // This is the class name of the Mailable
                        'content' => $content,
                    ];
					
                    //dispatch(new SendEmailJob($data));
					
                }catch(\Exception $e){
                    \Log::info($e->getMessage());
                }
            } else {
                /** .... Send SMS Getlead  ...*/
                /*$mobile = $customer->country_code . $customer->mobile;
                $template = User::getModuleSmsTemplate($vendor_id, 2, 1);
                if ($template)
                    $message = str_replace(['{offer}', '{redeem_id}'], [$offetText, $uniqueId], $template->template);
                else
                    $message = ('Congratulations!! You have won ' . $offetText . '.And Your Redeem Id is ' . $uniqueId . '. Getlead');
                // $message = ('Congratulations!! You have won ' . $offetText . '.And Your Redeem Id is ' . $uniqueId);
                //Send API Call to a Specific Customer
                if($customer->user_id==1012 || $customer->user_id==631){
                    try{
                        $client = new Client();
                        $params = [
                            'couponCode' => $uniqueId,
                            'mobile_number' => $customer->mobile,
                            'amountLabel' => $offetText
                        ];
                        $data=[
                            'json'=>$params,
                            'headers' => [
                                'Content-Type' => 'application/json',
                                'Authorization' => 'Bearer l738h8acwp5jylb4pbohcii3gobb9iam',
                                'Cookie' => 'PHPSESSID=vjh9fvg7iospf0ndfd32894g9o'
                            ]
                        ];
                        $client->post('https://oxygen-new.webc.in/rest/V1/scratch_coupon/save', $data);
                    }catch(\Exception $exp){
                        \Log::error($exp->getMessage());
                    }
                }*/
                //
               /*$defaultSenderId = SingleSMS:: getSenderid($vendor_id, EnquiryType::GLSCRATCH);
                $defaultRoute = SingleSMS:: getRoute($vendor_id, EnquiryType::GLSCRATCH);
                $apitemplate = CustomClass::userDefaultApiTemplate($vendor_id);

                if (!empty($apitemplate)) {
                    // $input['template_id']=$apitemplate->pk_int_api_template_id;
                    $msg_template = $apitemplate->text_api_template_description;
                    $response = CustomClass::urlReplacement($msg_template, $message, $mobile, $defaultSenderId);

                } else {
                    $send = new SingleSMS();
                    $smsPanel = $send->getSmsPanel($defaultRoute, $vendor_id);
                    $balanceSms = $send->getSMSBalance($vendor_id, $defaultRoute, $smsPanel);

                    if ($balanceSms > 0) {
                        if ($smsPanel->title == SmsPanel::ALERTBOX) {
                            $routeCode = $send->getRouteDetails($defaultRoute)->int_sms_route_code;
                            $smsUrl = $send->getSmsUrl($defaultSenderId, $mobile, $message, $defaultRoute, $routeCode, $vendor_id, '0');
                            $smsCount = $send->getInputSMSCount($message, '0');
                            $templateId = $send->getSmsTemplateId($defaultRoute, $vendor_id);
                            $routeName = $send->getRouteDetails($defaultRoute)->vchr_sms_route;
                            $insertSms = $send->storeSmsData($vendor_id, $templateId, $mobile, $defaultSenderId, '0', $routeName, $message, EnquiryType::GLSCRATCH, $routeCode, $defaultRoute, '1', $smsCount);
                            $response = $send->sendSms($defaultSenderId, $mobile, $message, $routeCode, $balanceSms, $templateId, $defaultRoute, '0', $vendor_id, $smsUrl);
                            $response = $send->getResponse($insertSms, $response, $templateId, $defaultRoute, $vendor_id, $smsCount);
                        } else {
                            $routeCode = $send->getRouteDetails($defaultRoute)->short_code;
                            $smsUrl = $send->getSmsMerabtUrl($defaultSenderId, $mobile, $message, $defaultRoute, $routeCode, $vendor_id, '0');
                            $smsCount = $send->getInputSMSCount($message, '0');
                            $templateId = $send->getSmsTemplateId($defaultRoute, $vendor_id);

                            $routeName = $send->getRouteDetails($defaultRoute)->vchr_sms_route;
                            $insertSms = $send->storeSmsData($vendor_id, $templateId, $mobile, $defaultSenderId, '0', $routeName, $message, EnquiryType::GLSCRATCH, $routeCode, $defaultRoute, '1', $smsCount);
                            //$response = $send->sendSms($defaultSenderId, $mobile, $message, $routeCode, $balanceSms, $templateId, $defaultRoute, '0', $vendor_id, $smsUrl);
                            $response = $send->sendSmsPost($defaultSenderId, $mobile, $message, $routeCode, $balanceSms, $templateId, $defaultRoute, '0', $vendor_id);
                            $response = $send->getMetabtResponse($insertSms, $response, $templateId, $defaultRoute, $vendor_id, $smsCount);
                        }
                    }
                }*/
                /** ....End Getlead SMS ...*/
            }
            // }
        }else{
            try {
                $message = "Sorry $customer->name !! You have lost scratch card. Better luck next time";
                //event(new CreateFollowup($message,EnquiryFollowup::TYPE_NOTE,$enq_id,$vendor_id));
            } catch (\Exception $e) {
                 \Log::info($e->getMessage());
            } 
        }

        if(request()->has('branch_id')){
            $branch = ScratchBranch::find(request('branch_id'));
            $branch_name = $branch ? $branch->branch : '';
        }
        else{
            $branch_name = '';
        }


        /** .... SMS End ....*/

        /** .... Notifications ... */
        /*$userObject = User::getUserDetails($vendor_id);
        $userAdminObject = User::getSingleAdminDetails();
        $notifications = new Notifications();
        $from = env('MAIL_FROM_ADDRESS');
        $to = $userObject->email;
        $subject = "GL Scratch Web Notifications";
        $name = $userObject->vchr_user_name;
        $logo = $userAdminObject->vchr_logo;
        $attachment = "";
        $telegramId = $userObject->telegram_id;
        $mobileNumber = $userObject->vchr_user_mobile;
        $defaultSenderIdAdmin = SingleSMS:: getSenderid($userAdminObject->pk_int_user_id, '');
        $defaultRouteAdmin = SingleSMS:: getRoute($userAdminObject->pk_int_user_id, '');
        // $content1 = "New Leads via GL Scratch Web- " . $customer->mobile . "";
        $content1 = "🔅 Hey, You Have Got a New Lead via Digital Scratch Card. 🔅 

        Customer Name : ".$customer->name." 
        Customer Number : +" . $customer->country_code .' '. $customer->mobile . "
        Bill Number : ".$customer->bill_no."
        Branch Name : ".$branch_name."
        Date and Time : " . Carbon::now();
        // $content2 = "You have new leads via GL Scratch Web - " . $customer->mobile . "";
        $content2 = $content1;
        $notification_data = [
            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
            "sound" => "default",
            // "page" => "enquiry_details",
            // "id" => (string)$insertCrm
        ];
        $dataSend['message'] = $content1;
        $dataSend['user_id'] =  $customer->user_id ?? $vendor_id;
        $dataSend['page'] = 'Scratch';
        $notifications->notifications($from, $to, $subject, $name, $content1, $content2, $logo, $attachment, $telegramId, $vendor_id, $mobileNumber, $defaultRouteAdmin, $defaultSenderIdAdmin,$dataSend);

        /** .... End Notification ... */

        // if($web_api == "scratch_api"){
        $offerListing = ScratchOffersListing::where('pk_int_scratch_offers_listing_id', $customer->offer_list_id)->where('int_scratch_offers_balance','>', '0')->first();
        if($offerListing){
            $offerListing->int_scratch_offers_balance--;
            $offerListing->save();
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

    /*public function searchAutocompleteBranch($user_id)
    {
        $vendor_id = User::getVendorIdApi($user_id);
        if(request()->filled('term'))
            $branches = ScratchBranch::where('vendor_id',$vendor_id)
                            ->select('id','branch')
                            ->where(function($q){
                                $q->where('branch','LIKE','%'.request('term').'%');
                            })
                            ->get();
        else
            $branches = [];
                
        return response()->json([ 'status' => 'success','data' => $branches]);
    }
	*/
	
	public function getBranchAutocomplete($user_id)
    {
        $vendor_id = User::getVendorIdApi($user_id);
        if(request()->filled('term'))
            $branches = ScratchBranch::where('vendor_id',$vendor_id)
                            ->select('id','branch')
                            ->where(function($q){
                                $q->where('branch','LIKE','%'.request('term').'%');
                            })
                            ->get();
        else
            $branches = [];
                
        return response()->json([ 'status' => 'success','data' => $branches]);
    }



    public function fetchHiliteOffers($request)
    {
        \Log::info($request->all());
        $today = now();
        $date = "2023-04-13";
        $expiryNow = Carbon::parse($date);
        $expiry = $expiryNow->addDays(15);
        $limit_days =  $today->diffInDays($expiry);
        
        if($today->toDateString() == $expiry->toDateString()){
            $offerListing = ScratchOffersListing::where('fk_int_scratch_offers_id', $request->offer_id)
                                    ->where('int_scratch_offers_balance', '>', '0')
                                    ->where('int_status',1)
                                    ->inRandomOrder()
                                    ->get();

            return $offerListing->first();
        }
        $offerListing = ScratchOffersListing::where('fk_int_scratch_offers_id', $request->offer_id)
                                            ->where('int_scratch_offers_balance', '>', '0')
                                            ->where('int_status',1)
                                            ->inRandomOrder()
                                            ->get();

        $offerListing->map(function($offer,$key) use($limit_days,$offerListing){
            $count_limit = $offer->int_scratch_offers_balance / $limit_days +2;
            $getScrtach = ScratchWebCustomer::where('offer_list_id',$offer->pk_int_scratch_offers_listing_id)
                                            ->whereDate('created_at',Carbon::today())
                                            ->count();

            if($getScrtach == (int) $count_limit){
                return $offerListing->forget($key);
            }else{
                return $offer;
            }  
        });

        return $offerListing->first();
    }


    function resendOtp(){
        /** .... Send SMS Getlead  ...*/
        $mobile = $request->country_code . $request->mobile;
        $input = $request->all();
        $number = $mobile;
        Session::put('number', $number);
        $otp = rand(1111, 9999);
        $user = User::where('pk_int_user_id', $request->user_id)->first();
        $vendor_id = User::getVendorIdApi($user->pk_int_user_id);
        $template = User::getModuleSmsTemplate($vendor_id, 2, 2);
        if ($template){
            $message = str_replace('{otp}', $otp, $template->template);
        } else{
            if($user->pk_int_user_id == 1815){  // for hilite
                $url = 'https://app.getlead.co.uk/api/pushsms?username=917561086668&token=gl_64584620ae5fcbda186d&sender=HTMALL&to='.$mobile.'&message=Your%20HiLITE%20Login%20OTP%20No%20:%20'.$otp.'&priority=11&message_type=0';
                $client = new \GuzzleHttp\Client();
                $client_request = $client->get($url); 
                goto label;
            }if($user->pk_int_user_id == 832){  // for vismaya
                $url = 'https://app.getlead.co.uk/api/pushsms?username=919048506041&token=gl_0e3aebf369da45359dad&sender=VSMYAA&to='.$mobile.'&message='.$otp.' is your OTP from Vismaya Park.&priority=11&message_type=0';
                $client = new \GuzzleHttp\Client();
                $client_request = $client->get($url); 
                goto label;
            }if($user->pk_int_user_id == 1265){  // futura labs
                $url = 'https://app.getlead.co.uk/api/pushsms?username=917994420040&token=gl_a6654d895ab52769f4dc&sender=TRNPVT&to='.$mobile.'&message=Dear Customer Your One Time Password Is '.$otp.' TRNPVT&priority=4&message_type=0';
                $client = new \GuzzleHttp\Client();
                $client_request = $client->get($url); 
                goto label;
            }elseif($user->pk_int_user_id == 4164){ //teammates academy
                $url = 'https://app.getlead.co.uk/api/pushsms?username=917994420040&token=gl_a6654d895ab52769f4dc&sender=TRNPVT&to='.$mobile.'&message=Dear Customer Your One Time Password Is '.$otp.' TRNPVT&priority=4&message_type=0';
                $client = new \GuzzleHttp\Client();
                $client_request = $client->get($url); 
                goto label;
            }elseif($user->pk_int_user_id == 4216){ // muhammed irfan
                $url = 'https://app.getlead.co.uk/api/pushsms?username=918606498065&token=gl_9b677c6bf232a77fda81&sender=TRNPVT&to='.$mobile.'&message=Dear Customer Your One Time Password Is '.$otp.' TRNPVT&priority=4&message_type=0';
                $client = new \GuzzleHttp\Client();
                $client_request = $client->get($url); 
                goto label;
            }else{
                try {
                    $message = ('Hello, '.$otp.' is the OTP for your request for Scratch through Getlead');
                    // $message = ('Dear Customer Your One Time Password Is '.$otp.' TRNPVT');
                } catch (\Exception $e) {
                    \Log::info($e->getMessage());
                }
                
            }
        }

        $defaultSenderId = 'GLTCKT';
        $defaultRoute = '2';
        $apitemplate = CustomClass::userDefaultApiTemplate($vendor_id);

        if (!empty($apitemplate)) {
            $msg_template = $apitemplate->text_api_template_description;
            $response = CustomClass::urlReplacement($msg_template, $message, $mobile, $defaultSenderId);

        } else {
            $send = new SingleSMS();
            $smsPanel = $send->getSmsPanel($defaultRoute, $vendor_id);
            $balanceSms = $send->getSMSBalance($vendor_id, $defaultRoute, $smsPanel);
            if ($balanceSms > 0) {
                if ($smsPanel->title == SmsPanel::ALERTBOX) {
                    $routeCode = $send->getRouteDetails($defaultRoute)->int_sms_route_code;
                    $smsUrl = $send->getSmsUrl($defaultSenderId, $mobile, $message, $defaultRoute, $routeCode, $vendor_id, '0');
                    $smsCount = $send->getInputSMSCount($message, '0');
                    $templateId = $send->getSmsTemplateId($defaultRoute, $vendor_id);
                    $routeName = $send->getRouteDetails($defaultRoute)->vchr_sms_route;
                    $insertSms = $send->storeSmsData($vendor_id, $templateId, $mobile, $defaultSenderId, '0', $routeName, $message, EnquiryType::GLSCRATCH, $routeCode, $defaultRoute, '1', $smsCount);
                    $response = $send->sendSms($defaultSenderId, $mobile, $message, $routeCode, $balanceSms, $templateId, $defaultRoute, '0', $vendor_id, $smsUrl);

                    $response = $send->getResponse($insertSms, $response, $templateId, $defaultRoute, $vendor_id, $smsCount);
                } else {
                    $routeCode = $send->getRouteDetails($defaultRoute)->short_code == 'OTP' ? 'TL' : $send->getRouteDetails($defaultRoute)->short_code;
                    $check_token = GlApiTokens::where('fk_int_user_id',$vendor_id)->first();
                    $smsUrl = 'https://smschub.com/api/sms/format/json/key/327f07a76e38f94a2c0484d22968bb02/method/MT/mobile/'.$mobile.'/sender/GLTCKT/route/TL/pe_id/1201159196247518420/pe_template_id/1207170029991290303/text/Hello, '.$otp.' is the OTP for your request for Scratch through Getlead';
                    // $smsUrl = $send->getSmsMerabtUrl($defaultSenderId, $mobile, $message, $defaultRoute, $routeCode, $vendor_id, '0');
                    $smsCount = $send->getInputSMSCount($message, '0');
                    $templateId = $send->getSmsTemplateId($defaultRoute, $vendor_id);
                    $routeName = $send->getRouteDetails($defaultRoute)->vchr_sms_route;
                    $insertSms = $send->storeSmsData($vendor_id, $templateId, $mobile, $defaultSenderId, '0', $routeName, $message, EnquiryType::GLSCRATCH, $routeCode, $defaultRoute, '1', $smsCount);
                    $response = $send->sendSms($defaultSenderId, $mobile, $message, $routeCode, $balanceSms, $templateId, $defaultRoute, '0', $vendor_id, $smsUrl);
                    // $response = $send->sendSmsPost($defaultSenderId, $mobile, $message, $routeCode, $balanceSms, $templateId, $defaultRoute, '0',$vendor_id);
                    $response = $send->getMetabtResponse($insertSms, $response, $templateId, $defaultRoute, $vendor_id, $smsCount);
                }
            }
        }
        label:    /** ....End Getlead SMS ...*/
    }
}
