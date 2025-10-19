<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionOrder extends Model
{
    protected $table = 'subscription_orders';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'user_id','plan_id','amount','invoice_number','status','payment_status',
        'issue_date','expire_date','user_subscription_id','payment_method'
    ];
}
