<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScratchCustomer extends Model
{
	use SoftDeletes;
	const ACTIVATE = 1;
	const DEACTIVATE = 0; 
	protected $primaryKey = 'pk_int_scratch_customers_id';
	protected $table = 'tbl_scratch_customers';

	protected $fillable = [
		'vchr_name','fk_int_user_id', 'int_status','vchr_mobno','vchr_billno','fk_int_offer_id','branch_id','campaign_id', 'unique_id'
	];

    

   
}
