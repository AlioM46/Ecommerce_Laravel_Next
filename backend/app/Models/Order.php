<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // use SoftDeletes;

    protected $fillable = [
        'user_id','shipping_address_id','total_price','status'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function address() {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    public function products() {
        return $this->belongsToMany(Product::class, 'order_product')
                    ->withPivot('quantity','price')
                    ->withTimestamps();
    }

    public function payment() {
        return $this->hasOne(Payment::class);
    }
}
