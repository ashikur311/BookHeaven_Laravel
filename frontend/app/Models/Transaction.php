<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'order_id','payment_method','payment_status','transaction_date','payment_reference'
    ];
}
