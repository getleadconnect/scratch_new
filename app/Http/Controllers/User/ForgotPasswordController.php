<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Stevebauman\Location\Facades\Location;

use App\Common\Common;
use App\Models\User;
use App\Models\UserOtp;

use App\Models\ScratchCount;
use App\Models\ScratchOffer;
use App\Models\ScratchWebCustomer;
use App\Models\ScratchCustomer;

use Validator;
use DataTables;
use Session;
use Auth;
use Log;
use Carbon\Carbon;
use DB;

class ForgotPasswordController extends Controller
{
	
	protected $user_mob=null;
	
  public function __construct()
  {
     //$this->middleware('admin');
  }
  
  public function index()
  {
	   $ip = Common::getIp();
        //$ip="144.25.10.2";
        if($ip){
            $data = \Location::get($ip);
            if ($data && $data->countryCode) {
                $countryCode = $data->countryCode;
            } else {
                $countryCode = "IN";
            } 
        }else{
        $countryCode = "IN";
      }      
	return view('onboarding.forgot-password',compact('countryCode'));
  }	
  
public function verifyOtp()
{
	$user_mob=Session::get('user_mob');
	return view('onboarding.verify-otp',compact('user_mob'));
}

public function changeUserPassword()
{
	$user_mob=Session::get('user_mob');
	if($user_mob!="")
	return view('onboarding.new-password',compact('user_mob'));

	return redirect('forgot-password');
	
}


public function sendForgotPasswordOtp(Request $request)
{

	try
	{
		$validate = Validator::make(request()->all(),[
            'mobile'=>'required|numeric',
        ]);

	    if ($validate->fails())
        {
			return back()->withErrors($validate)->withInput();
        }

		$randomNumber = random_int(1000, 9999);
		$user_mob=$request->mobile_phoneCode.$request->mobile;
		
		$user=User::where('vchr_user_mobile',$user_mob)->first();

		if(!$user)
		{
			$err=['fail'=>"User account does'nt exist, Try again!"];
			return redirect()->back()->withErrors($err);
		}
				
		$votp=UserOtp::where('number',$user_mob)->first();
		
			if(!empty($votp))
			{				
				$votp->number=$user_mob;
				$votp->otp=$randomNumber;
				$votp->save();
			}
			else
			{
			$res=UserOtp::create([
				'number'=>$user_mob,
				'otp'=>$randomNumber,
			]);
		}

		//code here - > to send otp to whatsapp

		$this->user_mob=$user_mob;
		
		Session::put('user_mob',$user_mob);
		return redirect('verify-otp');
			
	}
	catch(\Exception $e)
	{
		Session::flash('fails',$e->getMessage());
		return redirect()->back();
		
	}

}

public function checkForgotPasswordOtp(Request $request)
{

	$validate = Validator::make(request()->all(),[
            'otp'=>'required|numeric',
        ]);
	  
	if ($validate->fails())
       {
			return back()->withErrors($validate)->withInput();
       }

	$user_otp=$request->otp;
	$user_mob=$request->user_mob;
	
	$otp_data=UserOtp::where('number',$user_mob)->first();
	
	if(!empty($otp_data))
	{
		if($user_otp==$otp_data->otp)
		{
			Session::put('user_mob',$user_mob);
			return redirect('change-user-password');
		}
		else
		{
			Session::flash('fail',"Incorrect Otp");
			$err=['fail'=>"incorrect otp"];
			return redirect()->back()->withErrors($err);
		}
	}
	else
	{
		Session::flash('fail',"User details not found, try again");
		return redirect()->back();
	}
}

public function updateUserPassword(Request $request)
{
	$validate = Validator::make(request()->all(),[
            'password'=>'required|min:6|max:20',
			'password_confirmation'=>'required|min:6',
       ]);
	  
	if ($validate->fails())
       {
			return back()->withErrors($validate)->withInput();
       }
	   
	$npass=$request->password;
	$cpass=$request->password_confirmation;
	$user_mob=$request->user_mob;  
	
	if($npass!=$cpass)
	{
		$err=['fail'=>"Confirm password does'nt match"];
		return back()->withErrors($err);
	}
	else
	{
	
		$result=User::where('vchr_user_mobile',$user_mob)->first();
		if(!empty($result))
		{
			$result->password=\Hash::make($npass);
			$result->save();
			Session::flush('user_mob');
			Session::flash('fp-success', 'Password successfully changed. You can login now!');
			return redirect('login');
		}
		else
		{
			return redirect()->back();
		}
	}
}

 
public function resendForgotPasswordOtp($email)
{

	try
	{
		$randomNumber = random_int(1000, 9999);
		$user_email=$email;
		
		$admin=Admin::where('email',$user_email)->first();

		if(!$admin)
		{
			return response()->json(['status'=>false,'msg'=>"User email does'nt exist, Try again!"]);
		}
		else
		{			
		    $votp=VerificationOtp::where('email',$user_email)->first();
		
			if(!empty($votp))
			{				
				$votp->email=$user_email;
				$votp->otp=$randomNumber;
				$votp->save();
			}
			else
			{
				$res=VerificationOtp::create([
					'email'=>$user_email,
					'otp'=>$randomNumber,
				]);
			
			}

		    $data = [
			  'to_address'=>$user_email,
			  'subject' => 'Verification otp from GL-Partner Portal',
			  'body' => '',
			  'view' => 'partner.send_otp_mail_template', // Optional view page location
			  'attachments' => [], //Optional attachments array
			  'otp'=>$randomNumber,
		    ];
				
			$this->sendMail->send($data);
			
			Session::put('user_email',$user_email);
			return response()->json(['status'=>true,'msg'=>"Otp successfully send!"]);
		}	
	}
	catch(\Exception $e)
	{
		\Log::info($e->getMessage());
		return response()->json(['status'=>false,'msg'=>$e->getMessage()]);
	}
}
 
  
}
