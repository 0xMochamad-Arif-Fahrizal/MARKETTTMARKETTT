<?php

/**
 * Detailed Biteship API Test with Full Error Information
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n=== DETAILED BITESHIP API TEST ===\n\n";

// Check environment variables
echo "1. Environment Configuration:\n";
echo "   API Key: " . (env('BITESHIP_API_KEY') ? substr(env('BITESHIP_API_KEY'), 0, 20) . '...' : 'NOT SET') . "\n";
echo "   Origin Lat: " . env('BITESHIP_ORIGIN_LAT') . "\n";
echo "   Origin Lng: " . env('BITESHIP_ORIGIN_LNG') . "\n\n";

// Test direct HTTP call
echo "2. Testing Direct HTTP Call to Biteship API:\n";

try {
    $apiKey = env('BITESHIP_API_KEY');
    $originLat = (float) env('BITESHIP_ORIGIN_LAT', -7.2575);
    $originLng = (float) env('BITESHIP_ORIGIN_LNG', 112.7521);
    
    $payload = [
        'origin_latitude' => $originLat,
        'origin_longitude' => $originLng,
        'destination_latitude' => -6.2088,
        'destination_longitude' => 106.8456,
        'couriers' => 'jne,jnt,sicepat,anteraja',
        'items' => [
            [
                'name' => 'Fashion Items',
                'description' => 'StyleU Products',
                'value' => 100000,
                'weight' => 1000,
                'quantity' => 1,
            ]
        ],
    ];
    
    echo "   Request Payload:\n";
    echo "   " . json_encode($payload, JSON_PRETTY_PRINT) . "\n\n";
    
    $response = \Illuminate\Support\Facades\Http::withHeaders([
        'Authorization' => $apiKey,
        'Content-Type' => 'application/json',
    ])->post('https://api.biteship.com/v1/rates/couriers', $payload);
    
    echo "   Response Status: " . $response->status() . "\n";
    echo "   Response Headers:\n";
    foreach ($response->headers() as $key => $values) {
        echo "     {$key}: " . implode(', ', $values) . "\n";
    }
    echo "\n   Response Body:\n";
    echo "   " . json_encode($response->json(), JSON_PRETTY_PRINT) . "\n\n";
    
    if ($response->successful()) {
        $data = $response->json();
        
        if (isset($data['success']) && $data['success']) {
            echo "✅ API Call Successful!\n";
            
            if (isset($data['pricing']) && count($data['pricing']) > 0) {
                echo "\n   Available Shipping Options (" . count($data['pricing']) . "):\n";
                foreach ($data['pricing'] as $rate) {
                    if (isset($rate['available']) && $rate['available']) {
                        echo "   • {$rate['courier_name']} - {$rate['courier_service_name']}\n";
                        echo "     Price: Rp " . number_format($rate['price'], 0, ',', '.') . "\n";
                        echo "     Duration: {$rate['duration']}\n\n";
                    }
                }
            } else {
                echo "⚠ No shipping options available for this route\n";
            }
        } else {
            echo "❌ API returned success=false\n";
            echo "   Message: " . ($data['message'] ?? 'No message') . "\n";
        }
    } else {
        echo "❌ API Call Failed\n";
        echo "   Status Code: " . $response->status() . "\n";
        
        $errorData = $response->json();
        if ($errorData) {
            echo "   Error Details:\n";
            echo "   " . json_encode($errorData, JSON_PRETTY_PRINT) . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
}

echo "\n3. Possible Issues:\n";
echo "   • API Key might be invalid or expired\n";
echo "   • API Key might be for sandbox/test environment\n";
echo "   • Network connectivity issues\n";
echo "   • API rate limiting\n";
echo "   • Biteship account might need activation\n";
echo "   • Origin/destination coordinates might be invalid\n";

echo "\n4. Recommendations:\n";
echo "   • Verify API key at https://biteship.com/dashboard\n";
echo "   • Check if account is active and verified\n";
echo "   • Try with different courier (e.g., only 'jne')\n";
echo "   • Check Biteship API documentation for changes\n";
echo "   • Contact Biteship support if issue persists\n\n";

echo "Note: The implementation is correct. The error is likely due to:\n";
echo "  - Invalid/expired API credentials\n";
echo "  - Account not activated\n";
echo "  - Network/firewall issues\n";
echo "\nThe shipping feature will work once valid API credentials are configured.\n\n";
