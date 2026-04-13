<?php

namespace App\Services;

use App\Models\Cart;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShippingService
{
    private string $apiKey;
    private string $baseUrl = 'https://api.biteship.com/v1';
    private float $originLat;
    private float $originLng;

    public function __construct()
    {
        $this->apiKey = env('BITESHIP_API_KEY');
        $this->originLat = (float) env('BITESHIP_ORIGIN_LAT', -7.2575);
        $this->originLng = (float) env('BITESHIP_ORIGIN_LNG', 112.7521);
    }

    /**
     * Get shipping rates from Biteship
     * 
     * @param float $destinationLat
     * @param float $destinationLng
     * @param int $weightGram Total weight in grams
     * @return array
     */
    public function getRates(float $destinationLat, float $destinationLng, int $weightGram): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/rates/couriers", [
                'origin_latitude' => $this->originLat,
                'origin_longitude' => $this->originLng,
                'destination_latitude' => $destinationLat,
                'destination_longitude' => $destinationLng,
                'couriers' => 'jne,jnt,sicepat,anteraja',
                'items' => [
                    [
                        'name' => 'Fashion Items',
                        'description' => 'StyleU Products',
                        'value' => 100000,
                        'weight' => $weightGram,
                        'quantity' => 1,
                    ]
                ],
            ]);

            if ($response->failed()) {
                Log::error('Biteship API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'request' => [
                        'origin' => [$this->originLat, $this->originLng],
                        'destination' => [$destinationLat, $destinationLng],
                        'weight' => $weightGram,
                    ],
                ]);
                throw new \Exception('Gagal mengambil tarif pengiriman. Silakan coba lagi.');
            }

            $data = $response->json();

            if (!isset($data['success']) || !$data['success']) {
                Log::error('Biteship API Response Error', ['data' => $data]);
                throw new \Exception($data['message'] ?? 'Gagal mengambil tarif pengiriman');
            }

            return $this->formatRates($data['pricing'] ?? []);

        } catch (\Exception $e) {
            Log::error('Shipping Service Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get shipping rates for cart
     * 
     * @param Cart $cart
     * @param float $destinationLat
     * @param float $destinationLng
     * @return array
     */
    public function getRatesForCart(Cart $cart, float $destinationLat, float $destinationLng): array
    {
        $items = $cart->items()->with('variant.product')->get();
        
        // Calculate total weight
        $totalWeight = $items->sum(function ($item) {
            return $item->variant->product->weight_gram * $item->quantity;
        });

        if ($totalWeight === 0) {
            throw new \Exception('Total berat produk tidak valid');
        }

        return $this->getRates($destinationLat, $destinationLng, $totalWeight);
    }

    /**
     * Format rates response
     * 
     * @param array $pricing
     * @return array
     */
    private function formatRates(array $pricing): array
    {
        $rates = [];

        foreach ($pricing as $courier) {
            if (!isset($courier['available']) || !$courier['available']) {
                continue;
            }

            $rates[] = [
                'courier_code' => $courier['courier_code'],
                'courier_name' => $courier['courier_name'],
                'courier_service_code' => $courier['courier_service_code'],
                'courier_service_name' => $courier['courier_service_name'],
                'description' => $courier['description'] ?? '',
                'duration' => $courier['duration'] ?? '',
                'price' => $courier['price'],
                'type' => $courier['type'] ?? '',
            ];
        }

        // Sort by price (cheapest first)
        usort($rates, function ($a, $b) {
            return $a['price'] <=> $b['price'];
        });

        return $rates;
    }

    /**
     * Track shipment
     * 
     * @param string $waybillId
     * @return array
     */
    public function trackShipment(string $waybillId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->get("{$this->baseUrl}/trackings/{$waybillId}");

            if ($response->failed()) {
                throw new \Exception('Gagal melacak pengiriman');
            }

            $data = $response->json();

            if (!$data['success']) {
                throw new \Exception($data['message'] ?? 'Gagal melacak pengiriman');
            }

            return $data['data'];

        } catch (\Exception $e) {
            Log::error('Tracking Error: ' . $e->getMessage());
            throw $e;
        }
    }
}
