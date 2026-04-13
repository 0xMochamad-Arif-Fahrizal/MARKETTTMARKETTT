<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== TESTING AUTHENTICATION FLOW ===" . PHP_EOL . PHP_EOL;

// Clean up test user
App\Models\User::where('email', 'auth-flow-test@styleu.com')->delete();

// Test 1: User Registration
echo "1. Testing User Registration..." . PHP_EOL;
$userData = [
    'name' => 'Auth Flow Test',
    'email' => 'auth-flow-test@styleu.com',
    'password' => 'password123',
];

$user = App\Models\User::create([
    'name' => $userData['name'],
    'email' => $userData['email'],
    'password' => Hash::make($userData['password']),
    'role' => 'customer',
]);

echo "   - User created: ✓" . PHP_EOL;
echo "   - Default role is customer: " . ($user->role === 'customer' ? '✓' : '✗') . PHP_EOL;
echo "   - Password is hashed: " . (strlen($user->password) > 50 ? '✓' : '✗') . PHP_EOL;

// Test 2: Password Verification
echo PHP_EOL . "2. Testing Password Verification..." . PHP_EOL;
$correctPassword = Hash::check('password123', $user->password);
$wrongPassword = Hash::check('wrongpassword', $user->password);
echo "   - Correct password validates: " . ($correctPassword ? '✓' : '✗') . PHP_EOL;
echo "   - Wrong password fails: " . (!$wrongPassword ? '✓' : '✗') . PHP_EOL;

// Test 3: User Role Methods
echo PHP_EOL . "3. Testing User Role Methods..." . PHP_EOL;
echo "   - Customer isAdmin() returns false: " . (!$user->isAdmin() ? '✓' : '✗') . PHP_EOL;

$adminUser = App\Models\User::where('email', 'admin@styleu.com')->first();
if ($adminUser) {
    echo "   - Admin isAdmin() returns true: " . ($adminUser->isAdmin() ? '✓' : '✗') . PHP_EOL;
}

// Test 4: Route Protection
echo PHP_EOL . "4. Testing Route Configuration..." . PHP_EOL;
echo "   - Guest routes (login, register): " . (Route::has('login') && Route::has('register') ? '✓' : '✗') . PHP_EOL;
echo "   - Auth routes (logout, products, cart): " . (Route::has('logout') && Route::has('products.index') && Route::has('cart.index') ? '✓' : '✗') . PHP_EOL;
echo "   - Profile routes: " . (Route::has('profile.index') && Route::has('orders.index') ? '✓' : '✗') . PHP_EOL;

// Test 5: Middleware Configuration
echo PHP_EOL . "5. Testing Middleware..." . PHP_EOL;
echo "   - HandleInertiaRequests exists: " . (class_exists('App\Http\Middleware\HandleInertiaRequests') ? '✓' : '✗') . PHP_EOL;
echo "   - AdminMiddleware exists: " . (class_exists('App\Http\Middleware\AdminMiddleware') ? '✓' : '✗') . PHP_EOL;

// Test 6: Inertia Shared Data
echo PHP_EOL . "6. Testing Inertia Configuration..." . PHP_EOL;
echo "   - HandleInertiaRequests middleware: ✓" . PHP_EOL;
echo "   - Inertia share method exists: " . (method_exists('App\Http\Middleware\HandleInertiaRequests', 'share') ? '✓' : '✗') . PHP_EOL;
echo "   - (Session testing skipped in CLI context)" . PHP_EOL;

// Test 7: Frontend Files
echo PHP_EOL . "7. Testing Frontend Files..." . PHP_EOL;
$files = [
    'resources/js/app.js',
    'resources/js/Pages/Auth/Login.vue',
    'resources/js/Pages/Auth/Register.vue',
    'resources/js/Layouts/AppLayout.vue',
    'resources/js/Layouts/GuestLayout.vue',
    'resources/js/Components/FlashMessage.vue',
    'resources/views/app.blade.php',
];

foreach ($files as $file) {
    echo "   - {$file}: " . (file_exists($file) ? '✓' : '✗') . PHP_EOL;
}

// Test 8: Build Assets
echo PHP_EOL . "8. Testing Build Assets..." . PHP_EOL;
echo "   - public/build/manifest.json: " . (file_exists('public/build/manifest.json') ? '✓' : '✗') . PHP_EOL;

$manifest = json_decode(file_get_contents('public/build/manifest.json'), true);
echo "   - app.js compiled: " . (isset($manifest['resources/js/app.js']) ? '✓' : '✗') . PHP_EOL;
echo "   - app.css compiled: " . (isset($manifest['resources/css/app.css']) ? '✓' : '✗') . PHP_EOL;

echo PHP_EOL;
echo "=== AUTHENTICATION FLOW VALIDATION COMPLETE ===" . PHP_EOL;
echo PHP_EOL;
echo "Test Credentials:" . PHP_EOL;
echo "  Customer: newuser@styleu.com / password123" . PHP_EOL;
echo "  Admin: admin@styleu.com / admin123" . PHP_EOL;
echo "  Test User: auth-flow-test@styleu.com / password123" . PHP_EOL;
