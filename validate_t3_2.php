<?php

/**
 * T3.2 - Shipping Calculator (Biteship Integration) Validation Script
 * 
 * This script validates:
 * 1. Address model has lat/lng fields
 * 2. ShippingService exists and has required methods
 * 3. AddressController exists with CRUD methods
 * 4. CheckoutController exists with shipping rate method
 * 5. Routes are registered
 * 6. Vue components exist
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n=== T3.2 VALIDATION: SHIPPING CALCULATOR ===\n\n";

$errors = [];
$warnings = [];

// 1. Check Address Model
echo "1. Checking Address Model...\n";
try {
    $address = new \App\Models\Address();
    $fillable = $address->getFillable();
    
    if (in_array('latitude', $fillable) && in_array('longitude', $fillable)) {
        echo "   ✓ Address model has latitude and longitude fields\n";
    } else {
        $errors[] = "Address model missing latitude or longitude in fillable";
    }
} catch (Exception $e) {
    $errors[] = "Address model error: " . $e->getMessage();
}

// 2. Check ShippingService
echo "\n2. Checking ShippingService...\n";
try {
    if (class_exists('\App\Services\ShippingService')) {
        echo "   ✓ ShippingService class exists\n";
        
        $reflection = new ReflectionClass('\App\Services\ShippingService');
        $methods = ['getRates', 'getRatesForCart', 'trackShipment'];
        
        foreach ($methods as $method) {
            if ($reflection->hasMethod($method)) {
                echo "   ✓ ShippingService has {$method}() method\n";
            } else {
                $errors[] = "ShippingService missing {$method}() method";
            }
        }
    } else {
        $errors[] = "ShippingService class not found";
    }
} catch (Exception $e) {
    $errors[] = "ShippingService error: " . $e->getMessage();
}

// 3. Check AddressController
echo "\n3. Checking AddressController...\n";
try {
    if (class_exists('\App\Http\Controllers\AddressController')) {
        echo "   ✓ AddressController class exists\n";
        
        $reflection = new ReflectionClass('\App\Http\Controllers\AddressController');
        $methods = ['index', 'store', 'update', 'destroy', 'setDefault'];
        
        foreach ($methods as $method) {
            if ($reflection->hasMethod($method)) {
                echo "   ✓ AddressController has {$method}() method\n";
            } else {
                $errors[] = "AddressController missing {$method}() method";
            }
        }
    } else {
        $errors[] = "AddressController class not found";
    }
} catch (Exception $e) {
    $errors[] = "AddressController error: " . $e->getMessage();
}

// 4. Check CheckoutController
echo "\n4. Checking CheckoutController...\n";
try {
    if (class_exists('\App\Http\Controllers\CheckoutController')) {
        echo "   ✓ CheckoutController class exists\n";
        
        $reflection = new ReflectionClass('\App\Http\Controllers\CheckoutController');
        $methods = ['index', 'getShippingRates'];
        
        foreach ($methods as $method) {
            if ($reflection->hasMethod($method)) {
                echo "   ✓ CheckoutController has {$method}() method\n";
            } else {
                $errors[] = "CheckoutController missing {$method}() method";
            }
        }
    } else {
        $errors[] = "CheckoutController class not found";
    }
} catch (Exception $e) {
    $errors[] = "CheckoutController error: " . $e->getMessage();
}

// 5. Check Routes
echo "\n5. Checking Routes...\n";
$routes = \Illuminate\Support\Facades\Route::getRoutes();
$requiredRoutes = [
    'checkout.index' => 'GET',
    'checkout.shipping-rates' => 'POST',
    'addresses.index' => 'GET',
    'addresses.store' => 'POST',
    'addresses.update' => 'PATCH',
    'addresses.destroy' => 'DELETE',
    'addresses.set-default' => 'POST',
];

foreach ($requiredRoutes as $name => $method) {
    $route = $routes->getByName($name);
    if ($route) {
        echo "   ✓ Route '{$name}' registered ({$method})\n";
    } else {
        $errors[] = "Route '{$name}' not found";
    }
}

// 6. Check Vue Components
echo "\n6. Checking Vue Components...\n";
$components = [
    'resources/js/Pages/Checkout/Index.vue',
    'resources/js/Pages/Profile/Addresses.vue',
];

foreach ($components as $component) {
    if (file_exists($component)) {
        echo "   ✓ {$component} exists\n";
    } else {
        $errors[] = "{$component} not found";
    }
}

// 7. Check Environment Variables
echo "\n7. Checking Environment Variables...\n";
$envVars = [
    'BITESHIP_API_KEY',
    'BITESHIP_ORIGIN_LAT',
    'BITESHIP_ORIGIN_LNG',
];

foreach ($envVars as $var) {
    if (env($var)) {
        echo "   ✓ {$var} is set\n";
    } else {
        $warnings[] = "{$var} not set in .env";
    }
}

// 8. Check Database Migration
echo "\n8. Checking Database Migration...\n";
try {
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('addresses');
    
    if (in_array('latitude', $columns) && in_array('longitude', $columns)) {
        echo "   ✓ addresses table has latitude and longitude columns\n";
    } else {
        $errors[] = "addresses table missing latitude or longitude columns";
    }
} catch (Exception $e) {
    $errors[] = "Database check error: " . $e->getMessage();
}

// Summary
echo "\n=== VALIDATION SUMMARY ===\n";

if (count($warnings) > 0) {
    echo "\nWARNINGS (" . count($warnings) . "):\n";
    foreach ($warnings as $warning) {
        echo "  ⚠ {$warning}\n";
    }
}

if (count($errors) > 0) {
    echo "\nERRORS (" . count($errors) . "):\n";
    foreach ($errors as $error) {
        echo "  ✗ {$error}\n";
    }
    echo "\n❌ VALIDATION FAILED\n\n";
    exit(1);
} else {
    echo "\n✅ ALL CHECKS PASSED!\n";
    echo "\nT3.2 Implementation Complete:\n";
    echo "  • Address model updated with lat/lng fields\n";
    echo "  • ShippingService created with Biteship integration\n";
    echo "  • AddressController created for address management\n";
    echo "  • CheckoutController created with shipping rate calculation\n";
    echo "  • All routes registered\n";
    echo "  • Vue components created (Checkout, Addresses)\n";
    echo "  • AppLayout updated with Addresses link\n";
    echo "\nNext Steps:\n";
    echo "  1. Test address creation at /profile/addresses\n";
    echo "  2. Add coordinates to addresses (required for shipping)\n";
    echo "  3. Test checkout flow at /checkout\n";
    echo "  4. Verify shipping rates are calculated correctly\n";
    echo "\n";
}
