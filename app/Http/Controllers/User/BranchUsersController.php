<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Facades\FileUpload;

use App\Models\User;
use App\Models\ScratchCount;

use Validator;

use DataTables;
use Session;
use Auth;
use Log;


class BranchUsersController extends Controller
{
  public function __construct()
  {
     //$this->middleware('admin');
  }
  
  public function index()
  {
	 return view('users.settings.branch_users_list');
  }	

  	
 public function viewBranchUsers()
    {
      $id=User::getVendorId();

      $users = User::where('parent_user_id',$id)->orderby('pk_int_user_id','DESC')->get()->map(function($q)
	  {
		  $credit=ScratchCount::where('fk_int_user_id',$q->pk_int_user_id)->first();
		  $q['credit_balance']=$credit->total_count."/".$credit->balance_count;
		  return $q;
	  });
	
        return Datatables::of($users)
		->addIndexColumn()
		->addColumn('name', function ($row) {
			return $row->vchr_user_name;
        })
		->addColumn('status', function ($row) {
            if ($row->int_status==1) {
                $status='<span class="badge rounded-pill bg-success">Active</span>';
            } else {
                $status='<span class="badge rounded-pill bg-danger">Inactive</span>';
            }
			return $status;
        })
		
		->addColumn('subscription', function ($row) {
            
			return $row->subscription_start_date." => ".$row->subscription_end_date;
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
                              <li><a class="dropdown-item edit-user" href="javascript:void(0)" id="'.$row->pk_int_user_id.'" data-bs-toggle="offcanvas" data-bs-target="#edit-user"  ><i class="lni lni-pencil-alt"></i> Edit</a></li>'
                               .$btn.
							  '</ul>
                        </div>';
			return $action;
        })
        ->rawColumns(['action','status'])
        ->make(true);
    }
	

public function edit($id)
{
	$usr=User::where('pk_int_user_id',$id)->first();
	return view('users.settings.edit_user',compact('usr'));
}


public function updateBranchUser(Request $request)
    {
        $validator=validator::make($request->all(), User::$shopEditRule, User::$shopEditMessage);

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
					'vchr_user_mobile'=>$request->country_code_edit.$request->mobile_edit,
				];
				
				if($request->password_edit!='')
				{
					$data['password']=Hash::make($request->password_edit);
				}
								
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

}
