<?php

namespace App\Models;

use App\ApiHistory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EnquiryType extends Model
{
    const MISSEDCALL = "Missed Call";
    const GLSCRATCH = "GL Scratch";
    const GLSCRATCH_WEB = "GL Scratch Web";
    const GLPROMO = "GL Promo";
    const GLVERIFY = "GL Verify";
    const CRM = "CRM";
    const IVR = "IVR";
    const API = "API";
    const NOTIFICATIONS = "NOTIFICATIONS";
    const GLEVENTS = "GL Events";
    const CHATSPAZ = "CHATSPAZ";
    const INTERAKT = "INTERAKT";
    const TELINFY = "TELINFY";
    const LIBROMI = "LIBROMI";
    const PICKY_ASSIST = "Picky Assist";
    const ALERTS_PANEL = "Alerts Panel";
    const WABIS = "Wabis";
    const DOUBLETICK = "Doubletick";
    //
    use SoftDeletes;

    protected $dates = ['deleted_at'];
    protected $primaryKey = 'pk_int_enquiry_type_id';
    protected $table = 'tbl_enquiry_types';

    protected $fillable = [
        'vchr_enquiry_type', 'fk_int_user_id'
    ];

    public static $rules = [
        'vchr_enquiry_type' => 'required|unique:tbl_enquiry_types,deleted_at,NULL',
    ];

    public static $rulesMessage = [
        'vchr_enquiry_type.required' => 'Enquiry Source is required.',
        'vchr_enquiry_type.unique' => 'This Source is already taken.'
    ];
    public function scopeOnlyUser($query){
        $query->where('vendor_id','!=',0);
    }
    // public function scopeUsedOnly($query,$vendorId){
    //     $Ids = Enquiry::where('fk_int_user_id',$vendorId)->pluck('fk_int_enquiry_type_id')->unique()->toArray();
    //     $query->where(function($where) use($Ids,$vendorId){
    //          $where->where('fk_int_user_id',$vendorId)->orWhere('vendor_id',$vendorId)->orwhereIn('pk_int_enquiry_type_id',$Ids);
    //      });
    // }

    public function scopeUsedOnly($query,$vendorId){
        if($vendorId == 1344){ // alukkas
            $query->where(function($where) use($vendorId){
                $where->where('tbl_enquiry_types.fk_int_user_id',$vendorId)->orWhere('vendor_id',$vendorId);
                $where->orWhereIn('pk_int_enquiry_type_id',[42,123]);
            });
        }else{
            $Ids = Enquiry::where('fk_int_user_id',$vendorId)
            ->whereIn('fk_int_enquiry_type_id',[1,2,3,4,5,6,7,8,9,31,33,34,35,36,37,38,39,40,41,42,43,44,45,47,69,
            67,71,72,80,81,123,3329,3330,3331,3332,3333,3334,3335,3336,3337,3338,3339,3340,3341,3342,3343,
            3344,3345,3346,3347,3348,3349,3350,3351,3352,3353,3354,3355,3356,3357,3358,3359,8017,327,328])
            ->pluck('fk_int_enquiry_type_id')->unique()->toArray();

            $query->where(function($where) use($vendorId,$Ids){
            $where->where('tbl_enquiry_types.fk_int_user_id',$vendorId)
            ->orWhere('vendor_id',$vendorId)/* ->orwhereIn('pk_int_enquiry_type_id',[0]) */;
            if($Ids)
                $where->orWhereIn('pk_int_enquiry_type_id',$Ids);
            });
        }
    }

    public function Enquiry()
    {
        return $this->belongsTo(Enquiry::class,'lead_type_id');
    }

    public function lead(){
        return $this->hasMany(Enquiry::class,'fk_int_enquiry_type_id');
    }

    public function apiHistory()
    {
        return $this->belongsTo(ApiHistory::class);
    }

    public function leadSource(){
        return $this->belongsTo(Enquiry::class,'pk_int_enquiry_type_id','fk_int_enquiry_type_id');
    }
}
