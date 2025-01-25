<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Settings extends Model
{
    //
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $primaryKey = 'pk_int_settings_id';
    protected $table = 'tbl_settings';

    protected $fillable = [
        'vchr_settings_type', 'vchr_settings_value', 'int_status','fk_int_user_id'
    ];

    public static $rules = [
        'photo'=>'required|mimes:jpeg,png,jpg,gif,svg|max:2048|dimensions:min_width=400, max_width=400,min_height=200,max_height=200'
    ];

    public static $message = [
        'photo.required'=>'System image is required',
        'photo.max'=>'Maximum 2MB file is permitted',
        'photo.mimes'=>'Unsupported image file',
        'photo.dimensions'=>'Image size should be 200*400.'
    ];

}
