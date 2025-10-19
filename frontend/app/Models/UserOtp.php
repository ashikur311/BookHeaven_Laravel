<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOtp extends Model
{
    protected $table = 'user_otp';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['user_id','otp_code','otp_time','purpose','otp_attempts'];
}
