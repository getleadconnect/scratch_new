<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Common\Variables;
use Carbon\Carbon;
use Auth;
use App\Facades\FileUpload;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{
	
//implements JWTSubject
	use Notifiable, HasApiTokens;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    const ACTIVATE = 1;
    const DEACTIVATE = 0;

    const ADMIN = 1;
    const USERS = 2;
    const SHOPS = 3;
	
	protected $table = 'tbl_users';
    protected $primaryKey = 'pk_int_user_id';
	
    protected $dates = ['deleted_at'];
	
	protected $fillable = [
        'vchr_user_name', 'countrycode', 'mobile', 'email','company_name', 'location','address','vchr_user_mobile', 
		'password','int_status','int_role_id','vchr_user_imei', 'datetime_last_login', 'int_module', 'int_registration_from',
		'int_is_emergency_account', 'vchr_logo', 'designation_id', 'rank','parent_user_id', 'reward', 'is_co_admin',
		'calling_method', 'enquiry_display_fields', 'extension','web_notification','employee_code','time_zone',
    ];

    protected $casts = [
        'enquiry_display_fields' => 'array'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $guarded = ['int_role_id', 'int_status'];


    public static $userRule = [
        'user_name' => 'required|max:25',
        'email' => 'required|email',
        'mobile' => 'required|numeric|digits_between:8,15|unique:tbl_users,vchr_user_mobile',
        'password' => 'required|min:6',
        // 'password_confirmation' => 'required_with:password|confirmed|min:6'
    ];

 
    public static $userRuleMessage = [
        'user_name.required' => 'Username is required',
        // 'lastname.required'=>'Last name is required',
        'email.required' => 'Email is required',
        'email.email' => 'Incorrect Email format',
        'mobile.required' => 'Mobile Number is required',
        'mobile.numeric' => 'Enter number in correct format ',
    ];
	
	
	public static $userUpdate = [
        'user_name_edit' => 'required|max:25',
        'email_edit' => 'required',
        'mobile_edit' => 'required|numeric|digits_between:8,15',
    ];
	
 
    public static $updateMessage = [
        'user_name_edit.required' => 'Username is required',
        'email_edit.required' => 'Email is required',
        'email_edit.email' => 'Incorrect Email format',
        'mobile_edit.required' => 'Mobile Number is required',
        'mobile_edit.numeric' => 'Enter number in correct format ',
    ];


	public static $shopUserRule = [
        'user_name' => 'required|max:25',
        'mobile' => 'required|numeric|digits_between:8,15|unique:tbl_users,mobile',
		'password' => 'required|min:6',
    ];
	 
    public static $shopUserMessage = [
        'user_name.required' => 'Username is required',
        'mobile.required' => 'Mobile Number is required',
        'mobile.numeric' => 'Enter number in correct format ',
		'mobile.unique' => 'Mobile number already exist.',
		'password.required' => 'password is required',
    ];


	public static $shopEditRule = [
        'user_name_edit' => 'required|max:25',
        'mobile_edit' => 'required|numeric|digits_between:8,15',
    ];
	 
    public static $shopEditMessage = [
        'user_name_edit.required' => 'Username is required',
        'mobile_edit.required' => 'Mobile Number is required',
        'mobile_edit.numeric' => 'Enter number in correct format ',
    ];

    public static $passwordRule = ['password' => 'required|min:6|confirmed'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }


    public static $telegramUpdate = [
        'telegram_id' => 'required',
    ];

    public static $messageTelegramUpdate = [
        'telegram_id.required' => 'Telegram Id is  required',
   ];

    public function scopeActive()
    {
            return $this->where('int_status',1);
    }

    /**
     * @return mixed
     */
	 
    public static function getVendorId()
    {
        if(auth()->check()){
            if (Auth::user()->int_role_id == User::USERS) {
                $vendorId = Auth::user()->pk_int_user_id;
            } elseif (Auth::user()->int_role_id == User::SHOPS) {
                $vendorId = Auth::user()->parent_user_id;
            } else {
                $vendorId = Auth::user()->pk_int_user_id;
            }
            return $vendorId;
        }else{
            return null;
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public static function getVendorIdApi($userId)
    {
        $user = User::select('pk_int_user_id','int_role_id','parent_user_id')->find($userId);
        if ($user) {
            if ($user->parent_user_id == NULL && $user->int_role_id == User::USERS) {
                $vendorId = $userId;
            } elseif ($user->parent_user_id != NULL && $user->int_role_id == User::SHOPS) {
                $vendorId = $user->parent_user_id;
            } else {
                $vendorId = $userId;
            }
            return $vendorId;
        } else {
            return false;
        }

    }

    public function isAdmin()
    {
        if (Auth::user()->int_role_id == Variables::ROLE_ADMIN) {
            return true;
        }
    }
	
	public function isUser()
    {
        if (Auth::user()->int_role_id == Variables::USER) {
            return true;
        }
    }
	
	public function isShops()
    {
        if (Auth::user()->int_role_id == Variables::SHOPS) {
            return true;
        }
    }
  
    public static function getUserName($id)
    {
        $user = User::select('vchr_user_name')->find($id);
        if ($user) {
            $username = $user->vchr_user_name;
        } else {
            $username = "USER NOT FOUND";
        }
        return $username;
    }
  

    public static function getUserVendorId($id)
    {
        $user = User::select('pk_int_user_id', 'int_role_id', 'vchr_user_name', 'email', 'int_status','parent_user_id')->find($id);
        if ($user->int_role_id == User::USERS) {
            $vendorId = $user->pk_int_user_id;
        } elseif ($user->int_role_id == User::SHOPS) {
            $vendorId = $user->parent_user_id;
        } else {
            $vendorId = $user->pk_int_user_id;
        }
        return $vendorId;
    }

 /*public function getProfilePicAttribute()
    {
        if($this->vchr_logo == null){
            return null;
        }else{
            FileUpload::viewFile('uploads/user-profile/' . $this->vchr_logo,'local');
        }
    }*/
}
