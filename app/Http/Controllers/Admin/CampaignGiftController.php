<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Facades\FileUpload;

use App\Models\ScratchOffer;
use App\Models\ScratchOffersListing;
use App\Models\ScratchType;
use App\Models\ScratchCount;
use App\Models\User;


use Validator;

use DataTables;
use Session;
use Auth;
use Log;
use DB;


class CampaignGiftController extends Controller
{
  public function __construct()
  {
     //$this->middleware('admin');
  }
  
  public function index()
  {
	 $user=User::where('int_status',1)->get();
	 return view('admin.view_campaign_gifts_list',compact('user'));
  }	
  
   public function deletedGiftsList()
  {
	 $user=User::where('int_status',1)->get();
	 return view('admin.view_deleted_gifts_list',compact('user'));
  }	
	
 public function viewCampaignGiftListings(Request $request)
    {
		
      $offer = ScratchOffersListing::select('tbl_scratch_offers_listing.*','scratch_type.type as type_name','tbl_users.vchr_user_name','tbl_scratch_offers.vchr_scratch_offers_name')
	  ->leftJoin('tbl_scratch_offers','tbl_scratch_offers_listing.fk_int_scratch_offers_id','=','tbl_scratch_offers.pk_int_scratch_offers_id')
	  ->leftJoin('tbl_users','tbl_scratch_offers_listing.fk_int_user_id','=','tbl_users.pk_int_user_id')
	  ->leftJoin('scratch_type','tbl_scratch_offers_listing.type_id','=','scratch_type.id');
	  	  
	  if($request->user_id!="")
	  {
		  $offer->where('tbl_scratch_offers_listing.fk_int_user_id',$request->user_id);
	  }
	  
	  if($request->offer_id!="")
	  {
		  $offer->where('tbl_scratch_offers_listing.fk_int_scratch_offers_id',$request->offer_id);
	  }
	  
	  $offers=$offer->orderby('pk_int_scratch_offers_listing_id','Desc')->get();
	
        return Datatables::of($offers)
		->addIndexColumn()
        		
		->editColumn('image', function ($row) {
            if ($row->image !='') {
				return  '<img src='.FileUpload::viewFile($row->image,'local').' width="50" height="50" >';
                //return  '<img src='.FileUpload::viewFile($row->image,'local').' width="50" height="50" data-id='.$row->pk_int_scratch_offers_listing_id.'" id="imgUpload" style="cursor:pointer" title="Click to change image"> </img>';
            } else {
                return "--Nil--";
            }
        })
		
		 ->addColumn('username', function ($row) 
        {
            return $row->vchr_user_name;
        })
		
		 ->addColumn('campaign', function ($row) 
        {
            return $row->vchr_scratch_offers_name;
        })
				
		 ->addColumn('win_status', function ($row) 
        {
            if ($row->int_winning_status== 1) 
			{
                $wst='<i class="fa fa-trophy text-success  fa-2x" aria-hidden="true"></i>';
            }
            else
            {
                $wst= '<i class="fa fa-frown fa-2x" style="color:#ff9f43"></i>';
			}
            return $wst;
        })
		
		->addColumn('status', function ($row) 
        {
             if ($row->int_status==1) {
                $status='<span class="badge rounded-pill bg-success">Active</span>';
            } else {
                $status='<span class="badge rounded-pill bg-danger">Inactive</span>';
            }
			return $status;
        })
			
		
        ->rawColumns(['image','win_status','status'])
        ->make(true);
    }
	
	
	
	public function viewDeletedGiftListings(Request $request)
    {
      $offer = ScratchOffersListing::onlyTrashed()->select('tbl_scratch_offers_listing.*','scratch_type.type as type_name','tbl_users.vchr_user_name','tbl_scratch_offers.vchr_scratch_offers_name')
	  ->leftJoin('tbl_scratch_offers','tbl_scratch_offers_listing.fk_int_scratch_offers_id','=','tbl_scratch_offers.pk_int_scratch_offers_id')
	  ->leftJoin('tbl_users','tbl_scratch_offers_listing.fk_int_user_id','=','tbl_users.pk_int_user_id')
	  ->leftJoin('scratch_type','tbl_scratch_offers_listing.type_id','=','scratch_type.id')
	  ->where('tbl_scratch_offers_listing.deleted_at','<>',NULL);
	  	  
	  if($request->user_id!="")
	  {
		  $offer->where('tbl_scratch_offers_listing.fk_int_user_id',$request->user_id);
	  }
	  
	  if($request->offer_id!="")
	  {
		  $offer->where('tbl_scratch_offers_listing.fk_int_scratch_offers_id',$request->offer_id);
	  }
	  
	  $offers=$offer->orderby('pk_int_scratch_offers_listing_id','Desc')->get();
	  
		 
        return Datatables::of($offers)
		->addIndexColumn()
        		
		->editColumn('image', function ($row) {
            if ($row->image !='') {
				return  '<img src='.FileUpload::viewFile($row->image,'local').' width="50" height="50" >';
            } else {
                return "--Nil--";
            }
        })
		
		 ->addColumn('username', function ($row) 
        {
            return $row->vchr_user_name;
        })
		
		 ->addColumn('campaign', function ($row) 
        {
            return $row->vchr_scratch_offers_name;
        })
				
		 ->addColumn('status', function ($row) 
        {
            if ($row->int_winning_status== 1) 
			{
                $wst='<i class="fa fa-trophy text-success  fa-2x" aria-hidden="true"></i>';
            }
            else
            {
                $wst= '<i class="fa fa-frown fa-2x" style="color:#ff9f43"></i>';
			}
            return $wst;
        })
        ->rawColumns(['image','status'])
        ->make(true);
    }

}
