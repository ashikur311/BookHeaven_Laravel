<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Admin extends Model
{
    protected $table = 'admin';          // existing table name
    protected $primaryKey = 'admin_id';  // existing PK
    public $timestamps = true;           // uses created_at/updated_at

    protected $fillable = [
        'username', 'email', 'password', 'full_name',
    ];

    protected $hidden = ['password'];

    // Helper to set hashed password if you assign plain text
    public function setPasswordAttribute($value)
    {
        if ($value && !str_starts_with($value, '$2y$')) {
            $this->attributes['password'] = Hash::make($value);
        } else {
            $this->attributes['password'] = $value;
        }
    }
}
