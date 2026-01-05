<?php

namespace App\Services;

use App\Models\Category;

class CategoryService
{
    public function getAll()
    {
        return Category::all();
    }

public function getTop10Categories()
{
    return Category::withCount('products')
        ->orderByDesc('products_count')
        ->take(10)
        ->get();

}


   public function getCategoryTreeByCategoryFirstLevel($categoryId)
{
    // Load category + direct children only
    $category = Category::with('children')
        ->where('id', $categoryId)
        ->first();

    if (!$category) {
        return null;
    }

    // Build breadcrumb (walk up parents)
    $breadcrumb = [];
    $currentParentId = $category->parent_id;

    while ($currentParentId) {
        $parent = Category::find($currentParentId);
        if (!$parent) {
            break;
        }

        array_unshift($breadcrumb, [
            'id' => $parent->id,
            'name' => $parent->name,
        ]);

        $currentParentId = $parent->parent_id;
    }

    // Map to DTO-like array (Laravel style)
    return [
        'id' => $category->id,
        'name' => $category->name,
        'img_url' => $category->img_url,
        'parent_id' => $category->parent_id,
        'parent_name' => $breadcrumb[count($breadcrumb) - 1]['name'] ?? null,
        'breadcrumb' => $breadcrumb,
        'children' => $category->children->map(function ($sub) {
            return [
                'id' => $sub->id,
                'name' => $sub->name,
                'img_url' => $sub->img_url,
                'parent_id' => $sub->parent_id,
                'children' => [] // intentionally empty
            ];
        })->values()
    ];
}

    public function getByName($name)
    {
        return Category::where('name', $name)->firstOrFail();
    }

    public function getById($id)
    {
        return Category::findOrFail($id);
    }

    public function create(array $data)
    {
        return Category::create($data);
    }

    public function update($id, array $data)
    {
        $category = Category::findOrFail($id);
        $category->update($data);

        return $category;
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return true;
    }
}
