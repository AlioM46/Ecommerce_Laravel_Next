<?php

namespace App\Models;

use App\Services\StorageService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ProductImage extends Model
{

    protected $fillable = ['url', 'product_id'];
    public $timestamps = false; 
    
        protected $appends = ['full_url'];


    public function getFullUrlAttribute(): ?string
    {
        // Resolve the StorageService from Laravel container
        $storage = app(StorageService::class);

        // Check if url (key) exists
        if (!$this->url ) {
            return null;
        }

            $key = trim($this->url);

    if (Str::startsWith($key, ['http://', 'https://'])) {
        return $key;
    }
        $data = $storage->temporaryUrl($key, 60);

        return   $data;
    }
    public function product() {
        return $this->belongsTo(Product::class);
    }
}
