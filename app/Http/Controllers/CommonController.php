<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ScratchBranch;

use DB;
use Validator;

class CommonController extends Controller
{
  
    public function getBranchAutocomplete($user_id)
    {
        $data = ScratchBranch::where('vendor_id',$user_id)->get();
        return response()->json(['status' => true,'data' =>$data]);
    }
	
}

