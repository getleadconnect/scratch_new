<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\ScratchWebCustomer;
use App\Models\ScratchOffer;
use App\Models\ScratchBranch;
use App\Models\User;

use App\Exports\CustomersList;

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
		$users=User::where('int_role_id','!=',1)->get();
		return view('admin.customers.scratch_web_customers',compact('branches','offers','users'));
    }

    public function getWebCustomers(Request $request)
    {
		
		
        $customers= ScratchWebCustomer::select('scratch_web_customers.*', 'tbl_users.vchr_user_name as redeemed_agent','scratch_branches.branch_name')
			->leftjoin('tbl_users', 'scratch_web_customers.user_id', 'tbl_users.pk_int_user_id')
			->leftjoin('scratch_branches', 'scratch_web_customers.branch_id', 'scratch_branches.id')
            ->where('redeem_source','web')
		    ->where(function($where)use($request){
				if($request->user_id)
					$where->where('scratch_web_customers.user_id', $request->user_id);
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
					return $row->redeemed_agent;
				    return '---';
            })
			
			
			->addColumn('status', function ($row) {
                if($row->status==1)
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

        $customers= ScratchWebCustomer::select('scratch_web_customers.*', 'tbl_users.vchr_user_name as redeemed_agent','scratch_branches.branch_name')
			->leftjoin('tbl_users', 'scratch_web_customers.user_id', 'tbl_users.pk_int_user_id')
			->leftjoin('scratch_branches', 'scratch_web_customers.branch_id', 'scratch_branches.id')
            ->where('redeem_source','app')
		    ->where(function($where)use($request){
				if($request->user_id)
					$where->where('scratch_web_customers.user_id', $request->user_id);
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
					return $row->redeemed_agent;
				    return '---';
            })
 			
			->addColumn('status', function ($row) {
                if($row->status==1)
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

		
	public function exportCustomersList(Request $request)
	{
		$sdate="";
		$edt="";
		$branch=$request->branch;
		$campaign=$request->campaign;
		$user=$request->user_id;
		
		if($request->start_date!="")
			$sdate=date_create($request->start_date)->format('Y-m-d');
		
		if($request->end_date!="")
			$edate=date_create($request->end_date)->format('Y-m-d');

	 
		 //$dat=new CustomersList($sdate,$edate,$user,$branch,$campaign);
		 //dd($dat);
		 
		 
        return Excel::download(new CustomersList($sdate,$edate,$user,$branch,$campaign), 'scratch_customers_list'.'_'.date('Y-m-d').'.'.'xlsx');
    }
		


	public function getBranches($user_id)
	{
		$branches=ScratchBranch::where('vendor_id',$user_id)->get();
		$opt="<option value=''> Select Branch </option>";
		if($branches)
		{
			foreach($branches as $row)
			{
				$opt.="<option value='".$row->id."'>".$row->branch_name."</option>";
			}
		}
		return $opt;
	}


	public function getOffers($user_id)
	{
		$offers=ScratchOffer::where('fk_int_user_id',$user_id)->get();
		$opt="<option value=''> Select Campaign </option>";
		if($offers)
		{
			foreach($offers as $row)
			{
				$opt.="<option value='".$row->pk_int_scratch_offers_id."'>".$row->vchr_scratch_offers_name."</option>";
			}
		}
		return $opt;
	}
	
}
