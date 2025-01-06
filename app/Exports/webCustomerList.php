<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\WithHeadings;
//use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

use App\Models\ScratchWebCustomer;

class ScratchWebCustomersList implements FromCollection,WithHeadings
{
	//use Exportable;
		

	function __construct()
	{

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
        ];
		
    } 
	
    public function collection()
    {
		$scdts=ScratchWebCustomer::select('scratch_web_customers.*','scratch_branches.branch')
		->leftJoin('scratch_branches','scratch_web_customers.branch_id','=','scratch_branches.id')
		->orderBy('scratch_web_customers.id','ASC')->get();

		$data = array();
		$uData = array();
		
        if(!empty($scdat))
        {
			foreach ($scdat as $key=>$r)
            {
				
					$uData['slno'] = ++$key;
					$uData['name'] =$r->name;
					$uData['ccode'] =$r->country_code;
					$uData['mobile'] =$r->mobile;
					$uData['email'] =$r->email;
					$uData['branch'] =$r->branch??"--";
			    $data[] = $uData;
			}
        }

		return collect($data);   

    }

	
}
