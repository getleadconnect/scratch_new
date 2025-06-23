<?php

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use App\Facades\FileUpload;

use App\Models\ScratchBranch;
use App\Models\ScratchWebCustomer;
use App\Models\ScratchOffer;
use App\Models\ScratchOffersListing;
use App\Models\ScratchType;
use App\Models\UserOtp;
use App\Models\User;

use App\Common\Variables;
use App\Common\WhatsappSend;

use App\Services\WhatsappService;

use Carbon\Carbon;
use DB;
use Hash;
use Validator;
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
            'email' => 'required',
            'password'=>'required'
        ];
        
        $validator = Validator::make($input,$rule);
        if ($validator->passes()) 
        {
            try
            {
				$user = User::active()->where('email', $request->email)->first();
				
                if ($user && Hash::check($request->password,$user->password)) 
                {
					$success['token'] =  $user->createToken('scratchMyApp')->plainTextToken; 
					$success['user'] =  $user;
										
                    return response()->json(['data'=>$success,'message'=>'Logged Successfully']);  
                }   
                else
                {
                    return response()->json(['message'=>'Invalid Credentials','status'=>false]); 
                }
            }catch(\Exception $e){
				return response()->json(['message'=>$e->getMessage(),'status'=>false]); 
            }
        } else{
			return response()->json(['message'=>$validator->messages()->first(),'status'=>false]); 
        }
    }
	
	/**
    * Display a listing of the scratch offers (campaigns).
    * Method: POST
	* Parms: user_id(int)
    * @return \Illuminate\Http\Response
    */	
	    
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
                    return response()->json(['message'=>'Successfully listed','offers'=>$offers,'path'=>url('uploads').'/', 'status' => true]);
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
				return response()->json(['msg' => "You already Scratched with this mobile number. Please try with other.", 'status' => false]);
			}
		}
				
        if($request->has('bill_no'))
		{
			$check_bill = ScratchWebCustomer::where('bill_no', $request->bill_no)->where('user_id',$userid)->first();
            if($check_bill)
			{
                return response()->json(['msg' => "You already Scratched with this bill number. Please try with other.", 'status' => false]);
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

/**
    * to set scratch customer details.
    * Method: POST
	* Parms: user_id (int),campaign_id (int), customer_name (string), country_code (string), mobile_no (string), type_id (int), 
		optional parameters ( bill_no (string/null), email (string/null) )
    * @return \Illuminate\Http\Response
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
							return response()->json(['msg' => "You already Scratched with this mobile number. Please try with other.", 'status' => false]);
						}
					}
					
				if($request->has('bill_no'))
				{
					$check_bill = ScratchWebCustomer::where('bill_no', $request->bill_no)->where('user_id',$vendor_id)->first();
					if($check_bill)
					{
						return response()->json(['msg' => "You already Scratched with this bill number. Please try with other.", 'status' => false]);
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
						'redeem'=>0,
						'redeem_source'=>'app',
						];
				
				$flag=ScratchWebCustomer::create($cust_data);
				
				if($otp_verify_status=="Disabled")
					$customer->otp_verify_status="Disabled";
				else
					$customer->otp_verify_status="Enabled";
				
				if($flag){

						//$offerlisting->int_scratch_offers_balance--;
						//$offerlisting->save();
						
						return response()->json(['data'=>$flag,'message'=> 'Customer details added successfully','status' =>true]);
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