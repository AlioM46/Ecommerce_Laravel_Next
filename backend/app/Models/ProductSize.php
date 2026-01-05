<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    protected $fillable = ['size', 'product_id'];
    public $timestamps = false; 
    
    public function product() {
        return $this->belongsTo(Product::class);
    }
}
