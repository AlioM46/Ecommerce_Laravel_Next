<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'name',
        'description',
        'price',
        'discount_price',
        'brand',
        'rating',
        'reviews_count',
        'created_at',
        'updated_at',
        'user_id',
        'in_stock',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'rating' => 'float',
        'reviews_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'user_id' => 'integer',
        'in_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function user()
    {
        // each product belongs to a ONE user (1-1)
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categories() {
    return $this->belongsToMany(Category::class);

}

    public function images() {
        return $this->hasMany(ProductImage::class);
    }

    public function colors() {
        return $this->hasMany(ProductColor::class);
    }

    public function sizes() {
        return $this->hasMany(ProductSize::class);
    }


}
