<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;


use App\Models\User;

use Validator;
use DataTables;
use Session;
use Auth;
use Log;
use DB;

class DashboardController extends Controller
{
  public function __construct()
  {
     //$this->middleware('admin');
  }
  
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
