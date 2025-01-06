<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\WithHeadings;
//use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

use App\Models\ScratchCustomer;
use App\Models\User;

class ScratchCustomersList implements FromCollection,WithHeadings
{
	//use Exportable;
	
	protected $sDate=null;
	protected $eDate=null;
	protected $branch=null;
	protected $campaign=null;

	function __construct($data)
	{
		$this->sDate=$data['sdate'];
		$this->eDate=$data['edate'];
		$this->branch=$data['branch'];
		$this->campaign=$data['campaign'];
	}

    /**
    * @return \Illuminate\Support\Collection
    */

	  public function headings():array{
        return[
            'Slno',
			'Name',
            'Mobile',
			'Email',
			'Branch',
			'Offer',
			"Status"
        ];
    } 
	
    public function collection()
    {
		
		$user_id=User::getVendorId();
		$sdate=$this->sDate;
		$edate=$this->eDate;
		$branch=$this->branch;
		$campaign=$this->campaign;
		
		
      $cust = ScratchCustomer::select('tbl_scratch_customers.*','scratch_branches.branch','tbl_scratch_offers.vchr_scratch_offers_name')
      ->leftJoin('scratch_branches','tbl_scratch_customers.branch_id','=','scratch_branches.id')
	  ->leftJoin('tbl_scratch_offers','tbl_scratch_customers.fk_int_offer_id','=','tbl_scratch_offers.pk_int_scratch_offers_id')
	  ->where('tbl_scratch_customers.fk_int_user_id',$user_id);
	  
	    if($branch!="")
            $cust->where('tbl_scratch_customers.branch_id',$branch);
		
        if($campaign!="")
            $cust->where('tbl_scratch_customers.campaign_id',$campaign);
		
        if($sdate && $edate)  
        {
            $cust->whereDate('tbl_scratch_customers.created_at','>=',$sdate)
               ->whereDate('tbl_scratch_customers.created_at','<=',$edate);
        }  

        $customers=$cust->orderby('pk_int_scratch_customers_id','Desc')->get();
				
		$data = array();
		$uData = array();
		
        if(!empty($customers))
        {
			foreach ($customers as $key=>$r)
            {
					$uData['slno'] = ++$key;
					$uData['name'] =$r->vchr_name;
					$uData['mobile'] =$r->vchr_mobno;
					$uData['email'] =$r->email;
					$uData['branch'] =$r->branch??"--";
					$uData['offer'] =$r->offer_text??"--";
					$uData['status'] =($r->int_status==1)?"Redeemed":"Pending";
			    $data[] = $uData;
			}
        }

		return collect($data);   

    }

	
}
