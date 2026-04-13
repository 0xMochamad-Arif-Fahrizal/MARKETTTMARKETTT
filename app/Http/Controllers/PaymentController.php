<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    /**
     * Handle Midtrans payment notification webhook
     */
    public function notification(Request $request)
    {
        try {
            $notificationData = $request->all();
            
            Log::info('Midtrans Notification Received', $notificationData);

            $order = $this->paymentService->handleNotification($notificationData);

            return response()->json([
                'success' => true,
                'message' => 'Notification processed',
                'order_status' => $order->status,
            ]);

        } catch (\Exception $e) {
            Log::error('Payment Notification Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
