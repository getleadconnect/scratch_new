<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScratchType extends Model
{
    use SoftDeletes;
	
	const ACTIVATE = 1;
	const DEACTIVATE = 0; 

	protected $primaryKey = 'id';
	protected $table = 'scratch_type';

	protected $fillable = [
        'type',
		'vendor_id',
        'status', 
	];

}




