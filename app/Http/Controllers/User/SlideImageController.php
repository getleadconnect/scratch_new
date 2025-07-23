<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Str;

use App\Models\SlideImage;
use App\Common\Variables;
use App\Facades\FileUpload;
use App\Models\User;

use DB;
use Auth;
use Validator;
use DataTables;

//use App\Http\Resources\Testimonial\TestimonialResource;

class SlideImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('users.slides.scratch_slide_image');
    }
	
    public function getSlideImages()
    {
		
      $vendorId=User::getVendorId();
	  
      $ads = SlideImage::select('slide_images.*','tbl_users.vchr_user_name as user_name')
	  ->leftJoin('tbl_users','slide_images.created_by','=','tbl_users.pk_int_user_id')
	  ->where('image_file','!=',NULL)->where('user_id',$vendorId)->orderby('id','Desc')->get();
        
        return Datatables::of($ads)
        ->addIndexColumn()
		->editColumn('image', function ($row) {
            if ($row->image_file !='') {
                return  '<a href="'.FileUpload::viewFile($row->image_file,'local').'" target="_blank" title="View Image"><img class="img-border" src='.FileUpload::viewFile($row->image_file,'local').' width="80"></a>';
            } else {
                return "--Nil--";
            }
        })
		
		->addColumn('created_by', function ($row) 
        {
            return $row->user_name;
        })
        
        ->addColumn('action', function ($row) {
                
				$action='<div class="fs-5 ms-auto dropdown">
							  <div class="dropdown-toggle dropdown-toggle-nocaret cursor-pointer" data-bs-toggle="dropdown"><i class="fadeIn animated bx bx-dots-vertical"></i></div>
								<ul class="dropdown-menu">
								  <li><a class="dropdown-item ads-delete" href="javascript:void(0)" id="'.$row->id.'"><i class="lni lni-trash"></i> Delete</a></li>
								  </ul>
							</div>';
							
				return $action;
        })
        ->rawColumns(['action','status','image'])
        ->toJson(true);
    }
		

    public function store(Request $request)
    {
      	$input = $request->all();
      	$userId=Auth::user()->pk_int_user_id;
        $validator=Validator::make($input, SlideImage::$ruleImage, SlideImage::$messageImage);
       // if validator passes
	   
        if ($validator->fails()) 
        {
			return response()->json(['msg'=>$validator->messages(), 'status' => false]);
		}
		else
		{
       		try
            {

				if ($request->hasFile('image_file')) 
            	{
					
					$path = 'slides/';

            		$file = $request->file('image_file');
					
					$sld = new SlideImage();
					$fileName="";
					$extension = $file->getClientOriginalExtension();
					$fileName = Str::random(5)."-".date('his')."-".Str::random(3).".".$extension;
					
					FileUpload::uploadFile($file, $path,$fileName,'local');
					$sld->image_file = $path.$fileName;
					$sld->created_by = $userId;
					$sld->user_id = User::getVendorId();
					$flag=$sld->save();
					
					if($flag)
					{
						 return response()->json(['msg'=>'Scratch slide image successfully added.', 'status'=>true]);
					}
					else
					{
						 return response()->json(['msg'=>'Something went wrong, please try again later.', 'status'=>false]);
					}

            	}
				else
				{
					 return response()->json(['msg'=>'Image not found, Try again.', 'status'=>false]);
				}
                
            }
            catch(\Exception $e)
            {
                return response()->json(['msg'=>$e->getMessage(), 'status' => false]);
            }
        } 

    }


    public function destroy($id)
    {
       try 
       {
            $sld = SlideImage::find($id);
                if ($sld) {

					FileUpload::deleteFile($sld->image_file,'local');
                    $sld->delete();
                    return response(['msg' => 'Slide image has been deleted.', 'status' => true]);
				}
                else
                {
                    return response(['msg' => 'Something Went Wrong', 'status' => false]);
                }
            
        }
        catch (\Exception $ex) {
          return response(['msg' => 'Something Went Wrong', 'status' => false]);

            }
    }


}
