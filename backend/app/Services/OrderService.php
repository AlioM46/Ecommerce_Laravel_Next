<?php 
namespace App\Services;

use App\Models\Order;

class OrderService
{
    public function all() {
        return Order::with('products','address','payment')->get();
    }

    public function find($id) {
        return Order::with('products','address','payment')->findOrFail($id);
    }

    public function create($data) {
        return Order::create($data);
    }

    public function update($order, $data) {
        $order->update($data);
        return $order;
    }

    public function delete($order) {
        $order->delete();
    }
}
