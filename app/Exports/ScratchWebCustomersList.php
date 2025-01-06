<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\WithHeadings;
//use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

use App\Models\ScratchWebCustomer;
use App\Models\User;

class ScratchWebCustomersList implements FromCollection,WithHeadings
{
	//use Exportable;
	
	protected $sDate=null;
	protected $eDate=null;

	function __construct($sdate,$edate)
	{
		$this->sDate=$sdate;
		$this->eDate=$edate;
	}

    /**
    * @return \Illuminate\Support\Collection
    */

	  public function headings():array{
        return[
            'Slno',
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
				
		$scdt=ScratchWebCustomer::select('scratch_web_customers.*','scratch_branches.branch')
		->leftJoin('scratch_branches','scratch_web_customers.branch_id','=','scratch_branches.id')
		->where('user_id',$user_id);
		
		if($sdate!="")
		{
			$scdt->whereDate('scratch_web_customers.created_at','>=',$sdate);
		}
		
		if($edate!="")
		{
			$scdt->whereDate('scratch_web_customers.created_at','<=',$edate);
		}
		
		$scdats=$scdt->orderBy('scratch_web_customers.id','ASC')->get();
			

		$data = array();
		$uData = array();
		
        if(!empty($scdats))
        {
			foreach ($scdats as $key=>$r)
            {
					$uData['slno'] = ++$key;
					$uData['name'] =$r->name;
					$uData['ccode'] =$r->country_code;
					$uData['mobile'] =$r->mobile;
					$uData['email'] =$r->email;
					$uData['branch'] =$r->branch??"--";
					$uData['offer'] =$r->offer_text??"--";
					$uData['redeem'] =$r->redeem==1?"Redeemed":"Pending";
			    $data[] = $uData;
			}
        }

		return collect($data);   

    }

	
}
