<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionTransaction extends Model
{
    protected $table = 'subscription_transactions';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'user_subscription_id','amount','payment_method','payment_status',
        'transaction_code','payment_provider','transaction_date'
    ];
}
