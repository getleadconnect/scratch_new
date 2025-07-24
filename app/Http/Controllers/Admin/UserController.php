<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Facades\FileUpload;

use App\Models\User;
use App\Models\PurchaseScratch;
use App\Models\ScratchCount;
use App\Models\Settings;

use Validator;

use DataTables;
use Session;
use Auth;
use Log;
use DB;
use Carbon\Carbon;

class UserController extends Controller
{
  public function __construct()
  {
     //$this->middleware('admin');
  }
  
  public function index()
  {
	 $ad_users=User::where('admin_status',1)->get();
	return view('admin.users.users_list',compact('ad_users'));
  }	
    
  public function store(Request $request)
    {
        // return $request;

		DB::beginTransaction();

        $validator=validator::make($request->all(),User::$userRule,User::$userRuleMessage);
		
        if ($validator->fails()) 
		{
			return response()->json(['msg'=>$validator->messages()->first(),'status'=>false]);
		}
		else
		{
            try
			{
				$mob=User::where('mobile',$request->mobile)->first();
				if($mob)
				{
					return response()->json(['msg'=>'Mobile number already exisits, try again.','status'=>false]);
				}

				
				if($request->user_role=='admin')
				{
					$role_id=1;
					$admin_status=1;
					$parent_id=null;
				}
				else if($request->user_role=='child')
				{
					$role_id=2;
					$admin_status=0;
					$parent_id=$request->admin_user_id;
				}
				else
				{
					$role_id=2;
					$admin_status=0;
					$parent_id=null;
				}

				$data=[
					'vchr_user_name'=>$request->user_name,
					'email'=>$request->email,
					'countrycode'=>$request->country_code,
					'mobile'=>$request->mobile,
					'vchr_user_mobile'=>$request->country_code.$request->mobile,
					'designation_id'=>$request->designation,
					'company_name'=>$request->company,
					'location'=>$request->location,
					'password'=>Hash::make($request->password),
					'address'=>$request->address,
					'int_status'=>User::ACTIVATE,
					'int_role_id'=>$role_id,
					'admin_status'=>$admin_status,
					'parent_user_id'=>$parent_id,
				];
				
				$result=User::create($data);
				$user_id=$result->pk_int_user_id;
				
				//update unique id------------
				$le=str::length($user_id);
				$uniq_id="DS".str_pad("0",(8-$le),'0').$user_id;
				$res=User::where('pk_int_user_id',$user_id)->update(['unique_id'=>$uniq_id]);
				//---------------
				
				$sdata=[
					'vchr_settings_type'=>"scratch_otp_enabled",
					'vchr_settings_value'=>"Enabled",
					'fk_int_user_id'=>$user_id,
					'int_status'=>1,
				];
				
				$res=Settings::create($sdata);
				
				if($result)
        		{   
					DB::commit();
					return response()->json(['msg'=>'User successfully added.','status'=>true]);
        		}
        		else
        		{
					DB::rollback();
					return response()->json(['msg'=>'Something wrong, Try again.','status'=>false]);
        		}

           }
            catch(\Exception $e)
            {
				DB::rollback();
			  return response()->json(['msg'=>$e->getMessage(),'status'=>false]);
            }
        } 
    }
	
 public function viewUsers()
    {
      $id=User::getVendorId();

      $users = User::select('tbl_users.*','A.unique_id as parent_unique_id')
	  ->leftJoin('tbl_users as A','A.pk_int_user_id','=','tbl_users.parent_user_id')
	  ->where('tbl_users.int_role_id','!=',0)->orderby('tbl_users.pk_int_user_id','Desc')->get()->map(function($q)
	  {
		  if($q->admin_status==1)
		  {
			  $ucount=User::where('parent_user_id',$q->pk_int_user_id)->count();
			  $q['user_count']=$ucount;
		  }
		  else
		  {
			  $q['user_count']='';
		  }
		  return $q;
	  });	  
	  
        return Datatables::of($users)
		->addIndexColumn()
		->addColumn('name', function ($row) {
			if($row->admin_status==1)
			{
			
				$ust='<span class="badge bg-warning text-dark">'.$row->user_count.' users</span>';
			}
			else
				$ust='';
			$uname='<a href="'.route('admin.user-profile',$row->pk_int_user_id).'" style="font-weight:500;">'.strtoupper($row->vchr_user_name).'</a>'.$ust;
			return $uname;
        })
		
		->addColumn('unique_id', function ($row) {
            
			return $row->unique_id??"--";
        })
				
		->addColumn('pname', function ($row) {
			return $row->parent_unique_id;
        })
		
		->addColumn('status', function ($row) {
            if ($row->int_status==1) {
                $status='<span class="badge rounded-pill bg-success">Active</span>';
            } else {
                $status='<span class="badge rounded-pill bg-danger">Inactive</span>';
            }
			
			$subscription_date = Carbon::create($row->subscription_end_date)->addDays(1)->format('Y-m-d');
			if($subscription_date<=date('Y-m-d'))
			{
				$status='<span class="badge rounded-pill bg-danger">Expired</span>';
			}
			
			return $status;
        })
		->editColumn('mobile', function ($row) {
            
			$mob="+".$row->countrycode." ".$row->mobile;
			return $mob;
        })
		->editColumn('address', function ($row) {
            
			return $row->address??"--";
        })
		->editColumn('location', function ($row) {
            
			return $row->location??"--";
        })
		->addColumn('company', function ($row) {
            
			return $row->company_name??"--";
        })
		->addColumn('cdate', function ($row) {
            
			return Carbon::parse($row->created_at)->format('d-m-Y');
        })
		
		->addColumn('subscription', function ($row) {
            
			$dt="Start:".Carbon::parse($row->subscription_start_date)->format('d-m-Y');
			
			$subscription_date = Carbon::create($row->subscription_end_date)->addDays(1)->format('Y-m-d');
			if($subscription_date<=date('Y-m-d'))
				$dt.="<br>End:<span style='color:red'>".Carbon::parse($row->subscription_end_date)->format('d-m-Y')."</span>";
			else
				$dt.="<br>End:<span>".Carbon::parse($row->subscription_end_date)->format('d-m-Y')."</span>";
			
			return $dt;
        })
		
		
        ->addColumn('action', function ($row)
        {
			if ($row->int_status == 1)
			{
				$btn='<li><a class="dropdown-item btn-act-deact" href="javascript:void(0)" id="'.$row->pk_int_user_id.'" data-option="2" ><i class="lni lni-close"></i> Deactivate</a></li>';
			}
			else
			{
				$btn='<li><a class="dropdown-item btn-act-deact" href="javascript:void(0)" id="'.$row->pk_int_user_id.'" data-option="1"><i class="lni lni-checkmark"></i> Activate</a></li>';
			}
			
			$action='<div class="fs-5 ms-auto dropdown">
                          <div class="dropdown-toggle dropdown-toggle-nocaret cursor-pointer" data-bs-toggle="dropdown"><i class="fadeIn animated bx bx-dots-vertical"></i></div>
                            <ul class="dropdown-menu">
                              <li><a class="dropdown-item edit-user" href="javascript:void(0)" id="'.$row->pk_int_user_id.'" data-bs-toggle="offcanvas" data-bs-target="#edit-user"  ><i class="lni lni-pencil-alt"></i> Edit</a></li>
                              <li><a class="dropdown-item delete-user" href="javascript:void(0)" id="'.$row->pk_int_user_id.'"><i class="lni lni-trash"></i> Delete</a></li>
							  <li><a class="dropdown-item view-user" href="'.route('admin.user-profile',$row->pk_int_user_id).'" id="'.$row->pk_int_user_id.'"><i class="lni lni-eye"></i> View</a></li>
							  <li><a class="dropdown-item change-pass" href="javascript:;" id="'.$row->pk_int_user_id.'" data-bs-toggle="modal" data-bs-target="#change-pass-modal"><i class="lni lni-lock"></i> Change Password</a></li>'
							   .$btn.
							  '</ul>
                        </div>';
			return $action;
        })
        ->rawColumns(['action','name','status','subscription'])
        ->make(true);
    }


public function addSubscription(Request $request)
{

	try
	{
		
		$user_id=$request->user_id;
		$start_date=$request->start_date;
		$end_date=$request->end_date;
		
		$data=['subscription_start_date'=>$start_date,'subscription_end_date'=>$end_date];
		$result=User::where('pk_int_user_id',$user_id)->update($data);
		
			$sc=ScratchCount::where('fk_int_user_id',$user_id)->first();
			if($sc)
			{
				$sc->total_count=0;
				$sc->used_count=0;
				$sc->balance_count=0;
				$sc->save();
			}
			else
			{
				$dat=[
					'fk_int_user_id'=>$user_id,
					'total_count'=>0,
					'used_count'=>0,
					'balance_count'=>0,
				];
				$res=ScratchCount::create($dat);	
			}
		
		//to add child users subscription date----------------------------------------------
		if($request->has('all_users') and $request->all_users=="on")
		{
			
			$users=User::where('parent_user_id',$user_id)->pluck('pk_int_user_id')->toArray();
			if(!empty($users))
			{
				foreach($users as $userid)
				{
					
					$data=['subscription_start_date'=>$start_date,'subscription_end_date'=>$end_date];
					$result=User::where('pk_int_user_id',$userid)->update($data);
					
						$sc=ScratchCount::where('fk_int_user_id',$userid)->first();
						if($sc)
						{
							$sc->total_count=0;
							$sc->used_count=0;
							$sc->balance_count=0;
							$sc->save();
						}
						else
						{
							$dat=[
								'fk_int_user_id'=>$userid,
								'total_count'=>0,
								'used_count'=>0,
								'balance_count'=>0,
							];
							$res=ScratchCount::create($dat);	
						}
					
				}
				
			}	
		
		}

		if($result)
		{   
			return response()->json(['msg'=>'User subscription updated.','status'=>true]);
		}
		else
		{
			return response()->json(['msg'=>'Something wrong, try again.','status'=>false]);
		}

	}
	catch(\Exception $e)
	{
		return response()->json(['msg'=>$e->getMessage(),'status'=>false]);
	}
	
}


public function edit($id)
{
	$usr=User::where('pk_int_user_id',$id)->first();
	return view('admin.users.edit_user',compact('usr'));
}


  public function updateUser(Request $request)
    {

        $validator=validator::make($request->all(), User::$userUpdate, User::$updateMessage);
        if ($validator->fails()) 
		{
			return response()->json(['msg'=>$validator->messages()->first(),'status'=>false]);
		}
		else
		{
			try{

				$user_id=$request->user_id;
            	
				$data=[
					'vchr_user_name'=>$request->user_name_edit,
					'email'=>$request->email_edit,
					'countrycode'=>$request->country_code_edit,
					'mobile'=>trim($request->mobile_edit),
					'vchr_user_mobile'=>$request->country_code_edit.trim($request->mobile_edit),
					//'designation_id'=>$request->designation_edit,
					'company_name'=>$request->company_edit,
					'location'=>$request->location_edit,
					'address'=>$request->address_edit,
				];
						
				$result=User::where('pk_int_user_id',$user_id)->update($data); 
				
				if($result)
        		{   
					return response()->json(['msg'=>'User successfully updated.','status'=>true]);
        		}
        		else
        		{
					return response()->json(['msg'=>'Something wrong, try again.','status'=>false]);
        		}
	
            }
            catch(\Exception $e)
            {
                return response()->json(['msg'=>$e->getMessage(),'status'=>false]);
            }
        } 
    }


public function destroy($id)
{
	try
	{
		$users=User::where('pk_int_user_id',$id)->first();
		
		if($users)
		{
			$res=$users->delete();
			if($res)
			{   
				return response()->json(['msg'=>'User successfully removed.','status'=>true]);
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

public function activateDeactivate($op,$id)
	{
		if($op==1)
		{
		   $new=['int_status'=>1];
		}
		else
		{	
		   $new=['int_status'=>0];
		}

		$result=User::where('pk_int_user_id',$id)->update($new);
		
			if($result)
			{
				if($op==1)
					return response()->json(['msg' =>'User successfully activated!' , 'status' => true]);
				else
				    return response()->json(['msg' =>'User successfully deactivated!' , 'status' => true]);
			}
			else
			{
				return response()->json(['msg' =>'Something wrong, try again!' , 'status' => false]);
			}				

	}

//---------------------------------------------------------------------------

public function userProfile($user_id)
{
	///dd(request()->segment(3));

	$usr=User::where('pk_int_user_id',$user_id)->get()->map(function($q)
	{
		  if($q->vchr_logo!="")
			  $q->user_logo=FileUpload::viewFile($q->vchr_logo,'local');
		  else
			  $q->user_logo=asset('assets/images/avatars/1.png');
		  return $q;
	  })->first();
	
	
	if($usr->subscription_end_date<date('Y-m-d') && $usr->subscription_end_date!='')
	{
		$subscription="Expired";
	}
	else if($usr->subscription_end_date=='' && $usr->subscription_end_date=='')
	{
		$subscription='No Subscription';
	}
	else
	{
		$subscription='Active';
	}

	$data['tot_count']=ScratchCount::getTotalScratchCount($user_id);
	$data['used_count']=ScratchCount::getUsedScratchCount($user_id);
	$data['bal_count']=ScratchCount::getBalanceScratchCount($user_id);
	
	$data['ch_users']=User::where('parent_user_id',$user_id)->get();
	
	return view('admin.users.user_profile',compact('user_id','usr','data','subscription'));
}

public function viewScratchHistory($id)
{

  $data = PurchaseScratch::where('fk_int_user_id',$id)->orderby('id','ASC')->get();

	return Datatables::of($data)
	->addIndexColumn()
	->addColumn('narration', function ($row) {
		return $row->narration;
	})
	
	->addColumn('cdate', function ($row) {
		return date_create($row->created_at)->format('d-m-Y');
	})
			
	->addColumn('action', function ($row)
	{
		$btn='';
		if($row->status==1)
			$btn='<a class="link-delete" href="javascript:void(0)" id="'.$row->id.'" data-userid="'.$row->fk_int_user_id.'" title="delete" ><i class="lni lni-trash"></i></a>';
					
		return $btn;
	})
	->rawColumns(['action','status'])
	->make(true);
}


public function addScratchCount(Request $request)
{
	
	$validator=validator::make($request->all(), ['scratch_count'=>'required']);
	
        if ($validator->fails()) 
		{
			return response()->json(['msg'=>"Scratch count missing.",'status'=>false]);
		}
		else
		{
			DB::beginTransaction();
			try{
				$user_id=$request->user_id;
				
				$sc=PurchaseScratch::where('fk_int_user_id',$user_id)->latest()->first();
				
				if($sc)
				{
					$sc->status=0;
					$sc->save();
				}
						
				$data=[
					'fk_int_user_id'=>$user_id,
					'narration'=>"To purchase ". $request->scratch_count. " scratch count dated on ".date('d-m-Y'),
					'scratch_count'=>$request->scratch_count,
					'status'=>1
				];
				
				$result=PurchaseScratch::create($data);
								
				if($result)
        		{   
					$scnt=ScratchCount::where('fk_int_user_id',$user_id)->first();
					if($scnt)
					{
						$scnt->total_count=$scnt->total_count+$request->scratch_count;
						$scnt->balance_count=$scnt->balance_count+$request->scratch_count;
						$scnt->save();
					}
					else
					{
						
						$dat=[
							'fk_int_user_id'=>$user_id,
							'total_count'=>$request->scratch_count,
							'balance_count'=>$request->scratch_count,
						];
						$scnt=ScratchCount::create($dat);
					}
					
					
					$response=['msg'=>'Scratch count successfully added.','status'=>true];
        		}
        		else
        		{
					$response=['msg'=>'Something wrong, try again.','status'=>false];
        		}
				
				
				//to add child users credit count----------------------------------------------
				if($request->has('all_users_count') and $request->all_users_count=="on")
				{
					
					$users=User::where('parent_user_id',$user_id)->pluck('pk_int_user_id')->toArray();
					if(!empty($users))
					{
						foreach($users as $userid)
						{
							
							$sc=PurchaseScratch::where('fk_int_user_id',$userid)->latest()->first();
							
							if($sc)
							{
								$sc->status=0;
								$sc->save();
							}
									
							$data=[
								'fk_int_user_id'=>$userid,
								'narration'=>"To purchase ". $request->scratch_count. " scratch count dated on ".date('d-m-Y'),
								'scratch_count'=>$request->scratch_count,
								'status'=>1
							];
							
							$result=PurchaseScratch::create($data);
											
							if($result)
							{   
								$scnt=ScratchCount::where('fk_int_user_id',$userid)->first();
								if($scnt)
								{
									$scnt->total_count=$scnt->total_count+$request->scratch_count;
									$scnt->balance_count=$scnt->balance_count+$request->scratch_count;
									$scnt->save();
								}
								else
								{
									
									$dat=[
										'fk_int_user_id'=>$userid,
										'total_count'=>$request->scratch_count,
										'balance_count'=>$request->scratch_count,
									];
									$scnt=ScratchCount::create($dat);
								}
							}
										
						}
					}
				}

				DB::commit();

				return response()->json($response);
				
            }
            catch(\Exception $e)
            {
				DB::rollback();
                return response()->json(['msg'=>$e->getMessage(),'status'=>false]);
            }
        } 
    }


public function deleteScratchCount(Request $request)
{
			$id=$request->id;
			$user_id=$request->user_id;

			try{
				
				$pscratch=PurchaseScratch::where('id',$id)->first();
				$scount=(!empty($pscratch))?$pscratch->scratch_count:0;
				$result=$pscratch->delete();
				
				$sc=PurchaseScratch::where('fk_int_user_id',$user_id)->latest()->first();
				
				if($result)
        		{   

					if($sc)
					{
						$sc->status=1;
						$sc->save();
					}
					
					$psc=ScratchCount::where('fk_int_user_id',$user_id)->first();
					$psc->total_count=$psc->total_count-$scount;
					$psc->balance_count=$psc->balance_count-$scount;
					$psc->save();
					
					return response()->json(['msg'=>'Scratch count successfully removed.','status'=>true]);
        		}
        		else
        		{
					return response()->json(['msg'=>'Something wrong, try again.','status'=>false]);
        		}
	
            }
            catch(\Exception $e)
            {
                return response()->json(['msg'=>$e->getMessage(),'status'=>false]);
            }
      
    }

public function changeUserPassword(Request $request)
{
			$npass=$request->password;
			$user_id=$request->user_id;

			try{
				
				$user=User::where('pk_int_user_id',$user_id)->first();
				
				if($user)
        		{   
					$user->password=Hash::make($npass);
					$result=$user->save();
					return response()->json(['msg'=>'Password successfully changed.','status'=>true]);
        		}
        		else
        		{
					return response()->json(['msg'=>'User were not found!','status'=>false]);
        		}
	
            }
            catch(\Exception $e)
            {
                return response()->json(['msg'=>$e->getMessage(),'status'=>false]);
            }
      
    }


}
