<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
//use App\Core\CustomClass;
//use App\CustomField;


use App\Mail\VerifyEmailScratch;
use App\Services\WhatsappService;
//use App\BillingSubscription;
//use App\SmsPanel;

use Carbon\Carbon;
use DB;
use Log;
use Mail;


class GetleadScratchController extends Controller
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
            'mobile' => 'required',
            'password'=>'required'
        ];
        
        $validator = Validator::make($input,$rule);
        if ($validator->passes()) 
        {
            try
            {
				$user_mob=$request->country_code.$request->mobile;
				$user = User::active()->where('vchr_user_mobile', $user_mob)->orWhere('mobile', $request->mobile)->first();
				
                if ($user && Hash::check($request->password,$user->password)) 
                {
                    $vendor_id = User::getVendorIdApi($user->pk_int_user_id );
					//Auth::login($user);
					//$token = $user->createToken('scratch')->accessToken;
					//$user = Auth::user();
					$success['token'] =  $user->createToken('scratchMyApp')->plainTextToken; 
					$success['user'] =  $user;
										
                    return response()->json(['message' => 'Logged Successfully','data'=>$success,'status' => true]);  
                }   
                 else
                 {
                    return response()->json(['message' => 'Invalid Credentials', 'status' => false]); 
                 }
            }catch(\Exception $e){
                return response()->json(['message' => $e->getMessage(), 'status' => false]);
            }
        } else{
            return response()->json(['message' => $validator->messages(), 'status' => false]);
        }
    }
	
    
    public function getOffers(Request $request)
    {

        $rule=[ 
            'user_id' => 'required',
        ];
        
        $validator = Validator::make($request->all(),$rule);
        if ($validator->passes()) 
        {
            $vendor_id = User::getVendorIdApi($request->user_id);
            try
            {
				
                $user = User::active()->where('pk_int_user_id', $vendor_id)->first();
                if ($user) {
                    $offers = ScratchOffer::where('int_status','1')->where('fk_int_user_id',$vendor_id)->whereDate('end_date','>=',Date('Y-m-d'))->get();
                    return response()->json(['message'=> 'Successfully listed','offers'=>$offers,'path'=>url('uploads'), 'status' => true]);
                }else{
                    return response()->json(['message'=> 'User Not Found', 'status' => false]); 
                }  
            }catch(\Exception $e){
                return response()->json(['message'=>$e->getMessage(), 'status' => false]);
            }
        }else{
            return response()->json(['message'=>$validator->messages(), 'status' => false]);
        }
    }
    	
	/*
    public function type(Request $request)
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
                ->where('tbl_scratch_offers.int_status',ScratchOffers::ACTIVATE)
                ->where('tbl_scratch_offers_listing.fk_int_scratch_offers_id',$request->campaign_id)
                ->whereNull('tbl_scratch_offers.deleted_at')
                ->whereNull('tbl_scratch_offers_listing.deleted_at')
                ->whereNull('scratch_type.deleted_at')
                ->select('scratch_type.id','scratch_type.type')->groupBy('id')
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
 
*/

/* To send otp for customer mobile verify  and collect user data 
	fucntion sendOtp
	Method :post
	params: user_id (int) ,name(string), mobile_no (string) include country code , campaign_id (int), type_id (int) 
	 [ bill_no (int), email (string), branch( int)  -> if vendor user required ]
*/



    public function sendOtp(Request $request)
    {

        $rule=[
            'user_id' => 'required',
            'campaign_id' => 'required',
            'name' => 'required',
			'country_code'=>'required',
            'mobile_no' => 'required|numeric|digits_between:8,14',
            'type_id'=>'required',
        ];

        $validator = Validator::make($request->all(),$rule);
        if ($validator->fails()) 
        {
            return response()->json(['msg'=>$validator->messages(), 'status' => false]);
        }
		
        $userid=User::getVendorIdApi($request->user_id);
		
		if(!$request->has('bill_no'))
		{
			$check_mob = ScratchWebCustomer::where('mobile', $request->mobile_no)->where('user_id',$userid)->whereDate('created_at',date('Y-m-d'))->first();
			if($check_mob){
				return response()->json(['msg' => "You already Scratched with this mobile number.Please try with other.", 'status' => false]);
			}
		}
				
        if($request->has('bill_no'))
		{
			$check_bill = ScratchWebCustomer::where('bill_no', $request->bill_no)->where('user_id',$userid)->first();
            if($check_bill)
			{
                return response()->json(['msg' => "You already Scratched with this bill number.Please try with other.", 'status' => false]);
            }
        }
		
		//to insert this code to link scan api--------------------
        $offerListing = ScratchOffersListing::where('fk_int_scratch_offers_id', request('campaign_id'))
                ->where('int_scratch_offers_balance', '!=', '0')
                ->where('int_status',1)
                ->inRandomOrder()
                ->first();
        
        if(!$offerListing)
			return response()->json(['msg' => "Scratch offers corrently not available. Please try after sometimes.", 'status' => false]);
		//---------------------------------------------
		
        				
        $mobile = $request->country_code.$request->mobile_no;
        try {
			
			$otp_enabled=Variables::getScratchBypass($userid);
			
			if($otp_enabled=="Disabled")
                return response()->json(['msg' => "Scratch otp bypass enabled", 'status' => true, 'otp'=>null]);
			
            $number = $mobile;
            $otp = rand(1111, 9999);
            $matchThese = ['number' => $mobile, 'user_id' => $request->user_id,'otp_type' => 'scratch_api'];
            UserOtp::updateOrCreate($matchThese, ['otp' => $otp]);
            
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
            Log::info($e->getMessage());
            return $e->getMessage();
        }
    }
    
/* To verify otp 
	fucntion verifyOtp
	Method :post
	params: user_id (int) , otp (int), mobile_no (string) include country code , campaign_id (int)
*/

    public function verifyOtp(Request $request)
	{
        
		$rule=[
            'user_id' => 'required', 
            'otp' => 'required',
			'mobile_no'=>'required',
			'campaign_id'=>'required'
        ];
		
        $validator = Validator::make(request()->all(),$rule);
		
        if ($validator->passes()) 
        {
            $requestOtp = request('otp');
            $otpOld = UserOtp::where('number',$request->mobile_no)->where('user_id',$request->user_id)->where('otp_type','scratch_api')->latest()->first();
            
            // Check if an OTP was found and if it has expired by 2 minutes
			
            if ($otpOld) {
                $now = Carbon::now();
                // Check if the OTP is expired by 3 minutes
                if ($now->diffInMinutes($otpOld->updated_at) > 3) 
				{
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
            
            $otpOld->delete();
			
            return response()->json(['message' => "Otp verified successfully", 'status' => true,'data' => $offerListing]);
            
        }else{
            return response()->json(['message' => $validator->messages(), 'status' => false]);
        }
    }

/* To get vendor branches 
	fucntion verifyOtp
	Method :post
	params: user_id (int)
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
                                        ->select('scratch_branches.id','scratch_branches.branch_name')->get();
                
                    if($branches->isEmpty()){
                        return response()->json(['message'=> 'No branches available now!','branches'=>$branches,'status' =>false]);
                    }
                    
                    return response()->json(['message'=> 'Successfully listed','branches'=>$branches,'status' =>true]);
                }catch(\Exception $e){
                    return response()->json(['message'=>$e->getMessage(), 'status' => false]);
                }
            }else{     
                return response()->json(['message'=>$validator->messages(), 'status' => false]);
            }
    }

/* To store customer scratched details  
	fucntion scratchCustomer
	Method :post
	params: user_id (int) ,name(string), mobile_no (string) include country code , campaign_id (int), type_id (int)
*/

 public function scratchCustomer(Request $request)
 {

        $rule = [
            'user_id' => 'required',
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
							return response()->json(['msg' => "You already Scratched with this mobile number.Please try with other.", 'status' => false]);
						}
					}
					
				if($request->has('bill_no'))
				{
					$check_bill = ScratchWebCustomer::where('bill_no', $request->bill_no)->where('user_id',$vendor_id)->first();
					if($check_bill)
					{
						return response()->json(['msg' => "You already Scratched with this bill number.Please try with other.", 'status' => false]);
					}
				}

				do {
					$uniqueId = 'GA' . strtoupper(substr(uniqid(), 8));
					$unique_flag = ScratchWebCustomer::where('unique_id', $uniqueId)->exists();
				} while ($unique_flag);
				
				$offer = ScratchOffer::find($request->campaign_id);
				$offerlisting = ScratchOffersListing::where('fk_int_scratch_offers_id',$request->campaign_id)
													->where('pk_int_scratch_offers_listing_id',$request->offer_listing_id)
													->first();
				$bill_no=$email=$branch_id=null;
				
				if($request->has('bill_no'))
					$bill_no=$request->bill_no;
				
				if($request->has('email'))
					$email=$request->email;
				
				if($request->has('branch_id'))
					$branch_id=$request->branch_id;

				$cust_data=[
						'user_id' => $vendor_id,
						'unique_id' => $uniqueId,
						'name' => $request->customer_name,
						'country_code' => $request->country_code,
						'mobile' => $request->mobile_no,
						'vchr_mobile' => $mobile,
						'email' => $email??null,
						'branch_id' => $branch_id??null,
						'bill_no' => $bill_no??null,
						'offer_id' => $request->campaign_id,
						'offer_list_id' => $request->offer_listing_id,
						'status' => 1,
						'type_id' => $request->type_id,
						'offer_text' => $offerlisting->txt_description,
						'win_status'=>$offerlisting->int_winning_status,
						'redeem_source'=>'app',
						];
				
				$flag=ScratchWebCustomer::create($cust_data);
				
				if($flag){

						$offerlisting->int_scratch_offers_balance--;
						$offerlisting->save();
						return response()->json(['message'=> 'Customer details added successfully','status' =>true]);
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
	
}