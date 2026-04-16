<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class PaymentService
{
    public function __construct()
    {
        // Set Midtrans configuration
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$clientKey = env('MIDTRANS_CLIENT_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    /**
     * Create Snap payment token for order
     * 
     * @param Order $order
     * @return string Snap token
     */
    public function createSnapToken(Order $order): string
    {
        try {
            // Ensure order has items loaded
            if (!$order->relationLoaded('items')) {
                $order->load('items.variant.product');
            }

            // Ensure user is loaded
            if (!$order->relationLoaded('user')) {
                $order->load('user');
            }

            $itemDetails = $this->getItemDetails($order);

            // Validate that we have items
            if (empty($itemDetails)) {
                throw new \Exception('Order has no items');
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $order->order_number,
                    'gross_amount' => (int) $order->total,
                ],
                'customer_details' => [
                    'first_name' => $order->user->name,
                    'email' => $order->user->email,
                    'phone' => $order->shipping_address_snapshot['phone'] ?? '',
                ],
                'item_details' => $itemDetails,
                'enabled_payments' => [
                    'credit_card',
                    'bca_va',
                    'bni_va',
                    'bri_va',
                    'permata_va',
                    'other_va',
                    'gopay',
                    'shopeepay',
                    'qris',
                ],
            ];

            Log::info('Creating Midtrans Snap Token', [
                'order_number' => $order->order_number,
                'gross_amount' => $order->total,
                'items_count' => count($itemDetails),
            ]);

            $snapToken = Snap::getSnapToken($params);
            
            return $snapToken;

        } catch (\Exception $e) {
            Log::error('Midtrans Snap Token Error', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total' => $order->total,
                'items_loaded' => $order->relationLoaded('items'),
                'items_count' => $order->items->count(),
                'error' => $e->getMessage(),
            ]);
            throw new \Exception('Gagal membuat token pembayaran: ' . $e->getMessage());
        }
    }

    /**
     * Get item details for Midtrans
     * 
     * @param Order $order
     * @return array
     */
    private function getItemDetails(Order $order): array
    {
        $items = [];

        // Add order items
        foreach ($order->items as $item) {
            // Skip if variant or product is not loaded
            if (!$item->variant || !$item->variant->product) {
                Log::warning('Order item missing variant or product', [
                    'order_id' => $order->id,
                    'item_id' => $item->id,
                ]);
                continue;
            }

            $items[] = [
                'id' => $item->product_variant_id,
                'price' => (int) $item->unit_price,
                'quantity' => $item->quantity,
                'name' => $item->variant->product->name . ' - ' . $item->variant->size,
            ];
        }

        // Add shipping cost
        if ($order->shipping_cost > 0) {
            $items[] = [
                'id' => 'SHIPPING',
                'price' => (int) $order->shipping_cost,
                'quantity' => 1,
                'name' => 'Ongkos Kirim (' . $order->shipping_courier . ')',
            ];
        }

        return $items;
    }

    /**
     * Handle payment notification from Midtrans webhook
     * 
     * @param array $notificationData
     * @return Order
     */
    public function handleNotification(array $notificationData): Order
    {
        try {
            $notification = new Notification();

            $orderNumber = $notification->order_id;
            $transactionStatus = $notification->transaction_status;
            $fraudStatus = $notification->fraud_status ?? null;
            $paymentType = $notification->payment_type;

            // Verify signature
            $signatureKey = hash('sha512', 
                $orderNumber . 
                $notification->status_code . 
                $notification->gross_amount . 
                Config::$serverKey
            );

            if ($signatureKey !== $notification->signature_key) {
                throw new \Exception('Invalid signature');
            }

            // Find order
            $order = Order::where('order_number', $orderNumber)->firstOrFail();

            // Update order status based on transaction status
            $this->updateOrderStatus($order, $transactionStatus, $fraudStatus, $paymentType);

            return $order;

        } catch (\Exception $e) {
            Log::error('Midtrans Notification Error', [
                'error' => $e->getMessage(),
                'data' => $notificationData,
            ]);
            throw $e;
        }
    }

    /**
     * Update order status based on Midtrans transaction status
     * 
     * @param Order $order
     * @param string $transactionStatus
     * @param string|null $fraudStatus
     * @param string $paymentType
     * @return void
     */
    private function updateOrderStatus(Order $order, string $transactionStatus, ?string $fraudStatus, string $paymentType): void
    {
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'accept') {
                $order->update([
                    'status' => 'paid',
                    'payment_method' => $paymentType,
                ]);
            }
        } elseif ($transactionStatus == 'settlement') {
            $order->update([
                'status' => 'paid',
                'payment_method' => $paymentType,
            ]);
        } elseif ($transactionStatus == 'pending') {
            $order->update([
                'status' => 'pending_payment',
                'payment_method' => $paymentType,
            ]);
        } elseif ($transactionStatus == 'deny') {
            $order->update([
                'status' => 'payment_failed',
                'payment_method' => $paymentType,
            ]);
        } elseif ($transactionStatus == 'expire') {
            $order->update([
                'status' => 'cancelled',
                'payment_method' => $paymentType,
            ]);
        } elseif ($transactionStatus == 'cancel') {
            $order->update([
                'status' => 'cancelled',
                'payment_method' => $paymentType,
            ]);
        }

        Log::info('Order Status Updated', [
            'order_number' => $order->order_number,
            'status' => $order->status,
            'transaction_status' => $transactionStatus,
        ]);
    }

    /**
     * Check transaction status from Midtrans
     * 
     * @param string $orderNumber
     * @return array
     */
    public function checkStatus(string $orderNumber): array
    {
        try {
            $status = \Midtrans\Transaction::status($orderNumber);
            return (array) $status;
        } catch (\Exception $e) {
            Log::error('Midtrans Status Check Error', [
                'order_number' => $orderNumber,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
