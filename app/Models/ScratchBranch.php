<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScratchBranch extends Model
{
    use SoftDeletes;
    const ACTIVATE = 1;
    const DEACTIVATE = 0;
	
    protected $primaryKey = 'id';
    protected $table = 'scratch_branches';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

     protected $fillable = [
        'vendor_id',
        'status',
        'branch',
    ];

     public static $rule = [
      'branch'=> 'required',
    ];

    public static $message = [
        'branch.required'=>'Branch is required',
     ];
	 
	 
}
