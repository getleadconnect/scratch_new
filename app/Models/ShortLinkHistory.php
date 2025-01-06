<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ShortLinkHistory extends Model
{
    use SoftDeletes;
	
    const MOBILE = 'Mobile';
    const TABLET = 'Tablet' ;
    const DESKTOP = 'Desktop';
    const PHONE = 'Phone' ;
    const ROBOT= 'Robot';
	
    protected $fillable = [
        'short_link_id',
        'date',
        'ip_address',
        'mac_address',
        'device',
        'os',
        'browser',
        'device_type',
        'country',
        'city',
        'region',
        'area_code',
        'country_code',
        'continent',  
        'latitude',
        'logitude',
        'currency',
        'timezone',
    ];
}
