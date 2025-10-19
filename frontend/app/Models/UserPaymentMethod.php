<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPaymentMethod extends Model
{
    protected $table = 'user_payment_methods';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'user_id','card_type','card_number','card_name','expiry_date','cvv','is_default'
    ];
}
