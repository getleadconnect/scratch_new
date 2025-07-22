<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SentServiceJob;
use GuzzleHttp\Client;

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
	  
	  $otp_status=Settings::where('vchr_settings_type','scratch_otp_enabled')->where('fk_int_user_id',$user_id)->pluck('vchr_settings_value')->first();
	  $data['otp_bypass_value']=$otp_status;
	  
	  $crmapi=Settings::where('vchr_settings_type','crm_api_token')->where('fk_int_user_id',$user_id)->first();
	  if($crmapi)
	  {
		 $data['crm_api_token']=$crmapi->vchr_settings_value;
		 $data['crm_api_status']=$crmapi->int_status;
	  }else {$data['crm_api_token']='';$data['crm_api_status']=0;}
	  
	  $srequired=Settings::where('vchr_settings_type','shop_required_in_link')->where('fk_int_user_id',$user_id)->pluck('vchr_settings_value')->first();
	  $data['shop_required']=$srequired;
		 
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


public function saveCrmApiToken(Request $request)
{
	
	$api_token=$request->crm_api_token;
	$user_id=User::getVendorId();
	$len=strlen($api_token);
		
	if(substr($api_token,0,3)!="gl_" and ($len>23 or $len<23))
	{
		$sdt=Settings::where('vchr_settings_type','crm_api_token')->where('fk_int_user_id',$user_id)->delete();
		return response()->json(['msg'=>"Crm api token is invalid!",'status'=>false]);
	}
	else if(substr($api_token,0,3)=="gl_" and ($len>23 or $len<23))
	{
		$sdt=Settings::where('vchr_settings_type','crm_api_token')->where('fk_int_user_id',$user_id)->delete();
		return response()->json(['msg'=>"Crm api token is invalid!",'status'=>false]);
	}
	
	try{
		$sdt=Settings::where('vchr_settings_type','crm_api_token')->where('fk_int_user_id',$user_id)->first();
		
		if($sdt){
			$sdt->vchr_settings_type="crm_api_token";
			$sdt->vchr_settings_value=$api_token;
			$sdt->fk_int_user_id=$user_id;
			$sdt->int_status=1;
			$flag=$sdt->save();
		}
		else{
			$sdt=new Settings();
			$sdt->vchr_settings_type="crm_api_token";
			$sdt->vchr_settings_value=$api_token;
			$sdt->fk_int_user_id=$user_id;
			$sdt->int_status=1;
			$flag=$sdt->save();
		}
		
		if($flag){   
		return response()->json(['msg'=>'Api token successfully added','status'=>true]);
		}
		else{
			return response()->json(['msg'=>'Something wrong, try again.','status'=>false]);
		}
	}
	catch(\Exception $e)
	{
		return response()->json(['msg'=>$e->getMessage(),'status'=>false]);
	}

}


public function setCrmApiStatus(Request $request)
{
	$validate=Validator::make($request->all(),
			[
			'api_status'=>'required',
			]);
	
		if($validate->fails())
		{
			return response()->json(['msg'=>'Something wrong, Try again!','status'=>false]);
		}
		
		try
			{
            	$user_id=User::getVendorId();
				$api_state=$request->api_status;
				
				$api_state=($api_state==0)?1:0;
					
				$sdt=Settings::where('vchr_settings_type','crm_api_token')->where('fk_int_user_id',$user_id)->first();
		
				if($sdt and $sdt->vchr_settings_value!="")
				{
					$sdt->int_status=$api_state;
					$sdt->save();
					$api_val=($api_state==1)?"Enabled":"Disabled";
					
					return response()->json(['msg'=>'Crm api service enabled','status'=>true,'api_status'=>$api_state,'status_value'=>$api_val]);
				}
				else 
				{
					$status=0;
					return response()->json(['msg'=>'Crm api token missing!','status'=>false,'api_status'=>0,'status_value'=>"Disabled"]);
				}
	
           }
            catch(\Exception $e)
            {
	       		return response()->json(['msg'=>$e->getMessage(),'status'=>false]);
            }
	
}



/*    public function sendWhatsappOtp()
    {
        // Send the message
		$bussinessId = 107390568652882;
        $url = 'https://graph.facebook.com/v19.0/'.$bussinessId.'/messages';
		$token = 'EAAtUQaQveEkBO0gmcNGs7gwa5Q6tch09XviFFSevZAlfUePAuiBHqrY42EdhicnxrQZAPsowjXEARlQaUz2AmoWu7T8rxAxQfWZAE4SjaWvLmazWYd2gscSgC8A1p3dcsJKELfZBW0Kdw9aY3bEYi1PIXSDGjVZA78MCg4Mn0yw76DJYe3rl772KVMgDvKQzp3Sk6svkZB9MhhPkDu';

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

			$client = new Client();
            $response = $client->request('POST', $url, [
                'json' => $params,
                'headers' => $headers,
            ]);
            
			$result=json_decode($response->getBody(), true);
			//return $result['messages'][0]['message_status'];  //will return 'accepted'
			return $result
			
        } catch (\Exception $e) {
            Log::info('Sent service job failed: ' . $e->getMessage());
            return $e->getMessage();
        }

    }*/
}
