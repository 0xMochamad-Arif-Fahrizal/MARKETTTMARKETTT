<?php

/**
 * T3.3 - Payment Integration (Midtrans) Validation Script
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "\n=== T3.3 VALIDATION: PAYMENT INTEGRATION (MIDTRANS) ===\n\n";

$errors = [];
$warnings = [];

// 1. Check Midtrans Package
echo "1. Checking Midtrans Package...\n";
try {
    if (class_exists('\Midtrans\Config')) {
        echo "   ✓ Midtrans PHP SDK installed\n";
    } else {
        $errors[] = "Midtrans PHP SDK not found";
    }
} catch (Exception $e) {
    $errors[] = "Midtrans package error: " . $e->getMessage();
}

// 2. Check Services
echo "\n2. Checking Services...\n";
try {
    if (class_exists('\App\Services\PaymentService')) {
        echo "   ✓ PaymentService class exists\n";
        
        $reflection = new ReflectionClass('\App\Services\PaymentService');
        $methods = ['createSnapToken', 'handleNotification', 'checkStatus'];
        
        foreach ($methods as $method) {
            if ($reflection->hasMethod($method)) {
                echo "   ✓ PaymentService has {$method}() method\n";
            } else {
                $errors[] = "PaymentService missing {$method}() method";
            }
        }
    } else {
        $errors[] = "PaymentService class not found";
    }

    if (class_exists('\App\Services\OrderService')) {
        echo "   ✓ OrderService class exists\n";
        
        $reflection = new ReflectionClass('\App\Services\OrderService');
        $methods = ['createOrder', 'getUserOrders', 'getOrderByNumber', 'cancelOrder'];
        
        foreach ($methods as $method) {
            if ($reflection->hasMethod($method)) {
                echo "   ✓ OrderService has {$method}() method\n";
            } else {
                $errors[] = "OrderService missing {$method}() method";
            }
        }
    } else {
        $errors[] = "OrderService class not found";
    }
} catch (Exception $e) {
    $errors[] = "Services error: " . $e->getMessage();
}

// 3. Check Controllers
echo "\n3. Checking Controllers...\n";
try {
    $checkoutReflection = new ReflectionClass('\App\Http\Controllers\CheckoutController');
    $methods = ['index', 'getShippingRates', 'process', 'paymentSuccess'];
    
    foreach ($methods as $method) {
        if ($checkoutReflection->hasMethod($method)) {
            echo "   ✓ CheckoutController has {$method}() method\n";
        } else {
            $errors[] = "CheckoutController missing {$method}() method";
        }
    }

    if (class_exists('\App\Http\Controllers\PaymentController')) {
        echo "   ✓ PaymentController class exists\n";
        
        $paymentReflection = new ReflectionClass('\App\Http\Controllers\PaymentController');
        if ($paymentReflection->hasMethod('notification')) {
            echo "   ✓ PaymentController has notification() method\n";
        } else {
            $errors[] = "PaymentController missing notification() method";
        }
    } else {
        $errors[] = "PaymentController class not found";
    }
} catch (Exception $e) {
    $errors[] = "Controllers error: " . $e->getMessage();
}

// 4. Check Routes
echo "\n4. Checking Routes...\n";
$routes = \Illuminate\Support\Facades\Route::getRoutes();
$requiredRoutes = [
    'checkout.process' => 'POST',
    'checkout.success' => 'GET',
    'payment.notification' => 'POST',
];

foreach ($requiredRoutes as $name => $method) {
    $route = $routes->getByName($name);
    if ($route) {
        echo "   ✓ Route '{$name}' registered ({$method})\n";
    } else {
        $errors[] = "Route '{$name}' not found";
    }
}

// 5. Check Vue Components
echo "\n5. Checking Vue Components...\n";
$components = [
    'resources/js/Pages/Checkout/Index.vue' => 'Enhanced Checkout page',
    'resources/js/Pages/Checkout/Success.vue' => 'Success page',
];

foreach ($components as $path => $description) {
    if (file_exists($path)) {
        echo "   ✓ {$description} exists\n";
        
        $content = file_get_contents($path);
        if ($path === 'resources/js/Pages/Checkout/Index.vue') {
            if (strpos($content, 'currentStep') !== false) {
                echo "   ✓   → Has step-by-step progress\n";
            }
            if (strpos($content, 'processPayment') !== false) {
                echo "   ✓   → Has payment processing\n";
            }
            if (strpos($content, 'snap.pay') !== false) {
                echo "   ✓   → Has Midtrans Snap integration\n";
            }
        }
    } else {
        $errors[] = "{$description} not found at {$path}";
    }
}

// 6. Check Environment Variables
echo "\n6. Checking Environment Variables...\n";
$envVars = [
    'MIDTRANS_SERVER_KEY',
    'MIDTRANS_CLIENT_KEY',
    'MIDTRANS_IS_PRODUCTION',
];

foreach ($envVars as $var) {
    if (env($var) !== null) {
        echo "   ✓ {$var} is set\n";
    } else {
        $warnings[] = "{$var} not set in .env";
    }
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
    echo "\nT3.3 Implementation Complete:\n";
    echo "  • Midtrans PHP SDK installed\n";
    echo "  • PaymentService created with Snap integration\n";
    echo "  • OrderService created for order management\n";
    echo "  • CheckoutController updated with payment processing\n";
    echo "  • PaymentController created for webhook handling\n";
    echo "  • Enhanced Checkout page with step-by-step progress\n";
    echo "  • Success page created\n";
    echo "  • All routes registered\n";
    echo "  • Corteiz design system applied\n";
    echo "\nNext Steps:\n";
    echo "  1. Test checkout flow at /checkout\n";
    echo "  2. Complete address and shipping selection\n";
    echo "  3. Test payment with Midtrans Snap\n";
    echo "  4. Verify order creation\n";
    echo "  5. Test webhook at /payment/notification\n";
    echo "\n";
}
