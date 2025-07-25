<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Facades\FileUpload;

use App\Models\ScratchOffer;
use App\Models\ScratchOffersListing;
use App\Models\ScratchType;
use App\Models\ScratchCount;
use App\Models\User;
use App\Models\ShortLink;

use App\Models\ScratchWebCustomer;
use App\Traits\GeneralTrait;

use Validator;

use DataTables;
use Session;
use Auth;
use Log;
use DB;
use PDF;

class CampaignController extends Controller
{
  use GeneralTrait;
  
  public function __construct()
  {
     //$this->middleware('admin');
  }
  
  public function index()
  {
	 	 
	 $user_id=User::getVendorId();
	 
	 $subscription=$this->checkUserStatus($user_id);
	 
	 $type=ScratchType::where('status',1)->get(); 
	 $sbal_count=ScratchCount::where('fk_int_user_id',$user_id)->pluck('balance_count')->first();
	 $offers=ScratchOffer::where('fk_int_user_id',$user_id)->get();
	 return view('users.campaign.campaign_list',compact('type','sbal_count','subscription','offers','user_id'));
  }	
      
  public function addCampaign()
  {
	 $user_id=User::getVendorId();
	 	  
	 $type=ScratchType::where('status',1)->get();
	 $sbal_count=ScratchCount::where('fk_int_user_id',$user_id)->pluck('balance_count')->first();
	 return view('users.campaign.add_campaign',compact('type','sbal_count'));
  }	
  
  
  public function getCampaignDetails($id)
  {
	  $offer = ScratchOffer::select('tbl_scratch_offers.*','scratch_type.type')
	  ->leftJoin('scratch_type','tbl_scratch_offers.type_id','=','scratch_type.id')
	  ->where('tbl_scratch_offers.pk_int_scratch_offers_id',$id)->first();
	  
	 return view('users.campaign.view_campaign_gifts_list',compact('offer'));
  }	
  
 
    
  public function store(Request $request)
    {
        // return $request;

        $validator=validator::make($request->all(), ScratchOffer::$rule, ScratchOffer::$message);
        if ($validator->fails()) 
		{
			Session::flash('fail',$validator->messages());
			return back()->withInput();
		}
		else
		{
			
			DB::beginTransaction();
			
            try
			{
            	$user_id=User::getVendorId();
            	
				$path = 'campaign/';

				// Desktop banner
                    $image = $request->file('offer_image');
                    $name = rand(10, 100). date_timestamp_get(date_create()). '.' . $image->getClientOriginalExtension();
                    FileUpload::uploadFile($image, $path,$name,'local');
					$fname=$path.$name;
				$data=[
					'fk_int_user_id'=>$user_id,
					'vchr_scratch_offers_name'=>$request->offer_name,
					'vchr_scratch_offers_image'=>$fname,
					'mobile_image'=>$fname,
					'type_id'=>$request->offer_type,
					'end_date'=>$request->campaign_end_date,
					'int_status'=>1,
				];
								
				$offers=ScratchOffer::create($data);
	
				if($offers)
        		{   
					Session::flash('success',"Campaign successfully added.");
					DB::commit();
					return redirect('users/campaigns');
        		}
        		else
        		{
					Session::flash('fail',"Something wrong, Try again.");
					DB::rollback();
					return back()->withInput();
        		}
	
           }
            catch(\Exception $e)
            {
				DB::rollback();
			    Session::flash('fail',$e->getMessage());
        		return back()->withInput();
            }
        } 
    }

	
 public function viewOffers()
    {
      $id=User::getVendorId();
	
	/*if(Auth::user()->int_role_id==1 and Auth::user()->admin_status==1)
	{
		$offers=collect();
		$userids=User::where('parent_user_id',$id)->pluck('pk_int_user_id')->toArray();
		foreach($userids as $userid)
		{
			 $offers1 = ScratchOffer::select('tbl_scratch_offers.*','scratch_type.type')
				->leftJoin('scratch_type','tbl_scratch_offers.type_id','=','scratch_type.id')
				->leftJoin('scratch_type','tbl_scratch_offers.type_id','=','scratch_type.id')
				->where('fk_int_user_id',$userid)->orderby('pk_int_scratch_offers_id','Desc')->get();
			
			$offers=$offers->merge($offers1);
		}
	}
	else
	{
	  $offers = ScratchOffer::select('tbl_scratch_offers.*','scratch_type.type')
	  ->leftJoin('scratch_type','tbl_scratch_offers.type_id','=','scratch_type.id')
	  ->where('fk_int_user_id',$id)->orderby('pk_int_scratch_offers_id','Desc')->get();
	}*/
	
	 $offers = ScratchOffer::select('tbl_scratch_offers.*','scratch_type.type')
	  ->leftJoin('scratch_type','tbl_scratch_offers.type_id','=','scratch_type.id')
	  ->where('fk_int_user_id',$id)->orderby('pk_int_scratch_offers_id','Desc')->get();

	
        return Datatables::of($offers)
		->addIndexColumn()
        ->editColumn('name', function ($row) {
            if ($row->vchr_scratch_offers_name != null) {
				return '<a class="view-gifts" href="'.route('users.get-campaign',$row->pk_int_scratch_offers_id).'" >'.ucwords($row->vchr_scratch_offers_name).'</a>';
            } else {
                return "--Nil--";
            }
        })
		
		->editColumn('offer_image', function ($row) {
            if ($row->vchr_scratch_offers_image !='') {
                return  '<img src='.FileUpload::viewFile($row->vchr_scratch_offers_image,'local').' width="50" height="50" data-id='.$row->pk_int_scratch_offers_id.'" id="imgUpload" style="cursor:pointer" title="Click to change image"> </img>';
            } else {
                return "--Nil--";
            }
        })
		
		->addColumn('status', function ($row) {
            if ($row->int_status==1) {
                $status='<span class="badge rounded-pill bg-success">Active</span>';
            } else {
                $status='<span class="badge rounded-pill bg-danger">Inactive</span>';
            }
			return $status;
        })
		
		->addColumn('enddate', function ($row) {
            if($row->end_date!="")
				return date_create($row->end_date)->format('d-m-Y');
			
			return  "--";
        })
				
		->addColumn('created', function ($row) {
            if ($row->created_at!="")
				return date_create($row->created_at)->format('d-m-Y');
			return "--";
        })
		
		->addColumn('add-gift', function ($row) {
            	return '<a class="btn btn-primary btn-sm"  href="'.route('users.add-gifts',$row->pk_int_scratch_offers_id).'" title="Add Gifts" ><i class="lni lni-plus"></i></a>';
        })
				
        ->addColumn('action', function ($row)
        {
			if ($row->int_status == 1)
			{
				$btn='<li><a class="dropdown-item btn-act-deact" href="javascript:void(0)" id="'.$row->pk_int_scratch_offers_id.'" data-option="2" ><i class="lni lni-close"></i> Deactivate</a></li>';
			}
			else
			{
				$btn='<li><a class="dropdown-item btn-act-deact" href="javascript:void(0)" id="'.$row->pk_int_scratch_offers_id.'" data-option="1"><i class="lni lni-checkmark"></i> Activate</a></li>';
			}

			$action='<div class="fs-5 ms-auto dropdown">
                          <div class="dropdown-toggle dropdown-toggle-nocaret cursor-pointer" data-bs-toggle="dropdown"><i class="fadeIn animated bx bx-dots-vertical"></i></div>
                            <ul class="dropdown-menu">
                              <li><a class="dropdown-item offer-edit" href="javascript:void(0)" id="'.$row->pk_int_scratch_offers_id.'" data-bs-toggle="offcanvas" data-bs-target="#edit-campaign" aria-controls="offcanvasScrolling" ><i class="lni lni-pencil-alt"></i> Edit</a></li>
                              <li><a class="dropdown-item offer-delete" href="javascript:void(0)" id="'.$row->pk_int_scratch_offers_id.'"><i class="lni lni-trash"></i> Delete</a></li>
							  <li><a class="dropdown-item "  href="'.route('users.get-campaign',$row->pk_int_scratch_offers_id).'" ><i class="lni lni-eye"></i> View Campaign</a></li>'
							  .$btn.
							  '</ul>
                        </div>';
			return $action;
        })
        ->rawColumns(['action','name','offer_image','add-gift','status'])
        ->make(true);
    }
	

public function edit($id)
{
	$sdt=ScratchOffer::where('pk_int_scratch_offers_id',$id)->first();
	$type=ScratchType::get();
	return view('users.campaign.edit_campaign',compact('type','sdt'));
}

  public function update(Request $request)
    {
        // return $request;

        $validator=validator::make($request->all(), ScratchOffer::$ruleUpdate, ScratchOffer::$messageUpdate);
        if ($validator->fails()) 
		{
			Session::flash('fail',$validator->messages());
			return redirect()->back();
		}
		else
		{
            try
            {
				
				$path = 'campaign/';
				$offer_id=$request->offer_id;
				$off_img=$request->offer_img;
				
					if ($request->hasFile('offer_image_edit')) {
						$image = $request->file('offer_image_edit');
						$name = mt_rand().'.' . $image->getClientOriginalExtension();
						FileUpload::uploadFile($image, $path,$name,'local');
						($off_img!="")?FileUpload::deleteFile($off_img,'local'):'';
						$off_img=$path.$name;
					}
				 
				$data=[
					'vchr_scratch_offers_name'=>$request->offer_name_edit,
					'vchr_scratch_offers_image'=>$off_img,
					'mobile_image'=>$off_img,
					'type_id'=>$request->offer_type_edit,
					'int_status'=>1,
				];
				
							
				$offers=ScratchOffer::where('pk_int_scratch_offers_id',$offer_id)->update($data);
	
				

				if($offers)
        		{   
					Session::flash('success',"Campaign successfully updated.");
					return redirect('users/campaigns');
        		}
        		else
        		{
					Session::flash('fail',"Something wrong, Try again.");
					return redirect()->back();
        		}
	
            }
            catch(\Exception $e)
            {
                Session::flash('fail',$e->getMessage());
        		return redirect()->back();
            }
        } 
    }
    	

public function destroy($id)
{
	
	try
	{
		$offer=ScratchOffer::where('pk_int_scratch_offers_id',$id)->first();
		
		if($offer)
		{
			$offer_id=$offer->pk_int_scratch_offers_id;
			FileUpload::deleteFile($offer->vchr_scratch_offers_image,'local');
			$res=$offer->delete();
			
			$offerListing=ScratchOffersListing::where('fk_int_scratch_offers_id',$offer_id)->get();
			
			foreach($offerListing as $row)
			{
				if($row->int_winning_status==1)
					FileUpload::deleteFile($row->image,'local');
				
				$row->delete();
			}
			
			if($res)
			{   
				return response()->json(['msg'=>'Campaign successfully removed.','status'=>true]);
			}
			else
			{
				return response()->json(['msg'=>'Something wrong, Try again.','status'=>false]);
			}
		}
	}
	catch(\Exception $e)
	{
		return response()->json(['msg'=>$e->getMessage(),'status'=>false]);
	}
}



 public function viewCampaignGiftListings()
    {
      $id=User::getVendorId();

      $offers = ScratchOffersListing::select('tbl_scratch_offers_listing.*','scratch_type.type as type_name')
	  ->leftJoin('scratch_type','tbl_scratch_offers_listing.type_id','=','scratch_type.id')
	  ->where('fk_int_user_id',$id)->orderby('pk_int_scratch_offers_listing_id','Desc')->get();
	
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
				//FileUpload::deleteFile($data->image,'local');
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

public function generateQrcodePdf(Request $request)
{
	try
	{
		$offer_id=$request->offer_id;
		$user_id=$request->user_id;
		
		$qrimages=ShortLink::select('qrcode_file')->where('offer_id',$offer_id)->where('vendor_id',$user_id)->where('link_type','Multiple')->get();
		if(!$qrimages->isEmpty())
		{
			$pdf = PDF::loadView('users.links.generate_qrcode_pdf', compact('qrimages'));
			$filename="qr_codes-".date('Ymdhis').".pdf";
			return $pdf->download($filename);
		}
	}
	catch(\Exception $e)
	{
		\Log::info($e->getMessage());
		return false; 
	}
}

}
