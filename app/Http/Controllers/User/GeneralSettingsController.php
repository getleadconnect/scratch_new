<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SentServiceJob;

use App\Models\Settings;
use App\Models\User;

use Validator;

use DataTables;
use Session;
use Auth;
use Log;
use DB;
use Carbon\Carbon;


class GeneralSettingsController extends Controller
{

  public function __construct()
  {
     //$this->middleware('admin');
  }
  
  public function index()
  {
	  $user_id=User::getVendorId();
	  
	  $sett=Settings::where('vchr_settings_type','scratch_otp_enabled')->where('fk_int_user_id',$user_id)->first();
	  $data['otp_bypass_value']='Disabled';
	  if($sett)
	  {
		  $data['otp_bypass_value']=$sett->vchr_settings_value;
	  }
	  
	 return view('users.settings.general_settings',compact('data')); 
  }
 
public function setScratchOtpEnabled(Request $request)
{
	$validate=Validator::make($request->all(),
			[
			'otp_bypass_value'=>'required',
			]);
	
		if($validate->fails())
		{
			return response()->json(['msg'=>'Something wrong, Try again!','status'=>false]);
		}
		
		try
			{
            	$user_id=User::getVendorId();
				$otp_val=$request->otp_bypass_value;

				if($otp_val=="Disabled")
					$otp_val="Enabled";
				else
				   $otp_val="Disabled";

				$setv=$sett=Settings::where('vchr_settings_type','scratch_otp_enabled')->where('fk_int_user_id',$user_id)->first();
				if($setv)
				{
					$setv->vchr_settings_value=$otp_val;
					$flag=$setv->save();
				}
				else
				{
					$set=new Settings();
					$set->fk_int_user_id=$user_id;
					$set->vchr_settings_type='scratch_otp_enabled';
					$set->vchr_settings_value=$otp_val;
					$set->int_status=1;
              		$flag=$set->save();
				}
				
				if($flag)
        		{   
					if($otp_val=="Enabled")
						return response()->json(['msg'=>'Scratch customer OTP enabled','status'=>true,'bypass_value'=>$otp_val]);
					else
						return response()->json(['msg'=>'Scratch customer OTP disabled','status'=>true,'bypass_value'=>$otp_val]);
        		}
        		else
        		{
					return response()->json(['msg'=>'Something wrong, try again.','status'=>false]);
        		}
	
           }
            catch(\Exception $e)
            {
	       		return response()->json(['msg'=>$e->getMessage(),'status'=>false]);
            }
	
}


    public function sendWhatsappOtp()
    {
        // Send the message
		$bussinessId = 107390568652882;
        $url = 'https://graph.facebook.com/v19.0/'.$bussinessId.'/messages';
		$token = 'EAAtUQaQveEkBO0gmcNGs7gwa5Q6tch09XviFFSevZAlfUePAuiBHqrY42EdhicnxrQZAPsowjXEARlQaUz2AmoWu7T8rxAxQfWZAE4SjaWvLmazWYd2gscSgC8A1p3dcsJKELfZBW0Kdw9aY3bEYi1PIXSDGjVZA78MCg4Mn0yw76DJYe3rl772KVMgDvKQzp3Sk6svkZB9MhhPkDu';$token=
		
		$data['mobile_no']="+919995338385";
		$data['otp']=1234;
		
        $params = [
            "messaging_product" => "whatsapp",
            "to" => $data['mobile_no'],
            "type" => "template"
        ];
        $params['template'] = [
            "name" => "getleadotp",
            "language" => [
                "code" => "en"
            ]
        ];
        $components['components'] = [
            [
                "type" => "body",
                "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => $data['otp']
                                ]
                            ]
                ],
                [
                    "type" => "button",
                    "sub_type" => "url",
                    "index" => 0,
                    "parameters" => [ // Optional
                        [
                            "type" => "text",
                            "text" => $data['otp']
                        ]
                    ]
                ]
            ];
        $params['template']["components"] = $components['components'];

        try {
            $headers = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer '.$token
            ];

            $result=SentServiceJob::dispatch($url, $params,$headers);
			return $result;

        } catch (\Exception $e) {
            Log::info($e->getMessage());
            throw new \Exception($e->getMessage());
        }
    }
}
