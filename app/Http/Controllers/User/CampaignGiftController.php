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

		$validate=Validator::make($request->all(),
			[
			'offer_count'=>'required',
			//'gift_image'=>'required|mimes:jpeg,png,jpg,gif,svg|max:524288',    //500kb
			'description'=>'required',
			'winning_status'=>'required']);
		
		if($validate->fails())
		{
			return response()->json(['msg'=>'Gift  details missing!','status'=>false]);
		}
				
		DB::beginTransaction();

            try
			{
            	$user_id=User::getVendorId();

					$offerId=$request->campaign_id;
					$gift_image=$request->better_luck_image;
					$filePath = 'offers_listing/';
					
					if($request->gift_image)
					{
						$file_image=$request->gift_image;  
						$gift_image = uniqid(). '.' . $file_image->getClientOriginalExtension();
						
						FileUpload::uploadFile($file_image, $filePath,$gift_image,'local');
					}
											
						$lst=new ScratchOffersListing();
						$lst->fk_int_user_id=$user_id;
						$lst->created_by=$user_id;
						$lst->fk_int_scratch_offers_id=$offerId;
						$lst->int_scratch_offers_count=$request->offer_count;
						$lst->int_scratch_offers_balance=$request->offer_count;
						$lst->txt_description=$request->description;
						$lst->type_id=$request->offer_type_id;
						$lst->int_winning_status=$request->winning_status;
						$lst->int_status="1";
              
						//$lst->gift_image=$filePath.$nameOffer;
						$lst->image=$filePath.$gift_image;      						
						$flag=$lst->save();

				
				$sc=ScratchCount::where('fk_int_user_id',$user_id)->first();  //update scratch count
				$offer_count=$request->offer_count;
				$ucount=$sc->total_count-($sc->used_count+$request->offer_count);
				
				$sc->used_count=$sc->used_count+$offer_count;
				$sc->balance_count=$sc->balance_count-$offer_count;
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
                $wst='<i class="fa fa-trophy text-success" style="font-size:20px;" aria-hidden="true"></i>';
            }
            else
            {
                $wst= '<i class="fa fa-frown " style="color:#ff9f43;font-size:20px;"></i>';
			}
            return $wst;
        })
		
        ->addColumn('action', function ($row)
        {
			return '<a href="#" id="'.$row->pk_int_scratch_offers_listing_id.'" class="btn btn-sm btn-outline-light edit-gift" data-bs-toggle="offcanvas" data-bs-target="#edit_gift"><i class="fa fa-pencil-alt" style="font-size:14px;color:#5779f1;"></i></a>
					<a href="#" id="'.$row->pk_int_scratch_offers_listing_id.'" class="btn btn-sm btn-outline-light delete-gift" aria-expanded="false"><i class="fa fa-trash" style="font-size:14px;color:#eb4e4e;"></i></a>';
            
        })
        ->rawColumns(['action','image','status'])
        ->make(true);
    }

public function deleteGift($id)
    {
		
		$user_id=User::getVendorId();
         try {
			 
			$cnt=ScratchWebCustomer::where('offer_list_id',$id)->count();
			if($cnt>0)
			{
				return response()->json(['msg' => "Customer already scratched, Can't remove this gift!.",'status' =>false]);
			}	

			 
            $data = ScratchOffersListing::where('pk_int_scratch_offers_listing_id', $id)->first();
            if ($data) 
			{
				
				$scount=$data->int_scratch_offers_balance;
				$lst_id=$data->id;
				if($data->winning_status==1)
					FileUpload::deleteFile($data->image,'local');
				
				$res=$data->delete();
								
				$sc=ScratchCount::where('fk_int_user_id',$user_id)->first();  //update scratch count
				$sc->used_count=($sc->used_count-$scount);
				$sc->balance_count=($sc->balance_count+$scount);
				$sc->save();

				return response()->json(['msg' => 'Gift details successfully removed.','offer_count'=>$scount,'status' =>true]);
            }
            else
            {
                return response()->json(['msg' => 'Something Went Wrong','status' => false]);
            }
        }
        catch (\Exception $ex) {
              return response()->json(['msg' => $ex->getMessage(), 'status' => false]);
        }
    }
	
	
/*public function uploadOfferGiftImage(Request $request)
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

}*/


//--------------------------------------------------------------------------------------------------------


  public function giftsList()
  {
	 $user_id=User::getVendorId();
	 $offers=ScratchOffer::where('fk_int_user_id',$user_id)->get();
	 return view('users.campaign.view_gifts_list',compact('offers'));
  }	
  
	
 public function viewGiftListings(Request $request)
    {
	  $user_id=User::getVendorId();
      $offer = ScratchOffersListing::select('tbl_scratch_offers_listing.*','scratch_type.type as type_name','tbl_users.vchr_user_name','tbl_scratch_offers.vchr_scratch_offers_name')
	  ->leftJoin('tbl_scratch_offers','tbl_scratch_offers_listing.fk_int_scratch_offers_id','=','tbl_scratch_offers.pk_int_scratch_offers_id')
	  ->leftJoin('tbl_users','tbl_scratch_offers_listing.fk_int_user_id','=','tbl_users.pk_int_user_id')
	  ->leftJoin('scratch_type','tbl_scratch_offers_listing.type_id','=','scratch_type.id')
	  ->where('tbl_scratch_offers_listing.fk_int_user_id',$user_id);
	  
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
		
		 ->addColumn('gift_count', function ($row) 
        {
            return $row->int_scratch_offers_balance."/".$row->int_scratch_offers_count;
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
		
		->addColumn('action', function ($row)
        {
			$action='<div class="fs-5 ms-auto dropdown">
                          <div class="dropdown-toggle dropdown-toggle-nocaret cursor-pointer" data-bs-toggle="dropdown"><i class="fadeIn animated bx bx-dots-vertical"></i></div>
                            <ul class="dropdown-menu">
                              <li><a class="dropdown-item edit-gift" href="javascript:;" id="'.$row->pk_int_scratch_offers_listing_id.'" data-bs-toggle="offcanvas" data-bs-target="#edit-gift" ><i class="lni lni-pencil-alt"></i> Edit</a></li>
                              <li><a class="dropdown-item delete-gift" href="javascript:;" id="'.$row->pk_int_scratch_offers_listing_id.'"><i class="lni lni-trash"></i> Delete</a></li>
							  </ul>
                        </div>';
			return $action;
        })

        ->rawColumns(['image','action','status'])
        ->make(true);
    }
	
public function edit($id)
{
	$gft=ScratchOffersListing::where('pk_int_scratch_offers_listing_id',$id)->first();
	return view('users.campaign.edit_gift',compact('gft'));
}

public function updateGift(Request $request)
{

		$url=substr($request->prev_url,strpos($request->prev_url,'/',3));

		$user_id=User::getVendorId();

		$gift_id=$request->gift_id;
		$file_name=$request->gimage;

		$lst=ScratchOffersListing::where('pk_int_scratch_offers_listing_id',$gift_id)->first();
		$old_cnt=$lst->int_scratch_offers_count;
		$dif=$old_cnt-$request->offer_count_edit;

		$sc=ScratchCount::where('fk_int_user_id',$user_id)->first();  //update scratch count
		
		if(abs($dif)>$sc->balance_count)
		{
			Session::flash("fail","Insufficient scratch count!");
			return redirect($url);
		}
		
		if($request->customer_count>$request->offer_count_edit)	
		{
			Session::flash("fail","Already (".$request->customer_count .") customers scratch this offer, Can't reduce count.!");
			return redirect($url);
		}
		
		DB::beginTransaction();
		try
		{
		
				$filePath='offers_listing/';
				if($request->gift_image_edit)
				{
					$file_image=$request->gift_image_edit;  
					$gift_image = uniqid(). '.' . $file_image->getClientOriginalExtension();
					FileUpload::uploadFile($file_image, $filePath,$gift_image,'local');
					FileUpload::deleteFile($lst->image,'local');
					$file_name=$filePath.$gift_image;
				}
				
				if($request->customer_count<$request->offer_count_edit)
				{
					$lst->int_scratch_offers_count=$request->offer_count_edit;
					$lst->int_scratch_offers_balance=$request->offer_count_edit-$request->customer_count;
				}
				
				if($request->has('winning_status_edit'))
					$lst->int_winning_status=$request->winning_status_edit;
				$lst->txt_description=$request->description_edit;
				$lst->image=$file_name;  
				$flag=$lst->save();
				
				
				$sc->used_count=$sc->used_count-$dif;
				$sc->balance_count=$sc->balance_count+$dif;
				$sc->save();
								

				if($flag)
        		{   
					DB::commit();
					Session::flash('success',"Gift successfully updated.");
					return redirect($url);
        		}
        		else
        		{
					DB::rollback();
					Session::flash('fail',"Something wrong, Try again.");
					return redirect()->back();
        		}
	
           }
            catch(\Exception $e)
            {
				DB::rollback();
				Session::flash('fail',$e->getMessage());
				return redirect()->back();
            }
   } 


}
