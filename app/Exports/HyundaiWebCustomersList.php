<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\WithHeadings;
//use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

use App\Models\ScratchWebCustomer;
use App\Models\User;
use Auth;

class HyundaiWebCustomersList implements FromCollection,WithHeadings
{
	//use Exportable;
	
	protected $sDate=null;
	protected $eDate=null;
	protected $branch=null;
	protected $campaign=null;
	
	function __construct($sdate,$edate)
	{
		$this->sDate=$sdate;
		$this->eDate=$edate;
		$this->branch="";
	}

    /**
    * @return \Illuminate\Support\Collection
    */

	  public function headings():array{
        return[
            'Slno',
			'Created At',
			'Name',
            'Country_code',
			'Mobile',
			'Email',
			'Branch',
			'Offer',
			'Redeem'
        ];
    } 
	
	
    public function collection()
    {
		
		$user_id=User::getVendorId();
		
		$sdate=$this->sDate;
		$edate=$this->eDate;
		$branch=$this->branch;
		
        if(Auth::user()->int_role_id==2 and Auth::user()->parent_user_id!="")
        {
            $userids=User::where('parent_user_id',$user_id)->pluck('pk_int_user_id')->toArray();
		    $scdt=ScratchWebCustomer::select('scratch_web_customers.*')->whereIn('user_id',$userids);
        }
		else
        {
            $scdt=ScratchWebCustomer::select('scratch_web_customers.*','scratch_branches.branch_name')
		    ->where('user_id',$user_id);
        }
		if($sdate!=""){
			$scdt->whereDate('scratch_web_customers.created_at','>=',$sdate);
		}
		
		if($edate!=""){
			$scdt->whereDate('scratch_web_customers.created_at','<=',$edate);
		}
		
		if($branch!=""){
			$scdt->where('scratch_web_customers.branch_id',$branch);
		}
		
		$scdats=$scdt->orderBy('scratch_web_customers.id','ASC')->get();

		$data = array();
		$uData = array();
		
        if(!empty($scdats))
        {
			foreach ($scdats as $key=>$r)
            {
					$uData['slno'] = ++$key;
					$uData['created'] =$r->created_at;
					$uData['name'] =$r->name;
					$uData['ccode'] =$r->country_code;
					$uData['mobile'] =$r->mobile;
					$uData['email'] =$r->email;
					$uData['branch'] =$r->branch_name??"--";
					$uData['offer'] =$r->offer_text??"--";
					$uData['redeem'] ="Redeemed";
			    $data[] = $uData;
			}
        }

		return collect($data);   

    }

	
}
