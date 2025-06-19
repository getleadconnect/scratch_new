<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

use App\Models\ScratchOffer;
use App\Models\ScratchOffersListing;
use App\Models\ScratchType;

use App\Models\ScratchCustomer;
use App\Models\ScratchBranch;

use App\Exports\ScratchCustomersList;

use App\Models\User;

use Session;
use DataTables;
use Auth;


class ScratchHistoryController extends Controller
{
    
	
  public function index()
  {
	 $branches=ScratchBranch::where('vendor_id',User::getVendorId())->get();
     $offers=ScratchOffer::where('fk_int_user_id',User::getVendorId())->get();
	 return view('users.history.redeem_history',compact('branches','offers'));
  }	
  
  

  public function viewRedeemHistory(Request $request)
    {
		
      $userid=User::getVendorId();
      $customers = ScratchCustomer::where('fk_int_user_id',$userid)
      ->leftJoin('scratch_branches','scratch_branches.id','=','tbl_scratch_customers.branch_id')
      ->select('tbl_scratch_customers.*','scratch_branches.branch')
      ->where(function($where)use($request){
        if($request->branch)
            $where->where('branch_id',$request->branch);
        if($request->campaign)
            $where->where('campaign_id',$request->campaign);
        if($request->start_date &&  $request->end_date)  
        {
            $where->whereDate('tbl_scratch_customers.created_at','>=',$request->start_date)
               ->whereDate('tbl_scratch_customers.created_at','<=',$request->end_date);
        }  
       })
        /*->whereYear('tbl_scratch_customers.created_at', 2022)*/
        ->orderby('pk_int_scratch_customers_id','Desc')->get();
		
        foreach ($customers as $key => $row) {
                    
            if($row->offer_text==NULL)
            {
                $offer_text=ScratchOffersListing::where('pk_int_scratch_offers_listing_id',$row->fk_int_offer_id)->pluck('txt_description')->first();
                if($offer_text){
                    $row->offer_text=$offer_text;
                }
                $row->save();
                //$offer_text=FALSE;
                //$type_text=FALSE;
            }
						
            $type_text=ScratchType::where('id',$row->type_id)->pluck('type')->first();
            if($type_text)
                $row->type_name=$type_text;
            else
                $row->type_name='';

            $campaign=ScratchOffer::where('pk_int_scratch_offers_id',$row->campaign_id)->pluck('vchr_scratch_offers_name')->first();
            
            if($campaign)
                $row->campaign=$campaign;
            else
                $row->campaign='';
        }
		

        return Datatables::of($customers)
				->addIndexColumn()
                ->editColumn('name', function ($row) {
                    if ($row->vchr_name != null) {
                        return $row->vchr_name;
                    } else {
                        return "No name";
                    }
                    
                })
                ->editColumn('mobileno', function ($row) {
                    if ($row->vchr_mobno != null) {
                        return $row->vchr_mobno; 
                    } else {
                        return "No Mobile";
                    }
                })
                ->editColumn('dob', function ($row) {
                    if ($row->vchr_dob != null) {
                        return $row->vchr_dob;
                    } else {
                        return "No DOB";
                    }
                })
                ->editColumn('billno', function ($row) {
                    if ($row->vchr_billno != null) {
                        return $row->vchr_billno;
                    } else {
                        return "No billno";
                    }
                })
                ->editColumn('details', function ($row) {
                    if ($row->extrafield_values != null) {
                        $someObject = json_decode($row->extrafield_values);
                        if(!is_array($someObject)){
                            $someObject=array();
                            foreach($someObject as $key=>$someObject1)  
                            {
                                array_push($someObject,['label'=>$key,'value'=>$someObject1]);
                            }
                        }
                        $details = array();
                        foreach($someObject as $key=>$someObject1)  
                        { 
                            $details[] = $someObject[$key]->label.":".$someObject[$key]->value;
                        }
                        $implodeDetails= implode(',', $details);
                        // $replaceDetails = str_replace(' ', '', $implodeDetails);
                        return str_replace(',', "<br>", $implodeDetails);
                    } else {
                        return "No Details";
                    }
                })
				
                ->editColumn('created_at',function($row){
                    return date('d M Y h:i A',strtotime($row->created_at));
                })
                ->addColumn('action', function ($row) 
                {
				if ($row->int_status == 1) 
                    return '<button class="btn btn-sm btn-success btn-md-badge" data-toggle="tooltip" title="Redeemed"> <p class="text-white mb-0">Redeemed</p></button>';
                else
                    return '<a href="javascript:;" class="btn btn-sm btn-warning btn-md-badge offer-redeem testiminial-act" date-customerid="'.$row->pk_int_scratch_customers_id.'" data-toggle="tooltip" title="Redeem offer"> Redeem</a>';
                })
                ->rawColumns(['action','details'])
                ->toJson(true);
    }
	
	
	public function redeemOffers($id)
    {
      $offers = ScratchCustomer::where('pk_int_scratch_customers_id',$id)->first();
        if ($offers) {
            $offers->int_status = ScratchCustomer::ACTIVATE;
            $flag = $offers->save();
           if ($flag) {
                return response()->json(['msg' => "Redeemed Successfully.", 'status' => true]);
            } else {
                return response()->json(['msg' => "Something wrong, try again.", 'status' =>false]);
            }
        } else {
            return response()->json(['msg' => "Offers not found.", 'status' => false]);
        }
    }
	
	
	
	public function exportCustomersList(Request $request)
	{
	
		if($request->start_date!="")
			$sdate=date_create($request->start_date)->format('Y-m-d');
		
		if($request->end_date!="")
			$edate=date_create($request->end_date)->format('Y-m-d');
		
		$data['sdate']=$sdate;
        $data['edate']=$edate;
        $data['branch']=$request->branch;
        $data['campaign']=$request->campaign;
			
		 //return Excel::download($export, 'test.xlsx');
        return Excel::download(new ScratchCustomersList($data), 'scratch_customers_list'.'_'.date('Y-m-d').'.'.'xlsx');
    }
	
	
	
	
	
}
