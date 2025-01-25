<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Scratchads extends Model
{
   // use SoftDeletes;

	const ACTIVE = 1;
    const DEACTIVE = 0;

    protected $primaryKey = 'id';
    protected $table = 'scratch_ads';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'image','video','status','user_id',
    ];

     public static $ruleImage = [
      'image'=> 'required',
      'image.*' => 'image|mimes:jpeg,png,jpg'
    ];

    public static $messageImage = [
        'image.required'=>'Image is required',
        'image.mimes'=>'Unsupported image file',
       
     ];
      public static $ruleVideo = [
       'video'=> 'required',
        'video.*' => 'video|mimes:mp4'
     ];

     public static $messageVideo = [
         'video.required'=>'Video is required',
         'video.mimes'=>'Unsupported video file',
        
      ];

}
