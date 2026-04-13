<?php

namespace App\Http\Controllers;

use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService,
        private PaymentService $paymentService
    ) {}

    /**
     * Display user's orders
     */
    public function index(Request $request)
    {
        $orders = $this->orderService->getUserOrders($request->user()->id);

        return Inertia::render('Orders/Index', [
            'orders' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'subtotal' => $order->subtotal,
                    'shipping_cost' => $order->shipping_cost,
                    'total' => $order->total,
                    'payment_method' => $order->payment_method,
                    'created_at' => $order->created_at,
                    'items' => $order->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'variant' => [
                                'id' => $item->variant->id,
                                'size' => $item->variant->size,
                                'color' => $item->variant->color,
                                'product' => [
                                    'id' => $item->variant->product->id,
                                    'name' => $item->variant->product->name,
                                    'slug' => $item->variant->product->slug,
                                    'images' => $item->variant->product->images()
                                        ->orderBy('sort_order')
                                        ->limit(1)
                                        ->get(['image_url']),
                                ],
                            ],
                        ];
                    }),
                ];
            }),
            'midtrans_client_key' => env('MIDTRANS_CLIENT_KEY'),
        ]);
    }

    /**
     * Display specific order details
     */
    public function show(Request $request, string $orderNumber)
    {
        $order = $this->orderService->getOrderByNumber($orderNumber, $request->user()->id);

        return Inertia::render('Orders/Show', [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'status' => $order->status,
                'subtotal' => $order->subtotal,
                'shipping_cost' => $order->shipping_cost,
                'total' => $order->total,
                'payment_method' => $order->payment_method,
                'shipping_courier' => $order->shipping_courier,
                'tracking_number' => $order->tracking_number,
                'shipping_address_snapshot' => $order->shipping_address_snapshot,
                'created_at' => $order->created_at,
                'items' => $order->items->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'quantity' => $item->quantity,
                        'unit_price' => $item->unit_price,
                        'variant' => [
                            'id' => $item->variant->id,
                            'size' => $item->variant->size,
                            'color' => $item->variant->color,
                            'product' => [
                                'id' => $item->variant->product->id,
                                'name' => $item->variant->product->name,
                                'slug' => $item->variant->product->slug,
                                'images' => $item->variant->product->images()
                                    ->orderBy('sort_order')
                                    ->limit(1)
                                    ->get(['image_url']),
                            ],
                        ],
                    ];
                }),
            ],
            'midtrans_client_key' => env('MIDTRANS_CLIENT_KEY'),
        ]);
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, string $orderNumber)
    {
        try {
            $order = $this->orderService->getOrderByNumber($orderNumber, $request->user()->id);
            $this->orderService->cancelOrder($order);

            return redirect()->back()->with('success', 'Pesanan berhasil dibatalkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Retry payment for pending order
     */
    public function retryPayment(Request $request, string $orderNumber)
    {
        try {
            $order = $this->orderService->getOrderByNumber($orderNumber, $request->user()->id);

            if ($order->status !== 'pending_payment') {
                return response()->json([
                    'error' => 'Order tidak dapat dibayar',
                ], 400);
            }

            // Create new Snap token
            $snapToken = $this->paymentService->createSnapToken($order);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
