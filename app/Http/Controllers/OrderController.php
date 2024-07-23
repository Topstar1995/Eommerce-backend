<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index()
    {
        return Order::with('orderItems.product')->where('user_id', Auth::id())->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $total = 0;
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            if ($product->quantity < $item['quantity']) {
                return response()->json(['error' => 'Not enough stock for product: ' . $product->name], 400);
            }
            $total += $product->price * $item['quantity'];
        }

        $order = Order::create([
            'user_id' => Auth::id(),
            'total' => $total,
        ]);

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $product->quantity -= $item['quantity'];
            $product->save();

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $product->price,
            ]);
        }

        return response()->json($order->load('orderItems.product'), 201);
    }
}
