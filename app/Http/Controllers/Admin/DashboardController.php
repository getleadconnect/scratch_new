<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Traits\TelegramNotificationTrait;
use App\Models\ScratchWebCustomer;
use App\Models\ScratchOffer;

use App\Models\User;
use GuzzleHttp\Client;

use Validator;
use DataTables;
use Session;
use Auth;
use Log;
use DB;

class DashboardController extends Controller
{

  use TelegramNotificationTrait;
  public function __construct()
  {
     //$this->middleware('admin');
  }

public function sendmyotp()
{

	$otp = rand(1111, 9999);

  		$phoneid = 589291010945154;
        $otp_url = "https://api.msg.bonvoice.com/v3/".$phoneid."/messages";
        //$this->token = "EAAtUQaQveEkBO0gmcNGs7gwa5Q6tch09XviFFSevZAlfUePAuiBHqrY42EdhicnxrQZAPsowjXEARlQaUz2AmoWu7T8rxAxQfWZAE4SjaWvLmazWYd2gscSgC8A1p3dcsJKELfZBW0Kdw9aY3bEYi1PIXSDGjVZA78MCg4Mn0yw76DJYe3rl772KVMgDvKQzp3Sk6svkZB9MhhPkDu";
				
		try {

			$params=[ 
				"messaging_product"=> "whatsapp", 
				"recipient_type"=> "individual", 
				"to"=> "919995338385", 
				"type"=> "template", 
				"template"=> [ 
					"name"=> "zero_tap_ttt_vk_fff", 
					"language"=> [ 
					"code"=> "en_US" 
					],
				"components"=> [ 
					[ 
						"type"=> "body", 
						"parameters"=> [ 
							[ 
							"type"=> "text", 
							"text"=> "84984519" 
							] 
						] 
					], 
					[ 
						"type"=> "button", 
						"sub_type"=> "url", 
						"index"=> "0", 
						"parameters"=> [ 
							[ 
							"type"=> "text", 
							"text"=> "8498" 
							] 
						] 
					] 
				] 
				]
			];

			$headers = [
        		'apikey' => 'f08d3ea7-3fa1-11f0-98fc-02c8a5e042bd',
        		'Content-Type' => 'application/json',
    		];

			$client = new Client();
            $response = $client->request('POST', $otp_url, [
                'json' => $params,
                'headers' => $headers,
            ]);
            
			$result=json_decode($response->getBody(), true);
			return $result;

        } catch (\Exception $e) {
            Log::info('Whatsapp message failed: ' . $e->getMessage());
            return $e->getMessage();
		}

}
  
/*public function test_telegram($id)
{
 
	try
	{

		$customer = ScratchWebCustomer::find($id);
		$usr=User::where('pk_int_user_id',$customer->user_id)->first();

		$mobile=$customer->country_code.$customer->mobile;
		$campaign_name=ScratchOffer::where('pk_int_scratch_offers_id',$customer->offer_id)->pluck('vchr_scratch_offers_name')->first();

		$dataSend = [
						'branch'=>"#".$usr->pk_int_user_id.":".$usr->vchr_user_name,
						'customer'=>ucfirst($customer->name),
						'mobile_no'=>$mobile,
						'gift_name'=>$customer->offer_text,
						'campaign'=>$campaign_name,
						'reference_id'=>$customer->unique_id,
					];

		$msg_result=$this->send_telegram_notification($dataSend);
		\Log::info($msg_result);
		\log::info($dataSend);
	}
	catch(\Exception $e)
	{
		\Log::info($e->getMessage());
	}

}
*/





  public function index()
  {
	  
	  //-----------------DONUT chart ----------------------------
				
		$csdat=User::select(DB::raw('YEAR(created_at) as user_year'), DB::raw('COUNT(*) as user_count'))
			  ->groupBy('user_year')
			  ->orderBy('user_year','DESC')
			  ->take(5)->get();

		$ur_year=[];
		$ur_cnt=[];
		
		foreach($csdat as $key=>$r)
		{
			$ur_year[$key]=$r->user_year."(".$r->user_count.")";
			$ur_cnt[$key]=$r->user_count;
		}
		
		$dn_lbl=implode(',',$ur_year);
		$dn_cnt=implode(',',$ur_cnt);
			
		
	$chart['user_year']=implode(",",$ur_year);
	$chart['user_count']=implode(",",$ur_cnt);
	//---------------------------------------------------
		
	$data['usr_count']=User::totalUserCount();
	$data['exp_count']=User::expiredUserCount();
	$data['active_count']=User::activeUserCount();
	  
	 return view('admin.dashboard',compact('chart','data'));
  }	
  
 
}
