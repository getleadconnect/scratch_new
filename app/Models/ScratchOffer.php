<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScratchOffer extends Model
{
    use SoftDeletes;
	
	const ACTIVATE = 1;
	const DEACTIVATE = 0; 
	
    const TYPES = [
        "1" => 'Scratch and Win',
        "2" => 'Lucky Draw'
    ];
	
	protected $primaryKey = 'pk_int_scratch_offers_id';
	protected $table = 'tbl_scratch_offers';

	protected $fillable = [
        'vchr_scratch_offers_name',
        'fk_int_user_id', 
        'int_status',
        'vchr_scratch_offers_image',
		'mobile_image',
        'type_id',
	];

    public static $rule = [
    	'offer_name' => 'required',
        'offer_image'=> 'mimes:jpeg,png,jpg,svg',
		'offer_type'=>'required',
		'mobile_image'=>'required'
    ];

    public static $message = [
    	'offer_name.required' => 'Offer Name is required',
        'offer_image.required' => 'Image is required',
        'offer_type.required'=>'Offer type required',
		'mobile_image.required'=>'Mobile image required.',
		'mobile_image.mimes'=>'Unsupported files, Try again.',
		'offer_image.mimes'=>'Unsupported files, Try again.',
    ];

    public static $ruleUpdate = [
        'offer_name_edit' => 'required',
        'offer_image_edit'=> 'mimes:jpeg,png,jpg,svg',
		'offer_type_edit'=>'required',
		'mobile_image_edit'=>'mimes:jpeg,png,jpg,svg',
     ];

    public static $messageUpdate = [
        'offer_name_edit.required' => 'Offer Name is required',
        'offer_image_edit.required' => 'Image is required',
        'offer_type_edit.required'=>'Campaign type required',
		'mobile_image_edit.required'=>'Mobile image required.',
		'mobile_image_edit.mimes'=>'Unsupported files, Try again.',
		'offer_image_edit.mimes'=>'Unsupported files, Try again.',
    ];
	
	
	
   /* public function offerListing(){
        $this->hasMany('App\BackendModel\ScratchOffersListing');
    }
    public function getTypeNameAttribute(){
        if($this->type_id){
            if(isset(ScratchOffers::TYPES[$this->type_id]))
                return ScratchOffers::TYPES[$this->type_id];
            else
                return ScratchOffers::TYPES["1"];
        }else
            return "";
    }

    public function shortLink(){
        return $this->hasMany('App\BackendModel\ShortLink','offer_id','pk_int_scratch_offers_id');
    }
*/
   
}
