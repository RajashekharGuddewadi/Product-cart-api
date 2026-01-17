<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Customer\AddToCartRequest;
use App\Http\Requests\Customer\UpdateCartItemRequest;

class CartController extends Controller
{
    /**
     * Show Product List Page
     * GET /products
     */
    public function productListing()
    {
        $products = Product::active()
            ->latest()
            ->paginate(12);

        return view('customer.products.list', compact('products'));
    }

    
    public function apiProducts(Request $request)
    {
        $products = Product::active()
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('sku', 'like', "%{$request->search}%");
            })
            ->latest()
            ->paginate(12);

        return response()->json([
            'status' => true,
            'message' => 'Products fetched successfully',
            'data' => $products->items(),
            'links' => $products->links()->toArray(),
        ]);
    }

    /**
     * Add to Cart
     * POST /api/cart/items
     */
    public function addToCart(AddToCartRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $product = Product::findOrFail($request->product_id);

            // Check if product is active and in stock
            if (!$product->is_active) {
                return response()->json([
                    'status' => false,
                    'message' => 'Product is not available',
                ], 422);
            }

            if ($product->stock < $request->quantity) {
                return response()->json([
                    'status' => false,
                    'message' => 'Insufficient stock. Available: ' . $product->stock,
                ], 422);
            }

            // Get or create active cart
            $cart = Cart::where('user_id', $user->id)->where('is_active', true)->first();

            if (!$cart) {
                $cart = Cart::create([
                    'user_id' => $user->id,
                    'is_active' => true,
                ]);
            }

            // Check if item already exists in cart (merge functionality)
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                // Update quantity
                $newQuantity = $cartItem->quantity + $request->quantity;

                // Validate stock for merged quantity
                if ($product->stock < $newQuantity) {
                    DB::rollBack();
                    return response()->json([
                        'status' => false,
                        'message' => 'Insufficient stock. Available: ' . $product->stock,
                    ], 422);
                }

                $cartItem->update([
                    'quantity' => $newQuantity,
                ]);

                $message = 'Cart updated successfully';
            } else {
                // Create new cart item
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                    'price_at_time' => $product->price,
                ]);

                $message = 'Added to cart successfully';
            }

            DB::commit();

            // Reload cart data
            $cart->load('items.product');

            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => $this->formatCartData($cart),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to add to cart: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get Cart Details
     * GET /api/cart
     */
    public function getCart()
    {
        $user = Auth::user();
        $cart = Cart::with(['items.product'])->where('user_id', $user->id)->where('is_active', true)->first();

        if (!$cart) {
            return response()->json([
                'status' => true,
                'message' => 'Cart is empty',
                'data' => [
                    'items' => [],
                    'total' => 0,
                ],
            ], 200);
        }

        return response()->json([
            'status' => true,
            'message' => 'Cart fetched successfully',
            'data' => $this->formatCartData($cart),
        ], 200);
    }

    /**
     * Update Cart Item Quantity
     * PATCH /api/cart/items/{product_id}
     */
    public function updateCartItem(UpdateCartItemRequest $request, $productId)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $cart = Cart::where('user_id', $user->id)->where('is_active', true)->first();

            if (!$cart) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart not found',
                ], 404);
            }

            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->first();

            if (!$cartItem) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart item not found',
                ], 404);
            }

            $product = $cartItem->product;

            // Validate stock
            if ($product->stock < $request->quantity) {
                return response()->json([
                    'status' => false,
                    'message' => 'Insufficient stock. Available: ' . $product->stock,
                ], 422);
            }

            if ($request->quantity === 0) {
                $cartItem->delete();
                $message = 'Item removed from cart';
            } else {
                $cartItem->update(['quantity' => $request->quantity]);
                $message = 'Cart item updated successfully';
            }

            DB::commit();

            $cart->load('items.product');

            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => $this->formatCartData($cart),
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to update cart: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete Cart Item
     * DELETE /api/cart/items/{product_id}
     */
    public function deleteCartItem($productId)
    {
        try {
            $user = Auth::user();
            $cart = Cart::where('user_id', $user->id)->where('is_active', true)->first();

            if (!$cart) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart not found',
                ], 404);
            }

            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $productId)
                ->first();

            if (!$cartItem) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart item not found',
                ], 404);
            }

            $cartItem->delete();
            $cart->load('items.product');

            return response()->json([
                'status' => true,
                'message' => 'Item removed from cart',
                'data' => $this->formatCartData($cart),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete cart item: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Checkout
     * POST /api/cart/checkout
     */
    public function checkout()
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $cart = Cart::with(['items.product'])->where('user_id', $user->id)->where('is_active', true)->first();

            if (!$cart || $cart->items->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart is empty',
                ], 422);
            }

            $orderData = [];
            $totalAmount = 0;

            // Validate stock for all items
            foreach ($cart->items as $item) {
                $product = $item->product;

                if ($product->stock < $item->quantity) {
                    return response()->json([
                        'status' => false,
                        'message' => "Insufficient stock for {$product->name}. Available: {$product->stock}",
                    ], 422);
                }

                // Deduct stock
                $product->decrement('stock', $item->quantity);

                $totalAmount += $item->price_at_time * $item->quantity;

                $orderData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'price_at_time' => $item->price_at_time,
                ];
            }

            // Deactivate cart (simulate order completion)
            $cart->update(['is_active' => false]);

            // For production: Create Order and OrderItem models here
            // For now, we just clear the cart

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Checkout successful! Order has been placed.',
                'data' => [
                    'order_data' => $orderData,
                    'total_amount' => $totalAmount,
                ],
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Checkout failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show Cart Page
     * GET /cart
     */
    public function showCart()
    {
        $user = Auth::user();
        $cart = Cart::with(['items.product'])->where('user_id', $user->id)->where('is_active', true)->first();

        return view('customer.cart.index', compact('cart'));
    }

    /**
     * Format cart data for JSON response
     */
    private function formatCartData($cart)
    {
        $items = $cart->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'sku' => $item->product->sku,
                'quantity' => $item->quantity,
                'price_at_time' => (float) $item->price_at_time,
                'subtotal' => $item->quantity * $item->price_at_time,
            ];
        });

        return [
            'items' => $items,
            'total' => (float) $cart->total,
            'item_count' => $cart->items->count(),
        ];
    }
}
