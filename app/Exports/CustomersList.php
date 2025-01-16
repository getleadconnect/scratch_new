<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
//use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

use App\Models\ScratchWebCustomer;
use App\Models\User;

class CustomersList implements FromCollection,WithHeadings
{
	//use Exportable;
	
	protected $sDate=null;
	protected $eDate=null;
	protected $branch=null;
	protected $campaign=null;
	protected $user=null;
	
	function __construct($sdate,$edate,$user,$branch,$campaign)
	{
		$this->sDate=$sdate;
		$this->user=$user;
		$this->eDate=$edate;
		$this->branch=$branch;
		$this->campaign=$campaign;
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
		
			
		$sdate=$this->sDate;
		$edate=$this->eDate;
		$branch=$this->branch;
		$campaign=$this->campaign;
		$user_id=$this->user;
		
		$scdt=ScratchWebCustomer::select('scratch_web_customers.*', 'tbl_users.vchr_user_name as redeemed_agent','scratch_branches.branch_name')
			->leftjoin('tbl_users', 'scratch_web_customers.user_id', 'tbl_users.pk_int_user_id')
			->leftjoin('scratch_branches', 'scratch_web_customers.branch_id', 'scratch_branches.id');
		
		if($user_id!=""){
			$scdt->where('scratch_web_customers.user_id','=',$user_id);
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
		
		if($campaign!=""){
			$scdt->where('scratch_web_customers.offer_id',$campaign);
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
					$uData['redeem'] =$r->redeem==1?"Redeemed":"Pending";
			    $data[] = $uData;
			}
        }

		return collect($data);   

    }

	
}
