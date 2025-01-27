<?php

namespace App\Http\Controllers\Shops;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Models\ScratchCount;
use App\Models\ScratchOffer;
use App\Models\ScratchWebCustomer;
use App\Models\ScratchCustomer;

use Validator;
use DataTables;
use Session;
use Auth;
use Log;
use Carbon\Carbon;
use DB;

class DashboardShopController extends Controller
{
  public function __construct()
  {
     //$this->middleware('admin');
  }
  
  public function index()
  {
	
	$user_id=User::getVendorId();
	
	$tot_count=ScratchCount::getTotalScratchCount($user_id);
	$used_count=ScratchCount::getUsedScratchCount($user_id);
	$bal_count=ScratchCount::getBalanceScratchCount($user_id);

	return view('shops.dashboard',compact('tot_count','used_count','bal_count'));
	
  }	
  
   
}
