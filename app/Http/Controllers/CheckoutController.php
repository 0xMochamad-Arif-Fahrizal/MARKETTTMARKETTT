<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Order;
use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private OrderService $orderService,
        private PaymentService $paymentService
    ) {}

    /**
     * Show checkout page
     */
    public function index(Request $request)
    {
        $cart = $this->cartService->getCart($request);
        $cartData = $this->cartService->getTotal($cart);

        // Check if cart is empty
        if ($cartData['item_count'] === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty');
        }

        // Get user addresses
        $addresses = $request->user()
            ->addresses()
            ->orderBy('is_default', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Checkout/Index', [
            'cart' => [
                'items' => $cartData['items']->filter(function ($item) {
                    // Filter out items with missing variant or product
                    return $item->variant && $item->variant->product;
                })->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'quantity' => $item->quantity,
                        'price_snapshot' => $item->price_snapshot,
                        'variant' => [
                            'id' => $item->variant->id,
                            'size' => $item->variant->size,
                            'color' => $item->variant->color,
                            'color_hex' => $item->variant->color_hex,
                            'product' => [
                                'id' => $item->variant->product->id,
                                'name' => $item->variant->product->name,
                                'slug' => $item->variant->product->slug,
                                'weight_gram' => $item->variant->product->weight_gram,
                                'images' => $item->variant->product->images()
                                    ->orderBy('sort_order')
                                    ->limit(1)
                                    ->get(['image_url']),
                            ],
                        ],
                    ];
                })->values(),
                'subtotal' => $cartData['subtotal'],
                'item_count' => $cartData['item_count'],
            ],
            'addresses' => $addresses,
            'midtrans_client_key' => env('MIDTRANS_CLIENT_KEY'),
        ]);
    }

    /**
     * Process checkout and create order
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'address_id' => 'required|exists:addresses,id',
        ]);

        try {
            $address = Address::findOrFail($validated['address_id']);

            // Ensure user owns this address
            if ($address->user_id !== $request->user()->id) {
                abort(403);
            }

            // Check if user has a pending payment order
            $pendingOrder = Order::where('user_id', $request->user()->id)
                ->where('status', 'pending_payment')
                ->with(['items.variant.product', 'user'])
                ->latest()
                ->first();

            // If pending order exists and has items, use it instead of creating new one
            if ($pendingOrder && $pendingOrder->items->count() > 0 && $pendingOrder->total > 0) {
                $snapToken = $this->paymentService->createSnapToken($pendingOrder);

                return response()->json([
                    'success' => true,
                    'snap_token' => $snapToken,
                    'order_number' => $pendingOrder->order_number,
                ]);
            }

            $cart = $this->cartService->getCart($request);
            $cartData = $this->cartService->getTotal($cart);

            // Validate cart is not empty
            if ($cartData['item_count'] === 0) {
                return response()->json([
                    'error' => 'Your cart is empty',
                ], 400);
            }

            // Free shipping data
            $shipping = [
                'courier_code' => 'FREE',
                'courier_name' => 'Free Shipping',
                'courier_service_code' => 'FREE',
                'courier_service_name' => 'Free Shipping',
                'price' => 0,
                'duration' => '3-5 business days',
            ];

            // Create order
            $order = $this->orderService->createOrder(
                $cart,
                $address,
                $shipping
            );

            // Create Snap token
            $snapToken = $this->paymentService->createSnapToken($order);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'order_number' => $order->order_number,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Payment success callback
     */
    public function paymentSuccess(Request $request, string $orderNumber)
    {
        $order = $this->orderService->getOrderByNumber($orderNumber, $request->user()->id);

        return Inertia::render('Checkout/Success', [
            'order' => $order,
        ]);
    }
}
