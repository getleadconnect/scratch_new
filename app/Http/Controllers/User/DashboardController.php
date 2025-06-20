<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
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

class DashboardController extends Controller
{
  public function __construct()
  {
     //$this->middleware('admin');
  }
  
  public function index()
  {
	
	$user_id=User::getVendorId();
	
	$tot_count=ScratchCount::getTotalScratchCount($user_id);
	$used_count=ScratchCount::getUsedScratchCount($user_id);
	$bal_count=ScratchCount::getBalanceScratchCount($user_id);

	$user=User::where('pk_int_user_id',$user_id)->first();
	
		$sub['subscription']='Active';
		$sub['start_date']="";
		$sub['end_date']="";
			
		$date = Carbon::create($user->subscription_end_date);
		$now = Carbon::now();
		$sub_diff_days= round($now->diffInDays($date),0);

			if($user->subscription_start_date=="" and $user->subscription_end_date=="")
			{
				$sub['subscription']="No";
			}
			else 
			{
			
				$subscription_date = Carbon::create($user->subscription_end_date)->addDays(1)->format('Y-m-d');
				if($subscription_date<=date('Y-m-d'))
					$sub['subscription']="Expired";

				$sub['start_date']=$user->subscription_start_date?Carbon::createFromFormat('Y-m-d',$user->subscription_start_date)->format('d-m-Y'):'';
				$sub['end_date']=$user->subscription_end_date?Carbon::createFromFormat('Y-m-d',$user->subscription_end_date)->format('d-m-Y'):'';
			}

// BAR chart data------------------------------
		$offers=ScratchOffer::select('tbl_scratch_offers.pk_int_scratch_offers_id','tbl_scratch_offers.vchr_scratch_offers_name')
			  ->where('tbl_scratch_offers.fk_int_user_id',$user_id)->where('tbl_scratch_offers.int_status',1)
			  ->get()->map(function($q)
			  {
				  $win1=ScratchWebCustomer::where('status',1)->where('offer_id',$q['pk_int_scratch_offers_id'])->count();
				  $los1=ScratchWebCustomer::where('status',0)->where('offer_id',$q['pk_int_scratch_offers_id'])->count();
				  
				  $win2=ScratchCustomer::where('int_status',1)->where('fk_int_offer_id',$q['pk_int_scratch_offers_id'])->count();
				  $los2=ScratchCustomer::where('int_status',0)->where('fk_int_offer_id',$q['pk_int_scratch_offers_id'])->count();
				  $q['win_count']=$win1+$win2;
				  $q['los_count']=$los1+$los2;
				  return $q;
			  });	  
	
	$camp=[];
	$win_count=[];
	$los_count=[];
	foreach($offers as $key=>$r)
	{
		$camp[$key]=$r->vchr_scratch_offers_name;
		$win_count[$key]=$r->win_count;
		$los_count[$key]=$r->los_count;
	}
	$chart['campaigns']=implode(",",$camp);
	$chart['win_count']=implode(",",$win_count);
	$chart['los_count']=implode(",",$los_count);
	//--------------------------------------------------
	
	
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
	
	$tc_count=ScratchWebCustomer::where('user_id',$user_id)->count();
	$twin_count=ScratchWebCustomer::where('user_id',$user_id)->where('win_status',1)->count();
	$tlos_count=ScratchWebCustomer::where('user_id',$user_id)->where('win_status',0)->count();
	
	$pie['tot_cust']=$tc_count;
	$pie['win_per']=($twin_count/$tc_count)*100;
	$pie['los_per']=($tlos_count/$tc_count)*100;
	$pie['tot_count']=$tc_count;
	$pie['win_count']=$twin_count;
	$pie['los_count']=$tlos_count;
	
	return view('users.dashboard',compact('tot_count','used_count','bal_count','sub','chart','sub_diff_days','pie'));
	
  }	
      
  
  public function shops()
  {
	
	$user_id=User::getVendorId();
	
	$tot_count=ScratchCount::getTotalScratchCount($user_id);
	$used_count=ScratchCount::getUsedScratchCount($user_id);
	$bal_count=ScratchCount::getBalanceScratchCount($user_id);

	return view('users.shop_dashboard',compact('tot_count','used_count','bal_count'));
	
  }	
   
}
