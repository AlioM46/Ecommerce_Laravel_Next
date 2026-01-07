<?php 
namespace App\Services;
use App\Enums\enOrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{

    //   'user_id','shipping_address_id','total_price','status'
public function createOrder(int $userId, int $addressId, array $items)
{
    return DB::transaction(function () use ($userId, $addressId, $items) {

        $user = User::findOrFail($userId);

        $address = $user->addresses()
                        ->where('id', $addressId)
                        ->firstOrFail();

        $order = Order::create([
            'user_id' => $userId,
            'shipping_address_id' => $addressId,
            'status' => enOrderStatus::Pending,
            'total_price' => 0
        ]);

        $total = 0;


        foreach ($items as $item) {

            $product = Product::findOrFail($item['product_id']);

            $quantity = (int) $item['quantity'];
            $price    = $product->price; 

            $order->products()->attach($product->id, [
                'quantity' => $quantity,
                'price' => $price
            ]);

            $total += $quantity * $price;
        }


        $order->update([
            'total_price' => $total
        ]);

return response()->json([
    'isSuccess' => true,
    'message' => 'Order created successfully',
    'order' => $order
]);    });
}

public function getUserOrders($userId) {

    $ordersList = Order::where("user_id", $userId)
    ->with([
            'products:id,name,price',
            'address:id,full_name,city,country,postal_code,phone',
            'payment:id,order_id,amount,status,payment_method'
            ])
            ->orderBy('created_at', 'desc')
            ->get();    

                    
    return $ordersList;

}

public function getOrderById(int $orderId, int $userId)
{
    return Order::where('id', $orderId)
        ->where('user_id', $userId)
        ->with([
            'products:id,name,price',
            'address:id,full_name,address,city,state,country,postal_code,phone',
            'payment:id,order_id,amount,status,payment_method'
        ])
        ->firstOrFail();
}

public function cancelOrder(int $orderId, int $userId)
{
    $order = Order::where('id', $orderId)
        ->where('user_id', $userId)
        ->firstOrFail();

    if ($order->status !== enOrderStatus::Pending) {
        throw new \Exception('Order cannot be cancelled at this stage');
    }

    $order->update([
        'status' => enOrderStatus::Cancelled
    ]);

    return $order;
}

// why its has no userId parameter like other methods?
// because this function is intended to be used by admin or system processes like:
// payment gateways callbacks or admin.
public function markOrderAsCompleted(int $orderId)
{
    $order = Order::where('id', $orderId)
        ->firstOrFail();

    if ($order->status !== enOrderStatus::Paid) {
        throw new \Exception('Order is not in a completed state');
    }

    $order->update([
        'status' => enOrderStatus::Completed
    ]);

    return $order;
}

public function markOrderAsPaid(int $orderId)
{
    $order = Order::where('id', $orderId)
        ->firstOrFail();

    if ($order->status !== enOrderStatus::Pending) {
        throw new \Exception('Order is not in a payable state');
    }

    $order->update([
        'status' => enOrderStatus::Paid
    ]);

    return $order;
}




}
