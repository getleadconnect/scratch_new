<?php

namespace App\Http\Controllers\Api\AgentApp;

use DB;
use Auth;
use File;
use JWTAuth;
use App\User;
use Validator;
use App\ContactUs;
use App\CampaignStage;
use App\Subscription\Plan;
use App\BackendModel\UserOtp;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use  App\BackendModel\ScratchType;
use  App\BackendModel\ScratchOffers;
use  App\BackendModel\ScratchOffersListing;
use App\Common\Application;
use App\Common\AgentApp;
use App\Common\Variables;
use App\Core\CustomClass;
use App\BackendModel\ShortLink;
use Illuminate\Support\Facades\Hash;
use App\BackendModel\ScratchWebCustomer;
use App\Facades\FileUpload;

class GlScratchController extends Controller
{
    protected $agentAppObj;
    protected $applicationObj;

    public function __construct()
    {
        $this->agentAppObj = new AgentApp();
        $this->applicationObj = new Application();
    }

    public function campaignTypes()
    {
        $data = ScratchType::where('id',2)->select('type','id')->get();
        return response()->json(['status'=>1,'message'=>'Success','data'=>$data]);
    }
    
    public function createCampaign(Request $request)
    {   
      
        $rules = [
            // 'image' => 'required|image:jpeg,png,jpg|max:10480'
            'campaign_name' => 'required',
            'campaign_type' => 'required',
        ];

        $input     = $request->only('campaign_name','campaign_type');
        $validator = Validator::make($input, $rules);
    
        if ($validator->fails()) 
        {
            return response()->json(['status' => 0, 'message' => $validator->messages()]);
        }

        $vendorId = $this->applicationObj->getVendorId(Auth::user()->pk_int_user_id);
       
        $camapign = new ScratchOffers();
        $camapign->vchr_scratch_offers_name = $request->campaign_name;
        $camapign->fk_int_user_id =  $vendorId;
        $camapign->type_id = 1 /* $request->campaign_type */;
        $camapign->int_status =  ScratchOffers::ACTIVATE;

        if($request->has('image'))
        {
            $image = $request->file('image');
            $name = mt_rand() . '.' . $image->getClientOriginalExtension();
            $path = 'uploads/offersListing/';
            FileUpload::uploadFile($image, $path,$name,'s3');
            
            $camapign->vchr_scratch_offers_image = $path.$name;
            $camapign->mobile_image = $path.$name;
        }else{
            $camapign->vchr_scratch_offers_image = '/uploads/offers/1431986498.svg';
            $camapign->mobile_image = '/uploads/offers/1431986498.svg';
        }
        $camapign->save();

        if($request->has('stage_name'))
        {
            foreach ($request->stage_name as $key => $value) {
                $stage = new CampaignStage;
                $stage->campaign_id = $camapign->pk_int_scratch_offers_id;
                $stage->stage_name = $value;
                $stage->save();
            }
        }
        
        $random = mt_rand();
        $url = new ShortLink();
        $url->code = $camapign->pk_int_scratch_offers_id;
        $url->vendor_id = $vendorId;
        $url->link = 'http://'.env('SHORT_LINK_DOMAIN')."/".$camapign->pk_int_scratch_offers_id;       
        $url->offer_id = $camapign->pk_int_scratch_offers_id;        
        $url->url = NULL;        
        $url->status = ShortLink::ACTIVE;
        $url->type = ShortLink::GL_SCRATCH;
        $url->save();

        if($request->has('gift_name'))
        {   
            foreach ($request->gift_name as $key => $value) 
            {
                $gift = new ScratchOffersListing();
                $gift->txt_description = $value;
                $gift->fk_int_scratch_offers_id = $camapign->pk_int_scratch_offers_id;
                $gift->int_scratch_offers_count = $request->count[$key];
                $gift->int_scratch_offers_balance = $request->count[$key];
                $gift->fk_int_user_id = $vendorId;
                $gift->int_status = 1;
                $gift->int_winning_status = $request->status[$key];

                $image = $request->gift_image[$key];
                $name = mt_rand() . '.' . $image->getClientOriginalExtension();
                $path = 'uploads/offersListing/';
                FileUpload::uploadFile($image, $path,$name,'s3');
                
                $gift->image = $path.$name;
                $gift->save();
            }
        }

        return response()->json(['status'=>1,'message'=>'Success','data'=>$camapign,'link'=>$url->link]);
    }

    public function createCampaignStage(Request $request)
    {
        $rules = [
            'campaign_id' => 'required',
            'stage_name' => 'required'
        ];

        $input  = $request->only('campaign_id','stage_name');
        $validator = Validator::make($input, $rules);
    
        if ($validator->fails()) 
        {
            return response()->json(['status' => 0, 'message' => $validator->messages()]);
        }

        $stage = new CampaignStage;
        $stage->campaign_id = $request->campaign_id;
        $stage->stage_name = $request->stage_name;
        $stage->save();

       return response()->json(['status'=>1,'message'=>'Success','data'=>$stage]);
        
    }

    public function listCampaignStages(Request $request)
    {
        $rules = [
            'campaign_id' => 'required'
        ];

        $input  = $request->only('campaign_id');
        $validator = Validator::make($input, $rules);
    
        if ($validator->fails()) 
        {
            return response()->json(['status' => 0, 'message' => $validator->messages()]);
        }

       $stages = CampaignStage::where('campaign_id',$request->campaign_id)->select('id','campaign_id','stage_name')->get();
       return response()->json(['status'=>1,'message'=>'Success','data'=>$stages]);
    }


    public function updateCampaignStage(Request $request)
    {
        $rules = [
            'stage_id' => 'required',
            'stage_name' => 'required',
        ];

        $input  = $request->only('stage_id','stage_name');
        $validator = Validator::make($input, $rules);
    
        if ($validator->fails()) 
        {
            return response()->json(['status' => 0, 'message' => $validator->messages()]);
        }

       $stage = CampaignStage::where('id',$request->stage_id)->first();
       $stage->stage_name = $request->stage_name;
       $stage->save();

       return response()->json(['status'=>1,'message'=>'Success','data'=>$stage]);
    }

    public function addGiftToCampaign(Request $request)
    {
            $rules = [
                'gift_image' => 'required|image:jpeg,png,jpg|max:10480',
                'gift_name' => 'required',
                'campaign_id' => 'required',
                'count' => 'required',
                'status' => 'required',
                // 'stage_id' => 'required',
            ];

            $input     = $request->only('gift_image','gift_name','campaign_id','count','status');
            $validator = Validator::make($input, $rules);
        
            if ($validator->fails()) 
            {
                return response()->json(['status' => 0, 'message' => $validator->messages()]);
            }

            $vendorId = $this->applicationObj->getVendorId(Auth::user()->pk_int_user_id);

            $gift = new ScratchOffersListing();
            $gift->txt_description = $request->gift_name;
            $gift->fk_int_scratch_offers_id = $request->campaign_id;
            $gift->int_scratch_offers_count = $request->count;
            $gift->int_scratch_offers_balance = $request->count;
            $gift->fk_int_user_id = $vendorId;
            $gift->int_winning_status = $request->status;
            $gift->int_status = 1;
            $gift->type_id = $request->stage_id ? $request->stage_id : '';

            $image = $request->file('gift_image');
            $name = mt_rand() . '.' . $image->getClientOriginalExtension();
            $path = 'uploads/offersListing/';
            FileUpload::uploadFile($image, $path,$name,'s3');

            $gift->image = $path.$name;
            $gift->save();

            return response()->json(['status'=>1,'message'=>'Success','data'=>$gift]);
    }

    public function deleteCampaign(Request $request)
    {
        $rules = [
            'campaign_id' => 'required'
        ];

        $input     = $request->only('campaign_id');

        $validator = Validator::make($input, $rules);
    
        if ($validator->fails()) 
        {
            return response()->json(['status' => 0, 'message' => $validator->messages()]);
        }

       $offer = ScratchOffers::where('pk_int_scratch_offers_id',$request->campaign_id)->first();
       $offer->delete();
       return response()->json(['status'=>1,'message'=>'Success','data'=>$offer]);
    }

    public function editGift(Request $request)
    {
        $rules = [
            // 'gift_image' => 'required|image:jpeg,png,jpg|max:10480',
            'gift_id' => 'required',
            'gift_name' => 'required',
            'count' => 'required',
            'status' => 'required',
        ];

        $input     = $request->only('gift_id','gift_name','count','status');
        $validator = Validator::make($input, $rules);
    
        if ($validator->fails()) 
        {
            return response()->json(['status' => 0, 'message' => $validator->messages()]);
        }

        $vendorId = $this->applicationObj->getVendorId(Auth::user()->pk_int_user_id);

        $gift = ScratchOffersListing::where('pk_int_scratch_offers_listing_id',$request->gift_id)->first();
        $gift->txt_description = $request->gift_name;
        $gift->int_scratch_offers_count = $request->count;
        $gift->fk_int_user_id = $vendorId;
        $gift->int_winning_status = $request->status;
        
        if($request->has('gift_image'))
        {
            $image = $request->file('gift_image');
            $name = mt_rand() . '.' . $image->getClientOriginalExtension();
            $path = 'uploads/offersListing/';
            FileUpload::uploadFile($image, $path,$name,'s3');
            $gift->image = $path.$name;
        }
        $gift->save();

        return response()->json(['status'=>1,'message'=>'Success','data'=>$gift]);
    }

    public function deleteGift(Request $request)
    {

        $rules = [
            'gift_id' => 'required'
        ];

        $input     = $request->only('gift_id');

        $validator = Validator::make($input, $rules);
    
        if ($validator->fails()) 
        {
            return response()->json(['status' => 0, 'message' => $validator->messages()]);
        }

       $gift = ScratchOffersListing::where('pk_int_scratch_offers_listing_id',$request->gift_id)->first();
       if($gift)
       {
        $gift->delete();
        return response()->json(['status'=>1,'message'=>'Success','data'=>$gift]);
       }
       else
       {
        return response()->json(['status'=>0,'message'=>'Gift not found !!!']);
       }
       
    }

    public function editCampaign(Request $request)
    {
        $rules = [
            // 'image' => 'required|image:jpeg,png,jpg|max:10480'
            'campaign_id' => 'required',
            'campaign_name' => 'required',
            'campaign_type' => 'required',
        ];

        $input     = $request->only('campaign_id','campaign_name','campaign_type');
        $validator = Validator::make($input, $rules);
    
        if ($validator->fails()) 
        {
            return response()->json(['status' => 0, 'message' => $validator->messages()]);
        }

        $vendorId = $this->applicationObj->getVendorId(Auth::user()->pk_int_user_id);
       
        $camapign = ScratchOffers::where('pk_int_scratch_offers_id',$request->campaign_id)->first();
        $camapign->vchr_scratch_offers_name = $request->campaign_name;
        $camapign->fk_int_user_id =  $vendorId;
        $camapign->type_id =  $request->campaign_type;
        $camapign->int_status =  $request->status;

        if($request->has('image'))
        {
            $image = $request->file('image');
            $name = mt_rand() . '.' . $image->getClientOriginalExtension();
            $path = 'uploads/offersListing/';
            FileUpload::uploadFile($image, $path,$name,'s3');
            $camapign->vchr_scratch_offers_image = $path.$name;
            $camapign->mobile_image = $path.$name;
        }
        
        $camapign->save();

        return response()->json(['status'=>1,'message'=>'Success','data'=>$camapign]);
    }

    public function listCampaigns(Request $request)
    {
        $vendorId = $this->applicationObj->getVendorId(Auth::user()->pk_int_user_id);
        $camapigns = ScratchOffers::where('fk_int_user_id',$vendorId)
                            ->orderby('pk_int_scratch_offers_id','Desc')->get(); 

        $data['campaigns'] = $camapigns->map(function ($item) {
            $code = (count($item->shortLink->where('status',1)))? $item->shortLink->where('status',1)->first()->code : $item->pk_int_scratch_offers_id;
            $item->link = 'http://'.env('SHORT_LINK_DOMAIN')."/".$code;
            $item->image_link = FileUpload::viewFile($item->vchr_scratch_offers_image,'s3');
            $item->count = (int)ScratchOffersListing::where('fk_int_scratch_offers_id',$item->pk_int_scratch_offers_id)->sum('int_scratch_offers_balance');
            return $item;
        });

        $data['active_campaigns'] = $data['campaigns']->where('int_status',1)->values();
        $data['inactive_campaigns'] = $data['campaigns']->where('int_status',0)->values();
        return response()->json(['status'=>1,'message'=>'Success','data'=>$data]);
    }

    public function getCampaignGifts(Request $request)
    {
        $rules = [
            'campaign_id' => 'required'
        ];

        $input     = $request->only('campaign_id');
        $validator = Validator::make($input, $rules);
    
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->messages()]);
        }

       $data = ScratchOffersListing::where('fk_int_scratch_offers_id',$request->campaign_id)->get();
       return response()->json(['status'=>1,'message'=>'Success','data'=>$data]);
    }

    public function sendFeedback(Request $request)
    {
        $rules = [
            'name' => 'required',
            'number' => 'required',
            'message' => 'required'
        ];

        $input = $request->only('name','number','message');
        $validator = Validator::make($input, $rules);
    
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->messages()]);
        }

        $feedback = new ContactUs;
        $feedback->name = $request->name;
        $feedback->number = $request->number;
        $feedback->message = $request->message;
        $feedback->source = "gl_scratch";
        $feedback->save();

        return response()->json(['status'=>1,'message'=>'Success','data'=>$feedback]);
    }

    public function helpCenter()
    {
        $faq =[['Question' => 'What is GL Scratch ?',
                 'Answer'  => 'GL scratch is a digital scratch card app that simulates the experience of playing a physical scratch card. This app allows users to purchase virtual scratch cards to engage their customers in a scratch and win program.

                 GL Scratch allows users to purchase virtual scratch cards that they can then scratch to reveal a hidden prize. This can be a fun and interactive way for businesses to engage with their customers and offer rewards or prizes through a scratch and win program. Digital scratch card apps can be a convenient and cost-effective alternative to physical scratch cards, as they can be accessed and played on a smartphone or other device.'],

                ['Question'=> 'How to use GL-Scratch ?',
                  'Answer' => 'Initially any registered customer will be provided with a demo campaign, where they will have a fixed number of gifts for both win and loose scenario.
                  
                  Inorder to add a gift,select the pre-created campaign, click on the edit button alongside, and add as many gifts as you like, also keep in mind the total scratch cards that you are left with.Make sure you add gifts for both win and loose conditions.

                  Once the user has used up this finite number of scratches, he/she can go for a plan upgradation, wherein new campaigns can be created and rest of the procedure is as explained above.
                  
                  To upgrade to a new plan, click on the upgrade now button, once you have used up all the scratch cards, where you will be prompted with the list of available plans.You can select one of them according to your usage needs and proceed to the payment section.Once the payment is successfull, your new plan will be activated immediately.
                  '],

                ['Question'=> 'How to upgrade to a new plan ?',
                  'Answer' => 'To upgrade to a new plan, click on the upgrade now button, once you have used up all the scratch cards, where you will be prompted with the list of available plans.You can select one of them according to your usage needs and proceed to the payment section.Once the payment is successfull, your new plan will be activated immediately.'],

                ['Question'=> 'How to know your active plan ?',
                 'Answer' => 'Click on the options menu on the top left of the home screen,there you can see the active plans menu, which on clicking will show the plan that you are active on.'],

                ['Question'=> 'How to use know your scratch balance ?',
                 'Answer' => 'The scratch balance available on your current plan will always be visible on the homescreen.The exact count balance for win and loose can be viewed by clicking on the particular campaign and the count will displayed underneath the win and loose cards.'],

                 ['Question'=> 'How to create a new campaign ?',
                 'Answer' => 'Click on the plus icon on the home screen,and provide the details for the new campaign and click on the continue button,you can either add you gifts along with the campaign creation or you may just save the campaign and come back to this at a later point of time.'],

                 ['Question'=> 'How to add gifts to the campaign ?',
                 'Answer' => 'Inorder to add a gift,select the pre-created campaign, click on the edit button alongside, and add as many gifts as you like, also keep in mind the total scratch cards that you are left with.Make sure you add gifts for both win and loose conditions.'],

                 ['Question'=> 'How to reset password ?',
                 'Answer' => 'Click on the options menu on the top left of the home screen,there you can see the change password menu.Further on clicking on this you will be prompted to enter your current and new passwords to successfully update your password.Note that you should remember your current password to update the existing password']

                ];

        return response()->json(['status'=>1,'message'=>'Success','data'=>$faq]);
    }

    public function register(Request $request)
    {
        $rules = [
            'name' => 'required',
            'mobile' => 'required|unique:tbl_users|digits:10',
            'email' => 'required|email|unique:tbl_users,email',
            'password' => 'required',
            'country_code' => 'required',
        ];

        $input = $request->only('name','mobile','email','password','country_code');
        $validator = Validator::make($input, $rules);
    
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->messages()]);
        }

        $user = new User;
        $user->vchr_user_name = $request->name;
        $user->email = $request->email;
        $user->vchr_user_mobile = $request->country_code.''.$request->mobile;
        $user->mobile = $request->mobile;
        // $user->vchr_user_mobile = $request->mobile;
        $user->countrycode = $request->country_code;
        $user->password = bcrypt($input['password']);
        $user->int_role_id = 2;
        $user->int_status = User::ACTIVATE;
        $user->int_registration_from = User::APPREGISTER;
        $flag = $user->save();

        $request->merge(['plan_id' => config('app.TRIAL_SCRATCH_PLAN'),'amount'=>0,'vendor'=>$user->pk_int_user_id]);

        $this->purchasePlan($request);

        $camapign = new ScratchOffers();
        $camapign->vchr_scratch_offers_name = "Demo Campaign";
        $camapign->fk_int_user_id =  $user->pk_int_user_id;
        $camapign->type_id =  1;
        $camapign->int_status =  ScratchOffers::ACTIVATE;
        $camapign->vchr_scratch_offers_image = '/uploads/offers/1431986498.svg';
        $camapign->mobile_image = '/uploads/offers/1431986498.svg';
        $camapign->save();

        $random = mt_rand();
        $url = new ShortLink();
        $url->code = $camapign->pk_int_scratch_offers_id;
        $url->vendor_id = $user->pk_int_user_id;
        $url->link = 'http://'.env('SHORT_LINK_DOMAIN')."/".$camapign->pk_int_scratch_offers_id;       
        $url->offer_id = $camapign->pk_int_scratch_offers_id;        
        $url->url = NULL;        
        $url->status = ShortLink::ACTIVE;
        $url->type = ShortLink::GL_SCRATCH;
        $url->save();

        $gift = new ScratchOffersListing();
        $gift->txt_description = "53 inch LED TV";
        $gift->fk_int_scratch_offers_id = $camapign->pk_int_scratch_offers_id;
        $gift->int_scratch_offers_count = 3;
        $gift->int_scratch_offers_balance = 3;
        $gift->fk_int_user_id = $user->pk_int_user_id;
        $gift->int_winning_status = 1;
        $gift->int_status = 1;
        $gift->image = '/uploads/offersListing/639c149963c24.jpg';
        $gift->save();

        $gift = new ScratchOffersListing();
        $gift->txt_description = "Better Luck Next Time";
        $gift->fk_int_scratch_offers_id = $camapign->pk_int_scratch_offers_id;
        $gift->int_scratch_offers_count = 2;
        $gift->int_scratch_offers_balance = 2;
        $gift->fk_int_user_id = $user->pk_int_user_id;
        $gift->int_winning_status = 0;
        $gift->int_status = 1;
        $gift->image = '/uploads/offersListing/639c14996795f.jpg';
        $gift->save();
        

        return response()->json(['status'=>1,'message'=>'Success','data'=>$user]);
    }

    public function sendOtp(Request $request)
    {
            $rules = [
                'mobile' => 'required|exists:tbl_users|digits:10',
                'country_code' => 'required',
            ];
    
            $input = $request->only('mobile','country_code');
            $validator = Validator::make($input, $rules);
        
            if ($validator->fails()) {
                return response()->json(['status' => 0, 'message' => $validator->messages()]);
            }
                $user = User::where('mobile',$request->mobile)->first();
                $number = $request->country_code.$request->mobile;
                $otp = CustomClass::makeOTP();

                $write_otp = UserOtp::create([
                'user_id'   => $user->pk_int_user_id,
                'otp' => $otp, 
                'otp_type' => "reset_password", 
                ]);

                $curl = curl_init();

                curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.getlead.co.uk/api/pushsms?username=918453555000&token=gl_d52aa6241238b4e44d9b&sender=GTLEAD&to='.$number.'&message=Hi+'.$otp.'+is+the+OTP+for+your+request+for+reset+password+through+Getlead.&priority=11',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Cookie: XSRF-TOKEN=eyJpdiI6Ik5BTHY1SGJqeTd6WFpySjNtcDNaS3c9PSIsInZhbHVlIjoiZUF1dnNpNEJCOTQ4XC9NTkFoMEVDV0UzVHlzTWpGeUc3aUlpY1hXRDBhekxvNWV0QzM0NTJYb0ZHc0c5S011Y1QiLCJtYWMiOiIzMzQ2YmQ1NDI0YTAzZDc1MWMwMjQwNGIzZTRhZWM4YTNmZjIzZWU4OWEyMzFkZWUzZWJjNjNiZWVjNTgxYWI4In0%3D; laravel_session=eyJpdiI6InVhRzgzc2lWaUx1OWI1TVZXQ05EcVE9PSIsInZhbHVlIjoidjNkc1V0d0hFYmYrRTlRaUdzODIxbkgranZUWGo2UDlHSnRLT3pHaVZybU1LaFE1djdhZG1nRTljam5YYXh4OCIsIm1hYyI6IjRmYmE0MTlmZmJkMGE5ZGNlZGJkYWNiMWNjYTAzZWNmN2FiMmZlNDgyMWJmYTJkODMzNjFhY2Q5YWRlM2Q4ODgifQ%3D%3D'
                ),
                ));
    
                $response = curl_exec($curl);
    
                curl_close($curl);

                return response()->json(['status'=>1,'message'=>'Success','user_id' => $user->pk_int_user_id,'data'=>'Otp sent !!!']);

    }

    public function verifyOtp(Request $request)
    {
        $rules = [
            'otp' => 'required',
            'mobile' => 'required|exists:tbl_users|digits:10',
            'country_code' => 'required',      
          ];

        $input = $request->only('otp','mobile','country_code');
        $validator = Validator::make($input, $rules);

        $user = User::where('mobile',$request->mobile)->first();

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->messages()]);
        }

        if($user)
        {
            $data = UserOtp::where('user_id',$user->pk_int_user_id)->where('otp',$request->otp)->first();
    
            if($data)
            {
                return response()->json(['status'=>1,'message'=>'Success','data'=>'Otp Verified']);
            }
            else
            {
                return response()->json(['status' => 0, 'message'=>'Incorrect data passed']);
            }
        } 
        else{
               return response()->json(['status' => 0, 'message'=>'User not found !!!']);
        }       
    }

    public function changePassword(Request $request)
    {
        $rules = [
            'password' => 'required',
            'mobile' => 'required|exists:tbl_users|digits:10',
            'country_code' => 'required',  
        ];

        $user_id = User::where('mobile',$request->mobile)->first()->pk_int_user_id;

        $input = $request->only('password','mobile','country_code');
        $validator = Validator::make($input, $rules);
    
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->messages()]);
        }

        $user =  User::find($user_id);
        $user->password = bcrypt($request->password);
        $user->save();

        return response()->json(['status'=>1,'message'=>'Success','data'=>$user]);
    }

    public function updatePassword(Request $request)
    {
        $rules = [
            'old_password' => 'required',
            'new_password' => 'required',
        ];

        $vendorId = $this->applicationObj->getVendorId(Auth::user()->pk_int_user_id);

        $input = $request->only('old_password','new_password');
        $validator = Validator::make($input, $rules);
    
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->messages()]);
        }

        $user = User::find($vendorId);

        if(Hash::check($request->old_password, $user->password))
        {
            $user->password = bcrypt($request->new_password);
            $user->save();

            return response()->json(['status'=>1,'message'=>'Success','data'=>$user]);
        }
        else
        {
            return response()->json(['status' => 0, 'message' => 'Old password does not match !!!']);
        }

    }

    public function listPlans(Request $request)
    {
       $plans = DB::table('plan_services')
                  ->where('service','GL Scratch')
                  ->leftJoin('plans','plans.id','plan_services.plan_id')
                  ->whereIn('plans.id',['277','278','279'])
                  ->select('plans.id as plan_id','plan_services.id as plan_services_id','service','name','description','count','currency_type','tax','tax_inclusive',DB::raw('CAST(tax_amount AS CHAR) AS tax_amount'),DB::raw('CAST(net_amount AS CHAR) AS net_amount'))
                  ->latest('plans.created_at')
                  ->get();

       return response()->json(['status'=>1,'message'=>'Success','data'=>$plans]);
    }

    public static function purchasePlan(Request $request)
    {  
        $rules = [
            'plan_id' => 'required',
            'amount' => 'required'
        ];

        if($request->has('vendor'))
        {
            $vendorId = $request->vendor;
        }
        else{
            $vendorId = $this->applicationObj->getVendorId(Auth::user()->pk_int_user_id);
        }

        $input = $request->only('plan_id','amount');
        $validator = Validator::make($input, $rules);
    
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->messages()]);
        }

        DB::table('tbl_user_subscription')->insert(
            ['fk_int_user_id' => $vendorId,
             'fk_int_plan_id' => $request->plan_id,
             'date_plan_from_date' => today(),
             'date_plan_to_date' => today(),
             'created_at' => now(),
             ]
        );


        if($request->has('payment_id'))
        {
            DB::table('tbl_payment')->insert(
                ['fk_int_user_id' => $vendorId,
                 'fk_razorpay_payment_id' => $request->payment_id,
                 'vchr_amount' => $request->amount,
                 'int_status' => 1,
                 'fk_int_service_id' => 1,
                 'created_at' => now(),
                 ]
            );

            $plan = DB::table('plans')->where('id',$request->plan_id)->first();
            $plan_details = ['plan_name' => $plan->name, 'amount' => $request->amount, 'tax' => $plan->tax, 'duration' => $plan->duration];

            DB::table('tbl_payment_status')->insert(
                ['vendor_id' => $vendorId,
                 'plan_id' => $request->plan_id,
                 'plan_details' => json_encode($plan_details),
                 'amount' => $request->amount,
                 'date_time' => now(),
                 'created_at' => now(),
                 ]
            );
        }

        return response()->json(['status'=>1,'message'=>'Success']);
    }

    public function getActivePlan()
    {
        $vendorId = $this->applicationObj->getVendorId(Auth::user()->pk_int_user_id);

        $active_plan = DB::table('tbl_user_subscription')->where('fk_int_user_id',$vendorId)->latest()->get();
        
        if($active_plan->count())
        {
            $plan_id = $active_plan->first()->fk_int_plan_id;
            $plan = DB::table('plan_services')
                  ->leftJoin('plans','plans.id','plan_services.plan_id')
                  ->where('plans.id',$plan_id)
                  ->select('plans.id as plan_id','plan_services.id as plan_services_id','service','name','description','count','currency_type','tax','tax_inclusive',DB::raw('CAST(tax_amount AS CHAR) AS tax_amount'),DB::raw('CAST(net_amount AS CHAR) AS net_amount'))
                  ->latest('plans.created_at')->get()->take(1);

            $balance = ScratchOffersListing::where('fk_int_user_id',$vendorId)->sum('int_scratch_offers_balance');

            $plan->map(function($item,$key) use($balance,$active_plan)
            {
                $item->scratch_balance = (int)$balance;
                $item->plan_purchased_on = $active_plan[$key]->created_at;
                return $item;
            });

            return response()->json(['status'=>1,'message'=>'Success','data'=>$plan]);
        }
        else
        {
            return response()->json(['status' => 1, 'message' => 'No active plans found !!!','data'=>[]]);
        }
    }

    public function redeemHistory(Request $request)
    {
        $vendorId = $this->applicationObj->getVendorId(Auth::user()->pk_int_user_id);
        
        $list_id = [];
        if($request->has('campaign_id'))
        {
            $list_id = ScratchOffersListing::where('fk_int_scratch_offers_id',$request->campaign_id)
                                            ->pluck('pk_int_scratch_offers_listing_id');
        }
        $redeem_history = ScratchWebCustomer::where('user_id',$vendorId)
                                            ->where(function($p) use($request,$list_id)
                                            {
                                                $request->has('campaign_id') ? $p->whereIn('offer_list_id',$list_id):'';
                                            })
                                            ->where(function($q) use($request)
                                            {
                                                $request->search_key ? $q->where('name', 'like', "%{$request->search_key}%")
                                                ->orWhere('mobile', 'like', "%{$request->search_key}%"):'';
                                            })
                                            ->orderBy('id','desc')
                                            ->get();
                                            
        return response()->json(['status'=>1,'message'=>'Success','data'=>$redeem_history]);
    }

    public function sentRedeemOtp(Request $request)
    {
        $rules = [
            'mobile' => 'required|digits:10',
            'country_code' => 'required',
        ];

        $input = $request->only('mobile','country_code');
        $validator = Validator::make($input, $rules);
    
        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->messages()]);
        }
            // $user = User::where('mobile',$request->mobile)->first();
            $number = $request->country_code.$request->mobile;

            $otp = CustomClass::makeOTP();

            $write_otp = UserOtp::create([
            'otp' => $otp, 
            'number' => $request->mobile,
            'otp_type' => "redeem_otp", 
            ]);

            $curl = curl_init();

            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://app.getlead.co.uk/api/pushsms?username=918453555000&token=gl_d52aa6241238b4e44d9b&sender=GTLEAD&to='.$number.'&message=Hi+'.$otp.'+is+the+OTP+for+your+request+for+reset+password+through+Getlead.&priority=11',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Cookie: XSRF-TOKEN=eyJpdiI6Ik5BTHY1SGJqeTd6WFpySjNtcDNaS3c9PSIsInZhbHVlIjoiZUF1dnNpNEJCOTQ4XC9NTkFoMEVDV0UzVHlzTWpGeUc3aUlpY1hXRDBhekxvNWV0QzM0NTJYb0ZHc0c5S011Y1QiLCJtYWMiOiIzMzQ2YmQ1NDI0YTAzZDc1MWMwMjQwNGIzZTRhZWM4YTNmZjIzZWU4OWEyMzFkZWUzZWJjNjNiZWVjNTgxYWI4In0%3D; laravel_session=eyJpdiI6InVhRzgzc2lWaUx1OWI1TVZXQ05EcVE9PSIsInZhbHVlIjoidjNkc1V0d0hFYmYrRTlRaUdzODIxbkgranZUWGo2UDlHSnRLT3pHaVZybU1LaFE1djdhZG1nRTljam5YYXh4OCIsIm1hYyI6IjRmYmE0MTlmZmJkMGE5ZGNlZGJkYWNiMWNjYTAzZWNmN2FiMmZlNDgyMWJmYTJkODMzNjFhY2Q5YWRlM2Q4ODgifQ%3D%3D'
            ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            return response()->json(['status'=>1,'message'=>'Success','data'=>'Otp sent !!!']);
    }

    public function verifyRedeemOtp(Request $request)
    {
        $rules = [
            'otp' => 'required',
            'mobile' => 'required|digits:10',
            'country_code' => 'required',      
            'gift_id' => 'required'      
          ];

        $input = $request->only('otp','mobile','country_code','gift_id');
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return response()->json(['status' => 0, 'message' => $validator->messages()]);
        }

        $user = UserOtp::where('number',$request->mobile)->where('otp',$request->otp)->first();
        

        if($user)
        {
            $redeem_gift = ScratchWebCustomer::where('id',$request->gift_id)->first();
            $redeem_gift->redeem = 1;
            $redeem_gift->redeemed_on = now();
            $redeem_gift->save();
            return response()->json(['status'=>1,'message'=>'Success','data'=>'Otp Verified']);
        } 
        else{
               return response()->json(['status' => 0, 'message'=>'Incorrect Data!!!']);
        } 
    }

}
