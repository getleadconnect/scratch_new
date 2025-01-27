<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\WithHeadings;
//use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

use App\Models\ScratchWebCustomer;
use App\Models\User;

class ShopCustomersList implements FromCollection,WithHeadings
{
	//use Exportable;
	
	protected $sDate=null;
	protected $eDate=null;
	
	function __construct($sdate,$edate,$user_id)
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
			'Created At',
			'Name',
            'Country_code',
			'Mobile',
			'Email',
			'Shop_Name'
			'Offer',
			'Redeem'
        ];
    } 
	
	
    public function collection()
    {
		
		$user_id=Auth::user()->pk_int_user_id;
		
		$sdate=$this->sDate;
		$edate=$this->eDate;
				
		$scdt=ScratchWebCustomer::select('scratch_web_customers.*','tbl_users.vchr_user_name as redeemed_agent_name')
		->leftjoin('tbl_users', 'scratch_web_customers.redeemed_agent', 'tbl_users.pk_int_user_id')
		->where('redeemed_agent',$user_id);
		
		if($sdate!=""){
			$scdt->whereDate('scratch_web_customers.created_at','>=',$sdate);
		}
		
		if($edate!=""){
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
					$uData['created'] =$r->created_at;
					$uData['name'] =$r->name;
					$uData['ccode'] =$r->country_code;
					$uData['mobile'] =$r->mobile;
					$uData['email'] =$r->email;
					$uData['Shop'] =$r->redeemed_agent_name;
					$uData['offer'] =$r->offer_text??"--";
					$uData['redeem'] =$r->redeem==1?"Redeemed":"Pending";
			    $data[] = $uData;
			}
        }

		return collect($data);   

    }

	
}
