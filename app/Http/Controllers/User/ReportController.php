<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;


use App\Models\User;

use Validator;
use DataTables;
use Session;
use Auth;
use Log;
use DB;

class ReportController extends Controller
{
  public function __construct()
  {
     //$this->middleware('admin');
  }
  
  public function index()
  {
	   
	 return view('users.reports.scratch_reports');
  }	
  
 
}
