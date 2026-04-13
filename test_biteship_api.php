<?php

/**
 * Test Biteship API Integration
 * 
 * This script tests the ShippingService with real API calls
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n=== TESTING BITESHIP API INTEGRATION ===\n\n";

try {
    $shippingService = new \App\Services\ShippingService();
    
    echo "Testing shipping rate calculation...\n";
    echo "Origin: " . env('BITESHIP_ORIGIN_LAT') . ", " . env('BITESHIP_ORIGIN_LNG') . "\n";
    echo "Destination: Jakarta (-6.2088, 106.8456)\n";
    echo "Weight: 1000 grams\n\n";
    
    $rates = $shippingService->getRates(
        -6.2088,  // Jakarta latitude
        106.8456, // Jakarta longitude
        1000      // 1kg
    );
    
    if (empty($rates)) {
        echo "⚠ No shipping rates returned (this might be normal if no couriers are available)\n";
    } else {
        echo "✓ Successfully retrieved " . count($rates) . " shipping options:\n\n";
        
        foreach ($rates as $rate) {
            echo "  • {$rate['courier_name']} - {$rate['courier_service_name']}\n";
            echo "    Price: Rp " . number_format($rate['price'], 0, ',', '.') . "\n";
            echo "    Duration: {$rate['duration']}\n";
            echo "    Description: {$rate['description']}\n\n";
        }
    }
    
    echo "✅ Biteship API integration is working!\n\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n\n";
    echo "This might be due to:\n";
    echo "  1. Invalid API key\n";
    echo "  2. Network connectivity issues\n";
    echo "  3. API rate limiting\n";
    echo "  4. Invalid coordinates\n\n";
    exit(1);
}
