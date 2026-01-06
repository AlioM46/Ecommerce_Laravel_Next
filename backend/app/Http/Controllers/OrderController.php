<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
        // $this->middleware('auth:sanctum'); // assume token auth
    }

    // List all orders for the logged-in user
    public function index(Request $request)
    {
        return $request->user();

        $orders = $this->orderService->getUserOrders($request->user()->id);
        return response()->json($orders);
    }

    // Get a single order by ID
    public function show($id, Request $request)
    {
        $order = $this->orderService->getOrderById($id, $request->user()->id);
        return response()->json($order);
    }

    // Create a new order
    public function store(Request $request)
    {
        $order = $this->orderService->createOrder(
            $request->user()->id,
            $request->input('address_id'),
            $request->input('items')
        );

        return response()->json($order);
    }

    // Cancel an order
    public function cancel($id, Request $request)
    {
        $order = $this->orderService->cancelOrder($id, $request->user()->id);
        return response()->json($order);
    }

    // Mark order as paid (system-only)
    public function markAsPaid($id)
    {
        $order = $this->orderService->markOrderAsPaid($id);
        return response()->json($order);
    }
}
