<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class LoginHistory extends Model
{
   // use SoftDeletes;

	const ACTIVATE = 1;
    const DEACTIVATE = 0;



    protected $primaryKey = 'pk_int_login_history_id';
    protected $table = 'tbl_login_history';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    // protected $fillable = [
    //     'vchr_designation','vchr_description','int_status',
    // ];

    //  public static $rules = [
    //     'vchr_designation' => 'required',
    // ];

    // public $rulesMessage = [
        
    // ];

     
}
