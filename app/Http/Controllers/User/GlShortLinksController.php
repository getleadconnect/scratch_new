<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Facades\FileUpload;

use App\Models\ScratchOffer;
use App\Models\ScratchOffersListing;
use App\Models\ScratchType;

use App\Models\ShortLink;
use App\Models\ShortLinkHistory;
use App\Models\User;
use App\Traits\GeneralTrait;
use App\Traits\EndroidQrcodeTrait;

use Validator;

use DataTables;
use Session;
use Auth;
use Log;


class GlShortLinksController extends Controller
{
  
  use GeneralTrait, EndroidQrcodeTrait;
  
  public function __construct()
  {
     //$this->middleware('admin');
  }
    
   
  public function index()
  {
	 
	 $user_id=User::getVendorId();
	 $subscription=$this->checkUserStatus($user_id);
	
	 $offers=ScratchOffer::where('fk_int_user_id',$user_id)->get();
	 return view('users.links.gl_short_links',compact('offers','subscription'));
	 
  }
   
 
  
  public function getShortLinks()
    {
        $links = ShortLink::where("vendor_id", User::getVendorId())->where('type', ShortLink::GL_SCRATCH)->orderBy('id', 'Desc')->get();
        		
        return DataTables::of($links)
			->addIndexColumn()
			->addColumn('offer', function ($links) 
			{
				$links->offer = $links->ScratchOffer ? $links->ScratchOffer->vchr_scratch_offers_name : 'N/A';
				return $links->offer;
			})
			->addColumn('status', function ($links) 
			{
				 if ($links->status==1) {
					$status='<span class="badge rounded-pill bg-success">Active</span>';
				} else {
					$status='<span class="badge rounded-pill bg-danger">Inactive</span>';
            }
			return $status;
			})
			->addColumn('email', function ($links) 
			{
				$email_req=($links->email_required==1)?"Yes":"No";
				return $email_req;
			})
			->addColumn('billno', function ($links) 
			{
				$bill_req=($links->custom_field==1)?"Yes":"No";
				return $bill_req;
			})
			
			->addColumn('qrcode', function ($links) 
			{
				if($links->qrcode_file!="")
				$qrc='<a  href="'.FileUpload::viewFile($links->qrcode_file,'local').'" target="_blank"><img class="qrcode-icon" src='.FileUpload::viewFile($links->qrcode_file,'local').' width="40" height="40"></a>';
				else
				$qrc="--";
				return $qrc;
			})
			
			->addColumn('branch', function ($links) 
			{
				$brn_req=($links->branch_required==1)?"Yes":"No";
				return $brn_req;
			})
									
            ->addColumn('action', function ($links) 
			{
				if ($links->status == ShortLink::ACTIVE) 
				{
					$btn='<li><a class="dropdown-item btn-act-deact" href="javascript:;" id="'.$links->id.'" data-option="2" ><i class="lni lni-close"></i> Deactivate</a></li>';
				}
				else
				{
					$btn='<li><a class="dropdown-item btn-act-deact" href="javascript:;" id="'.$links->id.'" data-option="1"><i class="lni lni-checkmark"></i> Activate</a></li>';
				}

				$action='<div class="fs-5 ms-auto dropdown">
							  <div class="dropdown-toggle dropdown-toggle-nocaret cursor-pointer" data-bs-toggle="dropdown"><i class="fadeIn animated bx bx-dots-vertical"></i></div>
								<ul class="dropdown-menu">
								<li><a class="dropdown-item link-edit" href="javascript:;" id="'.$links->id.'" data-bs-toggle="offcanvas" data-bs-target="#edit-link" aria-controls="offcanvasScrolling" ><i class="lni lni-pencil-alt"></i> Edit</a></li>
								<li><a class="dropdown-item link-view" href="'.route('users.web-click-link-history',$links->id).'"><i class="lni lni-eye"></i> View</a></li>
								<li><a class="dropdown-item gen-qrcode" href="javascript:;" id="'.$links->id.'"><i class="fa fa-qrcode"></i> Generate QrCode</a></li>
								  '.$btn.'<ul>
							</div>';
				return $action;				
            })
            ->rawColumns(['action','status','qrcode'])
            ->toJson(true);
   }


public function addLink(Request $request)
{
	$offer_id='';
	if($request->has('offer_id'))
		$offer_id=$request->offer_id;
	
	$user_id=User::getVendorId();
	$offers=ScratchOffer::where('fk_int_user_id',$user_id)->get();
	return view('users.links.add_short_link',compact('offers','offer_id'));
}



public function store(Request $request)
    {
        $input = $request->all();

        $validator = Validator::make($request->all(), [
            'code' => 'required',
			'offer_id'=>'required'
        ]);
		
        if ($validator->fails()) {
			return response()->json(['msg' =>$validator->messages()->first(), 'status' => false]);
        }

		$slink=ShortLink::where('vendor_id',USER::getVendorId())->where('code',strtoupper($request->code))->first();
		if($slink)
		{
			return response()->json(['msg' =>'Code already exist.!' , 'status' => false]);
		}
		else
		{
			try{
				$short_code=strtoupper($request->code);
				$user_id=User::getVendorId();
				
				$filename="qr_codes/".$short_code.'-'.time().'.png';
				$path = public_path('uploads/'.$filename);
				$short_link=env('SHORT_LINK_DOMAIN') . '/'.$user_id."/". $short_code;
				
				$result=$this->generateQrCode($short_link,$path);   //generate qrcode for scan link

				$link = new  ShortLink();
				$link->vendor_id = $user_id;
				$link->link = $short_link;
				$link->code = $short_code;
				$link->offer_id = $request->offer_id;
				$link->custom_field = $request->custom_field;
				$link->bill_number_only_apply_from_list = $request->custom_field; //bill no
				$link->email_required = $request->email_required;
				$link->branch_required = $request->branch_required;
				$link->status = ShortLink::ACTIVE;
				$link->qrcode_file=$filename;
				$flag = $link->save();

				if ($flag) {
					return response()->json(['msg' =>'Short link successfully added!' , 'status' => true]);
				}
				else
				{
					FileUpload::deleteFile($filename,'local');
					return response()->json(['msg' =>'Something went wrong. Try again later!', 'status' => false]);
				}
			}
			catch(\Exception $e)
			{
				return response()->json(['msg' =>$e->getMessage(), 'status' => false]);	
			}
		}
    }

public function reGenerateQrcode(Request $request)
{
	
	$id=$request->link_id;
	 $link = ShortLink::where("id", $id)->first();
	 if($link)
	 {
		$old_file=$link->qrcode_file;
		 
		$short_code=strtoupper($link->code);
		$user_id=User::getVendorId();
				
		$filename="qr_codes/".$short_code.'-'.time().'.png';
		$path = public_path('uploads/'.$filename);
		$short_link=env('SHORT_LINK_DOMAIN') . '/'.$user_id."/". $short_code;

		$result=$this->generateQrCode($short_link,$path);   //generate qrcode for scan link 
		
		$link->qrcode_file=$filename;
		$flag=$link->save();
		if ($flag)
			{
				FileUpload::deleteFile($old_file,'local');
				return response()->json(['msg' =>'Qrcode successfully added!' , 'status' => true]);
			}
			else
			{
				FileUpload::deleteFile($filename,'local');
				return response()->json(['msg' =>'Something went wrong. Try again later!', 'status' => false]);
			}	
		 
	 }
	
}


public function edit($id)
{
	$sl=ShortLink::where('id',$id)->first();
	$offers=ScratchOffer::where('fk_int_user_id',User::getVendorId())->get();
	return view('users.links.edit_short_link',compact('sl','offers'));
}

public function updateLink(Request $request)
    {
		
		$id=$request->link_id;
	
        $link = ShortLink::find($id);
		$link->vendor_id= User::getVendorId();
        $link->offer_id=$request->offer_id_edit;
        $link->custom_field=$request->custom_field_edit;
        $link->email_required=$request->email_required_edit;
		$link->bill_number_only_apply_from_list = $request->custom_field_edit;
		$link->branch_required=$request->branch_required_edit;
		$link->status= ShortLink::ACTIVE;
        $flag = $link->save();
		
        if ($flag) {
            Session::flash('success','Short link successfully  updated!');
            return redirect('users/gl-links');
        }

        Session::flash('fail','Something went wrong. Try again!!');
        return redirect('users/gl-links');
    }


public function linkActivateDeactivate($op,$id)
	{
		if($op==1)
		{
		   $new=['status'=>1];
		}
		else
		{	
		   $new=['status'=>0];
		}

		$result=ShortLink::where('id',$id)->update($new);
		
			if($result)
			{
				if($op==1)
					return response()->json(['msg' =>'Link successfully activated!' , 'status' => true]);
				else
				    return response()->json(['msg' =>'Link successfully deactivated!' , 'status' => true]);
			}
			else
			{
				return response()->json(['msg' =>'Something wrong, try again!' , 'status' => false]);
			}				

	}

//web click link history -------------------------------------------------
  
  public function webClickLinkHistory($id)
  {
	 $link = ShortLink::leftJoin('tbl_scratch_offers','short_links.offer_id','tbl_scratch_offers.pk_int_scratch_offers_id')
	 ->select('short_links.*','tbl_scratch_offers.vchr_scratch_offers_name')->where('short_links.id',$id)->first();
	 
	 return view('users.links.web_click_link_history',compact('link'));
  }	
   
  public function viewWebClickLinkHistory($id)
    {
        $url_history = ShortLinkHistory::where('short_link_id', $id)->orderBy('id', 'DESC')->get();
        foreach ($url_history as $key => $item) {
            $item->slno = ++$key;
        }
        return Datatables::of($url_history)
            ->addIndexColumn()
			->toJson(true);
    }


}
