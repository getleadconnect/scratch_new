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


class GeneralSettingsController extends Controller
{

  public function __construct()
  {
     //$this->middleware('admin');
  }
  
  public function index()
  {
	 return view('users.settings.general_settings'); 
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




}
