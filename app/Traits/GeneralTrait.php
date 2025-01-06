<?php

namespace App\Traits;


use App\Models\User;
use App\Models\ScratchCount;

use Session;

trait GeneralTrait
{
    	
	public function checkUserStatus($id)
	{
		$user_id=$id;
		$scnt=ScratchCount::where('fk_int_user_id',$user_id)->pluck('balance_count')->first();
		$user=User::where('pk_int_user_id',$user_id)->first();
		
		$result=true;
		if($user->subscription_start_date=='' || $user->subscription_end_date=='')	 
		 {
			Session::put('msg_title','You have no Subscription');
			Session::flash('msg_swal',"Please subscribe now!!!");
			$result=false;
		 }
		 else if($user->subscription_end_date<date('Y-m-d'))	 
		 {
			Session::put('msg_title','Subscription Expired!!!');
			Session::flash('msg_swal',"Re-new your subscription.");
			$result=false;
		 }
		 else if($scnt=='' || $scnt<=0)
		 {
			Session::put('msg_title','Insufficient Scratches.');
			Session::flash('msg_swal',"Purchase scratches now.");
			$result=false;
		 }
		 
		 return $result;
		 
	}
	
	
	
	
	
	
	
}
