<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /* ===================== GET ===================== */

    public function getById(int $id): Product
    {
        return Product::with(['categories', 'images', 'sizes', 'colors'])
                      ->findOrFail($id);
    }

    public function getAll(bool $onlyActive): \Illuminate\Support\Collection
    {
        $query = Product::with(['images', 'categories']);

        if ($onlyActive) {
            $query->where('is_active', true);
        }

        return $query->get();
    }

    /* ===================== CREATE ===================== */

    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {

            $product = Product::create([
                'created_at'     => $data['created_at'] ?? now(),
                'updated_at'     => $data['updated_at'] ?? now(),
                'name'           => $data['name'],
                'description'    => $data['description'] ?? null,
                'price'          => $data['price'],
                'discount_price' => $data['discount_price'] ?? null,
                'brand'          => $data['brand'] ?? null,
                'rating'         => $data['rating'] ?? 0,
                'reviews_count'  => $data['reviews_count'] ?? 0,
                'user_id'        => $data['user_id'],
                'in_stock'       => $data['in_stock'] ?? 0,
                'is_active'      => $data['is_active'] ?? true,
            ]);

            // ⚡ Categories (M-M) — always sync, even for new product
            if (!empty($data['category_ids'])) {
                $product->categories()->sync($data['category_ids']);
            }

            // ⚡ Images (1-M)
            if (!empty($data['images'])) {
                foreach ($data['images'] as $image) {
                    $product->images()->create(['url' => $image]);
                }
            }

            // ⚡ Sizes (1-M)
            if (!empty($data['sizes'])) {
                foreach ($data['sizes'] as $size) {
                    $product->sizes()->create(['size' => $size]);
                }
            }

            // ⚡ Colors (1-M)
            if (!empty($data['colors'])) {
                foreach ($data['colors'] as $color) {
                    $product->colors()->create(['color' => $color]);
                }
            }

            return $product;
        });
    }

    /* ===================== UPDATE ===================== */

    public function update(int $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {

            $product = Product::findOrFail($id);

            // Update main fields
            $product->update([
                'name'           => $data['name'],
                'description'    => $data['description'] ?? $product->description,
                'price'          => $data['price'] ?? $product->price,
                'discount_price' => $data['discount_price'] ?? $product->discount_price,
                'brand'          => $data['brand'] ?? $product->brand,
                'rating'         => $data['rating'] ?? $product->rating,
                'reviews_count'  => $data['reviews_count'] ?? $product->reviews_count,
                'updated_at'     => now(),
                'in_stock'       => $data['in_stock'] ?? $product->in_stock,
                'is_active'      => $data['is_active'] ?? $product->is_active,
            ]);

            // ⚡ Categories (M-M) — sync replaces old categories with new ones
            if (isset($data['category_ids'])) {
                $product->categories()->sync($data['category_ids']);
            }

            // ⚡ Images (1-M) — delete old, insert new
            if (isset($data['images'])) {
                $product->images()->delete();
                foreach ($data['images'] as $image) {
                    $product->images()->create(['image_url' => $image]);
                }
            }

            // ⚡ Sizes (1-M)
            if (isset($data['sizes'])) {
                $product->sizes()->delete();
                foreach ($data['sizes'] as $size) {
                    $product->sizes()->create(['size' => $size]);
                }
            }

            // ⚡ Colors (1-M)
            if (isset($data['colors'])) {
                $product->colors()->delete();
                foreach ($data['colors'] as $color) {
                    $product->colors()->create(['color' => $color]);
                }
            }

            return true;
        });
    }

    /* ===================== DELETE ===================== */

    public function delete(int $id): bool
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return true;
    }

    /* ===================== CHANGE STATUS ===================== */

    public function changeIsActive(int $productId, bool $isActive): bool
    {
        $product = Product::findOrFail($productId);
        $product->update(['is_active' => $isActive]);
        return true;
    }

    /* ===================== ORDERED / FILTERED ===================== */

    public function getOrdered(array $filters)
    {
        $query = Product::with(['images', 'categories']);

        if (!empty($filters['onlyActive'])) {
            $query->where('is_active', true);
        }

        if (!empty($filters['categoryId'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('categories.id', $filters['categoryId']);
            });
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        switch ($filters['orderedBy'] ?? 1) {
            case 1: $query->latest('created_at'); break;
            case 2: $query->orderBy('price'); break;
            case 3: $query->orderByDesc('price'); break;
            case 5: $query->whereNotNull('discount_price'); break;
        }

        return $query->paginate(
            $filters['pageSize'] ?? 20,
            ['*'],
            'page',
            $filters['pageNumber'] ?? 1
        );
    }
}
