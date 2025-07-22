<?php

namespace App\Http\Controllers\Api;

use App\Models\Enquiry;
use App\Models\EnquiryType;
use App\Models\ScratchBranch;
use App\Models\ScratchWebCustomer;
use App\Models\ScratchOffer;
use App\Models\ScratchOffersListing;
use App\Models\ScratchType;
use App\Models\UserOtp;
use App\Common\Common;
use App\Common\Notifications;
use App\Common\SingleSMS;
use Hash;
use App\Models\User;
use Validator;
use App\Common\Variables;
use App\Common\WhatsappSend;
use App\Core\CustomClass;
use App\CustomField;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\VerifyEmailScratch;
use App\Services\WhatsappService;
use App\SmsPanel;
use Carbon\Carbon;
use DB;
use Log;
use Mail;
use App\BillingSubscription;


class HyundaiScratchController extends Controller
{
    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function login(Request $request)
    {
        $input=$request->all();
        $rule=[ 
            'username' => 'required',
            'password'=>'required'
        ];
        
        $validator = Validator::make($input,$rule);
        if ($validator->passes()) 
        {
            try
            {
                $user = User::active()->where('email', $request->username)->orWhere('vchr_user_mobile', $request->username)->first();
                if ($user && Hash::check($request->password,$user->password)) {
                    $vendor_id = User::getVendorIdApi($user->pk_int_user_id );

                    if($user->subscription_end_date!=null  and $user->subscription_end_date <= (Carbon::today()->format('Y-m-d')))
                        {
                            return response()->json(['message' => 'You subscription plan expired. Please contact your administrator.', 'status' => false]); 
                        }
					$success['token'] =  $user->createToken('scratchMyApp')->plainTextToken; 
					$success['user'] =  $user;	
                    return response()->json(['message' => 'Logged Successfully','data'=>$success,'path'=>url('uploads').'/', 'status' => true]); 
                }else
                    return response()->json(['message' => 'Invalid Login', 'status' => false]); 
			
            }catch(\Exception $e){
                return response()->json(['message' => $e->getMessage(), 'status' => false]);
            }
        } else{
            return response()->json(['message' => $validator->messages(), 'status' => false]);
        }
    }
    	
    public function offers(Request $request)
    {
        $input=$request->all();
        $rule=[ 
            'user_id' => 'required',
        ];
        
        $validator = Validator::make($input,$rule);
        if ($validator->passes()) 
        {
            $vendor_id = User::getVendorIdApi($request->user_id);
            try
            {
                $user = User::active()->where('pk_int_user_id', $vendor_id)->first();
                if ($user) {
                    $offers = ScratchOffer::where('int_status','1')->where('fk_int_user_id',$vendor_id)->get(); 
                    return response()->json(['message'=> 'Successfully listed','offers'=>$offers,'path'=>url('uploads').'/', 'status' => 'success']);
                }else{
                    return response()->json(['message'=> 'User Not Found', 'status' => 'fail']); 
                }  
            }catch(\Exception $e){
                return response()->json(['message'=>$e->getMessage(), 'status' => 'fail']);
            }
        }else{
            return response()->json(['message'=>$validator->messages(), 'status' => 'fail']);
        }
    }
    
	
    public function scratchType(Request $request)
    {
        $input=$request->all();
        $userid=User::getVendorIdApi($request->user_id);
        $rule=[ 
            'user_id' => 'required',
            'campaign_id' => 'required'
        ];
        
        $validator = Validator::make($input,$rule);
        if ($validator->passes()) 
        {
            try
            {
                $type = ScratchType::where('scratch_type.vendor_id', $userid)->where('scratch_type.status',ScratchType::ACTIVATE)
                ->join('tbl_scratch_offers_listing','tbl_scratch_offers_listing.type_id','scratch_type.id')
                ->where('tbl_scratch_offers_listing.int_status',ScratchOffersListing::ACTIVATE)
                ->where('tbl_scratch_offers_listing.int_scratch_offers_balance','>','0')
                ->join('tbl_scratch_offers','tbl_scratch_offers.pk_int_scratch_offers_id','tbl_scratch_offers_listing.fk_int_scratch_offers_id')
                ->where('tbl_scratch_offers.int_status',ScratchOffer::ACTIVATE)
                ->where('tbl_scratch_offers_listing.fk_int_scratch_offers_id',$request->campaign_id)
                ->whereNull('tbl_scratch_offers.deleted_at')
                ->whereNull('tbl_scratch_offers_listing.deleted_at')
                ->whereNull('scratch_type.deleted_at')
                ->select('scratch_type.id','scratch_type.type')->groupBy('id','scratch_type.type')
                ->get();
                
				
                if($type->isEmpty()){
                    return response()->json(['message'=> 'No Offer Available Now ...','status' => 'fail','user'=>$type]);
                }
                return response()->json(['message'=> 'Successfully listed','user'=>$type,'status' => 'success']);
            }catch(\Exception $e){
                return response()->json(['message'=>$e->getMessage(), 'status' => 'fail']);
            }
        }else {   
            return response()->json(['msg'=>$validator->messages(), 'status' => 'fail']);
        }
    }
    	
	
    public function sendOtp(Request $request)
    {
        $input=$request->all();
        $rule=[
            'user_id' => 'required',
            'campaign_id' => 'required',
            'name' => 'required',
			'country_code' => 'required',
            'mobile_no' => 'required|numeric|digits_between:8,14',
            'type_id'=>'required',
        ];

        $validator = Validator::make($request->all(),$rule);
        if (!$validator->passes()) 
        {
            return response()->json(['msg'=>$validator->messages(), 'status' => 'fail']);
        }
        $userid=User::getVendorIdApi($request->user_id);
		
		$user = User::active()->where('pk_int_user_id', $userid)->first();			
		if($user->subscription_end_date!=null  and $user->subscription_end_date <= (Carbon::today()->format('Y-m-d')))
		    {
                return response()->json(['msg' => 'You are not subscribed to GL Scratch or plan expired. please contact your administrator.', 'status' => false]); 
            }
        if(request()->has('bill_no')){
            $check_bill = ScratchWebCustomer::where('bill_no', $request->bill_no)->where('user_id',$userid)->first();
            if($check_bill){
                return response()->json(['msg' => "You already Scratched with this bill number.Please try with other.", 'status' => false]);
            }
        }
        
        // Get the last 10 digits
        //$last10Digits = substr($request->mobile_no, -10);
        $check_num = ScratchWebCustomer::where('bill_no', $request->bill_no)->where('mobile',$request->mobile_no)->where('user_id',$userid)->first();
        if($check_num){
            return response()->json(['msg' => "You have already used up your chance.Please try with a different number", 'status' => false]);
        }
        
        $offerListing = ScratchOffersListing::where('fk_int_scratch_offers_id', request('campaign_id'))
                ->where('int_scratch_offers_balance', '!=', '0')
                ->where('int_status',1)
                ->inRandomOrder()
                ->first();
        
        if(!$offerListing)
        return response()->json(['msg' => "Scratch offers corrently not available. Please try after sometimes.", 'status' => false]);
        
        $mobile = $request->country_code . $request->mobile_no;
        try {
            $number = $mobile;
            $otp = rand(1111, 9999);
            $matchThese = ['user_id' => $userid, 'otp_type' => 'scratch_api'];
            UserOtp::updateOrCreate($matchThese, ['otp' => $otp]);
            
			
			$otp_enabled=Variables::getScratchBypass($userid);
			
			if($otp_enabled=="Disabled")
                return response()->json(['msg' => "Scratch otp bypass enabled", 'status' => true, 'otp'=>null]);
			
				//if(in_array($request->user_id, Variables::getScratchBypass()))
				  //  return response()->json(['msg' => "Scratch bypass enabled", 'status' => true,'otp'=>$otp]);
				
				/*if ($request->email != "") {
					$data['name'] = $request->name;
					$data['otp'] = $otp;
					
					if(!in_array($request->user_id, Variables::getScratchBypass())){
						try {
							Mail::to($request->email)->send(new VerifyEmailScratch($data));
						} catch (\Exception $e) {
							Log::info($e->getMessage());
						}
					}
				}*/
			            
            try {
                $dataSend = [
                    'mobile_no' => $mobile,
                    'otp' => $otp
                ];
            
				(new WhatsappSend(resolve(WhatsappService::class)))->sendWhatsappOtp($dataSend);
				
            } catch (\Exception $e) {
               Log::info($e->getMessage());
            }
            
			
            /** sms textlocal */
            /*if ($request->country_code == 911) {
                $senderid = "GTLEAD";
                $senderId = urlencode($senderid);
                $apiKey = urlencode('3278g+uz/AM-BVccjTq6yJinqAUjovB1OOt7ZMp2kp');
                
                $message = rawurlencode('Hi ' . $otp . ' is the OTP for your request for number verification through Getlead.');
                
                $data = 'apikey=' . $apiKey . '&numbers=' . $number . "&sender=" . $senderId . "&message=" . $message;
                
                $ch = curl_init('https://api.textlocal.in/send/?' . $data);
                
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);
                
            }else {
                /** .... Send SMS Getlead  ...*/
            /*    try {
                    //code...
                    $url = 'http://sms.getleadcrm.com/api/v1/sms/send?key=Jiim1l0BXFJRBsEW4MfXcDniTtFibxOh&type=1&to='.$mobile.'&sender=GLTCKT&message=Hello, '.$otp.' is the OTP for your request for Scratch through Getlead&flash=0&template_id=1207170029991290303';
                    $client = new \GuzzleHttp\Client();
                    $client->get($url);  
                } catch (\Exception $e) {
                    Log::info('Otp send issue scratch :'.$e->getMessage());
                    return response()->json(['msg' => "Something went wrong. Please try again.", 'status' => false]);
                }
            }*/
            
            //return response()->json(['msg' => "Please Wait For Your Otp", 'status' => true,'otp'=>$otp]);
			
			return response()->json(['msg' => "OTP successfully send!", 'status' => true,'otp'=>$otp]);
			
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $e->getMessage();
        }
    }
    
    public function verifyOtp(){
        $rule=[
            'user_id' => 'required', 
            'otp' => 'required'
        ];
        $validator = Validator::make(request()->all(),$rule);
        if ($validator->passes()) 
        {
            $requestOtp = request('otp');
            $otpOld = UserOtp::where('user_id',request('user_id'))->where('otp_type','scratch_api')->latest()->first();
            
            // Check if an OTP was found and if it has expired by 2 minutes
            if ($otpOld) {
                $now = Carbon::now();
                // Check if the OTP is expired by 3 minutes
                if ($now->diffInMinutes($otpOld->updated_at) > 3) {
                    return response()->json(['message' => "OTP Expired!! Try again", 'status' => false]);
                }
            } else {
                // No OTP found
                return response()->json(['message' => "OTP Expired!! Try again", 'status' => false]);
            }
            
            if((int)$requestOtp != (int)$otpOld->otp){
                return response()->json(['message' => "Invalid otp!! Try again", 'status' => false]);
            }
            
            $offerListing = ScratchOffersListing::where('fk_int_scratch_offers_id', request('campaign_id'))
                ->where('int_scratch_offers_balance', '!=', '0')
                ->where('int_status',1)
                ->inRandomOrder()
                ->first();
            
            return response()->json(['message' => "Otp verified successfull", 'status' => true,'data' => $offerListing]);
            
        }else{
            return response()->json(['message' => $validator->messages(), 'status' => false]);
        }
    }

    public function getBranches(){
        $rule=[ 
            'user_id' => 'required',
            ];
    
            $validator = Validator::make(request()->all(),$rule);
            if ($validator->passes()) 
            {
                $userid=User::getVendorIdApi(request('user_id'));
                try
                {
                    $branches = ScratchBranch::where('scratch_branches.vendor_id', $userid)->where('scratch_branches.status',ScratchBranch::ACTIVATE)
                                        ->select('scratch_branches.id','scratch_branches.branch_name')->groupBy('id','scratch_branches.branch_name')
                                        ->get();
                
                    if($branches->isEmpty()){
                        return response()->json(['message'=> 'No Branches Available Now ...','status' => 'fail','branches'=>$branches]);
                    }
                    
                    return response()->json(['message'=> 'Successfully listed','branches'=>$branches,'status' => 'success']);
                }catch(\Exception $e){
                    return response()->json(['message'=>$e->getMessage(), 'status' => 'fail']);
                }
            }else{     
                return response()->json(['message'=>$validator->messages(), 'status' => 'fail']);
            }
    }


    public function ScratchWebCustomer(){
        $input = request()->all();
        $rule = [
            'user_id' => 'required',
            'campaign_id' => 'required',
            'name' => 'required',
            'mobile_no' => 'required|numeric|digits_between:8,14',
            'type_id'=>'required',
        ];
        \Log::info($input);
        $validator = Validator::make(request()->all(),$rule);
        if ($validator->passes()) 
        {
            $vendor_id = User::getVendorIdApi(request('user_id'));
            $user = User::active()->where('pk_int_user_id', $vendor_id)->first();
            if ($user && request('type_id')) {
                try{
                    $someObject = request('extrafield_values');
                    $additional_fields = [];
                    if(is_array($someObject)){
                        foreach($someObject as $key=>$someObject1)  
                        { 
                            $additional_fields[$someObject[$key]['label']] = $someObject[$key]['value'];
                        }
                    }
                    else{
                        $additional_fields=$someObject;
                    }
                    request()->merge($additional_fields);
                }
                catch(\Exception $e){
                    Log::info('GL Scratch ExtraFields Error');
                    Log::info($e->getMessage());
                }
            }

            try
            {
                if($user) 
                {
                    do {
                        $uniqueId = 'GA' . strtoupper(substr(uniqid(), 8));
                        $unique_flag = ScratchWebCustomer::where('unique_id', $uniqueId)->exists();
                    } while ($unique_flag);
                    
                    $offersListing = ScratchOffer::find(request('campaign_id'));
                    $scratchOffer = ScratchOffersListing::where('fk_int_scratch_offers_id',request('campaign_id'))
                                                        ->where('pk_int_scratch_offers_listing_id',request('offer_id'))
                                                        ->first();

                    if(request('bill_no') != "" && request('type_id') == 1)
                    {
                        if($offersListing)
                        {
                            $customers = new ScratchWebCustomer();
                            $customers->user_id = $vendor_id;
                            $customers->unique_id = $uniqueId;
                            $customers->vchr_name = request('name');
                            $customers->vchr_mobno = request('mobile_no');
                            $customers->vchr_billno = request('bill_no');
                            $customers->fk_int_offer_id = request('offer_id');
                            $customers->int_status = "0";
                            $customers->extrafield_values = request('extrafield_values');
                            $customers->email = request('email');
                            $customers->type_id = request('type_id');
                            $customers->branch_id = request('branch_id');
                            $customers->campaign_id = request('campaign_id');
                            $customers->offer_text = $offersListing->txt_description;
                            $flag=$customers->save();

                            if(request('extrafield_values')){
                                try{
                                    if(request('type_id') === 'enquiryFollowup_glScratch')
                                    {
                                        $client = new \GuzzleHttp\Client();
                                        $client_url = "https://connect.pabbly.com/workflow/sendwebhookdata/Ijg5NzYi";
                                        $dealer = User::select('vchr_user_name')->find($vendor_id);
                                        request()->merge([
                                            'dealer'=>$dealer->vchr_user_name,
                                            'gift' => $offersListing->txt_description
                                        ]);
                                        $client_body = [
                                            "Sl_No"=> '=row()-1',
                                            "Ref_Id" => (string)$customers->pk_int_scratch_customers_id,
                                            "Dealer" => $dealer->vchr_user_name,
                                            "Retailer" => $additional_fields['retailer'],
                                            "Customer_Name" => (string)$customers->vchr_name,
                                            "Mobile_Number" => (string)$customers->vchr_mobno,
                                            "VIN" => $additional_fields['vehicle_number'],
                                            "Model" => $additional_fields['model'],
                                            "Gift" => $offersListing->txt_description,
                                            "Date" => Carbon::now()->format('Y-m-d'),
                                            "Time" => Carbon::now()->format('g:i A')
                                        ];
                                        $client_request = $client->post($client_url,  ['form_params'=>$client_body]);
                                    }
                                    
                                }
                                catch(\Exception $e){
                                    Log::info('GL Scratch ExtraFields Error');
                                    Log::info($e->getMessage());
                                }
                            }
                            
                            // Add values to request object
                            request()->merge((array)$additional_fields);
                            if (!request()->has('status') && !request()->filled('status')) {
                                request()->merge([
                                    'status' => 'New'
                                ]);
                            }

                            Enquiry::getCRMWebsiteUsers(
                                EnquiryType::GLSCRATCH,
                                request('mobile_no'),
                                $vendor_id,
                                request('name'),
                                request('email'),
                                '',
                                request('country_code'),
                                '',
                                request()
                            );
                            
                            $balance = $offersListing->int_scratch_offers_balance;
                            $actualBalance = $balance-1;
                            $getOffers = ScratchOffersListing::where('pk_int_scratch_offers_listing_id', request('offer_id'))
                                                            ->where('int_winning_status', '1')
                                                            ->first();
                            if($getOffers)
                            {
                                //Send Sms
                                $template = User::getModuleSmsTemplate($vendor_id,2,1);
                                if($template)
                                    $message = str_replace(['{offer}','{redeem_id}'],[$getOffers->txt_description,$customers->pk_int_scratch_customers_id],$template->template);
                                else
                                    $message='Congratulations!! You have won ' . $getOffers->txt_description . '.And Your Redeem Id is ' . $customers->pk_int_scratch_customers_id.'. Getlead';
                                $defaultSenderId=SingleSMS:: getSenderid($vendor_id,EnquiryType::GLSCRATCH);
                                $defaultRoute=SingleSMS:: getRoute($vendor_id,EnquiryType::GLSCRATCH);
                                $apitemplate=CustomClass::userDefaultApiTemplate($vendor_id);
                                if(!empty($apitemplate))
                                {
                                    $input['template_id']=$apitemplate->pk_int_api_template_id;
                                    $input['template']=$apitemplate->text_api_template_description;
                                    $response=CustomClass::urlReplacement(
                                        $input['template'],
                                        $message,
                                        request('mobile_no'),
                                        $defaultSenderId
                                    );
                                }else{
                                    $send=new SingleSMS();
                                    $smsPanel=$send->getSmsPanel($defaultRoute,$vendor_id);
                                    $balanceSms=$send->getSMSBalance($vendor_id,$defaultRoute,$smsPanel);
                                    if($balanceSms>0)
                                    {
                                        if($smsPanel->title==SmsPanel::ALERTBOX)
                                        {
                                            $routeCode=$send->getRouteDetails($defaultRoute)->int_sms_route_code;
                                            $smsUrl=$send->getSmsUrl($defaultSenderId, request('mobile_no'), $message, $defaultRoute,$routeCode,$vendor_id,'0');
                                            $smsCount=$send->getInputSMSCount($message, '0');
                                            $templateId=$send->getSmsTemplateId($defaultRoute,$vendor_id);
                                            $routeName=$send->getRouteDetails($defaultRoute)->vchr_sms_route;
                                            $insertSms=$send->storeSmsData($vendor_id,$templateId,request('mobile_no'),$defaultSenderId,'0',$routeName,$message,EnquiryType::GLSCRATCH,$routeCode,$defaultRoute,'1',$smsCount);
                                            $response = $send->sendSms($defaultSenderId, request('mobile_no'), $message, $routeCode,$balanceSms,$templateId,$defaultRoute,'0',$vendor_id,$smsUrl);
                                            $response=$send->getResponse($insertSms,$response,$templateId,$defaultRoute,$vendor_id,$smsCount);
                                        }elseif ($smsPanel->title == SmsPanel::TEXT_LOCAL) {
                                            $userId=$vendor_id;
                                            $routeDetails=$send->getRouteDetails($defaultRoute);
                                            $routeCode = $send->getRouteDetails($defaultRoute)->int_sms_route_code;
                                            $textLocalCredentials = $send->getTextLocalCredentials($userId, $routeDetails->pk_int_sms_route_id, $smsPanel->id);
                                            $apiKey = $textLocalCredentials->api_password;
                                            $numbers = request('mobile_no');
                                            $explodeTo=[$numbers];
                                            $countMobileno = 1;
                                            $message = request('message');
                                            $data = $send->getTextLocalSmsUrl($defaultSenderId, $message, $apiKey, $numbers);
                                            $getTextLocalSmsUrl = $smsPanel->domain . '/send?' . $data;
                                            
                                            $templateId = $textLocalCredentials->id;
                                            $routeName = $send->getRouteDetails($routeDetails->pk_int_sms_route_id)->vchr_sms_route;
                                            $messageType=0;
                                            $messageCount = $send->getInputSMSCount($message,$messageType);
                                            $insertMasterSms = $send->storeMasterSmsDataDeveloperApi($vendor_id, $templateId, '', $defaultSenderId, $messageType, $routeName, $message, EnquiryType::GLSCRATCH, $routeCode, $routeDetails->pk_int_sms_route_id, $countMobileno, $messageCount);
                                            $TextLocalResponses = $send->sendData($getTextLocalSmsUrl);
                                            $textLocalResponse = json_decode($TextLocalResponses, true);
                                            $shtime = 0;
                                            foreach ($explodeTo as $i => $numb) {
                                                $mobno = $numb;
                                                $smsHistoryId = $send->storeSmsDataDeveloperApi($vendor_id, $templateId, $mobno, $defaultSenderId, $messageType, $routeName, $message, EnquiryType::GLSCRATCH, $routeCode, $routeDetails->pk_int_sms_route_id, $countMobileno, $messageCount, $insertMasterSms, $TextLocalResponses, $shtime);
                                                if ($textLocalResponse['status'] === "failure") {
                                                    $message = $textLocalResponse['errors'][0]['message'];
                                                    $send->updateTextLocalResponse($smsHistoryId, $TextLocalResponses, Variables::SMS_STATUS_FAIL, $routeDetails->pk_int_sms_route_id);
                                                } elseif ($textLocalResponse['status'] === "success") {
                                                    $send->updateTextLocalResponse($smsHistoryId, $TextLocalResponses, Variables::SMS_STATUS_DELIVERED, $routeDetails->pk_int_sms_route_id);
                                                }
                                            }
                                            $send->getMasterSmsDetails($insertMasterSms);
                                        }else{
                                            $routeCode=$send->getRouteDetails($defaultRoute)->short_code;
                                            $smsUrl=$send->getSmsMerabtUrl($defaultSenderId, request('mobile_no'), $message, $defaultRoute,$routeCode,$vendor_id,'0');
                                            $smsCount=$send->getInputSMSCount($message, '0');
                                            $templateId=$send->getSmsTemplateId($defaultRoute,$vendor_id);
                                            $routeName=$send->getRouteDetails($defaultRoute)->vchr_sms_route;
                                            $insertSms=$send->storeSmsData($vendor_id,$templateId,request('mobile_no'),$defaultSenderId,'0',$routeName,$message,EnquiryType::GLSCRATCH,$routeCode,$defaultRoute,'1',$smsCount);
                                            $response = $send->sendSmsPost($defaultSenderId, request('mobile_no'), $message, $routeCode, $balance, $templateId, $defaultRoute, '0', $vendor_id);
                                            $response=$send->getMetabtResponse($insertSms,$response,$templateId,$defaultRoute,$vendor_id,$smsCount);
                                        }
                                    }
                                }
                                //End Send Sms-------------------------------------
                            }

                            $branch_name = '';
                            if(request('branch_id')){
                                $branch = ScratchBranch::find(request('branch_id'));
                                $branch_name = $branch ? $branch->branch : '';
                            }
                            $userObject = User::getUserDetails($vendor_id);
                            $userAdminObject = User::getSingleAdminDetails();
                            //Notifications-------------
                            $notifications=new Notifications();
                            $from=env('MAIL_FROM_ADDRESS');
                            $to=$userObject->email;
                            $subject="GL Scratch Notifications";
                            $name=$userObject->vchr_user_name;
                            $logo=$userAdminObject->vchr_logo;
                            $attachment="";
                            $telegramId=$userObject->telegram_id;
                            $mobileNumber=$userObject->vchr_user_mobile;
                            $defaultSenderIdAdmin=SingleSMS:: getSenderid($userAdminObject->pk_int_user_id,'');
                            $defaultRouteAdmin=SingleSMS:: getRoute($userAdminObject->pk_int_user_id,'');
                            $content1 = "🔅 Hey, You Have Got a New Lead via Digital Scratch Card. 🔅 
                            
                            Customer Name : ".request('name')." 
                            Customer Number : " . $input['mobileno'] . "
                            Bill Number : ".request('bill_no')."
                            Branch Name : ".$branch_name."
                            Date and Time : " . Carbon::now();
                            $content2 = $content1;
                            $dataSend['message'] = $content1;
                            $dataSend['user_id'] = request('user_id');
                            $dataSend['page'] = 'scratch';
                            $notifications->notifications($from,$to,$subject,$name,$content1,$content2,$logo,$attachment,$telegramId,$vendor_id,$mobileNumber,$defaultRouteAdmin,$defaultSenderIdAdmin,$dataSend);
                            
                            /**-------------Automation API---------------------------------------------**/
                            $automation_api = \App\AutomationRule::where('vendor_id', $vendor_id)
                                ->where('trigger', request('type_id') == 1 ? 'new_scratch' : 'new_luckydraw')
                                ->where('action', 'api')
                                ->orderBy('id', 'DESC')
                                ->first();
                            
                            if ($automation_api) {
                                $api=$automation_api->api;
                                $url= str_replace(["{number}","{redeem_id}","{name}","{bill_no}"],[request('mobile_no'),$uniqueId,request('name'),request('bill_no')],$api);
                                $client = new \GuzzleHttp\Client();
                                $client_request = $client->get($url);
                            }
                            /**------------End Automation API---------------------------------------------**/
                            return response()->json(['message'=> 'Customer details added successfully', 'status' => 'success']);
                        }else{
                            return response()->json(['message'=> 'No Offers Available', 'status' => 'fail']);
                        } 
                    } else{
                        if(request()->has('bill_no')){
                            $check_bill = ScratchWebCustomer::where('vchr_billno', request('bill_no'))->where('user_id',request('user_id'))->first();
                            if($check_bill){
                                return response()->json(['message' => "You already Scratched with this bill number. Please try with other.", 'status' => false]);
                            }
                        }
                        if($offersListing)
                        {
                            $customers = new ScratchWebCustomer();
                            $customers->user_id = $vendor_id;
                            $customers->unique_id = $uniqueId;
                            $customers->vchr_name = request('name');
                            $customers->vchr_mobno = request('mobile_no');
                            $customers->vchr_billno = request('bill_no');
                            $customers->fk_int_offer_id = request('offer_id');
                            $customers->int_status = "0";
                            $customers->extrafield_values = json_encode(request('extrafield_values')) ?? '[]';
                            $customers->email = request('email');
                            $customers->type_id = request('type_id');
                            $customers->branch_id = request('branch_id');
                            $customers->campaign_id = request('campaign_id');
                            $customers->offer_text = $scratchOffer->txt_description ?? '';
                            $flag=$customers->save();
                            
                            if (!request()->has('status') && !request()->filled('status')) {
                                request()->merge([
                                    'status' => 'New'
                                ]);
                            }

                            try{
                                $dealer = User::select('vchr_user_name','vchr_user_mobile')->find($vendor_id);
                                $client_body = [
                                    "Sl_No"=> '=row()-1',
                                    "Ref_Id" => (string)$customers->pk_int_scratch_customers_id,
                                    "Dealer" => $dealer->vchr_user_name.'-'.$dealer->vchr_user_mobile,
                                    "Retailer" => $additional_fields['retailer'],
                                    "Customer_Name" => (string)$customers->vchr_name,
                                    "Mobile_Number" => (string)$customers->vchr_mobno,
                                    "VIN" => $additional_fields['vehicle_number'],
                                    "Model" => $additional_fields['model'],
                                    "Gift" => $scratchOffer->txt_description ?? '',
                                    "Date" => Carbon::now()->format('Y-m-d'),
                                    "Time" => Carbon::now()->format('g:i A')
                                ];
                                $url = 'https://connect.pabbly.com/workflow/sendwebhookdata/IjU3NjUwNTY0MDYzMjA0MzM1MjY0NTUzMzUxMzIi_pc';
                                $sendWebhook = new Common();
                                $sendWebhook->postToServiceCall($url,$client_body);                                
                            }
                            catch(\Exception $e){
                                Log::info('Scratch webhook error');
                                Log::info($e->getMessage());
                            }

                            Enquiry::getCRMWebsiteUsers(
                                EnquiryType::GLSCRATCH,
                                request('mobile_no'),
                                $vendor_id,
                                request('name'),
                                request('email'),
                                '',
                                request('country_code'),
                                '',
                                request()
                            );

                            if(request('type_id')){
                                $balance=$scratchOffer->int_scratch_offers_balance;
                                $actualBalance=(int)$balance-1;
                                
                                DB::table('tbl_scratch_offers_listing')
                                    ->where('pk_int_scratch_offers_listing_id', request('offer_id'))
                                    ->update(['int_scratch_offers_balance' => $actualBalance]);
                                
                                //Sms
                                
                                $getOffers=ScratchOffersListing::where('pk_int_scratch_offers_listing_id', request('offer_id'))->where('int_winning_status', '1')->first();
                                if($getOffers)
                                {
                                    $message="Congratulations!!You have won".' '.$getOffers->txt_description;
                                    $defaultSenderId=SingleSMS:: getSenderid($vendor_id,EnquiryType::GLSCRATCH);
                                    $defaultRoute=SingleSMS:: getRoute($vendor_id,EnquiryType::GLSCRATCH);
                                    $apitemplate=CustomClass::userDefaultApiTemplate($vendor_id);
                                    if(!empty($apitemplate))
                                    {
                                        $input['template_id']=$apitemplate->pk_int_api_template_id;
                                        $input['template']=$apitemplate->text_api_template_description;
                                        $response=CustomClass::urlReplacement($input['template'],$message,request('mobile_no'),$defaultSenderId);
                                    }else{
                                        $send=new SingleSMS();
                                        $smsPanel=$send->getSmsPanel($defaultRoute,$vendor_id);
                                        $balanceSms=$send->getSMSBalance($vendor_id,$defaultRoute,$smsPanel);
                                        if($balanceSms>0)
                                        {
                                            if($smsPanel->title==SmsPanel::ALERTBOX)
                                            {
                                                $routeCode=$send->getRouteDetails($defaultRoute)->int_sms_route_code;
                                                $smsUrl=$send->getSmsUrl($defaultSenderId, request('mobile_no'), $message, $defaultRoute,$routeCode,$vendor_id,'0');
                                                $smsCount=$send->getInputSMSCount($message, '0');
                                                $templateId=$send->getSmsTemplateId($defaultRoute,$vendor_id);
                                                $routeName=$send->getRouteDetails($defaultRoute)->vchr_sms_route;
                                                $insertSms=$send->storeSmsData($vendor_id,$templateId,request('mobile_no'),$defaultSenderId,'0',$routeName,$message,EnquiryType::GLSCRATCH,$routeCode,$defaultRoute,'1',$smsCount);
                                                $response = $send->sendSms($defaultSenderId, request('mobile_no'), $message, $routeCode,$balanceSms,$templateId,$defaultRoute,'0',$vendor_id,$smsUrl);
                                                $response=$send->getResponse($insertSms,$response,$templateId,$defaultRoute,$vendor_id,$smsCount);
                                            }else{
                                                $routeCode=$send->getRouteDetails($defaultRoute)->short_code;
                                                $smsUrl=$send->getSmsMerabtUrl($defaultSenderId, request('mobile_no'), $message, $defaultRoute,$routeCode,$vendor_id,'0');
                                                $smsCount=$send->getInputSMSCount($message, '0');
                                                $templateId=$send->getSmsTemplateId($defaultRoute,$vendor_id);
                                                $routeName=$send->getRouteDetails($defaultRoute)->vchr_sms_route;
                                                $insertSms=$send->storeSmsData($vendor_id,$templateId,request('mobile_no'),$defaultSenderId,'0',$routeName,$message,EnquiryType::GLSCRATCH,$routeCode,$defaultRoute,'1',$smsCount);
                                                $response = $send->sendSms($defaultSenderId, request('mobile_no'), $message, $routeCode,$balance,$templateId,$defaultRoute,'0',$vendor_id,$smsUrl);
                                                $response=$send->getMetabtResponse($insertSms,$response,$templateId,$defaultRoute,$vendor_id,$smsCount);
                                            }
                                        }
                                    }
                                }
                            }
                            $branch_name = '';
                            if(request('branch_id')){
                                $branch = ScratchBranch::find(request('branch_id'));
                                $branch_name = $branch ? $branch->branch : '';
                            }

                            //Notifications-------------
                            $userObject = User::getUserDetails($vendor_id);
                            $userAdminObject = User::getSingleAdminDetails();
                            $notifications=new Notifications();
                            $from=env('MAIL_FROM_ADDRESS');
                            $to=$userObject->email;
                            $subject="GL Scratch Notifications";
                            $name=$userObject->vchr_user_name;
                            $logo=$userAdminObject->vchr_logo;
                            $attachment="";
                            $telegramId=$userObject->telegram_id;
                            $mobileNumber=$userObject->vchr_user_mobile;
                            $defaultSenderIdAdmin=SingleSMS:: getSenderid($userAdminObject->pk_int_user_id,'');
                            $defaultRouteAdmin=SingleSMS:: getRoute($userAdminObject->pk_int_user_id,'');
                            $content1 = "🔅 Hey, You Have Got a New Lead via Digital Scratch Card. 🔅 
                            
                            Customer Name : ".request('name')." 
                            Customer Number : " . request('mobile_no') . "
                            Bill Number : ".request('bill_no')."
                            Branch Name : ".$branch_name."
                            Date and Time : " . Carbon::now();
                            $content2 = $content1;
                            $notifications->notifications($from,$to,$subject,$name,$content1,$content2,$logo,$attachment,$telegramId,$vendor_id,$mobileNumber,$defaultRouteAdmin,$defaultSenderIdAdmin);
                            //-----------------
                            //Automation
                            /**--API---------------------------------------------**/
                            $automation_api = \App\AutomationRule::where('vendor_id', $vendor_id)
                            ->where('trigger', request('type_id') == 1 ? 'new_scratch' : 'new_luckydraw')
                            ->where('action', 'api')
                            ->orderBy('id', 'DESC')
                            ->first();
                            
                            if ($automation_api) {
                                $api=$automation_api->api;
                                $url= str_replace(["{number}","{redeem_id}","{name}","{bill_no}"],[request('mobile_no'),$uniqueId,request('name'),request('bill_no')],$api);
                                $client = new \GuzzleHttp\Client();
                                $client_request = $client->get($url);
                            }
                            /**--API---------------------------------------------**/
                            //
                            return response()->json(['message'=> 'Customer details added successfully', 'status' => 'success']);
                        }else{
                            return response()->json(['message'=> 'No Offers Available', 'status' => 'fail']);
                        }  
                    }
                }else{
                    return response()->json(['message'=> 'User Not Found', 'status' => 'fail']); 
                }
            } catch(\Exception $e){
                Log::info("Scratch API Error");
                Log::info($e->getMessage());
                return response()->json(['message'=>$e->getMessage(), 'status' => 'fail']);
            }
        }else{
            return response()->json(['message'=>$validator->messages(), 'status' => 'fail']);
        }
    }
}