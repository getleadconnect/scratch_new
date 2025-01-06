<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlApiTokens extends Model
{

    const ACTIVATE=1;
	const DEACTIVATE=0;

    protected $dates = ['deleted_at'];
    protected $primaryKey = 'pk_int_token_id';
    protected $table = 'tbl_gl_api_tokens';

 	protected $fillable = [
        'fk_int_user_id','vchr_token'
    ];
}
