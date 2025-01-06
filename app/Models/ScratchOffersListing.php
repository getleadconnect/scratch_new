<?php

namespace App\Models;

use App\Facades\FileUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScratchOffersListing extends Model
{
	use SoftDeletes;
	
	const ACTIVATE = 1;
	const DEACTIVATE = 0; 
	
	const WIN=1;
	const LOST=0;
	
	protected $primaryKey = 'pk_int_scratch_offers_listing_id';
	protected $table = 'tbl_scratch_offers_listing';

	protected $fillable = [
		'fk_int_scratch_offers_id',
		'int_scratch_offers_count', 
		'txt_description',
		'int_scratch_offers_balance',
		'fk_int_user_id',
		'int_status',
		'image',
		'type_id'
	];

    public static $rule = [
    	'offers_count' => 'required|numeric',
        'description' => 'required',
    ];

    public static $message = [
    	'offers_count.required' => 'Offer Count is required',
        'offers_count.numeric' => 'Offer Count must be numeric',
        'description.required' => 'Description is required',
    ];

	
	/*public function offer(){
		$this->belongTo('App\BackendModel\ScratchOffers','fk_int_scratch_offers_id');
	}
   
	public function getGiftImageAttribute()
	{
		return  FileUpload::viewFile($this->image,'s3');
	}
	*/
	
}
