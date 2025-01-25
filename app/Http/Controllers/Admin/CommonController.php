<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\ScratchOffer;
use App\Models\User;

use Validator;
use Session;
use Log;
use DB;


class CommonController extends Controller
{
  public function __construct()
  {
     
  }
 
 public function getUserCampaigns($id)
    {

      $offers = ScratchOffer::where('fk_int_user_id',$id)->get();
	  $opt="<option value=''>--select--</option>";
	  foreach($offers as $row)
	  {
		 $opt.="<option value='".$row->pk_int_scratch_offers_id."'>".$row->vchr_scratch_offers_name."</option>"; 
	  }
	  
	return $opt;	
		
    }
	

}
