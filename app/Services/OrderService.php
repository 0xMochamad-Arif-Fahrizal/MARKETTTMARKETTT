<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\Order;
use App\Models\Address;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Create order from cart
     * 
     * @param Cart $cart
     * @param Address $address
     * @param array $shippingData
     * @return Order
     */
    public function createOrder(Cart $cart, Address $address, array $shippingData): Order
    {
        return DB::transaction(function () use ($cart, $address, $shippingData) {
            // Calculate totals
            $subtotal = 0;
            $items = $cart->items()->with('variant.product')->lockForUpdate()->get();

            \Log::info('Creating order', [
                'cart_id' => $cart->id,
                'items_count' => $items->count(),
            ]);

            // Validate we have items
            if ($items->count() === 0) {
                throw new \Exception("Cart is empty");
            }

            // Validate stock and calculate subtotal
            foreach ($items as $item) {
                $variant = $item->variant;
                
                if (!$variant) {
                    throw new \Exception("Product variant not found for cart item {$item->id}");
                }
                
                if (!$variant->product) {
                    throw new \Exception("Product not found for variant {$variant->id}");
                }
                
                if ($variant->stock < $item->quantity) {
                    throw new \Exception("Stok tidak mencukupi untuk {$variant->product->name} - {$variant->size}");
                }
                
                $subtotal += $item->price_snapshot * $item->quantity;
            }

            $shippingCost = $shippingData['price'];
            $total = $subtotal + $shippingCost;

            \Log::info('Order totals calculated', [
                'subtotal' => $subtotal,
                'shipping' => $shippingCost,
                'total' => $total,
            ]);

            // Generate order number
            $orderNumber = $this->generateOrderNumber();

            // Create order
            $order = Order::create([
                'user_id' => $cart->user_id,
                'order_number' => $orderNumber,
                'status' => 'pending_payment',
                'shipping_address_snapshot' => [
                    'recipient_name' => $address->recipient_name,
                    'phone' => $address->phone,
                    'address_line' => $address->address_line,
                    'city' => $address->city,
                    'province' => $address->province,
                    'postal_code' => $address->postal_code,
                    'latitude' => $address->latitude,
                    'longitude' => $address->longitude,
                ],
                'shipping_courier' => $shippingData['courier_name'] . ' - ' . $shippingData['courier_service_name'],
                'shipping_cost' => $shippingCost,
                'subtotal' => $subtotal,
                'total' => $total,
                'midtrans_order_id' => $orderNumber,
            ]);

            \Log::info('Order created', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]);

            // Create order items and reduce stock
            foreach ($items as $item) {
                $variant = $item->variant;
                
                // Create order item
                $orderItem = $order->items()->create([
                    'product_variant_id' => $variant->id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price_snapshot,
                ]);

                \Log::info('Order item created', [
                    'order_item_id' => $orderItem->id,
                    'product_variant_id' => $variant->id,
                    'quantity' => $item->quantity,
                ]);

                // Reduce stock
                $variant->decrement('stock', $item->quantity);
            }

            // Clear cart
            $cart->items()->delete();

            // Reload order with items
            $order->load('items.variant.product', 'user');

            \Log::info('Order completed', [
                'order_id' => $order->id,
                'items_count' => $order->items->count(),
            ]);

            return $order;
        });
    }

    /**
     * Generate unique order number
     * 
     * @return string
     */
    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Get user orders
     * 
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserOrders(int $userId)
    {
        return Order::where('user_id', $userId)
            ->with(['items.variant.product.images'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get order by number
     * 
     * @param string $orderNumber
     * @param int $userId
     * @return Order
     */
    public function getOrderByNumber(string $orderNumber, int $userId): Order
    {
        return Order::where('order_number', $orderNumber)
            ->where('user_id', $userId)
            ->with(['items.variant.product.images', 'user'])
            ->firstOrFail();
    }

    /**
     * Cancel order
     * 
     * @param Order $order
     * @return void
     */
    public function cancelOrder(Order $order): void
    {
        if (!in_array($order->status, ['pending_payment', 'payment_failed'])) {
            throw new \Exception('Order tidak dapat dibatalkan');
        }

        DB::transaction(function () use ($order) {
            // Restore stock
            foreach ($order->items as $item) {
                $item->variant->increment('stock', $item->quantity);
            }

            // Update order status
            $order->update(['status' => 'cancelled']);
        });
    }
}
