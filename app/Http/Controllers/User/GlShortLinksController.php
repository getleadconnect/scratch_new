<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Facades\FileUpload;

use App\Models\ScratchOffer;
use App\Models\ScratchOffersListing;
use App\Models\ScratchType;
use App\Models\ScratchWebCustomer;

use App\Models\ShortLink;
use App\Models\ShortLinkHistory;
use App\Models\User;
use App\Models\LinkCountSection;
use App\Traits\GeneralTrait;
use App\Traits\EndroidQrcodeTrait;

use Validator;

use DataTables;
use Session;
use Auth;
use Log;
use PDF;
use DB;

class GlShortLinksController extends Controller
{
  
  use GeneralTrait, EndroidQrcodeTrait;
  
  public function __construct()
  {
     //$this->middleware('admin');
	 date_default_timezone_set('Asia/Kolkata');
	  
  }
    
   
  public function index(Request $request)
  {
	 $user_id=User::getVendorId();
	 $offer_id=$request->flt_offer_id;
	 $link_sec_id=$request->flt_link_section_id;
	 $search=$request->search;
	 
	 $link=ShortLink::leftJoin('tbl_scratch_offers','short_links.offer_id','tbl_scratch_offers.pk_int_scratch_offers_id')
	 ->select('short_links.*','tbl_scratch_offers.vchr_scratch_offers_name')
	 ->where("vendor_id", $user_id)->where('type', ShortLink::GL_SCRATCH);
	 
	 if($offer_id!="")
			$link->where('offer_id',$offer_id);
		
	 if($link_sec_id!="")
		$link->where('pdf_link_count_section_id',$link_sec_id);
	
		if($search!="")
		{
			if(strtoupper($search)=='YES') $search=1;  
			if(strtoupper($search)=="NO")$search=0;
			
			$link->where('vchr_scratch_offers_name','like','%'.$search.'%')
			->orWhere('code','like','%'.$search.'%')
			->orWhere('email_required','like','%'.$search.'%')
			->orWhere('branch_required','like','%'.$search.'%')
			->orWhere('bill_number_only_apply_from_list','like','%'.$search.'%');
		}
	
	 $links=$link->orderBy('id','ASC')->paginate(100);
			
	 $offers=ScratchOffer::where('fk_int_user_id',$user_id)->get();
	 
	 return view('users.links.gl_short_links',compact('offers','user_id','links'));
  }
      
  public function getShortLinks(Request $request)
    {
		$offer_id=$request->offer_id;
		$link_sec_id=$request->link_section_id;
				
		$link=ShortLink::where("vendor_id", User::getVendorId())->where('type', ShortLink::GL_SCRATCH);
		
		if($offer_id!="")
			$link->where('offer_id',$offer_id);
		
		if($link_sec_id!="")
			$link->where('pdf_link_count_section_id',$link_sec_id);
		
		$links=$link->orderBy('id','ASC')->get()->take(1000);
        		
        return DataTables::of($links)
			->addIndexColumn()
			->addColumn('chkbox', function ($links) 
			{
				$chk=$links->id;
				return $chk;
			})
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
				$qrc='<a  href="'.FileUpload::viewFile($links->qrcode_file,'local').'" target="_blank"><i class="fa fa-qrcode qrcode-icon" style="font-size:28px;color:#6c757d;padding:2px;"></i></a>';
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
								<li><a class="dropdown-item link-del" href="javascript:;" id="'.$links->id.'" ><i class="lni lni-trash"></i> Delete</a></li>
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

public function generateLinks(Request $request)
{
	$offer_id='';
	if($request->has('offer_id'))
		$offer_id=$request->offer_id;
	
	$user_id=User::getVendorId();
	$offers=ScratchOffer::where('fk_int_user_id',$user_id)->get();
	return view('users.links.generate_multiple_links',compact('offers','offer_id'));
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

		$gift_cnt=ScratchOffersListing::where('fk_int_scratch_offers_id',$request->offer_id)->where('int_status',1)->count();
		if($gift_cnt<=0)
		{
			return response()->json(['msg' =>'Offer gift listing not found!' , 'status' => false]);
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

//To generate multiple links with qrcodes -------------------------------------------

public function saveGeneratedMultipleLinks(Request $request)
{
	
		ini_set('max_execution_time', '300');
	
        $validator = Validator::make($request->all(), [
            //'code' => 'required|max:3|min:3',
			'offer_id'=>'required',
			'link_count'=>'required'
        ]);
		
        if ($validator->fails()) {
			return response()->json(['msg' =>$validator->messages()->first(), 'status' => false]);
        }

		/*$sol=ScratchOffersListing::select(\DB::raw('SUM(int_scratch_offers_balance) as gift_sum'),\DB::raw('count(*) as list_count'))
		->where('fk_int_scratch_offers_id',$request->offer_id)->first();
		
		if($sol->list_count<=0)
			return response()->json(['msg' =>'Offer gift listing not found!"' , 'status' => false]);
		
		if($request->link_count>$sol->gift_sum)
			return response()->json(['msg' =>'Insufficient gift counts. Available gift is '.$sol->gift_sum.')' , 'status' => false]);
		*/ 

			try
			{
				
				DB::beginTransaction();
								
				$user_id=User::getVendorId();
				
				$lcount=$request->link_count;
				
				$shortCodes=$this->getUniqueAlphabetsCode($lcount);
				$link_cat=$request->link_count." links (".date('d-m-Y-h-i-s').")";
								
				$res=LinkCountSection::create(['offer_id'=>$request->offer_id,'vendor_id'=>$user_id,'section_name'=>$link_cat]);
				$lnk_sec_id=$res->id;
								
				for($x=0;$x<count($shortCodes);$x++)
				{
					$short_code=strtoupper($shortCodes[$x]);
					
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
					$link->link_type = "Multiple";
					$link->qrcode_file=$filename;
					$link->pdf_link_count_section_id=$lnk_sec_id;
					$flag = $link->save();
					
				}
				if ($flag) {
					DB::commit();
					return response()->json(['msg' =>'Short link successfully added!' , 'status' => true]);
				}
				else
				{
					DB:;rollback();
					FileUpload::deleteFile($filename,'local');
					return response()->json(['msg' =>'Something went wrong. Try again later!', 'status' => false]);
				}
			}
			catch(\Exception $e)
			{
				DB::rollback();
				return response()->json(['msg' =>$e->getMessage(), 'status' => false]);	
			}
}

	
public function destroy($id)
{
	
	try
	{
		$slink=ShortLink::where('id',$id)->first();
		
		if($slink)
		{
			$cust=ScratchWebCustomer::where('offer_id',$slink->pk_int_scratch_offers_id)->count();
			if($cust<=0)
				$res=$slink->delete();
			else
				return response()->json(['msg'=>"Can't Remove, Already customer scratched this offer, Please deactivate this link.",'status'=>false]);
			
			
			if($res)
			{   
				return response()->json(['msg'=>'Short link successfully removed.','status'=>true]);
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

public function deleteMultipleLinks(Request $request)
{
	
	try
	{
		$link_ids=$request->link_ids;
		$lnk_ids=explode(",",$link_ids);
		
		$user_id=User::getVendorId();
		$links=ShortLink::whereIn('id',$lnk_ids)->get();

		if($links)
		{
			foreach($links as $row)
			{
				
				if(Storage::disk('local')->exists('/uploads/'.$row->qrcode_file))
				{
					FileUpload::deleteFile($row->qrcode_file,'local');
				}
				$result=$row->delete();
			}
			return response()->json(['msg'=>'Short link successfully removed.','status'=>true]);
		}
		else
		{
			return response()->json(['msg'=>'Something wrong, Try again.','status'=>false]);
		}
	}
	catch(\Exception $e)
	{
		return response()->json(['msg'=>$e->getMessage(),'status'=>false]);
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



public function generateQrcodePdf(Request $request)
{

	try
	{
		$offer_id=$request->offer_id;
		$user_id=$request->user_id;
		$section_id=$request->link_section_id;
		
		$qrimages=ShortLink::select('qrcode_file')->where('offer_id',$offer_id)->where('vendor_id',$user_id)
		->where('link_type','Multiple')->where('pdf_link_count_section_id',$section_id)->get();
		
		if(!$qrimages->isEmpty())
		{
			$pdf = PDF::loadView('users.links.generate_qrcode_pdf', compact('qrimages'));
			$filename="qr_codes-".date('Ymdhis').".pdf";
			return $pdf->download($filename);
		}
		else
		{
			Session::flash('fail','Short links were not found.');
			return back();
		}
		
		
	}
	catch(\Exception $e)
	{
		\Log::info($e->getMessage());
		return false; 
	}
}


//To generate qrcode pdf file for section count wise - (multiple links)

public function getLinkCountSection($offer_id)
{
	$user_id=User::getVendorId();
	$secdt=LinkCountSection::where('offer_id',$offer_id)->where('vendor_id',$user_id)->get();
		
	$opt='<option value="">-select--</option>';
	if(!$secdt->isEmpty())
	{
		foreach($secdt as $row)
		{
			$opt.='<option value="'.$row->id.'">'.$row->section_name.'</option>';
		}
	}
	
	return $opt;
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

//Generated unique short code -----------------------------------

public function getUniqueNumberCode($numCodes)
{

	$length = 8;
    $codes = [];
	
	$user_id=User::getVendorId();
	$codes=ShortLink::where('vendor_id',$user_id)->pluck('code')->toArray();

    while (count($codes) < $numCodes) {
        // Generate a unique ID
        $code = substr(uniqid(bin2hex(random_bytes(4)), true), -$length);  // Limiting the length to maxLength

        // Ensure the code is unique
        if (!in_array($code, $codes)) {
            $codes[] = $code;
        }
    }
	
    return $codes;
	//print_r($codes));
}


public function getUniqueAlphabetsCode($numCodes)
{
	$length=8;
	$codes = [];
	$characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz'; // Letters only
    $charactersLength = strlen($characters);
    $randomString = '';

    while (count($codes) < $numCodes) {
        // Generate a random alphabetic code
			$randomString='';
			for ($i = 0; $i < $length; $i++) {
					$randomString .= $characters[rand(0, $charactersLength - 1)];
				}
        // Ensure the code is unique
        if (!in_array($randomString, $codes)) 
		{
            $codes[] = $randomString;
        }
    }
	
	return $codes;
	//print_r($codes);
}


}
