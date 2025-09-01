<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Offering;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CartController extends Controller
{
    /**
     * Get user's cart contents
     */
    public function index(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $sessionId = $request->session()->getId();
        
        $cartData = Cart::getCartTotal($userId, $sessionId);
        
        return response()->json([
            'success' => true,
            'data' => $cartData
        ]);
    }

    /**
     * Add item to cart
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|integer',
            'item_type' => 'required|string|in:' . Offering::class . ',' . Service::class,
            'quantity' => 'integer|min:1|max:50',
            'branch_id' => 'nullable|integer',
            'time_slot' => 'nullable|array',
            'options' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = Auth::id();
            $sessionId = $request->session()->getId();
            $itemId = $request->input('item_id');
            $itemType = $request->input('item_type');
            $quantity = $request->input('quantity', 1);

            // Validate that the item exists and is available
            $item = $itemType::find($itemId);
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item not found'
                ], 404);
            }

            // Check availability for services/offerings
            if (method_exists($item, 'isAvailable') && !$item->isAvailable($quantity)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item is not available or insufficient quantity'
                ], 400);
            }

            // Prepare additional data
            $additionalData = [];
            if ($request->has('branch_id')) {
                $additionalData['branch'] = ['id' => $request->input('branch_id')];
            }
            if ($request->has('time_slot')) {
                $additionalData['time_slot'] = $request->input('time_slot');
            }
            if ($request->has('options')) {
                $additionalData['options'] = $request->input('options');
            }

            // Get item price
            $price = $item->price ?? $item->amount ?? 0;

            // Add to cart
            $cartItem = Cart::addItem($userId, $sessionId, $itemId, $itemType, $quantity, $price, $additionalData);

            // Get updated cart totals
            $cartData = Cart::getCartTotal($userId, $sessionId);

            return response()->json([
                'success' => true,
                'message' => 'Item added to cart successfully',
                'data' => [
                    'cart_item' => $cartItem,
                    'cart_totals' => $cartData
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Cart add error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to add item to cart'
            ], 500);
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $cartItemId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'quantity' => 'required|integer|min:1|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $userId = Auth::id();
            $sessionId = $request->session()->getId();
            
            // Find cart item
            $cartItem = Cart::where('id', $cartItemId)
                ->where(function ($query) use ($userId, $sessionId) {
                    if ($userId) {
                        $query->where('user_id', $userId);
                    } else {
                        $query->where('session_id', $sessionId)->whereNull('user_id');
                    }
                })
                ->first();

            if (!$cartItem) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found'
                ], 404);
            }

            $quantity = $request->input('quantity');

            // Check availability
            if ($cartItem->item && method_exists($cartItem->item, 'isAvailable') && !$cartItem->item->isAvailable($quantity)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Requested quantity is not available'
                ], 400);
            }

            // Update quantity
            $cartItem->update(['quantity' => $quantity]);

            // Get updated cart totals
            $cartData = Cart::getCartTotal($userId, $sessionId);

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'data' => $cartData
            ]);

        } catch (\Exception $e) {
            Log::error('Cart update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update cart'
            ], 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function destroy(Request $request, $cartItemId): JsonResponse
    {
        try {
            $userId = Auth::id();
            $sessionId = $request->session()->getId();
            
            // Find and delete cart item
            $deleted = Cart::where('id', $cartItemId)
                ->where(function ($query) use ($userId, $sessionId) {
                    if ($userId) {
                        $query->where('user_id', $userId);
                    } else {
                        $query->where('session_id', $sessionId)->whereNull('user_id');
                    }
                })
                ->delete();

            if (!$deleted) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart item not found'
                ], 404);
            }

            // Get updated cart totals
            $cartData = Cart::getCartTotal($userId, $sessionId);

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'data' => $cartData
            ]);

        } catch (\Exception $e) {
            Log::error('Cart delete error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove item from cart'
            ], 500);
        }
    }

    /**
     * Clear entire cart
     */
    public function clear(Request $request): JsonResponse
    {
        try {
            $userId = Auth::id();
            $sessionId = $request->session()->getId();
            
            Cart::clearCart($userId, $sessionId);

            return response()->json([
                'success' => true,
                'message' => 'Cart cleared successfully',
                'data' => [
                    'items' => [],
                    'subtotal' => 0,
                    'discount' => 0,
                    'total' => 0,
                    'count' => 0
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Cart clear error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cart'
            ], 500);
        }
    }

    /**
     * Get cart count (for navigation badge)
     */
    public function count(Request $request): JsonResponse
    {
        $userId = Auth::id();
        $sessionId = $request->session()->getId();
        
        $cartData = Cart::getCartTotal($userId, $sessionId);
        
        return response()->json([
            'success' => true,
            'count' => $cartData['count']
        ]);
    }

    /**
     * Merge guest cart with user cart after login
     */
    public function merge(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'User must be authenticated'
            ], 401);
        }

        try {
            $userId = Auth::id();
            $sessionId = $request->session()->getId();
            
            Cart::mergeGuestCart($sessionId, $userId);

            $cartData = Cart::getCartTotal($userId, $sessionId);

            return response()->json([
                'success' => true,
                'message' => 'Cart merged successfully',
                'data' => $cartData
            ]);

        } catch (\Exception $e) {
            Log::error('Cart merge error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to merge cart'
            ], 500);
        }
    }

    /**
     * Validate cart before checkout
     */
    public function validateCart(Request $request): JsonResponse
    {
        try {
            $userId = Auth::id();
            $sessionId = $request->session()->getId();
            
            $cartData = Cart::getCartTotal($userId, $sessionId);
            $issues = [];

            if (empty($cartData['items'])) {
                $issues[] = 'Cart is empty';
            }

            foreach ($cartData['items'] as $cartItem) {
                if (!$cartItem->isAvailable()) {
                    $issues[] = "Item '{$cartItem->getItemName()}' is no longer available";
                }
            }

            return response()->json([
                'success' => empty($issues),
                'valid' => empty($issues),
                'issues' => $issues,
                'data' => $cartData
            ]);

        } catch (\Exception $e) {
            Log::error('Cart validation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to validate cart'
            ], 500);
        }
    }
}