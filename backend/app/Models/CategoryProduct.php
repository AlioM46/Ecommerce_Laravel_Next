<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryProduct extends Model
{
    protected $table = 'product_categories';
    public $incrementing = false; // because composite key
    protected $primaryKey = null;

    protected $fillable = [
        'product_id','category_id'
    ];
}
