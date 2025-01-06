<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\ScratchWebCustomer;
use App\Models\ScratchBranch;
use App\Models\User;

use App\Exports\ScratchWebCustomersList;

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
        
		return view('users.customers.scratch_web_customers');
    }

    public function getCustomers(Request $request)
    {
			
        $user_id = User::getVendorId();
		
		$sdate=$request->start_date;
		$edate=$request->end_date;
		
        $cust = ScratchWebCustomer::leftjoin('tbl_users', 'redeemed_agent', 'tbl_users.pk_int_user_id')
           ->select('scratch_web_customers.*', 'tbl_users.vchr_user_name as redeemed_agent')
           ->where('user_id', $user_id);
		   
		 if($sdate!="")
			 $cust->where('scratch_web_customers.created_at','>=',$sdate);
		 
		 if($edate!="")
			 $cust->where('scratch_web_customers.created_at','<=',$edate);
		   
		   
		$customers=$cust->orderBy('id', 'Desc')->get();
		
        return DataTables::of($customers)
			->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return date('d M Y h:i A', strtotime($row->created_at));
            })
            ->editColumn('branch', function ($row) {
                $branch = ScratchBranch::find($row->branch_id);
                return optional($branch)->branch;
            })
			->addColumn('mobile', function ($row) {
                $mob="+".$row->country_code." ".$row->mobile;
				return $mob;
            })
            ->addColumn('show', function ($row) {
                if ($row->redeem == ScratchWebCustomer::REDEEMED) {
                    return ' <button class="btn btn-sm btn-success btn-md-badge" data-toggle="tooltip" title="Redeemed"> <p class="text-white mb-0">Redeemed</p></button>';
                } else if ($row->redeem == ScratchWebCustomer::NOT_REDEEMED) {
                    return '<a href="javascript:;" class="btn btn-sm btn-warning btn-md-badge scratch-web-redeem" customer-id="'.$row->id.'" data-toggle="tooltip" title="Redeem offer"> Redeem</a>';
                }
            })
            ->rawColumns(['show'])
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


    /*public function downloadHistory(Request $request)
    {

        $fromdate = $request->start_date;
        $todate = $request->end_date;

        $userid = User::getVendorId();
        $customers = ScratchWebCustomer::leftjoin('tbl_users', 'redeemed_agent', 'tbl_users.pk_int_user_id')
					->where('user_id', $userid)
					// ->where('status', ScratchWebCustomer::SCRATCHED)
					->whereDate('scratch_web_customers.created_at', '>=', $fromdate)
					->whereDate('scratch_web_customers.created_at', '<=', $todate)
					->select('scratch_web_customers.name', 'scratch_web_customers.mobile', 'scratch_web_customers.short_link', 'scratch_web_customers.offer_text', 'scratch_web_customers.created_at', 'scratch_web_customers.redeem')
					->orderBy('scratch_web_customers.id', 'DESC')
					->get();

        return View('users.history.history_download', compact('customers', 'fromdate', 'todate'));
    }
	*/
	
	
	 public function exportWebCustomersList(Request $request)
	{
		$sdate="0";
		$edt="0";
		if($request->start_date!="")
			$sdate=date_create($request->start_date)->format('Y-m-d');
		
		if($request->end_date!="")
			$edate=date_create($request->end_date)->format('Y-m-d');
	
		 //return Excel::download($export, 'test.xlsx');
        return Excel::download(new ScratchWebCustomersList($sdate,$edate), 'scratch_web_customers_list'.'_'.date('Y-m-d').'.'.'xlsx');
    }
	
	

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
