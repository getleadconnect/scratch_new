<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;


use App\Models\User;
use App\Models\ScratchOffer;
use App\Models\ScratchOffersListing;
use App\Exports\AnalyticsReport;

use Validator;
use DataTables;
use Session;
use Auth;
use Log;
use DB;

class ReportController extends Controller
{
  public function __construct()
  {
     //$this->middleware('admin');
  }
  
  public function index()
  {
	$user_id=User::getVendorId();
	 return view('users.reports.gift_analytics_reports');
  }	
  
  
   public function viewGiftAnalytics(Request $request)
    {
      $user_id=User::getVendorId();
	  	
		$giftDt=User::select('pk_int_user_id','vchr_user_name','mobile',DB::raw('SUM(int_scratch_offers_count) as total_gift_count'),DB::raw('SUM(int_scratch_offers_balance) as total_gift_balance'))
		->leftJoin('tbl_scratch_offers_listing','tbl_users.pk_int_user_id','=','tbl_scratch_offers_listing.fk_int_user_id')
		->where('parent_user_id',$user_id)->groupBy('pk_int_user_id','vchr_user_name','mobile')->get();
	
        return Datatables::of($giftDt)
		->addIndexColumn()
		
		->addColumn('user_id', function ($row) {
            return $row->pk_int_user_id;
        })
		
		->addColumn('name', function ($row) {
            return $row->vchr_user_name;
        })
		
		->addColumn('used_gift', function ($row) {
            return $row->total_gift_count-$row->total_gift_balance;
        })
		
        ->rawColumns(['action'])
        ->make(true);
    }
	
  
  public function exportAnalyticsReport(Request $request)
	{
        return Excel::download(new AnalyticsReport(), 'scratch_analytics_reports'.'_'.date('Y-m-d').'.'.'xlsx');
    }
	
  
 //-----------------------------------------------------------
 
 public function branchGiftReport()
  {
	$user_id=User::getVendorId();
		 	
		$giftDt=User::select('pk_int_user_id','vchr_user_name')
		->where('parent_user_id',$user_id)->get()->map(function($q)
		{
			$dt=ScratchOffersListing::where('fk_int_user_id',$q->pk_int_user_id)->get();
			$q['gift']=$dt;
			return $q;
		});
	
	
	 return view('users.reports.branch_wise_gift_reports',compact('giftDt'));
  }	
  
  
   public function viewBranchWiseGiftReport(Request $request)
    {
      $user_id=User::getVendorId();
	  	
		$giftDt=User::select('pk_int_user_id','vchr_user_name')
		->where('parent_user_id',$user_id)->get()->map(function($q)
		{
			$dt=ScratchOffersListing::where('fk_int_user_id',$q->pk_int_user_id)->get();
			$q['gift']=$dt;
			return $q;
		});

	
        return Datatables::of($giftDt)
		->addIndexColumn()
		
		->addColumn('user_id', function ($row) {
            return $row->pk_int_user_id;
        })
		
		->addColumn('name', function ($row) {
            return $row->vchr_user_name;
        })
		
		->addColumn('used_gift', function ($row) {
            return $row->total_gift_count-$row->total_gift_balance;
        })
		
        ->rawColumns(['action'])
        ->make(true);
    }
	
  
  /*public function exportAnalyticsReport(Request $request)
	{
        return Excel::download(new AnalyticsReport(), 'scratch_analytics_reports'.'_'.date('Y-m-d').'.'.'xlsx');
    }
	*/
 
 
}
