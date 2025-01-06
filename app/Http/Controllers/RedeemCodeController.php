<?php

namespace App\Http\Controllers\Api\AgentApp;

use DB;
use Validator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\BackendModel\ScratchWebCustomer;
use App\BackendModel\UserOtp;

class RedeemCodeController extends Controller
{
    public function verifyOtp(Request $request)
    {
        $rules = [
            'otp' => 'required',
            'user_id' => 'required'
        ];

        $input     = $request->only('otp','user_id');
        $validator = Validator::make($input, $rules);
    
        if ($validator->fails()) {
            return response()->json(['status' => "fail", 'error' => $validator->messages()]);
        }

        $user_otp = UserOtp::where('user_id',$request->user_id)->latest()->first();

        if($user_otp)
        {
            if($user_otp->otp == $request->otp)
            {
                return response()->json(['status' => "success",
                                        'message' => 'Otp verified successfully !!',
                                        ]);     
            }
            else{
                return response()->json(['status' => "fail",
                                         'message' => 'Otp does not match !!!'
                                        ]);
            }
        }
        else{
            return response()->json(['status' => "fail",
                                     'message' => 'Otp not found on the server !!!'
                                    ]);
        }
        
        
    }

    public function verifyRedeemCode(Request $request)
    {
        $rules = [
            'unique_id' => 'required',
            'user_id' => 'required'
        ];

        $input     = $request->only('unique_id','user_id');
        $validator = Validator::make($input, $rules);
    
        if ($validator->fails()) {
            return response()->json(['status' => "fail", 'error' => $validator->messages()]);
        }

        $row = ScratchWebCustomer::where('unique_id',$request->unique_id)
                ->first();

        if($row)
        {   
            //Checking if the code has already been redeemed
            if($row->redeem == 1)
            {
                return response()->json(['status' => "fail",
                                        'message' => 'Code was already redemeed once !!',
                                        'unique_id' => $request->unique_id,
                                        'offer_text' => ''
                                       
                                    ]);
            }

            $otp = rand(1111, 9999);

            $write_otp = UserOtp::create([
                'user_id'   => $request->user_id,
                'otp' => $otp, 
                'otp_type' => "scratch_api", 
                ]);

 
            if($write_otp->id)
            {   
                $message = ('Hi ' . $otp . ' is the OTP for your request for number verification through Getlead.');
                $url = 'https://app.getlead.co.uk/api/pushsms?username=918453555000&token=gl_d52aa6241238b4e44d9b&sender=GTLEAD&to='.$row->mobile.'&message='.$message.'&priority=11&message_type=0';

                $client = new \GuzzleHttp\Client();
                $client_request = $client->get($url);

                return response()->json(['status' => "success",
                                        'message' => 'OTP has been sent successfully !!',
                                        'otp' => $otp,
                                        'unique_id' => $request->unique_id,
                                        'offer_text' => $row->offer_text
                                        ]);
            }
            else
            {
                return response()->json(['status' => "fail",
                                        'message' => 'Otp could not be saved to the server  !!',
                                        'unique_id' => $request->unique_id,
                                        'offer_text' => ''
                                        ]);
            }
        }
        else{
            return response()->json(['status' => "fail",
                                     'message' => 'Redeem code not found !!',
                                     'unique_id' => $request->unique_id,
                                     'offer_text' => ''
                                    ]);
        }
    }

    public function listWinners(Request $request)
    {
        $rules = [
            'user_id' => 'required'
        ];

        $input     = $request->only('user_id');
        $validator = Validator::make($input, $rules);

        if ($validator->fails()) {
            return response()->json(['status' => "fail", 'error' => $validator->messages()]);
        }

        $data =  ScratchWebCustomer::where('user_id',$request->user_id)
                                    ->select('name','mobile','unique_id','offer_text')
                                    ->get();

        return response()->json(['status' => "success",
                                'message' => 'Redeem history retrieved successfully !!',
                                'data' => $data
                               ]);

    }

    public function redeemCode(Request $request)
    {
        $rules = [
            'unique_id' => 'required'
        ];

        $input     = $request->only('unique_id');
        $validator = Validator::make($input, $rules);
    
        if ($validator->fails()) {
            return response()->json(['status' => "fail", 'error' => $validator->messages()]);
        }

        $row = ScratchWebCustomer::where('unique_id',$request->unique_id)
                ->first();

        $row->status = 1;
        $row->redeem = 1;
        if(request()->has('user_id'))
            $row->redeemed_agent = request('user_id');
        
        $row->save();

        return response()->json(['status' => "success",
                                'message' => 'Code has been redemeed successfully !!',
                                ]);
    }
}
