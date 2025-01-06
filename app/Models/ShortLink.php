<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShortLink extends Model
{
    //
    use SoftDeletes;
	
    const ACTIVE=1;
    const DEACTIVE=0;

    const BILL_NO=1;

    const URL_SHORT=2;
    const GL_SCRATCH=1;
    
    protected $fillable=[
        'vendor_id',
        'offer_id',
        'code',
        'link',
        'url',
        'click_count',
        'custom_field',
        'type',
        'status',
        'email_required'
    ];
	
    public function scratchOffer(){
        return $this->belongsTo('App\Models\ScratchOffer','offer_id','pk_int_scratch_offers_id');
    }
	
    public function user()
    {
        return $this->belongsTo('App\User');
    }

}
