<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        return Product::where('supplier_id', Auth::id())->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'quantity' => $request->quantity,
            'supplier_id' => Auth::id(),
        ]);

        return response()->json($product, 201);
    }

    public function show($id)
    {
        $product = Product::where('supplier_id', Auth::id())->findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::where('supplier_id', Auth::id())->findOrFail($id);

        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'quantity' => 'sometimes|required|integer|min:1',
        ]);

        $product->update($request->all());

        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::where('supplier_id', Auth::id())->findOrFail($id);
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function orders($id)
    {
        $product = Product::where('supplier_id', Auth::id())->findOrFail($id);
        $orders = $product->orderItems()->with('order.user')->get();

        return response()->json($orders);
    }
}
