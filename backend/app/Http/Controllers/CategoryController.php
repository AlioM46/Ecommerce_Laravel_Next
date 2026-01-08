<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CategoryService;

class CategoryController extends Controller
{
    protected $service;

    public function __construct(CategoryService $service) {
        $this->service = $service;
    }

    public function index() {
        return response()->json($this->service->getAll());
    }

    public function show($id) {
        return response()->json($this->service->getById($id));
    }

    public function store(Request $request) {
        return response()->json(['data' => $this->service->create($request->all()), 'isSuccess' => true]);
    }

    public function update(Request $request, $id) {
        return response()->json(['data' =>  $this->service->update($id, $request->all()), 'isSuccess' => true]);
    }

    public function destroy($id) {
        return response()->json(['data' => $this->service->delete($id), 'isSuccess' => true]);
    }
       
    public function getCategoryTreeByCategoryFirstLevel($id) {
        return response()->json($this->service->getCategoryTreeByCategoryFirstLevel($id));
    }

    
    public function getByName($name) {
        return response()->json($this->service->getByName($name));
    }


    public function getTop10Categories() {
        return response()->json($this->service->getTop10Categories());
    }



}
