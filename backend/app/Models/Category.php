<?php

namespace App\Models;

use App\Services\StorageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'img_url',
        'parent_id'
    ];

       
    protected $appends = ['full_img_url'];


    public function allChildrenIds(): array
    {
    // Start with self
    $ids = [$this->id];

    // Add children recursively
    foreach ($this->children as $child) {
        $ids = array_merge($ids, $child->allChildrenIds());
    }

    return $ids;
    }

    /**
     * Accessor: full_img_url
     */
    public function getFullImgUrlAttribute(): ?string
    {
        if (!$this->img_url) {
            return null;
        }

        $key = trim($this->img_url);

        // If already a full URL, return as-is
        if (Str::startsWith($key, ['http://', 'https://'])) {
            return $key;
        }

        // Generate temporary signed URL from R2
        $storage = app(StorageService::class);
        return $storage->temporaryUrl($key, 60);
    }

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
