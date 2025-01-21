<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScratchWebCustomer extends Model
{
    //
 use SoftDeletes;
 
 const SCRATCHED=1;
 const NOT_SCRATCHED=0;
 const REDEEMED=1;
 const NOT_REDEEMED=0;
 
protected $guarded = [];  
 
/* protected $fillable=[
    'user_id',
    'unique_id',
    'name',
    'mobile',
    'country_code',
	'vchr_mobile',
	'offer_id',
    'offer_list_id',
    'offer_text',
    'bill_no',
    'short_link',
    'status',
    'redeem',
    'ip_address',
    'branch_id'

 ];*/
}
