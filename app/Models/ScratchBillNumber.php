<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScratchBillNumber extends Model
{
	
	
    protected $primaryKey = 'id';
	
    protected $table = 'scratch_bill_numbers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

     protected $fillable = [
        'vendor_id',
        'bill_number'
    ];

     public static $rule = [
      'bill_number'=> 'required',
      'offer_id'=> 'required',
    ];

    public static $message = [
        'bill_number.required'=>'Bill Number is required',
        'offer_id.required'=>'Offer is required',
     ];
	 
	 public static $editRule = [
      'bill_number_edit'=> 'required',
      'offer_id_edit'=> 'required',
    ];

    public static $editMessage = [
        'bill_number_edit.required'=>'Bill Number is required',
        'offer_id_edit.required'=>'Offer is required',
     ];
}

            
            
            
            
            
            
            
            