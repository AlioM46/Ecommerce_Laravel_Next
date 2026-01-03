<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $table = 'order_product';
    public $incrementing = false; // because composite key
    protected $primaryKey = null;

    protected $fillable = [
        'order_id','product_id','quantity','price'
    ];
}
