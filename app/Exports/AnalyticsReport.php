<?php

namespace App\Exports;


use Maatwebsite\Excel\Concerns\WithHeadings;
//use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;

use App\Models\User;
use App\Models\ScratchOffer;
use App\Models\ScratcOffersListing;

use Auth;
use DB;

class AnalyticsReport implements FromCollection,WithHeadings
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
			'User Id',
			'Name',
            'Mobile',
			'Total Gift',
			'Gift Used',
			'Gift Balance'
        ];
    } 
		
    public function collection()
    {
		
		$user_id=Auth::user()->pk_int_user_id;
	  	
		$giftDt=User::select('pk_int_user_id','vchr_user_name','mobile',DB::raw('SUM(int_scratch_offers_count) as total_gift_count'),DB::raw('SUM(int_scratch_offers_balance) as total_gift_balance'))
		->leftJoin('tbl_scratch_offers_listing','tbl_users.pk_int_user_id','=','tbl_scratch_offers_listing.fk_int_user_id')
		->where('parent_user_id',$user_id)->groupBy('pk_int_user_id','vchr_user_name','mobile')->get();


		$data = array();
		$uData = array();
		
        if(!empty($giftDt))
        {
			foreach ($giftDt as $key=>$r)
            {
					$uData['slno'] = ++$key;
					$uData['user'] =$r->user_id;
					$uData['name'] =$r->name;
					$uData['mobile'] =$r->mobile;
					$uData['total_gift'] =$r->total_gift_count;
					$uData['used_gift'] =$r->total_gift_count-$r->total_gift_balance;
					$uData['balance'] =$r->total_gift_balance;
			    $data[] = $uData;
			}
        }

		return collect($data);   

    }

	
}
