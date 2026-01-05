<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    /* ================= GET ALL PRODUCTS ================= */
    public function index(Request $request)
    {
        $onlyActive = $request->query('onlyActive', true); // default true
        $products = $this->service->getAll($onlyActive);
        return response()->json([
            'isSuccess' => true,
            'data' => $products
        ]);
    }

    /* ================= GET PRODUCT BY ID ================= */
    public function show($id)
    {
        $product = $this->service->getById($id);
        return response()->json([
            'isSuccess' => true,
            'data' => $product
        ]);
    }

    /* ================= CREATE PRODUCT ================= */
    public function store(Request $request)
    {
        $data = $request->all();
        $product = $this->service->create($data);
        return response()->json([
            'isSuccess' => true,
            'data' => $product
        ]);
    }

    /* ================= UPDATE PRODUCT ================= */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $this->service->update($id, $data);
        return response()->json([
            'isSuccess' => true,
            'message' => 'Product updated successfully'
        ]);
    }

    /* ================= DELETE PRODUCT ================= */
    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->json([
            'isSuccess' => true,
            'message' => 'Product deleted successfully'
        ]);
    }

    /* ================= CHANGE IS_ACTIVE ================= */
    public function changeIsActive(Request $request)
    {
        $productId = (int) $request->query('productId');
        $isActive  = filter_var($request->query('isActive'), FILTER_VALIDATE_BOOLEAN);

        $this->service->changeIsActive($productId, $isActive);

        return response()->json([
            'isSuccess' => true,
            'message' => 'Product status updated'
        ]);
    }

    /* ================= GET ORDERED / FILTERED ================= */
    public function getOrdered(Request $request)
    {
        $filters = $request->only([
            'orderedBy', 'pageNumber', 'pageSize', 'categoryId', 'searchQuery', 'onlyActive'
        ]);


        // normalize keys
        $filters['search']    = $filters['searchQuery'] ?? null;
        $filters['onlyActive']= isset($filters['onlyActive']) ? filter_var($filters['onlyActive'], FILTER_VALIDATE_BOOLEAN) : true;

        
        $products = $this->service->getOrdered($filters);

        return response()->json([
            'isSuccess' => true,
            'data' => $products
        ]);
    }
}
