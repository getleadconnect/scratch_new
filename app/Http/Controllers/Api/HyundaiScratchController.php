<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Facades\FileUpload;

use App\Models\ScratchBranch;
use App\Models\ScratchWebCustomer;
use App\Models\ScratchOffer;
use App\Models\ScratchOffersListing;
use App\Models\ScratchType;
use App\Models\SlideImage;
use App\Models\UserOtp;
use App\Models\User;
use App\Models\Settings;

use App\Common\Variables;
use App\Common\WhatsappSend;
use App\Services\WhatsappService;

//use App\Services\CrmApiService;
use App\Jobs\SentCrmServiceJob;


use Carbon\Carbon;
use Hash;
use Validator;
use DB;
use Log;


class HyundaiScratchController extends Controller
{
    //use CrmApiService;
	
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
	
	/**
    * Display a listing of the scratch offers (campaigns).
    * Method: POST
	* Parms: user_id (int)
    * @return \Illuminate\Http\Response
    */	
	
    	
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
    
	
	/**
    * Display a listing of the scratch types.
    * Method: POST
	* Parms: user_id (int),campaign_id (int)
    * @return \Illuminate\Http\Response
    */	
	
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
                $type = ScratchType::select('scratch_type.id','scratch_type.type')->where('scratch_type.status',ScratchType::ACTIVATE)
                ->whereNull('scratch_type.deleted_at')
                ->get();
				
                if($type->isEmpty()){
                    return response()->json(['message'=> 'No Offer type available.!','status' => 'fail','user'=>$type]);
                }
                return response()->json(['message'=> 'Successfully listed','user'=>$type,'status' => 'success']);
            }catch(\Exception $e){
                return response()->json(['message'=>$e->getMessage(), 'status' => 'fail']);
            }
        }else {   
            return response()->json(['msg'=>$validator->messages(), 'status' => 'fail']);
        }
    }
    
	/**
    * to send otp for verify mobile.
    * Method: POST
	* Parms: user_id (int),campaign_id (int), name (string), country_code (string), mobile_no (string), type_id (int)
    * @return \Illuminate\Http\Response
    */	
	
    public function sendOtp(Request $request)
    {
        $input=$request->all();
        $rule=[
            'user_id' => 'required',
            //'campaign_id' => 'required',
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
        
		//only one offer/campaign ----		
		$offer_id=ScratchOffer::where('fk_int_user_id',$userid)->where('int_status',1)->pluck('pk_int_scratch_offers_id')->first(); //newly added
				
        //$offerListing = ScratchOffersListing::where('fk_int_scratch_offers_id', request('campaign_id'))
		$offerListing = ScratchOffersListing::where('fk_int_scratch_offers_id', $offer_id)
                ->where('int_scratch_offers_balance', '!=', '0')
                ->where('int_status',1)
                ->inRandomOrder()
                ->first();
        
        if(!$offerListing)
        return response()->json(['msg' => "Scratch offers corrently not available. Please try after sometimes.", 'status' => false]);
        
		
		$country_code=91;
		if($request->has('country_code'))
		{
			$country_code=$request->country_code;
		}
				
        $mobile = $country_code . $request->mobile_no;
        try {
            $number = $mobile;
            $otp = rand(1111, 9999);
            $matchThese = ['user_id' => $userid, 'otp_type' => 'scratch_api'];
            UserOtp::updateOrCreate($matchThese, ['otp' => $otp]);
            
			
			$otp_enabled=Variables::getScratchBypass($userid);
			
			if($otp_enabled=="Disabled")
                return response()->json(['msg' => "Scratch otp bypass enabled", 'status' => true, 'otp'=>null]);

            try {
					$dataSend = [
						'mobile_no' => $mobile,
						'otp' => $otp
					];
				
					(new WhatsappSend(resolve(WhatsappService::class)))->sendWhatsappOtp($dataSend);
				
            } catch (\Exception $e) {
               Log::info($e->getMessage());
            }
            
			return response()->json(['msg' => "OTP successfully send!", 'status' => true,'otp'=>$otp]);
			
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return $e->getMessage();
        }
    }
	
	/**
    * to verify the customer mobile no.
    * Method: POST
	* Parms: user_id (int),otp (int)
    * @return \Illuminate\Http\Response
    */	
    
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


/**
    * Display a listing of the branches.
    * Method: POST
	* Parms: user_id (int)
    * @return \Illuminate\Http\Response
    */	
	
	
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

/**
    * Display a slide images .
    * Method: POST
	* Parms: user_id (int)
    * @return \Illuminate\Http\Response
    */	
	
public function getSlideImages()
{
	$rule=[ 
		'user_id' => 'required',
		];
		
		$userid=User::getVendorIdApi(request('user_id'));

		$validator = Validator::make(request()->all(),$rule);
		if ($validator->passes()) 
		{
			$user=User::where('pk_int_user_id',$userid)->first();
			if($user)
			{
				if($user->parent_user_id!=null)
					$userid=$user->parent_user_id;
			}
			
			try
			{
				$images = SlideImage::where('user_id', $userid)->get()->map(function($q)
				{
					$q['image']=FileUpload::viewFile($q['image_file'],'local');
					return $q;
				});

				if($images->isEmpty()){
					return response()->json(['message'=> 'No images were found..','status' => false,'slides'=>$images]);
				}

				return response()->json(['message'=> 'Successfully listed','slides'=>$images,'status' => true]);
			}catch(\Exception $e){
				return response()->json(['message'=>$e->getMessage(), 'status' => false]);
			}
		}else{     
			return response()->json(['message'=>$validator->messages(), 'status' => false]);
		}
}


	
/**
    * to set scratch customer details.
    * Method: POST
	* Parms: user_id (int),vin_number (string), campaign_id (int), customer_name (string), country_code (string), mobile_no (string), type_id (int), 
		optional parameters ( bill_no (string/null), email (string/null) )
    * @return \Illuminate\Http\Response
	
	* vin_number add to bill_no field
	
    */	

public function scratchCustomer(Request $request)
 {

	$rule = [
		'user_id' => 'required',
		'vin_number'=>'required',
		'campaign_id' => 'required',
		'customer_name' => 'required',
		'country_code' => 'required|numeric',
		'mobile_no' => 'required|numeric|digits_between:8,14',
		'type_id'=>'required',
	];

	$validator = Validator::make($request->all(),$rule);
	if ($validator->fails()) 
	{
		return response()->json(['message'=>$validator->messages(), 'status' => false]);
	}
	
	$vendor_id = User::getVendorIdApi($request->user_id);
	
	$otp_verify_status=Variables::getScratchBypass($vendor_id);
	
	$user = User::active()->where('pk_int_user_id', $vendor_id)->first();
	
	if(!$user)
	{
	   return response()->json(['message'=> 'User Not Found', 'status' => false]); 
	}

	try
	{
		$mobile=$request->country_code.$request->mobile_no;
		
		if(!$request->has('bill_no'))
			{
				$check_mob = ScratchWebCustomer::where('vchr_mobile', $mobile)->where('user_id',$vendor_id)->whereDate('created_at',date('Y-m-d'))->first();
				if($check_mob){
					return response()->json(['msg' => "You already scratched with this mobile number. Please try with other.", 'status' => false]);
				}
			}
			
		if($request->has('bill_no'))
		{
			$check_bill = ScratchWebCustomer::where('bill_no', $request->bill_no)->where('user_id',$vendor_id)->first();
			if($check_bill)
			{
				return response()->json(['msg' => "You already scratched with this bill number. Please try with other.", 'status' => false]);
			}
		}
		
		if($request->has('vin_number'))
		{
			$check_bill = ScratchWebCustomer::where('bill_no', $request->vin_number)->where('user_id',$vendor_id)->first();
			if($check_bill)
			{
				return response()->json(['msg' => "You already scratched with this vin number. Please try with other.", 'status' => false]);
			}
		}

		do {
			$uniqueId = 'GA' . strtoupper(substr(uniqid(), 8));
			$unique_flag = ScratchWebCustomer::where('unique_id', $uniqueId)->exists();
		} while ($unique_flag);
		
		$offer = ScratchOffer::find($request->campaign_id);
		/*$offerlisting = ScratchOffersListing::where('fk_int_scratch_offers_id',$request->campaign_id)
											->where('pk_int_scratch_offers_listing_id',$request->offer_listing_id)
											->first();
		*/

		$offerlisting = ScratchOffersListing::where('fk_int_scratch_offers_id', $request->campaign_id)
				  ->where('int_scratch_offers_balance', '>', '0')->where('int_status',1)
				  ->inRandomOrder()->first();
											
		$bill_no=$email=$branch_id=null;
		
		if($request->has('bill_no'))
			$bill_no=$request->vin_number;
		
		if($request->has('email'))
			$email=$request->email;
		
		//if($request->has('branch_id'))
		//$branch_id=$request->branch_id;
		
		$branch_id=$vendor_id;
		
		$country_code=91;
		if($request->has('country_code'))
		{
			$country_code=$request->country_code;
		}

		$cust_data=[
				'user_id' => $vendor_id,
				'unique_id' => $uniqueId,
				'name' => $request->customer_name,
				'country_code' => $country_code,
				'mobile' => $request->mobile_no,
				'vchr_mobile' => $mobile,
				'email' => $email??null,
				'branch_id' => $branch_id??null,
				'bill_no' => $request->vin_number,
				'offer_id' => $request->campaign_id,
				'offer_list_id' => $offerlisting->pk_int_scratch_offers_listing_id,
				'status' => 0,
				'type_id' => $request->type_id,
				'offer_text' => $offerlisting->txt_description,
				'win_status'=>$offerlisting->int_winning_status,
				'redeem'=>0,
				'redeem_source'=>'app',
				];

		$customer=ScratchWebCustomer::create($cust_data);
		$customer['image'] = FileUpload::viewFile($offerlisting->image,'local');
				
		if($otp_verify_status=="Disabled")
			$customer->otp_verify_status="Disabled";
		else
			$customer->otp_verify_status="Enabled";
						
		if($customer){
			
			//send data to crm -------------------------------
				
				/*try{
					$sdt=Settings::where('vchr_settings_type','crm_api_token')->where('fk_int_user_id',$vendor_id)->first();
					if($sdt)
					{
						if($sdt->vchr_settings_value!="" and $sdt->int_status==1)
						{
							$data=[
							  'token'=>trim($sdt->vchr_settings_value),
							  'name'=>$customer->name,
							  'email'=>$customer->email,
							  'country_code'=>$customer->country_code,
							  'mobileno'=>$customer->mobile,
							  'source'=>'Gl-Scratch',
							  //'company_name'	=>$customer->company_name,
							];
							dispatch(new SentCrmServiceJob($data));
						}
					}
												
				}Catch(\Exception $e)
				{
					\Log::info($e->getMessage());
				}
				*/

				//$offerlisting->int_scratch_offers_balance--;
				//$offerlisting->save();

				return response()->json(['data'=>$customer,'message'=> 'Customer details added successfully','status' =>true]);
			}
			else{
				return response()->json(['message'=> 'Something wrong, Try later.!', 'status' => false]);
			}  
	}				
	catch(\Exception $e){
		Log::info("Scratch API Error");
		Log::info($e->getMessage());
		return response()->json(['message'=>$e->getMessage(), 'status' => false]);
	}
}
	
  /**
	* to scratch the card.
	* Method: POST
	* Parms: customer_id (int)
	* @return Response - customer offer details
	*/	
	
public function getScratch(Request $request)
{
	
	$customer = ScratchWebCustomer::find($request->customer_id);

	if($customer)
	{
		if($customer->reedeem==1)
		{
			return response()->json(['msg' => "Sorry, Already redeemed this offer.!", 'status' => false]);
			
		}

		$vendor_id = User::getVendorIdApi($customer->user_id);
		
		$customer->status = ScratchWebCustomer::SCRATCHED;
		$uniqueId = $customer->unique_id;
		$offetText = $customer->offer_text;

		$offerListing = ScratchOffersListing::where('pk_int_scratch_offers_listing_id', $customer->offer_list_id)->where('int_scratch_offers_balance','>', '0')->first();
		
		if($offerListing){
			$offerListing->int_scratch_offers_balance--;
			$offerListing->save();
			
			/*$sl=ShortLink::where('code',$customer->short_code)->first();
			if($sl->link_type=="Multiple")
			{
				$sl->status=0;
				$sl->save();
			}*/
		}

		$flag = $customer->save();
		
		//send data to crm -------------------------------
					
			/*try{
				$sdt=Settings::where('vchr_settings_type','crm_api_token')->where('fk_int_user_id',$vendor_id)->first();
				if($sdt)
				{
					if($sdt->vchr_settings_value!="" and $sdt->int_status==1)
					{
						$data=[
						  'token'=>trim($sdt->vchr_settings_value),
						  'name'=>$customer->name,
						  'email'=>$customer->email,
						  'country_code'=>$customer->country_code,
						  'mobileno'=>$customer->mobile,
						  'source'=>'Gl-Scratch',
						  //'company_name'	=>$customer->company_name,
						];
						dispatch(new SentCrmServiceJob($data));
					}
				}
											
			}Catch(\Exception $e)
			{
				\Log::info($e->getMessage());
			}
			*/

		$offerListing->customer_id = $customer->id;
		$offerListing->unique_id = $uniqueId;
		$offerListing->customer_name = $customer->name;
		$offerListing['image'] = FileUpload::viewFile($offerListing->image,'local');
				
		if ($flag) {
			return response()->json(['msg' => "Success", 'offer' => $offerListing,'status' => true]);
		}
		return response()->json(['msg' => "Sorry Somthing Went Wrong .!! Try again", 'status' => false]);
	}
	else
	{
		return response()->json(['msg' => "Sorry, customer details were not found.!", 'status' => false]);
	}
}


}