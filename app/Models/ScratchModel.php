<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class ScratchModel extends Model
{
   // use SoftDeletes;

	const ACTIVE = 1;
    const DEACTIVE = 0;



    protected $primaryKey = 'id';
    protected $table = 'scratch_model';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'vendor_id','status'
    ];

    

     
}
