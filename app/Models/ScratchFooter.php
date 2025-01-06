<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ScratchFooter extends Model
{
   // use SoftDeletes;

	const ACTIVE = 1;
    const DEACTIVE = 0;



    protected $primaryKey = 'id';
    protected $table = 'scratch_footer';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'content','vendor_id',
    ];

      public static $rule = [

      
      'content'=> 'required',
     
      
      
    ];

    public static $message = [
        'content.required'=>'Content is required',
      
       
     ];

     

   
      
     
}
