<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Facades\FileUpload;

use App\Models\ScratchOffer;
use App\Models\ScratchOffersListing;
use App\Models\ScratchWebCustomer;
use App\Models\ScratchCustomer;

use App\Models\User;
use App\Traits\GeneralTrait;

use Validator;

use DataTables;
use Session;
use Auth;
use Log;
use DB;
use Carbon\Carbon;


class CampaignDetailController extends Controller
{
  use GeneralTrait;
  
  public function __construct()
  {
     //$this->middleware('admin');
  }
  
  public function index()
  {
	  
  }
  
  public function getCampaign($id)
  {
	    
	  $offer = ScratchOffer::select('tbl_scratch_offers.*','scratch_type.type')
	  ->leftJoin('scratch_type','tbl_scratch_offers.type_id','=','scratch_type.id')
	  ->where('tbl_scratch_offers.pk_int_scratch_offers_id',$id)->first();
	  
	  $diff_days=0;
	  if($offer)
	  {
		$now=Carbon::now()->format('Y-m-d');  
		$date = Carbon::create($offer->end_date);
		$now = Carbon::create($now);
		$diff_days= round($now->diffInDays($date),0);
	  }
	  	  
	  $counts['total_count']=0;
	  $counts['used_count']=0;
	  $counts['bal_count']=0;
	  
	  $cnt = ScratchOffersListing::select(DB::raw("SUM(int_scratch_offers_count) as total_count"),DB::raw("SUM(int_scratch_offers_balance) as balance_count"))
	  ->where('fk_int_scratch_offers_id',$id)->first();	  
	  
	  $counts['total_count']=$cnt->total_count;
	  $counts['bal_count']=$cnt->balance_count;
	  
	  $usd_cnt = ScratchWebCustomer::select(DB::raw("COUNT(*) as used_count"))
	  ->where('offer_id',$id)->first();
	  
	  $counts['used_count']=$usd_cnt->used_count;
	
	
		$dealer_name="";
	  if(Auth::user()->int_role_id==1 and Auth::user()->admin_status==1)  //for hyundai
		{
			$dealer_name=User::where('pk_int_user_id',ScratchOffer::where('pk_int_scratch_offers_id',$id)->pluck('fk_int_user_id')->first())->pluck('vchr_user_name')->first();
		}
	  
	 return view('users.campaign.view_campaign_details_new',compact('dealer_name','offer','diff_days','counts',));
  }	
  

  public function viewWebCustomers(Request $request)
  {
			
        $user_id = User::getVendorId();
		
		$camp_id=$request->campaign_id;
		
		if(Auth::user()->int_role_id==1 and Auth::user()->admin_status==1)  //for hyundai
		{
			$user_id=ScratchOffer::where('pk_int_scratch_offers_id',$camp_id)->pluck('fk_int_user_id')->first();
		}
		
        $customers = ScratchWebCustomer::select('scratch_web_customers.*','scratch_branches.branch_name')
		->leftJoin('scratch_branches','scratch_web_customers.branch_id','=','scratch_branches.id')
		->where('user_id', $user_id)->where('offer_id',$camp_id)->orderBy('id', 'Desc')->get();
		//->where('redeem_source','web')	
  
        return DataTables::of($customers)
			->addIndexColumn()
			
			->addColumn('name', function ($row) {
                return $row->name;
            })
			->addColumn('offer', function ($row) {
                return $row->offer_text;
            })
			
			->addColumn('email', function ($row) {
                return $row->email??"--";
            })
			
			->addColumn('branch', function ($row) {
                return $row->branch_name??"--";
            })
			
			->addColumn('status', function ($row) {
                if($row->status==1)
					$win="<span class='text-green'>Win</span>";
				else
					$win="<span class='text-danger'>loss</span>";
				return $win;
            })
			
            ->addColumn('created', function ($row) {
                return date('d M Y h:i A', strtotime($row->created_at));
            })
			
			->editColumn('redeem', function ($row) {
                if((Auth::user()->int_role_id==1 and Auth::user()->admin_status==1) or (Auth::user()->parent_user_id!=NULL))
				{
					$red="<span class='text-green'>Redeemed</span>";
					if($row->status==0) 
						$red="<span class='text-green'>--</span>";
				}
				else
				{
					if($row->redeem==1 && $row->status==1) 
						$red="<span class='text-green'>Redeemed</span>";
					else if($row->status==0) 
						$red="<span class='text-danger'>--</span>";
					else
						$red="<span class='text-danger'>Pending</span>";
				}
						
			return $red;
            })
			
			->addColumn('mobile', function ($row) {
                $mob="+".$row->country_code." ".$row->mobile;
				return $mob;
            })
            ->rawColumns(['redeem','status'])
            ->tojson(true);
    }
	
	
	//app scratch customers list
		
	public function viewAppCustomers(Request $request)
	{
			
        $user_id = User::getVendorId();
		
		$camp_id=$request->campaign_id;
		
		if(Auth::user()->int_role_id==1 and Auth::user()->admin_status==1)  //for hyundai
		{
			$user_id=ScratchOffer::where('pk_int_scratch_offers_id',$camp_id)->pluck('fk_int_user_id')->first();

			$customers = ScratchWebCustomer::select('scratch_web_customers.*','tbl_users.vchr_user_name as branch_name')
			->leftJoin('tbl_users','scratch_web_customers.branch_id','=','tbl_users.pk_int_user_id')
			->where('user_id', $user_id)->where('offer_id',$camp_id)->where('redeem_source','app')->orderBy('id', 'Desc')->get();
		}
		else
		{
			$customers = ScratchWebCustomer::select('scratch_web_customers.*','scratch_branches.branch_name')
			->leftJoin('scratch_branches','scratch_web_customers.branch_id','=','scratch_branches.id')
			->where('user_id', $user_id)->where('offer_id',$camp_id)->where('redeem_source','app')->orderBy('id', 'Desc')->get();	
		}
  
        return DataTables::of($customers)
			->addIndexColumn()
			
			->addColumn('name', function ($row) {
                return $row->name;
            })
			->addColumn('offer', function ($row) {
                return $row->offer_text;
            })
			
			->addColumn('email', function ($row) {
                return $row->email??"--";
            })
			
			->addColumn('branch', function ($row) {
                return $row->branch_name??"--";
            })
			
			->addColumn('status', function ($row) {
                if($row->status==1)
					$win="<span class='text-green'>Win</span>";
				else
					$win="<span class='text-danger'>loss</span>";
				return $win;
            })
			
            ->addColumn('created', function ($row) {
                return date('d M Y h:i A', strtotime($row->created_at));
            })
			
			->editColumn('redeem', function ($row) {
				
				if(Auth::user()->int_role_id==1 and Auth::user()->admin_status==1)
				{
					$red="<span class='text-green'>Redeemed</span>";
					if($row->status==0) 
						$red="<span class='text-green'>--</span>";
				}
				else
				{
					if($row->redeem==1 && $row->status==1) 
						$red="<span class='text-green'>Redeemed</span>";
					else if($row->status==0) 
						$red="<span class='text-danger'>--</span>";
					else
						$red="<span class='text-danger'>Pending</span>";
				}
			return $red;
            })
			
			->addColumn('mobile', function ($row) {
                $mob="+".$row->country_code." ".$row->mobile;
				return $mob;
            })
            ->rawColumns(['redeem','status'])
            ->tojson(true);
    }
	

}


