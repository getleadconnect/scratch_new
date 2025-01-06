<?php

namespace App\Models;

use App\AutomationRule;
use App\BackendModel\EnquiryType;
use App\BackendModel\FeedbackStatus;
use App\FrontendModel\AssignAgent;
use App\FrontendModel\LeadAdditionalDetails;
use App\FrontendModel\LeadAdditionalField;
use App\Mail\StatusMailSend;
use Getlead\Messagebird\Common\GupShup;
use Getlead\Messagebird\Models\WatsappCredential;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\BackendModel\EnquiryPurpose;
use App\Common\Common;
use App\User;
use App\BackendModel\EnquiryFollowup;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Propaganistas\LaravelPhone\PhoneNumber;
use App\CustomField;
use App\CustomFieldValue;
use Illuminate\Support\Str;
use Mail;
use App\Agency;
use App\Mail\NewLeadAdded;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Auth;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Jobs\SendNotification;
// use App\Jobs\SendPusherNotification;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Getlead\Sales\Models\Order;
use Getlead\Sales\Models\PaymentCollection;
use Getlead\Sales\Models\EnquirySalesField;
use App\BackendModel\District;
use App\IvrExtension;
use App\Task;
use App\Deal;
use App\Events\LeadAdded;
use Swift_SmtpTransport;
use Swift_Mailer;
use Illuminate\Mail\Mailer;
use Illuminate\Contracts\Encryption\DecryptException;
use App\BackendModel\MailConfiguration;
use App\BackendModel\LeadType;
use App\CallMaster;
use App\Common\Notifications;
use App\Common\Variables;
use App\Constants\MarbleGallery;
use App\Events\ApiHistoryPost;
use App\Events\CreateFollowup;
use App\Events\SendPusherNotification;
use App\Facades\AutomationFacade;
use Getlead\Campaign\Models\LeadCampaign;
use App\PusherSetting;
use Illuminate\Support\Facades\Crypt;
use DB;
use Illuminate\Support\Facades\Request;

class Enquiry extends Authenticatable implements JWTSubject
{
    //
    use SoftDeletes;

    const SHOW = 0;
    const NOTSHOW = 1;

    protected $dates = ['deleted_at'];
    protected $primaryKey = 'pk_int_enquiry_id';
    protected $table = 'tbl_enquiries';

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s', // Adjust the format according to your needs
        'updated_at' => 'datetime:Y-m-d H:i:s', // Adjust the format according to your needs
    ];

    protected $fillable = [
        'fk_int_user_id', 'vchr_customer_name', 'vchr_customer_company_name', 'vchr_customer_mobile', 'vchr_customer_email',
        'vchr_enquiry_feedback', 'fk_int_enquiry_type_id', 'fk_int_purpose_id', 'date_of_birth', 'feedback_status',
        'country_code', 'landline_number', 'event_date', 'more_phone_numbers', 'staff_id', 'designation_id','deleted_by',
        'mobile_no', 'address', 'new_status', 'created_at', 'updated_at', 'lead_type_id', 'purchase_date', 'exp_wt_grams', 'function_date',
        'otp', 'otp_validity', 'password', 'otp_attempts', 'otp_attempt_date', 'district_id', 'taluk_id', 'next_follow_up', 'assigned_date', 'read_status','customer_code','email_sync'
    ];

    public static $rules = [
        'fk_int_enquiry_type_id' => 'required',


        //  'fk_int_purpose_id' => 'required',
        //  'vchr_customer_name' => 'required|regex:/^[A-Za-z. -]+$/|max:255',
        //  //'vchr_customer_company_name' => 'required|regex:/^[A-Za-z. -]+$/|max:255',
        // // 'vchr_customer_mobile' => 'required|digits:10',
        //  'vchr_customer_company_name' => 'required|max:255', //regex:/^[A-Za-z. -]+$/|
        'vchr_customer_mobile' => 'required|digits_between:6,14',//|unique:tbl_enquiries,mobile_no',

        // 'vchr_customer_email' => 'email',
        // 'vchr_enquiry_feedback' => 'required',
        // 'landline_number' => 'digits_between:0,30',
        // 'feedback_status' => 'required',
        //  'more_phone_numbers'=>'digits_between:0,30',
    ];

    public static $rulesMessage = [
        'fk_int_enquiry_type_id.required' => 'Enquiry Source is required.',
        // 'fk_int_purpose_id.required' => 'Enquiry purpose  is required.',
        // 'vchr_customer_name.required' => 'Customer name is required.',
        // 'vchr_customer_name.regex' => 'Customer name is should be string.',
        // 'vchr_customer_company_name.required' => 'Company name is required.',
        //  'vchr_customer_company_name.regex' => 'Company name should be string.',
        // 'feedback_status.required' => 'Enquiry status is required.',
        'vchr_customer_mobile.required' => 'Mobile is required.',
        // 'vchr_customer_mobile.numeric' => 'Mobile number must be digits',
        'vchr_customer_mobile.digits_between' => 'Mobile number must be between 6 to 14 digits',
        'vchr_customer_mobile.unique' => 'Lead already exist with same mobile number.',
        //  'vchr_customer_email.required' => 'Email is required.',
        //  'vchr_customer_email.email' => 'please enter the correct email format.',
        // 'vchr_enquiry_feedback.required' => 'Feedback is required.',
        // 'more_phone_numbers.numeric'=>'Phone Numbers must be digits',
        // 'landline_number.digits_between' => "Landline number must be digits",

    ];


    public static $rulesUpdate = [
        // 'fk_int_purpose_id' => 'required',
        // 'vchr_customer_name' => 'required|regex:/^[A-Za-z. -]+$/|max:255',
        // 'vchr_customer_company_name' => 'required|max:255',//regex:/^[A-Za-z. -]+$/|
        'vchr_customer_mobile' => 'required',//|unique:tbl_enquiries,mobile_no',
//        'vchr_customer_mobile' => 'sometimes|digits_between:8,14',
        // 'vchr_customer_email' => 'email',
        // 'landline_number'=>'numeric',
        // 'landline_number' => 'digits_between:0,30',

    ];

    public static $rulesMessageUpdate = [
        // 'fk_int_purpose_id.required' => 'Enquiry purpose  is required.',
        // 'vchr_customer_name.required' => 'Customer name is required.',
        // 'vchr_customer_name.regex' => 'Customer name is should be string.',
        // 'vchr_customer_company_name.required' => 'Company name is required.',
        //  'vchr_customer_company_name.regex' => 'Company name should be string.',
        'vchr_customer_mobile.required' => 'Mobile is required.',
        // 'vchr_customer_mobile.unique' => 'Lead already exist with same mobile number.',
        //  'vchr_customer_email.required' => 'Email is required.',
        // 'vchr_customer_email.email' => 'please enter the correct email format.',
        // 'more_phone_numbers.numeric'=>'Phone Numbers must be digits',
        // 'landline_number.numeric'=>"Landline number must be digits",
        // 'vchr_customer_mobile.digits_between' => 'Mobile number must be between 8 to 15 digits.',
        // 'landline_number.digits_between' => "Landline number must be digits",
    ];
    public const DISPLAY_FIELDS=[
        [
            "id" => 1,
            "caption" => "Name",
            "code" => "name"
        ],
        [
            "id" => 2,
            "caption" => "Phone",
            "code" => "phone"
        ],
        [
            "id" => 3,
            "caption" => "Assigned To",
            "code" => "assigned_to"
        ],
        [
            "id" => 4,
            "caption" => "Purpose",
            "code" => "purpose"
        ],
        [
            "id" => 5,
            "caption" => "Type",
            "code" => "type"
        ],
        [
            "id" => 6,
            "caption" => "Status",
            "code" => "status"
        ],
        [
            "id" => 7,
            "caption" => "Source",
            "code" => "source"
        ],
        [
            "id" => 8,
            "caption" => "Email",
            "code" => "vchr_customer_email"
        ],
        [
            "id" => 9,
            "caption" => "Address",
            "code" => "address"
        ],
        [
            "id" => 10,
            "caption" => "Mobile",
            "code" => "mobile"
        ],
        [
            "id" => 11,
            "caption" => "Created Date",
            "code" => "created_date"
        ],
        [
            "id" => 12,
            "caption" => "Updated Date",
            "code" => "updated_date"
        ],
        [
            "id" => 13,
            "caption" => "Created By",
            "code" => "created_by"
        ],
        [
            "id" => 14,
            "caption" => "Next Followup",
            "code" => "next_followup"
        ],
        [
            "id" => 15,
            "caption" => "Assigned Date",
            "code" => "assigned_date"
        ]
        // [
        //     "id" => 15,
        //     "caption" => "ID",
        //     "code" => "id"
        // ]
    ];

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone(auth()->user()->time_zone ?? 'Asia/Kolkata');
    }
    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone(auth()->user()->time_zone ?? 'Asia/Kolkata');
    }

    public static function boot() {

	    parent::boot();

	    static::created(function($item) {
            if($item->fk_int_user_id != 1344)
                event(new LeadAdded($item));

            if($item->fk_int_user_id == MarbleGallery::MARBLE_GALLERY_ID){
                $settings = Settings::where('vchr_settings_type','Lead Prefix')
                       ->select('pk_int_settings_id','vchr_settings_value')
                        ->where('fk_int_user_id',$item->fk_int_user_id)
                        ->first();
            
                if($settings){
                    $lastCustomer = Enquiry::select('pk_int_enquiry_id','customer_code','fk_int_user_id')->where('fk_int_user_id',$item->fk_int_user_id)->latest()->skip(1)->first();
                    if($lastCustomer->customer_code){
                            $newCode = self::incrementCode($lastCustomer->customer_code,$settings->vchr_settings_value);
                            $item->customer_code = $newCode;
                            $item->update();

                            if($item->fk_int_user_id == MarbleGallery::MARBLE_GALLERY_ID){ // custom code for marble gallery
                            $addtionalField = $lastCustomer->additional_details()->where('field_name','Customer code')->where('enquiry_id',$item->pk_int_enquiry_id)->first();
                            if($addtionalField){
                                    $addtionalField->value = $newCode;
                                    $addtionalField->save();
                            }else{
                                    $additionalDetails = new LeadAdditionalDetails();
                                    $additionalDetails->enquiry_id = $item->pk_int_enquiry_id;
                                    $additionalDetails->field_id = 352; // Customer code additional field id : 352
                                    $additionalDetails->field_name = 'Customer code';
                                    $additionalDetails->value = $newCode;
                                    $additionalDetails->created_by = $item->fk_int_user_id;
                                    $additionalDetails->save();
                            }
                            }

                    }else{
                        $code = '00001';
                        $item->customer_code = $settings->vchr_settings_value . (string)$code;
                        $item->update();

                        if($item->fk_int_user_id == MarbleGallery::MARBLE_GALLERY_ID){ // custom code for marble gallery
                            $addtionalField = $lastCustomer->additional_details()->where('field_name','Customer code')->where('enquiry_id',$item->pk_int_enquiry_id)->first();
                            if($addtionalField){
                                $addtionalField->value = $settings->vchr_settings_value . (string)$code;
                                $addtionalField->save();
                            }else{
                                $additionalDetails = new LeadAdditionalDetails();
                                $additionalDetails->enquiry_id = $item->pk_int_enquiry_id;
                                $additionalDetails->field_id = 352; // Customer code additional field id : 352
                                $additionalDetails->field_name = 'Customer code';
                                $additionalDetails->value = $settings->vchr_settings_value . (string)$code;
                                $additionalDetails->created_by = $item->fk_int_user_id;
                                $additionalDetails->save();
                        }
                        }
                    }
                }
            }
            
	    });
	}

    private static function incrementCode($code,$prefix)
    {
        $codeNumber = (int) substr($code, 2);
        $codeNumber++;
        $paddedNumber = str_pad($codeNumber, 5, '0', STR_PAD_LEFT);
        
        return $prefix . $paddedNumber;
    }

    public static function getCRMUsers($type, $mobileno, $userid,$request=null)
    { 
        $vendor_id = User::getVendorIdApi($userid);
        $show = new Common();
        $enquiryType = EnquiryType::where('vchr_enquiry_type', $type)->where(function ($where) use ($vendor_id) {
            $where->where('fk_int_user_id', $vendor_id);
            $where->orWhere('vendor_id', $vendor_id);
        })->first();
        if ($enquiryType) {
            $typeId = $enquiryType->pk_int_enquiry_type_id;
        } else {
            $enType = new EnquiryType();
            $enType->vchr_enquiry_type = $type;
            $enType->fk_int_user_id = $vendor_id;
            $enType->vendor_id = $vendor_id;
            $enType->int_status = "1";
            $fl = $enType->save();
            $typeId = $enType->pk_int_enquiry_type_id;
        }

        $feedback_status = FeedbackStatus::where('vchr_status', 'New')->where(function ($where) use ($vendor_id) {
            $where->where('fk_int_user_id', $vendor_id);
        })->first();
        if (!$feedback_status) {
            $feedback_status = new FeedbackStatus();
            $feedback_status->vchr_status = 'New';
            $feedback_status->vchr_color = '#000000';
            $feedback_status->fk_int_user_id = $vendor_id;
            $feedback_status->created_by = $vendor_id;
            $feedback_status->save(); 
        }
        $statusId = $feedback_status->pk_int_feedback_status_id;
        
        $mobileno = str_replace('+', '', $mobileno);
        $phoneno = $mobileno;
        $code = null;
        try {
            $getCode = self::splitString($mobileno,2);
            $code = $getCode[0];
            $phoneno = $getCode[1];
        } catch (\Exception $exp) {
            $phoneno = substr($mobileno, 2);
            $code = null;
        }
        $requestMobile = '%'.$mobileno;
        $mobnoExist = Enquiry::select(
            'pk_int_enquiry_id',
            'feedback_status',
            'vchr_customer_name',
            'vchr_customer_email',
            'vchr_customer_mobile',
            'lead_attension',
            'more_phone_numbers'
        )
        ->where('fk_int_user_id', $vendor_id)
        ->where(function ($where) use ($phoneno,$requestMobile) {
            $where->where('vchr_customer_mobile','LIKE', $requestMobile)
            ->orWhere('more_phone_numbers', 'like', $requestMobile . '%')
            ->orWhere('mobile_no', $phoneno);
        })->first();

        if (!$mobnoExist) {
            $feedback = new Enquiry();
            $feedback->vchr_customer_mobile = $mobileno;
            $feedback->mobile_no = $phoneno;
            $feedback->country_code = $code;
            $feedback->read_status = 0;
            $feedback->fk_int_user_id = $vendor_id;
            $feedback->fk_int_enquiry_type_id = $typeId;
            $feedback->feedback_status = $statusId;
            $agent = AssignAgent::where('vendor_id', $vendor_id)->first();
            if ($agent && $type != EnquiryType::IVR) {
                $feedback->staff_id = $agent->agent_id;
                $feedback->assigned_date=Carbon::now();
            }else{
                $feedback->staff_id = null;
            }
            $flag = $feedback->save();
            try{
                Enquiry::newLeadFunctions($feedback->pk_int_enquiry_id);
            }catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
            try {
                event(new ApiHistoryPost(1,$feedback->pk_int_enquiry_id,0,$vendor_id,$typeId,2));
            } catch (\Exception $e) {
                \Log::info($e->getMessage());
            }
            // $show->showCrmUsersSubscription($userid);
            /**-------------------AUTOMATION_START-------------------WEBHOOK-----------------**/
            $automation_rule_webhook = $show->getRule($vendor_id, 'new_lead', 'webhook',$feedback->fk_int_enquiry_type_id);
            $status = FeedbackStatus::where('pk_int_feedback_status_id', $feedback->feedback_status)->select('vchr_status')->first(); 
            $post_data = [
                'customer_name' => $feedback->vchr_customer_name,
                'email' => $feedback->vchr_customer_email,
                'status' => ($status)? $status->vchr_status : "New Status",
                'phone' => $feedback->vchr_customer_mobile,
                'mobile' => $feedback->mobile_no,
                'flag' => "new_lead",
            ];
            if (count($automation_rule_webhook) > 0) {
                foreach($automation_rule_webhook as $w_hook){
                    if ($w_hook->webhook_id != NULL) {
                        try {
                            $webHook = $show->getWebHookById($w_hook->webhook_id);
                        } catch (\Exception $e) {
                            \Log::info($e->getMessage());
                        }
                        
                        if ($webHook) {
                            try {
                                $show->postToWebHook($webHook->url, $post_data);
                            } catch (\Exception $e) {
                                \Log::info($e->getMessage());
                            }
                        }
                    }
                }
            }
            $user = User::select('email')->find($vendor_id);
            // $show->showCrmUsersSubscription($userid);
            try {
                if(Common::checkEmailNotification($vendor_id)){
                    Mail::to($user->email)->send(new NewLeadAdded($feedback));
                }

            } catch (\Exception $exp) {

            }

            /**--API---------------------------------------------**/
            // $source = $feedback->fk_int_enquiry_type_id;
            // $automation_rule_api = $show->getRule(User::getVendorIdApi($userid), 'new_lead', 'api',$source);
            // $enquiryNotif = Enquiry::select('pk_int_enquiry_id','staff_id','fk_int_user_id')->find($feedback->pk_int_enquiry_id);
            // if ($automation_rule_api && $automation_rule_api->api != null) {
            //     try {
            //        //
            //     } catch (\Exception $e) {
            //         \Log::info($e->getMessage());
            //     }
            // }
            /**--API---------------------------------------------**/

            return $feedback->pk_int_enquiry_id;
        } else {
            try {
                event(new ApiHistoryPost(1,$mobnoExist->pk_int_enquiry_id,1,$vendor_id,$typeId,2));
            } catch (\Exception $e) {
                \Log::info($e->getMessage());
            }
            // $show->showCrmUsersSubscription($userid);
            /**-------------------AUTOMATION_START-------------------WEBHOOK-----------------**/
            $automation_rule_webhook = $show->getRule($vendor_id, 'new_lead', 'webhook',$mobnoExist->fk_int_enquiry_type_id);
            $status = FeedbackStatus::where('pk_int_feedback_status_id', $mobnoExist->feedback_status)->select('vchr_status')->first();
            $post_data = [
                'customer_name' => $mobnoExist->vchr_customer_name,
                'email' => $mobnoExist->vchr_customer_email,
                'status' => ($status)? $status->vchr_status : "New Status",
                'phone' => $mobnoExist->vchr_customer_mobile,
                'mobile' => $mobnoExist->mobile_no,
                'flag' => "new_lead",
            ];
            if (count($automation_rule_webhook) > 0) {
                foreach($automation_rule_webhook as $w_hook){
                    if ($w_hook->webhook_id != NULL) {
                        $webHook = $show->getWebHookById($w_hook->webhook_id);
                        if ($webHook) {
                            try{
                                $show->postToWebHook($webHook->url, $post_data);
                            }catch(\Exception $e){
                                \Log::info($e->getMessage());
                            }
                        }
                    }
                }
            }
    
            if($mobnoExist->lead_attension != 1){
                if(request('Direction') != 'Outbound' && request('callType') == 0){
                    //Update lead attension
                    $mobnoExist->lead_attension = 1;
                    $mobnoExist->updated_at = now();
                    $mobnoExist->update();
    
                    $message = $mobnoExist->vchr_customer_name.' tried to contact via call';
                    if ($message) {
                        try {
                            event(new CreateFollowup($message, EnquiryFollowup::TYPE_NOTE, $mobnoExist->pk_int_enquiry_id, $vendor_id));
                        } catch (\Exception $e) {
                            \Log::info($e->getMessage());
                        }
                    }
                }
            }
            else
            {
                $mobnoExist->updated_at = now();
                $mobnoExist->update();
            }
                
            /**--WEBHOOK---------------------------------------------**/
            return $mobnoExist->pk_int_enquiry_id;
        }
    }

    public static function getCRMWebsiteUsers($type, $mobileno, $userid, $name, $email, $feedbacks, $countrycode, $companyName, $request = null)
    {
    //    \Log::error($userid);
        $vendor_id = User::getVendorIdApi($userid);
        $staff_id = $statusId = $leadTypeId = $fk_int_purpose_id = null;
        $campaign_automation = false;
        $agency= Agency::select('id')->where('token',$request->agency)->first();
        $agency_id=$agency?$agency->id:null;
        $countrycode =  str_replace("+","",$countrycode);
        $mobnoExist = Enquiry::where('fk_int_user_id', $vendor_id)
                    ->where(function($q) use($countrycode , $mobileno){
                        $q->where('vchr_customer_mobile', $countrycode.$mobileno)->orWhere('mobile_no', $mobileno);    
                    })
                    ->lockForUpdate()
                    ->first();
                    
        $enquiryType = EnquiryType::select('pk_int_enquiry_type_id','vchr_enquiry_type','fk_int_user_id','vendor_id')
                            ->where('vchr_enquiry_type', $type)->where(function ($where) use ($vendor_id) {
                                $where->where('fk_int_user_id', $vendor_id);
                                $where->orWhere('vendor_id', $vendor_id);
                            })->first();
                            
        if ($enquiryType) {
            $typeId = $enquiryType->pk_int_enquiry_type_id;
        } else {
            $enType = new EnquiryType();
            $enType->vchr_enquiry_type = $type;
            $enType->fk_int_user_id = $vendor_id;
            $enType->vendor_id = $vendor_id;
            $enType->int_status = "1";
            $fl = $enType->save();
            $typeId = $enType->pk_int_enquiry_type_id;
        }
        
        if (request()->has('status') && request()->filled('status')) {
            $feedback_status = FeedbackStatus::select('vchr_status','fk_int_user_id','pk_int_feedback_status_id')
            ->where('vchr_status', $request->status)->where(function ($where) use ($vendor_id) {
                $where->where('fk_int_user_id', $vendor_id);
            })->first();
            if (!$feedback_status) {
                $feedback_status = new FeedbackStatus();
                $feedback_status->vchr_status = $request->status;
                $feedback_status->vchr_color = '#000000';
                $feedback_status->fk_int_user_id = $vendor_id;
                $feedback_status->created_by = $vendor_id;
                $feedback_status->save(); 
            }
            $statusId = $feedback_status->pk_int_feedback_status_id;
        }
        
       
        if(request()->has('type') && request()->filled('type')){
            $leadType = LeadType::where('name', $request->type)->where(function ($where) use ($vendor_id) {
                $where->where('vendor_id', $vendor_id);
            })->first();
            if(!$leadType){
                $leadType = new LeadType();
                $leadType->name = $request->type;
                $leadType->vendor_id = $vendor_id;
                $leadType->created_by = $vendor_id;
                $leadType->save();
            }
            $leadTypeId = $leadType->id;
        }

        if (request()->has('purpose') && request()->filled('purpose')) {
            $purpose = EnquiryPurpose::where('vchr_purpose', $request->purpose)
                            ->where('fk_int_user_id', $vendor_id)->first();
            if ($purpose) {
                $fk_int_purpose_id = $purpose->pk_int_purpose_id;
            } else {
                $purpose = new EnquiryPurpose();
                $purpose->vchr_purpose = $request->purpose;
                $purpose->vchr_purpose_description = $request->purpose;
                $purpose->fk_int_user_id = $vendor_id;
                $purpose->created_by = $vendor_id;
                $purpose->save();
                $fk_int_purpose_id = $purpose->pk_int_purpose_id;
            }
        }
        
        if($type == 'IVR'){
            $agent = AssignAgent::where('vendor_id', $vendor_id)->first();
            $staff_id =  ($agent) ? $agent->agent_id : null;
        }
        
        if ($request && $request->staff_name) {
            $staff = User::where('vchr_user_name', $request->staff_name)->where('parent_user_id', $vendor_id)->first();
            $staff_id =  ($staff) ? $staff->pk_int_user_id : null;
        }else
        {

        }

        if($type==EnquiryType::GLSCRATCH && request('userid')){
            $staff_id = request('userid');
        }
        if (!$mobnoExist) {
            DB::beginTransaction();
            $feedback = new Enquiry();
            $feedback->vchr_customer_mobile = $countrycode . $mobileno;
            $feedback->mobile_no = $mobileno;
            $feedback->country_code = $countrycode;
            $feedback->read_status = 0;
            $feedback->agency_id=$agency_id;
            if ($staff_id){
                $feedback->staff_id = $staff_id;
                $feedback->assigned_date=Carbon::now();}
            if ($request && isset($request['date_of_birth']))
                $feedback->date_of_birth = $request->date_of_birth;
            if ($request && isset($request['purpose']))
                $feedback->fk_int_purpose_id = $fk_int_purpose_id;

            $feedback->fk_int_user_id = $vendor_id;
            $feedback->fk_int_enquiry_type_id = $typeId;
            $feedback->feedback_status = $statusId;
            $feedback->vchr_customer_name = $name;
            $feedback->vchr_customer_email = $email;
            $feedback->vchr_enquiry_feedback = $feedbacks;
            $feedback->address = request('address') ?? '';
            $feedback->created_by = request('userid') ?? $userid;
            $feedback->vchr_customer_company_name = $companyName;
            $feedback->lead_type_id = $leadTypeId;
            $flag = $feedback->save();
            DB::commit();

            $enquiry_id = $feedback->pk_int_enquiry_id;
            try {
                event(new ApiHistoryPost(1,$enquiry_id,0,$vendor_id,$typeId,2));
            } catch (\Exception $e) {
                Log::info($e->getMessage());
            }
           
       if($userid==1265){$enq = Enquiry::select('pk_int_enquiry_id','staff_id','assigned_date')->find($enquiry_id); \Log::error('ApiHistoryPost-staff'.$enq->staff_id);}
            if (isset($request->lead_note) && $request->lead_note != '') {
                try {
                    event(new CreateFollowup($request->lead_note, EnquiryFollowup::TYPE_NOTE, $feedback->pk_int_enquiry_id, $vendor_id));
                } catch (\Exception $e) {
                    Log::info($e->getMessage());
                }
            }

            $show = new Common();
            $addional_fields = LeadAdditionalField::where('vendor_id', $vendor_id)->get();
            foreach ($addional_fields as $add_field) {
                try {
                    $modifiedString = str_replace(" ", "_", $add_field->field_name);
                    if (request()->has($add_field->field_name) || request()->has($modifiedString)) {
                        if($add_field->input_type == 8){
                            LeadAdditionalDetails::updateOrCreate(['enquiry_id' => $enquiry_id,
                            'field_id' => $add_field->id],
                            ['field_name' => $add_field->field_name, 'value' => json_encode(explode(',',$request[$add_field->field_name] ?? $request[$modifiedString])),
                                'created_by' => $userid]);
                        }else{
                            LeadAdditionalDetails::updateOrCreate(['enquiry_id' => $enquiry_id,
                            'field_id' => $add_field->id],
                            ['field_name' => $add_field->field_name, 'value' => $request[$add_field->field_name] ?? $request[$modifiedString],
                                'created_by' => $userid]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::info('Enquiry model additional field issue');
                    Log::info(request()->all());
                }
            }
            
            /**--AUTOMATION_WHATSAPP---------------------------------------------**/
                $automation_whatsapp = AutomationRule::where('vendor_id', $feedback->fk_int_user_id)
                    ->where('trigger', 'new_lead')
                    ->where('action', 'whatsapp')
                    ->where('enquiry_source_id', $feedback->fk_int_enquiry_type_id)
                    ->orderBy('id', 'DESC')
                    ->first();

                if ($automation_whatsapp) {
                    $whatsappTemplate = WhatsappTemplate::where('pk_int_whatsapp_template_id', $automation_whatsapp->whatsapp_template_id)
                        ->select('text_whatsapp_template_description')->first();
                    if ($whatsappTemplate) {
                        $gupshupObj = new GupShup();
                        $credientails = WatsappCredential::where('vendor_id', $feedback->fk_int_user_id)
                            ->where('status', 1)
                            ->where('platform_id', 2)
                            ->first();
                        if ($credientails) {
                            $data = [
                                "api_key" => $credientails->access_key,
                                "from_number" => $credientails->source_mobile_num,
                                "app_name" => $credientails->template_name
                            ];
                            $gupshupObj->sendWatsappMessageIndividal($feedback->country_id ?? '', $feedback->vchr_customer_mobile, str_replace("{{name}}", $feedback->vchr_customer_name, $whatsappTemplate->text_whatsapp_template_description),$data);
                        }
                    }
                }
            /**--END WHATSAPP AUTOMATION ---------------------------------------------**/

            /* ----------- Assign agent vise staff assign----- */
                if(request()->has('department')){
                    try{
                        AutomationRule::departmentViseAutoAssign($request,$feedback,$vendor_id);
                    }catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }

                    if($vendor_id == 2476)
                        AutomationRule::storeDepartmentToField($request,$feedback,$vendor_id);
                }
            /* ----------- End Assign agent vise staff assign----- */

            /**--API AUTOMATION NIKSHAN---------------------------------------------**/
                $source = $feedback->fk_int_enquiry_type_id;
                $post_data = [
                    'phone' => $feedback->vchr_customer_mobile,
                ];
                $automation_rule_api = $show->getRule($vendor_id, 'new_lead', 'api',$source);
                if ($automation_rule_api && $automation_rule_api->api != null) {
                    try {
                        if($vendor_id == Variables::NIKSHAN_USER_ID){
                            // Nikshan automation
                            $enquiryNotif = Enquiry::select('pk_int_enquiry_id','staff_id','fk_int_user_id')->find($feedback->pk_int_enquiry_id);
                            $usr = $enquiryNotif->assigned_user;
                            $extension = null;
                            if($usr){
                                $extension = $usr->userExtension ? $usr->userExtension->extension : null;
                            }
                            if($extension)
                                $show->postToIvrAutoCall($automation_rule_api->api,$extension, $post_data);
                        }
                    } catch (\Exception $e) {
                        Log::info($e->getMessage());
                    }
                }
            /**--API---------------------------------------------**/

            /** --------------------webhook Automation-------------------------------- */
                $automation_rule_webhook = $show->getRule($vendor_id, 'new_lead', 'webhook');
                if (count($automation_rule_webhook) > 0) {
                    $status = FeedbackStatus::where('pk_int_feedback_status_id', $feedback->feedback_status)->select('vchr_status')->first();
                    $post_data = [
                        'customer_name' => $feedback->vchr_customer_name,
                        'email' => $feedback->vchr_customer_email,
                        'status' => ($status)? $status->vchr_status : 'New Status',
                        'phone' => $feedback->vchr_customer_mobile,
                        'mobile' => $feedback->mobile_no,
                        'flag' => "new_lead",
                    ];
                    foreach($automation_rule_webhook as $w_hook){
                        if ($w_hook->webhook_id != NULL) {
                            try{
                                $webHook = $show->getWebHookById($w_hook->webhook_id);
                            }catch(\Exception $e){
                                Log::info($e->getMessage());
                            }
                            if ($webHook) {
                                try{
                                    $show->postToWebHook($webHook->url, $post_data);
                                }catch(\Exception $e){
                                    Log::info($e->getMessage());
                                }
                            }
                        }
                    }
                }
            /** --------------------End Webhook Automation-------------------------------- */
            
          
           
            try{
                // Enquiry::newLeadFunctions($feedback->pk_int_enquiry_id);
                AutomationFacade::newLeadFunctions($feedback->pk_int_enquiry_id);
            }catch (\Exception $e) {
                Log::error($e->getMessage());
            }
           /**--Assigned to Campaign Automation--------------------------------------------- */
           try{
            $automation_rule_campaign = $show->getRule($feedback->fk_int_user_id, 'new_lead', 'add_to_campaign',$feedback->fk_int_enquiry_type_id);
          
            if ($automation_rule_campaign && $automation_rule_campaign->campaign_id != NULL) {
                if($automation_rule_campaign->enquiry_source_id == $feedback->fk_int_enquiry_type_id){
                    $campaign_automation = true;
                    $cmp=LeadCampaign::find($automation_rule_campaign->campaign_id);
                    $show->addToCampaign($automation_rule_campaign,$feedback,$feedback->fk_int_user_id,$feedback->fk_int_user_id,$cmp->type);
                }
            }
        }catch(\Exception $e){
            \Log::error($e->getMessage());
            \Log::error('Automation failed campaign');
        }

    /**-- End Assigned to Campaign Automation ------------------------------------------- */
            //Start: Send Notification
                if ($staff_id) {
                    $notification_title = 'You have been assigned a new lead';
                    $notification_description = 'Lead Details: ' . $feedback->name_with_number;
                    $notification_data = [
                        "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                        "sound" => "default",
                        "page" => "enquiry_details",
                        "id" => (string)$feedback->pk_int_enquiry_id
                    ];

                    /* ----------Notification---------- */
                        $result = Notifications::getUserTokens($staff_id);
                        if($result){
                            dispatch(new SendNotification($result, $notification_title, $notification_description, $notification_data))->onQueue('notification');
                        }
                        try{
                            $existPusher = PusherSetting::where('vendor_id',$vendor_id)->active()->first();
                            if($existPusher){
                                $message = $notification_title.' '.$notification_description;
                                event(new SendPusherNotification($staff_id, $existPusher,$message));
                            }
                        }catch(\Exception $e){
                            Log::info('Push notification error');
                        }
                    /* ----------End Notification---------- */
                }
            // End Notification
        } else {
            try {
                event(new ApiHistoryPost(1,$mobnoExist->pk_int_enquiry_id,1,$vendor_id,$typeId,2));
            } catch (\Exception $e) {
                Log::info($e->getMessage());
            }
            //Start: Send duplicate lead Notification
                if ($mobnoExist) {
                    $user_name = $mobnoExist->vchr_customer_name .'('.$mobnoExist->vchr_customer_mobile .')';
                    $source = $mobnoExist->leadSource? 'via '.$mobnoExist->leadSource->vchr_enquiry_type :'';
                    $notification_title = $user_name.' tried to contacted via '.($type ?? $source);
                    $notification_description = 'Lead Details: ' . $mobnoExist->name_with_number;
                    $notification_data = [
                        "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                        "sound" => "default",
                        "page" => "enquiry_details",
                        "id" => (string)$mobnoExist->pk_int_enquiry_id
                    ];

                    try{
                        /* ----------Notification---------- */
                        if($mobnoExist->staff_id){
                            $result = Notifications::getUserTokens($mobnoExist->staff_id);
                            if($result){
                                dispatch(new SendNotification($result, $notification_title, $notification_description, $notification_data))->onQueue('notification');
                            }
                        }
                        /* ----------End Notification---------- */
                    }catch(\Exception $e){
                        Log::info('error from duplicate lead via pabbly');
                    }

                    //Update lead attension
                    $mobnoExist->lead_attension = 1;
                    $mobnoExist->updated_at = now();
                    $mobnoExist->update();

                    $message = $mobnoExist->vchr_customer_name.' tried to contacted via '.($type ?? $source);
                    if ($message) {
                        try {
                            event(new CreateFollowup($message, EnquiryFollowup::TYPE_NOTE, $mobnoExist->pk_int_enquiry_id, $vendor_id));
                        } catch (\Exception $e) {
                            Log::info($e->getMessage());
                        }
                    }
                }
            // End duplicate lead notification

            $enquiry_id = $mobnoExist->pk_int_enquiry_id;
        }
     
        $enq = Enquiry::select('pk_int_enquiry_id','staff_id','assigned_date')->find($enquiry_id);
        if(!$enq->staff_id && !$campaign_automation){
            $enq->staff_id = $userid;
            $enq->assigned_date=Carbon::today();
            $enq->save();
            $User=User::select('vchr_user_name')->find($userid);
            $agent_name= $User ? $User->vchr_user_name : 'Agent Not Exist';
            $note = $agent_name." has been designated as the lead.";
            event(new CreateFollowup($note, EnquiryFollowup::TYPE_ACTIVITY, $enq->pk_int_enquiry_id,$vendor_id));
        }

        return $enquiry_id;
    }

    public function custom_field_values()
    {
        return $this->hasMany(CustomFieldValue::class, 'related_id', 'pk_int_enquiry_id');
    }

    public function task()
    {
        return $this->hasMany(Task::class, 'enquiry_id', 'pk_int_enquiry_id');
    }

    public function last_task()
    {
        return $this->hasOne(Task::class, 'enquiry_id', 'pk_int_enquiry_id')->orderBy('id', 'DESC')->where('status',0)->where('task_category_id',2)->whereNotNull('scheduled_date')->whereNull('campaign_id');
    }

     public function lastCallTask()
    {
        return $this->hasOne(Task::class, 'enquiry_id', 'pk_int_enquiry_id')->select('status','enquiry_id','task_category_id','id','scheduled_date')->orderBy('scheduled_date', 'DESC')->where('status',0)->where('task_category_id',2)->whereNotNull('scheduled_date');
    }

    public function lastTask()
    {
        return $this->hasOne(Task::class, 'enquiry_id', 'pk_int_enquiry_id')->select('status','enquiry_id','task_category_id','id','scheduled_date')->orderBy('scheduled_date', 'DESC')->where('status',0)->whereNotNull('scheduled_date');
    }

    public function last_followup()
    {
        return $this->hasOne(EnquiryFollowup::class, 'enquiry_id', 'pk_int_enquiry_id')->orderBy('updated_at', 'DESC');
    }

    public function assigned_user()
    {
        return $this->hasOne(User::class, 'pk_int_user_id', 'staff_id');
    }

    public function campaignLead()
    {
        return $this->hasMany('Getlead\Campaign\Models\CampaignLead', 'lead_id', 'pk_int_enquiry_id');
    }

    public function did_number()
    {
        return $this->hasOne(VirtualNumber::class, 'fk_int_user_id', 'fk_int_user_id')->where('type', 'IVR')->where('int_status', 1)
        ->where(function ($where) {
            $where->whereNull('agent_id')
                ->orWhere('agent_id', auth()->user()->pk_int_user_id)
                ->orWhereHas('extensions', function ($query) {
                    $query->where('staff_id', auth()->user()->pk_int_user_id);
                });
        })->orderBy('agent_id', 'DESC');
    }

    public function additional_details()
    {
        return $this->hasMany('App\FrontendModel\LeadAdditionalDetails', 'enquiry_id', 'pk_int_enquiry_id');
    }

    public function additionalDetails()
    {
        return $this->hasMany('App\FrontendModel\LeadAdditionalDetails', 'enquiry_id', 'pk_int_enquiry_id')
            ->select('id','enquiry_id','field_name','field_id','value');
    }

    public function getDidCallNumberAttribute()
    {
        return $this->virtual_number ? json_encode(['type' => $this->virtual_number->ivr_type, 'number' => $this->virtual_number->ivr_type == 2 ? '+914847110101' : '+' . $this->virtual_number->vchr_virtual_number]) : null;
    }

    public function getNameWithNumberAttribute()
    {
        return $this->vchr_customer_name ? $this->vchr_customer_name . '(+' . str_replace("+", "", $this->vchr_customer_mobile) . ')' : "No Customer Name (+" . str_replace("+", "", $this->vchr_customer_mobile) . ")";
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public static function sendNotification(Enquiry $enq)
    {
        $customer_name = $enq->vchr_customer_name ? $enq->vchr_customer_name : "No Customer Name";
        $notification_title = "New Lead Arrived on your Getlead. " . $customer_name . "(+" . $enq->vchr_customer_mobile . ")";
        $notification_description = "Hey , You Have got a new lead on your Getlead.
Customer : " . $customer_name . "
Number : +" . $enq->vchr_customer_mobile . "
Date and Time : " . Carbon::now()->format('d M Y h:i A');
        $notification_data = [
            "click_action" => "FLUTTER_NOTIFICATION_CLICK",
            "sound" => "default",
            "page" => "enquiry_details",
            "id" => (string)$enq->pk_int_enquiry_id
        ];
        /* ----------Notification---------- */
            $result = Notifications::getUserTokens($enq->fk_int_user_id);
            if($result){
                dispatch(new SendNotification($result, $notification_title, $notification_description, $notification_data))->onQueue('notification');
            }
            try{
                $existPusher = PusherSetting::where('vendor_id',$enq->fk_int_user_id)->active()->first();
                if($existPusher){
                    event(new SendPusherNotification($enq->fk_int_user_id,$existPusher,$notification_title));
                    // dispatch(new SendPusherNotification($enq->fk_int_user_id,$existPusher,$notification_title))->onQueue('pusher-notification');
                }
            }catch(\Exception $e){
                \Log::info('Push notification error');
            }
            
        /* ----------End Notification---------- */

        //App
        // if ($user->fcm_token)
        //     dispatch(new SendNotification([$user->fcm_token], $notification_title, $notification_description, $notification_data))->onQueue('notification');
        //DB

    }

    public static $ruleUpload = [

        'contacts' => 'required|mimes:csv',

    ];

    public static $messageUpload = [
        'contacts.required' => 'File is required',
        'contacts.mimes' => 'Unsupported file',

    ];

    /**
     * Get all of the sales for the Enquiry
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Order::class, 'fk_int_enquiry_id', 'pk_int_enquiry_id');
    }

    /**
     * Get all of the payments for the Enquiry
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(PaymentCollection::class, 'fk_int_enquiry_id', 'pk_int_enquiry_id');
    }

    /**
     * Get the enquiry_sales associated with the Enquiry
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function enquiry_sales(): HasOne
    {
        return $this->hasOne(EnquirySalesField::class, 'fk_int_enquiry_id', 'pk_int_enquiry_id');
    }

    /**
     * Get the district associated with the Enquiry
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function district()
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Get the district_name associated with the Enquiry
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function getDistrictNameAttribute()
    {
        return $this->district ? $this->district->name : '';
    }

    public static function newLeadFunctions($lead,$is_notify=null)
    {
        $enq=Enquiry::find($lead);
        
        // New Lead automation with autoassign
        if(!$enq->staff_id){
            $assignSourceData=AutomationRule::where('trigger','new_lead')->where('vendor_id',$enq->fk_int_user_id)->where('action', 'assign')->where('enquiry_source_id', $enq->fk_int_enquiry_type_id)->orderby('id','DESC')->first();
            if($enq->fk_int_user_id==196)
            { \Log::error('new source automation 1'); \Log::error($enq);}
            if($assignSourceData)
            { 
                AutomationRule::autoassign($assignSourceData,$lead); 
            }
            else
            {
              $assignData=AutomationRule::where('trigger','new_lead')->where('vendor_id',$enq->fk_int_user_id)->where('action', 'assign')->whereNull('enquiry_source_id')->orderby('id','DESC')->first();
              if($assignData) 
              {
                AutomationRule::autoassign($assignData,$lead); 
              }
            }
        }

        // Source change automation to auto assign
        $assignSourceData=AutomationRule::where('trigger','source_change')->where('enquiry_source_id',$enq->fk_int_enquiry_type_id)->where('vendor_id',$enq->fk_int_user_id)->where('action', 'assign')->orderby('id','DESC')->first();
        if($assignSourceData)
            {
                AutomationRule::autoassign($assignSourceData,$lead);
            }  

        //  Source vise automation with task creation
        $automation_rule_task=AutomationRule::where('trigger','source_change')->where('enquiry_source_id',$enq->fk_int_enquiry_type_id)->where('vendor_id',$enq->fk_int_user_id)->where('action', 'task')->orderby('id','DESC')->first();
        if ($automation_rule_task) {
            if($automation_rule_task->duration)
            {
                $scheduled_date=Carbon::now()->addMinutes($automation_rule_task->duration);
            }
            else{
                $scheduled_date=Carbon::now();
            }
            $input = [
                'name' => $automation_rule_task->task_title,
                'description' => $automation_rule_task->task_description,
                'scheduled_date' => $scheduled_date,
                'task_category_id' => $automation_rule_task->task_category_id,
                'assigned_to' => $automation_rule_task->task_assigned_to,
                'assigned_by' =>$enq->fk_int_user_id,
                'vendor_id' => $enq->fk_int_user_id,
                'enquiry_id' => $enq->pk_int_enquiry_id,
                'status' => 0,
            ];
            Task::create($input);
        }
        
        // Status change automation
        $assignStatus=AutomationRule::where('trigger','status_change')->where('vendor_id',$enq->fk_int_user_id)->where('feedback_status_id',$enq->feedback_status)->where('action', 'assign')->orderby('id','DESC')->first();
        if($assignStatus)
        {
            AutomationRule::autoassign($assignStatus,$enq->pk_int_enquiry_id);
        }
     
        // Additional field vise automation
        $assignAdditionalData=AutomationRule::where('trigger','value_change')
                ->where('vendor_id',$enq->fk_int_user_id)
                ->where('enquiry_source_id',$enq->fk_int_enquiry_type_id)
                ->where('action', 'assign')
                ->orderby('id','DESC')
                ->get();

        if(count($assignAdditionalData) > 0){
            foreach ($assignAdditionalData as $key => $value) {
                $enqAddField = LeadAdditionalDetails::where('enquiry_id',$enq->pk_int_enquiry_id)
                                                    ->where('field_id', $value->additional_field)
                                                    ->where('value', $value->additional_field_value)
                                                    ->first();
                if($enqAddField)  {
                    $leadAdd = $enqAddField;
                }else{
                    $assignAdditionalData->forget($key);
                }        
            }
        }       
                
        $assignAdditionalData = $assignAdditionalData->first();

        if($assignAdditionalData && $leadAdd)
        {
            if($leadAdd->field_id == $assignAdditionalData->additional_field && $leadAdd->value == $assignAdditionalData->additional_field_value){
                AutomationRule::autoassign($assignAdditionalData,$lead);
            }
        }

        return $lead;

    }

    public static function statusChangeFunction($lead)
    {
        $assignStatus=AutomationRule::where('trigger','status_change')->where('vendor_id',$lead->fk_int_user_id)->where('feedback_status_id',$lead->feedback_status)->where('action', 'assign')->orderby('id','DESC')->first();
        if($assignStatus)
        {
            AutomationRule::autoassign($assignStatus,$lead->pk_int_enquiry_id);
        }
    }

    public static function statusMailSend($enquiry_id,$template_id)
    {
        $vendorId = User::getVendorId();
        $enquiry = Enquiry::find($enquiry_id);
        if ($enquiry->staff_id) {
            $staff = User::select('email','vchr_user_name','email_password','pk_int_user_id')->find($enquiry->staff_id);
        }
        else{
            $staff=Auth::user();
        }
        $status = FeedbackStatus::where('pk_int_feedback_status_id', $enquiry->feedback_status)->select('vchr_status')
            ->first();
        if ($status) {
            $lead_status = $status->vchr_status;
        } else {
            $lead_status = "New Status";
        }
        if($staff->email && $staff->email_password) {
            $settings = MailConfiguration::where('vendor_id', $vendorId)->first();
            $users = User::select('vchr_logo','pk_int_user_id')->find(2);
            $data['email'] = $enquiry->vchr_customer_email;
            $data['subject'] = 'Mail notification';
            $data['content'] = 'status updated';
            $data['name'] = $enquiry->vchr_customer_name;
            $data['from'] = $staff->email;
            $data['from_name'] = $staff->vchr_user_name;
            $data['logo'] = url('') . $users->vchr_logo;
            $data['mobile'] = $enquiry->vchr_customer_mobile;
            $data['path'] = "";
            $data['cc'] = '';
            $data['bcc'] = '';
           
            $company = $enquiry->vchr_customer_company_name;
            $sourceData = EnquiryType::where('pk_int_enquiry_type_id', $enquiry->fk_int_enquiry_type_id)->first();
            $typeData = LeadType::where('id', $enquiry->lead_type_id)->first();
            $source = $sourceData ? $sourceData->vchr_enquiry_type : '';
            $type = $typeData ? $typeData->vchr_enquiry_type : '';
            $designationData = ($staff) ? Designation::where('pk_int_designation_id', $staff->designation_id)->first():null;
            $designation = $designationData?$designationData->vchr_designation:'';

            $emailTemplate = new Common();
            $data['template'] = $emailTemplate->emailTemplateContent($vendorId, $data['name'],$staff->vchr_user_name,$company, $data['mobile'], $data['email'], $source, $type, $lead_status, $data['content'],$template_id,$designation);

            $transport = new Swift_SmtpTransport($settings->host, $settings->port, strtolower($settings->encryption));
            if($staff->email_password != null)
            {
                $email_password=Crypt::decryptString($staff->email_password);
                $transport->setUsername($staff->email);
                $transport->setPassword($email_password);
            }

            $swift_mailer = new Swift_Mailer($transport);

            $view = app()->get('view');
            $events = app()->get('events');

            $mailer = new Mailer($view, $swift_mailer, $events);

            $mailer->alwaysFrom($staff->email, $staff->vchr_user_name);
            $mailer->alwaysReplyTo($staff->email, $staff->vchr_user_name);

            $flag = $mailer->to($enquiry->vchr_customer_email)->send(new StatusMailSend($data));
            
            return $flag;
        }
    }

    public function feedbackStatus(){
        return $this->hasOne(FeedbackStatus::class, 'pk_int_feedback_status_id', 'feedback_status');
    }

    public function enquiryFollowup()
    {
        return $this->hasMany(EnquiryFollowup::class, 'enquiry_id', 'pk_int_enquiry_id');
    }
    public function enquiryFollowupBySystem()
    {
        return $this->hasMany(EnquiryFollowup::class, 'enquiry_id', 'pk_int_enquiry_id')->latest();
    }
    public function deal()
    {
        return $this->hasMany(Deal::class,'lead_id','pk_int_enquiry_id');
    }
    public function leadSource()
    {
        return $this->hasOne(EnquiryType::class,'pk_int_enquiry_type_id' ,'fk_int_enquiry_type_id');
    }
    public function leadPurpose()
    {
        return $this->hasOne(EnquiryPurpose::class,'pk_int_purpose_id' ,'fk_int_purpose_id');
    }

    public function visit()
    {
        return $this->hasMany('App\VisitUser','enquiry_id' ,'pk_int_enquiry_id')->latest();
    }
   
    public function createdBy()
    {
        return $this->hasOne(User::class, 'pk_int_user_id', 'created_by');
    }

    function callMaster(){
        return $this->hasMany(CallMaster::class,'caller_number','vchr_customer_mobile');
    }
    function ivrCall(){
        return $this->hasMany('App\BackendModel\IVR','caller_number','vchr_customer_mobile');
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where(function($q) use($keyword){
            $q->whereFullText(['vchr_customer_name','vchr_customer_mobile','vchr_customer_company_name','vchr_customer_email','mobile_no','more_phone_numbers'], $keyword,["mode" => "boolean"]);
        });
    }

    public static function splitString($string, $length) {
        if (strlen($string) >= $length) {
            $part1 = substr($string, 0, $length);
            $part2 = substr($string, $length);
            return [$part1, $part2];
        } else {
            return ["", $string]; // Return an empty part1 and the original string in part2
        }
    }

    public static function getCRMWebsiteUsersV1($type, $mobileno, $userid, $name, $email, $feedbacks, $countrycode, $companyName, $request = null)
    {
        $vendor_id = User::getVendorIdApi($userid);
        $staff_id = $statusId = $leadTypeId = $fk_int_purpose_id = null;
        $campaign_automation = false;
        $agency= Agency::select('id')->where('token',$request->agency)->first();
        $agency_id=$agency?$agency->id:null;
        $countrycode =  str_replace("+","",$countrycode);
        $mobnoExist = Enquiry::where('fk_int_user_id', $vendor_id)
                    ->where(function($q) use($countrycode , $mobileno){
                        $q->where('vchr_customer_mobile', $countrycode.$mobileno)->orWhere('mobile_no', $mobileno);    
                    })
                    ->lockForUpdate()
                    ->first();
                    
        $enquiryType = EnquiryType::select('pk_int_enquiry_type_id','vchr_enquiry_type','fk_int_user_id','vendor_id')
                            ->where('vchr_enquiry_type', $type)->where(function ($where) use ($vendor_id) {
                                $where->where('fk_int_user_id', $vendor_id);
                                $where->orWhere('vendor_id', $vendor_id);
                            })->first();
        if ($enquiryType) {
            $typeId = $enquiryType->pk_int_enquiry_type_id;
        } else {
            $enType = new EnquiryType();
            $enType->vchr_enquiry_type = $type;
            $enType->fk_int_user_id = $vendor_id;
            $enType->vendor_id = $vendor_id;
            $enType->int_status = "1";
            $fl = $enType->save();
            $typeId = $enType->pk_int_enquiry_type_id;
        }
        
        if (request()->has('status') && request()->filled('status')) {
            $feedback_status = FeedbackStatus::select('vchr_status','fk_int_user_id','pk_int_feedback_status_id')
            ->where('vchr_status', $request->status)->where(function ($where) use ($vendor_id) {
                $where->where('fk_int_user_id', $vendor_id);
            })->first();
            if (!$feedback_status) {
                $feedback_status = new FeedbackStatus();
                $feedback_status->vchr_status = $request->status;
                $feedback_status->vchr_color = '#000000';
                $feedback_status->fk_int_user_id = $vendor_id;
                $feedback_status->created_by = $vendor_id;
                $feedback_status->save(); 
            }
            $statusId = $feedback_status->pk_int_feedback_status_id;
        }
        
     
        if(request()->has('type') && request()->filled('type')){
            $leadType = LeadType::where('name', $request->type)->where(function ($where) use ($vendor_id) {
                $where->where('vendor_id', $vendor_id);
            })->first();
            if(!$leadType){
                $leadType = new LeadType();
                $leadType->name = $request->type;
                $leadType->vendor_id = $vendor_id;
                $leadType->created_by = $vendor_id;
                $leadType->save();
            }
            $leadTypeId = $leadType->id;
        }

        if (request()->has('purpose') && request()->filled('purpose')) {
            $purpose = EnquiryPurpose::where('vchr_purpose', $request->purpose)
                            ->where('fk_int_user_id', $vendor_id)->first();
            if ($purpose) {
                $fk_int_purpose_id = $purpose->pk_int_purpose_id;
            } else {
                $purpose = new EnquiryPurpose();
                $purpose->vchr_purpose = $request->purpose;
                $purpose->vchr_purpose_description = $request->purpose;
                $purpose->fk_int_user_id = $vendor_id;
                $purpose->created_by = $vendor_id;
                $purpose->save();
                $fk_int_purpose_id = $purpose->pk_int_purpose_id;
            }
        }

        if($type == 'IVR'){
            $agent = AssignAgent::where('vendor_id', $vendor_id)->first();
            $staff_id =  ($agent) ? $agent->agent_id : null;
        }
        
        if ($request && $request->staff_name) {
            $staff = User::where('vchr_user_name', $request->staff_name)->where('parent_user_id', $vendor_id)->first();
            $staff_id =  ($staff) ? $staff->pk_int_user_id : null;
        }

        if($type==EnquiryType::GLSCRATCH && request('userid')){
            $staff_id = request('userid');
        }

        if (!$mobnoExist) {
            DB::beginTransaction();
            $feedback = new Enquiry();
            $feedback->vchr_customer_mobile = $countrycode . $mobileno;
            $feedback->mobile_no = $mobileno;
            $feedback->country_code = $countrycode;
            $feedback->read_status = 0;
            $feedback->agency_id=$agency_id;
            if ($staff_id){
                $feedback->staff_id = $staff_id;
                $feedback->assigned_date=Carbon::now();}
            if ($request && isset($request['date_of_birth']))
                $feedback->date_of_birth = $request->date_of_birth;
            if ($request && isset($request['purpose']))
                $feedback->fk_int_purpose_id = $fk_int_purpose_id;

            $feedback->fk_int_user_id = $vendor_id;
            $feedback->fk_int_enquiry_type_id = $typeId;
            $feedback->feedback_status = $statusId;
            $feedback->vchr_customer_name = $name;
            $feedback->vchr_customer_email = $email;
            $feedback->vchr_enquiry_feedback = $feedbacks;
            $feedback->address = request('address') ?? '';
            $feedback->created_by = request('userid') ?? $userid;
            $feedback->vchr_customer_company_name = $companyName;
            $feedback->lead_type_id = $leadTypeId;
            $flag = $feedback->save();
            DB::commit();

            $enquiry_id = $feedback->pk_int_enquiry_id;
            try {
                event(new ApiHistoryPost(1,$enquiry_id,0,$vendor_id,$typeId,2));
            } catch (\Exception $e) {
                Log::info($e->getMessage());
            }

            if (isset($request->lead_note) && $request->lead_note != '') {
                try {
                    event(new CreateFollowup($request->lead_note, EnquiryFollowup::TYPE_NOTE, $feedback->pk_int_enquiry_id, $vendor_id));
                } catch (\Exception $e) {
                    Log::info($e->getMessage());
                }
            }

            $show = new Common();
            $addional_fields = LeadAdditionalField::where('vendor_id', $vendor_id)->get();
            foreach ($addional_fields as $add_field) {
                try {
                    $modifiedString = str_replace(" ", "_", $add_field->field_name);
                    if (request()->has($add_field->field_name) || request()->has($modifiedString)) {
                        if($add_field->input_type == 8){
                            LeadAdditionalDetails::updateOrCreate(['enquiry_id' => $enquiry_id,
                            'field_id' => $add_field->id],
                            ['field_name' => $add_field->field_name, 'value' => json_encode(explode(',',$request[$add_field->field_name] ?? $request[$modifiedString])),
                                'created_by' => $userid]);
                        }else{
                            LeadAdditionalDetails::updateOrCreate(['enquiry_id' => $enquiry_id,
                            'field_id' => $add_field->id],
                            ['field_name' => $add_field->field_name, 'value' => $request[$add_field->field_name] ?? $request[$modifiedString],
                                'created_by' => $userid]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::info('Enquiry model additional field issue');
                    Log::info(request()->all());
                }
            }

            /**--AUTOMATION_WHATSAPP---------------------------------------------**/
                $automation_whatsapp = AutomationRule::where('vendor_id', $feedback->fk_int_user_id)
                    ->where('trigger', 'new_lead')
                    ->where('action', 'whatsapp')
                    ->where('enquiry_source_id', $feedback->fk_int_enquiry_type_id)
                    ->orderBy('id', 'DESC')
                    ->first();

                if ($automation_whatsapp) {
                    $whatsappTemplate = WhatsappTemplate::where('pk_int_whatsapp_template_id', $automation_whatsapp->whatsapp_template_id)
                        ->select('text_whatsapp_template_description')->first();
                    if ($whatsappTemplate) {
                        $gupshupObj = new GupShup();
                        $credientails = WatsappCredential::where('vendor_id', $feedback->fk_int_user_id)
                            ->where('status', 1)
                            ->where('platform_id', 2)
                            ->first();
                        if ($credientails) {
                            $data = [
                                "api_key" => $credientails->access_key,
                                "from_number" => $credientails->source_mobile_num,
                                "app_name" => $credientails->template_name
                            ];
                            $gupshupObj->sendWatsappMessageIndividal($feedback->country_id ?? '', $feedback->vchr_customer_mobile, str_replace("{{name}}", $feedback->vchr_customer_name, $whatsappTemplate->text_whatsapp_template_description),$data);
                        }
                    }
                }
            /**--END WHATSAPP AUTOMATION ---------------------------------------------**/

            /* ----------- Assign agent vise staff assign----- */
                if(request()->has('department')){
                    try{
                        AutomationRule::departmentViseAutoAssign($request,$feedback,$vendor_id);
                    }catch (\Exception $e) {
                        Log::error($e->getMessage());
                    }

                    if($vendor_id == 2476)
                        AutomationRule::storeDepartmentToField($request,$feedback,$vendor_id);
                }
            /* ----------- End Assign agent vise staff assign----- */

            /**--API AUTOMATION NIKSHAN---------------------------------------------**/
                $source = $feedback->fk_int_enquiry_type_id;
                $post_data = [
                    'phone' => $feedback->vchr_customer_mobile,
                ];
                $automation_rule_api = $show->getRule($vendor_id, 'new_lead', 'api',$source);
                if ($automation_rule_api && $automation_rule_api->api != null) {
                    try {
                        if($vendor_id == Variables::NIKSHAN_USER_ID){
                            // Nikshan automation
                            $enquiryNotif = Enquiry::select('pk_int_enquiry_id','staff_id','fk_int_user_id')->find($feedback->pk_int_enquiry_id);
                            $usr = $enquiryNotif->assigned_user;
                            $extension = null;
                            if($usr){
                                $extension = $usr->userExtension ? $usr->userExtension->extension : null;
                            }
                            if($extension)
                                $show->postToIvrAutoCall($automation_rule_api->api,$extension, $post_data);
                        }
                    } catch (\Exception $e) {
                        Log::info($e->getMessage());
                    }
                }
            /**--API---------------------------------------------**/

            /** --------------------webhook Automation-------------------------------- */
                $automation_rule_webhook = $show->getRule($vendor_id, 'new_lead', 'webhook');
                if (count($automation_rule_webhook) > 0) {
                    $status = FeedbackStatus::where('pk_int_feedback_status_id', $feedback->feedback_status)->select('vchr_status')->first();
                    $post_data = [
                        'customer_name' => $feedback->vchr_customer_name,
                        'email' => $feedback->vchr_customer_email,
                        'status' => ($status)? $status->vchr_status : 'New Status',
                        'phone' => $feedback->vchr_customer_mobile,
                        'mobile' => $feedback->mobile_no,
                        'flag' => "new_lead",
                    ];
                    foreach($automation_rule_webhook as $w_hook){
                        if ($w_hook->webhook_id != NULL) {
                            try{
                                $webHook = $show->getWebHookById($w_hook->webhook_id);
                            }catch(\Exception $e){
                                Log::info($e->getMessage());
                            }
                            if ($webHook) {
                                try{
                                    $show->postToWebHook($webHook->url, $post_data);
                                }catch(\Exception $e){
                                    Log::info($e->getMessage());
                                }
                            }
                        }
                    }
                }
            /** --------------------End Webhook Automation-------------------------------- */
            
            /**--Assigned to Campaign Automation--------------------------------------------- */
                try{
                    $automation_rule_campaign = $show->getRule($feedback->fk_int_user_id, 'new_lead', 'add_to_campaign',$feedback->fk_int_enquiry_type_id);
                    if ($automation_rule_campaign && $automation_rule_campaign->campaign_id != NULL) {
                        if($automation_rule_campaign->enquiry_source_id == $feedback->fk_int_enquiry_type_id){
                            $campaign_automation = true;
                            $cmp=LeadCampaign::find($automation_rule_campaign->campaign_id);
                            $show->addToCampaign($automation_rule_campaign,$feedback,$feedback->fk_int_user_id,$feedback->fk_int_user_id,$cmp->type);
                        }
                    }
                }catch(\Exception $e){
                    Log::info($e->getMessage());
                    Log::info('Automation failed campaign');
                }
            /**-- End Assigned to Campaign Automation ------------------------------------------- */
           
            try{
                // Enquiry::newLeadFunctions($feedback->pk_int_enquiry_id);
                AutomationFacade::newLeadFunctions($feedback->pk_int_enquiry_id);
            }catch (\Exception $e) {
                Log::error($e->getMessage());
            }

            //Start: Send Notification
                if ($staff_id) {
                    $notification_title = 'You have been assigned a new lead';
                    $notification_description = 'Lead Details: ' . $feedback->name_with_number;
                    $notification_data = [
                        "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                        "sound" => "default",
                        "page" => "enquiry_details",
                        "id" => (string)$feedback->pk_int_enquiry_id
                    ];

                    /* ----------Notification---------- */
                        $result = Notifications::getUserTokens($staff_id);
                        if($result){
                            dispatch(new SendNotification($result, $notification_title, $notification_description, $notification_data))->onQueue('notification');
                        }
                        try{
                            $existPusher = PusherSetting::where('vendor_id',$vendor_id)->active()->first();
                            if($existPusher){
                                $message = $notification_title.' '.$notification_description;
                                event(new SendPusherNotification($staff_id, $existPusher,$message));
                            }
                        }catch(\Exception $e){
                            Log::info('Push notification error');
                        }
                    /* ----------End Notification---------- */
                }
            // End Notification
        } else {
            try {
                event(new ApiHistoryPost(1,$mobnoExist->pk_int_enquiry_id,1,$vendor_id,$typeId,2));
            } catch (\Exception $e) {
                Log::info($e->getMessage());
            }
            //Start: Send duplicate lead Notification
                if ($mobnoExist) {
                    $user_name = $mobnoExist->vchr_customer_name .'('.$mobnoExist->vchr_customer_mobile .')';
                    $source = $mobnoExist->leadSource? 'via '.$mobnoExist->leadSource->vchr_enquiry_type :'';
                    $notification_title = $user_name.' tried to contacted via '.($type ?? $source);
                    $notification_description = 'Lead Details: ' . $mobnoExist->name_with_number;
                    $notification_data = [
                        "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                        "sound" => "default",
                        "page" => "enquiry_details",
                        "id" => (string)$mobnoExist->pk_int_enquiry_id
                    ];

                    try{
                        /* ----------Notification---------- */
                        if($mobnoExist->staff_id){
                            $result = Notifications::getUserTokens($mobnoExist->staff_id);
                            if($result){
                                dispatch(new SendNotification($result, $notification_title, $notification_description, $notification_data))->onQueue('notification');
                            }
                        }
                        /* ----------End Notification---------- */
                    }catch(\Exception $e){
                        Log::info('error from duplicate lead via pabbly');
                    }

                    //Update lead attension
                    $mobnoExist->lead_attension = 1;
                    $mobnoExist->updated_at = now();
                    $mobnoExist->update();

                    $message = $mobnoExist->vchr_customer_name.' tried to contacted via '.($type ?? $source);
                    if ($message) {
                        try {
                            event(new CreateFollowup($message, EnquiryFollowup::TYPE_NOTE, $mobnoExist->pk_int_enquiry_id, $vendor_id));
                        } catch (\Exception $e) {
                            Log::info($e->getMessage());
                        }
                    }
                }
            // End duplicate lead notification

            $enquiry_id = $mobnoExist->pk_int_enquiry_id;
        }
        
        $enq = Enquiry::select('pk_int_enquiry_id','staff_id','assigned_date')->find($enquiry_id);
        if(!$enq->staff_id && !$campaign_automation){
            $enq->staff_id = $userid;
            $enq->assigned_date=Carbon::today();
            $enq->save();
            $User=User::select('vchr_user_name')->find($userid);
            $agent_name= $User ? $User->vchr_user_name : 'Agent Not Exist';
            $note = $agent_name." has been designated as the lead.";
            event(new CreateFollowup($note, EnquiryFollowup::TYPE_ACTIVITY, $enq->pk_int_enquiry_id,$vendor_id));
        }

        return $enquiry_id;
    }
}
