<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Facades\FileUpload;

use App\Models\ScratchOffer;
use App\Models\ScratchWebCustomer;
use App\Models\ScratchBranch;

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
use Carbon\Carbon;


class CampaignGiftController extends Controller
{
  
  public function __construct()
  {
     //$this->middleware('admin');
  }
  
  public function index()
  {
  }
 
 
public function addGifts($id)
{
	$user_id=User::getVendorId();
	$sof=ScratchOffer::select('tbl_scratch_offers.*','scratch_type.type')
		->leftJoin('scratch_type','tbl_scratch_offers.type_id','scratch_type.id')->where('pk_int_scratch_offers_id',$id)->first();
	$sbal_count=ScratchCount::where('fk_int_user_id',$user_id)->pluck('balance_count')->first();
	return view('users.campaign.add_campaign_gifts',compact('sof','sbal_count'));
}

   
 public function saveGifts(Request $request)  //additional gifts
    {

		if($request->offers_count=="")
		{
			Session::flash('fail',"Scratch gift details missing.");
        	return back()->withInput();
		}
				
		DB::beginTransaction();

            try
			{
            	$user_id=User::getVendorId();

            	$offerId=$request->campaign_id;
				$typeId=$request->offer_type_id;
				
            	$offerCounts=$request->offers_count;
            	$description=$request->description;
            	$winningstatus=$request->winning_status;
				
						$file_image=$request->image_list;  
						$nameOffer = uniqid(). '.' . $file_image->getClientOriginalExtension();
						$filePath = 'offersListing/';
						FileUpload::uploadFile($file_image, $filePath,$nameOffer,'local');
												
						$lst=new ScratchOffersListing();
						$lst->fk_int_user_id=$user_id;
						$lst->created_by=$user_id;
						$lst->fk_int_scratch_offers_id=$offerId;
						$lst->int_scratch_offers_count=$offerCounts;
						$lst->int_scratch_offers_balance=$offerCounts;
						$lst->txt_description=$description;
						$lst->type_id=$typeId;
						$lst->int_winning_status=$winningstatus;
						$lst->int_status="1";
              
						//$lst->gift_image=$filePath.$nameOffer;
						$lst->image=$filePath.$nameOffer;      						
						$flag=$lst->save();

				
				$sc=ScratchCount::where('fk_int_user_id',$user_id)->first();  //update scratch count
				$cbal=$request->scratch_balance;
				$ucount=$sc->total_count-$request->scratch_balance;
				
				$sc->used_count=$ucount;
				$sc->balance_count=$request->scratch_balance;
				$sc->save();

				if($flag)
        		{   
					DB::commit();
					return response()->json(['msg'=>'Gift successfully saved','status'=>true]);
        		}
        		else
        		{
					DB::rollback();
					return response()->json(['msg'=>'Something wrong, try again.','status'=>false]);
        		}
	
           }
            catch(\Exception $e)
            {
				DB::rollback();
	       		return response()->json(['msg'=>$e->getMessage(),'status'=>false]);
            }
    } 




	public function getCustomers(Request $request)
	{
			
        $user_id = User::getVendorId();
		
		$camp_id=$request->campaign_id;
		
        $customers = ScratchWebCustomer::select('scratch_web_customers.*','scratch_branches.branch_name')
		->leftJoin('scratch_branches','scratch_web_customers.branch_id','=','scratch_branches.id')
		->where('user_id', $user_id)->where('offer_id',$camp_id)->orderBy('id', 'Desc')->get();	
		   
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
                if($row->redeem==1)
				$red="<span class='text-green'>Redeemed</span>";
				else
				$red="<span class='text-danger'>Pending</span>";
			return $red;
			
            })
									
           /* ->editColumn('branch', function ($row) {
                $branch = ScratchBranch::find($row->branch_id);
                return optional($branch)->branch;
            })*/
			
			->addColumn('mobile', function ($row) {
                $mob="+".$row->country_code." ".$row->mobile;
				return $mob;
            })
            ->rawColumns(['redeem','status'])
            ->tojson(true);
    }

	
 public function viewCampaignGifts(Request $request)
    {
      $id=User::getVendorId();
	  $offer_id=$request->campaign_id;
		
      $offers = ScratchOffersListing::select('tbl_scratch_offers_listing.*','scratch_type.type as type_name')
	  ->leftJoin('scratch_type','tbl_scratch_offers_listing.type_id','=','scratch_type.id')
	  ->where('fk_int_user_id',$id)->where('fk_int_scratch_offers_id',$offer_id)
	  ->orderby('pk_int_scratch_offers_listing_id','Desc')->get();
	
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
		
		 ->addColumn('status', function ($offers) 
        {
            if ($offers->int_winning_status== 1) 
			{
                $wst='<i class="fa fa-trophy text-success  fa-2x" aria-hidden="true"></i>';
            }
            else
            {
                $wst= '<i class="fa fa-frown fa-2x" style="color:#ff9f43"></i>';
			}
            return $wst;
        })
		
        ->addColumn('action', function ($row)
        {
			if ($row->int_status == 1)
			{
				$btn='<a class="dropdown-item" href="#">Deactivate</a>';
			}
			else
			{
				$btn='<a class="dropdown-item" href="#">Activate</a>';
			}

			return '<a href="#" id="'.$row->pk_int_scratch_offers_listing_id.'" class="btn btn-sm btn-outline-light delete-gift" aria-expanded="false"><i class="fa fa-trash" style="font-size:14px;color:#eb4e4e;"></i></a>';
            
        })
        ->rawColumns(['action','image','status'])
        ->make(true);
    }



public function deleteGift($id)
    {
		
		$user_id=User::getVendorId();
         try {
            $data = ScratchOffersListing::where('pk_int_scratch_offers_listing_id', $id)->first();
            if ($data) 
			{
                
				$scount=$data->int_scratch_offers_balance;
				
				$lst_id=$data->id;
				FileUpload::deleteFile($data->image,'local');
				$res=$data->delete();
								
				$sc=ScratchCount::where('fk_int_user_id',$user_id)->first();  //update scratch count
				$sc->used_count=($sc->used_count-$scount);
				$sc->balance_count=($sc->balance_count+$scount);
				$sc->save();
				
				return response()->json(['msg' => 'Gift details successfully removed.', 'status' =>true]);
            }
            else
            {
                return response()->json(['msg' => 'Something Went Wrong', 'status' => false]);
            }
        }
        catch (\Exception $ex) {
              return response()->json(['msg' => $ex->getMessage(), 'status' => false]);
        }
    }
	
	
public function uploadOfferGiftImage(Request $request)
{
		$file_image= $request->picField;  
        $offer =  ScratchOffersListing::where('pk_int_scratch_offers_listing_id',$request->scrId)->first();
        $file_image= $request->picField;
        $path_list='/offersListing/';
        $nameScratchOffer = mt_rand(). '.' . $file_image->getClientOriginalExtension();
        FileUpload::uploadFile($file_image, $path_list,$nameScratchOffer,'local');

        $offer->image=$path_list.$nameScratchOffer;                    
        $offer->save();

        return redirect()->back()->with('success', 'Image update successfully!');

}


public function offerActivateDeactivate($op,$id)
	{
		if($op==1)
		{
		   $new=['int_status'=>1];
		}
		else
		{	
		   $new=['int_status'=>0];
		}

		$result=ScratchOffer::where('pk_int_scratch_offers_id',$id)->update($new);
		
			if($result)
			{
				if($op==1)
					return response()->json(['msg' =>'Campaign successfully activated!' , 'status' => true]);
				else
				    return response()->json(['msg' =>'Campaign successfully deactivated!' , 'status' => true]);
			}
			else
			{
				return response()->json(['msg' =>'Something wrong, try again!' , 'status' => false]);
			}				

	}


//View deleted gifts details 


 public function deleteGiftsList()
  {
	 $offers=ScratchOffer::where('fk_int_user_id',User::getVendorId())->get();
	 return view('users.campaign.view_deleted_gifts_list',compact('offers'));
  }	
  
  
public function viewDeletedGiftListings(Request $request)
    {
		
	  $user_id=User::getVendorId();
	  
	  $offer = ScratchOffersListing::onlyTrashed()->select('tbl_scratch_offers_listing.*','scratch_type.type as type_name','tbl_scratch_offers.vchr_scratch_offers_name')
	 ->leftJoin('tbl_scratch_offers','tbl_scratch_offers_listing.fk_int_scratch_offers_id','=','tbl_scratch_offers.pk_int_scratch_offers_id')
	 ->leftJoin('scratch_type','tbl_scratch_offers_listing.type_id','=','scratch_type.id')
	  ->where('tbl_scratch_offers_listing.fk_int_user_id',$user_id)->where('tbl_scratch_offers_listing.deleted_at','<>',NULL);
		  
	  
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
		 ->addColumn('campaign', function ($row) 
        {
            return $row->vchr_scratch_offers_name;
        })
				
		 ->addColumn('status', function ($offers) 
        {
            if ($offers->int_winning_status== 1) 
			{
                $wst='<i class="fa fa-trophy text-success  fa-2x" aria-hidden="true"></i>';
            }
            else
            {
                $wst= '<i class="fa fa-frown fa-2x" style="color:#ff9f43"></i>';
			}
            return $wst;
        })
		
        ->rawColumns(['action','image','status'])
        ->make(true);
    }





}
