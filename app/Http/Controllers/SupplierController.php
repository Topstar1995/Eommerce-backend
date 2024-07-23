<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SupplierController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:suppliers',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $supplier = Supplier::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        return response()->json(['supplier' => $supplier], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::guard('supplier')->attempt($credentials)) {
            $supplier = Auth::guard('supplier')->user();
            return response()->json(['supplier' => $supplier], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function products(Request $request)
    {
        $supplier = Auth::guard('supplier')->user();
        return response()->json(['products' => $supplier->products], 200);
    }
}
