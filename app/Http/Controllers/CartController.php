<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variation_id' => 'nullable|exists:product_variations,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $userId = Auth::id(); // Get authenticated user ID
        $sessionId = $userId ? null : session()->getId(); // Use session ID for guests

        $product = Product::findOrFail($validated['product_id']);
        $price = $validated['product_variation_id']
            ? ProductVariation::findOrFail($validated['product_variation_id'])->price
            : $product->price;

        // Check if the product or variation is already in the cart
        $cartItem = Cart::where('product_id', $validated['product_id'])
            ->when($validated['product_variation_id'], function ($query) use ($validated) {
                $query->where('product_variation_id', $validated['product_variation_id']);
            })
            ->when($userId, function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->when(!$userId, function ($query) use ($sessionId) {
                $query->where('session_id', $sessionId);
            })
            ->first();

        if ($cartItem) {
            // Update quantity if already in cart
            $cartItem->quantity += $validated['quantity'];
            $cartItem->save();
        } else {
            // Add new item to the cart
            Cart::create([
                'user_id' => $userId,
                'session_id' => $sessionId,
                'product_id' => $validated['product_id'],
                'product_variation_id' => $validated['product_variation_id'],
                'quantity' => $validated['quantity'],
                'price' => $price,
            ]);
        }

        return redirect()->back()->with('success', 'Product added to cart successfully.');
    }

    public function getCart()
    {
        $userId = Auth::id(); 
        $sessionId = session()->getId();
    
        // Fetch cart items with product and variation attributes
        $cartItems = Cart::with([
            'product',
            'variation.attributes.value.attribute' // Nested relationship to get attribute details
        ])
            ->when($userId, function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->when(!$userId, function ($query) use ($sessionId) {
                $query->where('session_id', $sessionId);
            })
            ->get();
    
        return Inertia::render('Cart/Index', ['cartItems' => $cartItems]);
    }
    

    public function deleteCartItem(Request $request)
{
    $validated = $request->validate([
        'cart_id' => 'required|exists:carts,id',
    ]);

    $userId = Auth::id();
    $sessionId = session()->getId();

    $cartItem = Cart::where('id', $validated['cart_id'])
        ->when($userId, function ($query) use ($userId) {
            $query->where('user_id', $userId);
        })
        ->when(!$userId, function ($query) use ($sessionId) {
            $query->where('session_id', $sessionId);
        })
        ->first();

    if ($cartItem) {
        $cartItem->delete();

        return redirect()->back()->with('success', 'Cart item deleted successfully.');
    }

    return redirect()->back()->with('success', 'Cart item deleted successfully.');
}


}
