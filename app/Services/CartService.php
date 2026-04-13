<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartService
{
    /**
     * Get or create cart for current user/session
     */
    public function getCart(Request $request): Cart
    {
        if ($request->user()) {
            // Logged in user - get or create cart
            return Cart::firstOrCreate(
                ['user_id' => $request->user()->id],
                ['session_id' => null]
            );
        } else {
            // Guest user - use session
            $sessionId = $request->session()->getId();
            return Cart::firstOrCreate(
                ['session_id' => $sessionId],
                ['user_id' => null]
            );
        }
    }

    /**
     * Add item to cart
     */
    public function addItem(Cart $cart, int $variantId, int $quantity): void
    {
        $variant = ProductVariant::with('product')->findOrFail($variantId);

        // Check stock
        if ($variant->stock < $quantity) {
            throw new \Exception('Stok tidak mencukupi. Tersedia: ' . $variant->stock);
        }

        // Check if item already exists in cart
        $existingItem = $cart->items()->where('product_variant_id', $variantId)->first();

        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem->quantity + $quantity;
            
            // Check stock for new quantity
            if ($variant->stock < $newQuantity) {
                throw new \Exception('Stok tidak mencukupi. Tersedia: ' . $variant->stock);
            }

            $existingItem->update([
                'quantity' => $newQuantity,
                'price_snapshot' => $variant->price, // Update price snapshot
            ]);
        } else {
            // Create new cart item
            $cart->items()->create([
                'product_variant_id' => $variantId,
                'quantity' => $quantity,
                'price_snapshot' => $variant->price,
            ]);
        }
    }

    /**
     * Update cart item quantity
     */
    public function updateItem(CartItem $item, int $quantity): void
    {
        if ($quantity < 1) {
            throw new \Exception('Quantity minimal adalah 1');
        }

        $variant = $item->variant;

        // Check stock
        if ($variant->stock < $quantity) {
            throw new \Exception('Stok tidak mencukupi. Tersedia: ' . $variant->stock);
        }

        $item->update(['quantity' => $quantity]);
    }

    /**
     * Remove item from cart
     */
    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    /**
     * Merge guest cart to user cart after login
     */
    public function mergeGuestCart(string $sessionId, User $user): void
    {
        $guestCart = Cart::where('session_id', $sessionId)->first();

        if (!$guestCart || $guestCart->items->isEmpty()) {
            return;
        }

        $userCart = $this->getCartForUser($user);

        DB::transaction(function () use ($guestCart, $userCart) {
            foreach ($guestCart->items as $guestItem) {
                $existingItem = $userCart->items()
                    ->where('product_variant_id', $guestItem->product_variant_id)
                    ->first();

                if ($existingItem) {
                    // Merge quantities
                    $newQuantity = $existingItem->quantity + $guestItem->quantity;
                    $variant = $guestItem->variant;

                    // Check stock
                    if ($variant->stock >= $newQuantity) {
                        $existingItem->update([
                            'quantity' => $newQuantity,
                            'price_snapshot' => $variant->price,
                        ]);
                    }
                } else {
                    // Move item to user cart
                    $userCart->items()->create([
                        'product_variant_id' => $guestItem->product_variant_id,
                        'quantity' => $guestItem->quantity,
                        'price_snapshot' => $guestItem->price_snapshot,
                    ]);
                }
            }

            // Delete guest cart
            $guestCart->delete();
        });
    }

    /**
     * Get cart for specific user
     */
    private function getCartForUser(User $user): Cart
    {
        return Cart::firstOrCreate(
            ['user_id' => $user->id],
            ['session_id' => null]
        );
    }

    /**
     * Get cart totals
     */
    public function getTotal(Cart $cart): array
    {
        $items = $cart->items()->with('variant.product')->get();
        
        $subtotal = $items->sum(function ($item) {
            return $item->price_snapshot * $item->quantity;
        });

        $itemCount = $items->sum('quantity');

        return [
            'subtotal' => $subtotal,
            'item_count' => $itemCount,
            'items' => $items,
        ];
    }

    /**
     * Clear cart
     */
    public function clearCart(Cart $cart): void
    {
        $cart->items()->delete();
    }
}
