<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\ScratchWebCustomer;
use App\Models\ScratchOffer;
use App\Models\ScratchOffersListing;
use App\Models\ScratchBranch;
use App\Models\User;

use App\Exports\ScratchWebCustomersList;
use App\Exports\ScratchRedeemedCustomersList;

use Auth;
use DataTables;
use Session;

class ScratchWebController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $branches=ScratchBranch::where('vendor_id',User::getVendorId())->get();
		$offers=ScratchOffer::where('fk_int_user_id',User::getVendorId())->get();
		return view('users.customers.scratch_web_customers',compact('branches','offers'));
    }

    public function getWebCustomers(Request $request)
    {
			
        $user_id = User::getVendorId();
		
        $customers= ScratchWebCustomer::select('scratch_web_customers.*', 'tbl_users.vchr_user_name as redeemed_agent_name','scratch_branches.branch_name')
			->leftjoin('tbl_users', 'scratch_web_customers.user_id', 'tbl_users.pk_int_user_id')
			->leftjoin('scratch_branches', 'scratch_web_customers.branch_id', 'scratch_branches.id')
            ->where('user_id', $user_id)->where('redeem_source','web')
		    ->where(function($where)use($request){
				if($request->branch)
					$where->where('scratch_web_customers.branch_id',$request->branch);
				if($request->campaign)
					$where->where('scratch_web_customers.offer_id',$request->campaign);
				if($request->start_date &&  $request->end_date)  
				{
					$where->whereDate('scratch_web_customers.created_at','>=',$request->start_date)
					   ->whereDate('scratch_web_customers.created_at','<=',$request->end_date);
				}  
			   })->orderBy('id', 'Desc')->get();


        return DataTables::of($customers)
			->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return date('d-m-Y h:i A', strtotime($row->created_at));
            })
            ->editColumn('branch', function ($row) {
                if($row->branch_name!="")
					return $row->branch_name;
					return "---";
            })
			->addColumn('billno', function ($row) {
                if($row->bill_no!="")
					return $row->bill_no;
					return "---";
            })
			->addColumn('email', function ($row) {
                if($row->email!="")
					return $row->email;
					return "---";
            })
			->addColumn('mobile', function ($row) {
                $mob="+".$row->country_code." ".$row->mobile;
				return $mob;
            })
			->addColumn('agent', function ($row) {
                if ($row->redeemed_agent!=null)
					return $row->redeemed_agent_name;
				    return '---';
            })

			->addColumn('status', function ($row) {
                if($row->win_status==1)
					$win="<span class='text-green'>Win</span>";
				else
					$win="<span class='text-danger'>loss</span>";
				return $win;
            })
			
			->addColumn('show', function ($row) {
                if($row->redeem==1 && $row->win_status==1) 
					$red="<span class='text-green'>Redeemed</span>";
				else if($row->win_status==0) 
					$red="<span class='text-danger'>--</span>";
				else
					$red="<span class='text-danger'>Pending</span>";
				
                return $red;
            })
			
            ->rawColumns(['show','status'])
            ->tojson(true);
    }
	
//app scratch cutomers 

public function getAppCustomers(Request $request)
    {
			
        $user_id = User::getVendorId();
		
        $customers= ScratchWebCustomer::select('scratch_web_customers.*', 'tbl_users.vchr_user_name as redeemed_agent_name','scratch_branches.branch_name')
			->leftjoin('tbl_users', 'scratch_web_customers.user_id', 'tbl_users.pk_int_user_id')
			->leftjoin('scratch_branches', 'scratch_web_customers.branch_id', 'scratch_branches.id')
            ->where('user_id', $user_id)->where('redeem_source','app')
		    ->where(function($where)use($request){
				if($request->branch)
					$where->where('scratch_web_customers.branch_id',$request->branch);
				if($request->campaign)
					$where->where('scratch_web_customers.offer_id',$request->campaign);
				if($request->start_date &&  $request->end_date)  
				{
					$where->whereDate('scratch_web_customers.created_at','>=',$request->start_date)
					   ->whereDate('scratch_web_customers.created_at','<=',$request->end_date);
				}  
			   })->orderBy('id', 'Desc')->get();
		
        return DataTables::of($customers)
			->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return date('d-m-Y h:i A', strtotime($row->created_at));
            })
            ->editColumn('branch', function ($row) {
                if($row->branch_name!="")
					return $row->branch_name;
					return "---";
            })
			->addColumn('billno', function ($row) {
                if($row->bill_no!="")
					return $row->bill_no;
					return "---";
            })
			->addColumn('email', function ($row) {
                if($row->email!="")
					return $row->email;
					return "---";
            })
			->addColumn('mobile', function ($row) {
                $mob="+".$row->country_code." ".$row->mobile;
				return $mob;
            })
			
			->addColumn('agent', function ($row) {
                if ($row->redeemed_agent!="")
					return $row->redeemed_agent_name;
				    return '---';
            })

			->addColumn('status', function ($row) {
                if($row->win_status==1)
					$win="<span class='text-green'>Win</span>";
				else
					$win="<span class='text-danger'>loss</span>";
				return $win;
            })
			
			->addColumn('show', function ($row) {
				
				if($row->redeem==1 && $row->win_status==1) 
					$red="<span class='text-green'>Redeemed</span>";
				else if($row->win_status==0) 
					$red="<span class='text-danger'>--</span>";
				else
					$red="<span class='text-danger'>Pending</span>";
				
                return $red;
            })
			
            ->rawColumns(['show','status'])
            ->tojson(true);
    }

    public function redeem($id)
    {
        $flag = ScratchWebCustomer::where('id', $id)->update([
            'redeem' => ScratchWebCustomer::REDEEMED,
            'redeemed_on' => now(),
            'redeemed_agent' => Auth::id()]);
        if ($flag) {
            return response()->json(['msg' => "Redeemed Successfully.", 'status' => true]);
        }
        return response()->json(['msg' => "Something Went Wrong !! Try Again Later", 'status' =>false]);
    }

		
	public function exportWebCustomersList(Request $request)
	{
		$sdate="";
		$edt="";
		$branch=$request->branch;
		$campaign=$request->campaign;
		
		if($request->start_date!="")
			$sdate=date_create($request->start_date)->format('Y-m-d');
		
		if($request->end_date!="")
			$edate=date_create($request->end_date)->format('Y-m-d');

		 //return Excel::download($export, 'test.xlsx');
        return Excel::download(new ScratchWebCustomersList($sdate,$edate,$branch,$campaign), 'scratch_web_customers_list'.'_'.date('Y-m-d').'.'.'xlsx');
    }
	
		
	public function redeemScratch()
	{
		return View('users.customers.redeem_scratch');
	}
	
	
	public function redeemScratchNow(Request $request)
	{
		$code_mob=$request->code_mobile;
		$user_id=User::getVendorId();
		$cust=[];
		
		$cust1=ScratchWebCustomer::select('scratch_web_customers.*','tbl_scratch_offers.vchr_scratch_offers_name','scratch_branches.branch_name')
			->leftJoin('tbl_scratch_offers','scratch_web_customers.offer_id','tbl_scratch_offers.pk_int_scratch_offers_id')
			->leftJoin('scratch_branches','scratch_web_customers.branch_id','scratch_branches.id')
			->where('user_id',$user_id);
		
		if($code_mob!="" and is_numeric($code_mob))
		{
			$cust1->where('mobile', 'like', "%".$code_mob);
			
		}
		else
		{
			$cust1->where('unique_id', 'like', "%".$code_mob);
		}
		
		$cust=$cust1->first();
		$offer_pic="";
		if(!empty($cust))
		{
			$offer_pic=ScratchOffersListing::where('pk_int_scratch_offers_listing_id',$cust->offer_list_id)->pluck('image')->first();
		}

		return View('users.customers.redeem_customer_detail',compact('cust','offer_pic'));
		
	}
	
	
	
	//REDEEM CUSTOMERS LIST -----------------------------------------------------
	
	public function redeemedCustomers()
    {
        $branches=ScratchBranch::where('vendor_id',User::getVendorId())->get();
		$offers=ScratchOffer::where('fk_int_user_id',User::getVendorId())->get();
		return view('users.customers.redeemed_customers_list',compact('branches','offers'));
    }

    public function viewRedeemedCustomers(Request $request)
    {
			
        $user_id = User::getVendorId();
		
        $customers= ScratchWebCustomer::select('scratch_web_customers.*', 'tbl_users.vchr_user_name as redeemed_agent_name','scratch_branches.branch_name')
			->leftjoin('tbl_users', 'scratch_web_customers.user_id', 'tbl_users.pk_int_user_id')
			->leftjoin('scratch_branches', 'scratch_web_customers.branch_id', 'scratch_branches.id')
            ->where('user_id', $user_id)->where('redeem',1)
		    ->where(function($where)use($request){
				if($request->branch)
					$where->where('scratch_web_customers.branch_id',$request->branch);
				if($request->campaign)
					$where->where('scratch_web_customers.offer_id',$request->campaign);
				if($request->start_date &&  $request->end_date)  
				{
					$where->whereDate('scratch_web_customers.created_at','>=',$request->start_date)
					   ->whereDate('scratch_web_customers.created_at','<=',$request->end_date);
				}  
			   })->orderBy('id', 'Desc')->get();


        return DataTables::of($customers)
			->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return date('d-m-Y h:i A', strtotime($row->created_at));
            })
            ->editColumn('branch', function ($row) {
                if($row->branch_name!="")
					return $row->branch_name;
					return "---";
            })
			->addColumn('billno', function ($row) {
                if($row->bill_no!="")
					return $row->bill_no;
					return "---";
            })
			->addColumn('email', function ($row) {
                if($row->email!="")
					return $row->email;
					return "---";
            })
			->addColumn('mobile', function ($row) {
                $mob="+".$row->country_code." ".$row->mobile;
				return $mob;
            })
			->addColumn('agent', function ($row) {
                if ($row->redeemed_agent!=null)
					return $row->redeemed_agent_name;
				    return '---';
            })

			->addColumn('status', function ($row) {
                if($row->win_status==1)
					$win="<span class='text-green'>Win</span>";
				else
					$win="<span class='text-danger'>loss</span>";
				return $win;
            })
			
			->addColumn('show', function ($row) {
                if($row->redeem==1 && $row->win_status==1) 
					$red="<span class='text-green'>Redeemed</span>";
				else if($row->win_status==0) 
					$red="<span class='text-danger'>--</span>";
				else
					$red="<span class='text-danger'>Pending</span>";
				
                return $red;
            })
			
            ->rawColumns(['show','status'])
            ->tojson(true);
    }
	
	public function exportRedeemedCustomersList(Request $request)
	{
		$sdate="";
		$edt="";
		$branch=$request->branch;
		$campaign=$request->campaign;
		
		if($request->start_date!="")
			$sdate=date_create($request->start_date)->format('Y-m-d');
		
		if($request->end_date!="")
			$edate=date_create($request->end_date)->format('Y-m-d');

		 //return Excel::download($export, 'test.xlsx');
        return Excel::download(new ScratchRedeemedCustomersList($sdate,$edate,$branch,$campaign), 'scratch_redeemed_customers_list'.'_'.date('Y-m-d').'.'.'xlsx');
    }
}
