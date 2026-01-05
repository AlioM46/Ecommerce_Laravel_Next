<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductColor extends Model
{
    protected $fillable = ['color', 'product_id'];
    public $timestamps = false; 


    public function product() {
        return $this->belongsTo(Product::class);
    }
}
