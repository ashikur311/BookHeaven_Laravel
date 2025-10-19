<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = ['order_id','book_id','quantity','price'];

    public function book()
    {
        return $this->belongsTo(Book::class, 'book_id', 'book_id');
    }
}
