<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillingSubscription extends Model
{
    protected $table = 'billing_subscriptions';

    protected $fillable = [
        'fk_int_user_id',
        'vendor_id',
        'no_of_licenses',
        'plan_type',
        'services',
        'expiry_date',
        'start_date',
        'status'
    ];
}