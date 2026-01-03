<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'img_url',
        'parent_id'
    ];

    // Relation: parent category
    // this function for accessing parent object category of a category eg: $category->parent
    public function parent() {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Relation: child categories
    // this function for accessing child categories object of a category eg: $category->children
    public function children() {
        return $this->hasMany(Category::class, 'parent_id');
    }


        // Relation: products Many to Many
    // this function for accessing all products of a category eg: $category->products
    public function products() {
    return $this->belongsToMany(Product::class);
}

}
