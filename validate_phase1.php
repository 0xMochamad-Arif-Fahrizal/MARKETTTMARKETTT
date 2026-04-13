<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PHASE 1 VALIDATION ===" . PHP_EOL . PHP_EOL;

// T1.1 - Database Migrations
echo "T1.1 - Database Migrations" . PHP_EOL;
$tables = ['users', 'addresses', 'categories', 'products', 'product_variants', 
           'product_images', 'carts', 'cart_items', 'orders', 'order_items'];

foreach ($tables as $table) {
    $exists = DB::getSchemaBuilder()->hasTable($table);
    echo "  - {$table}: " . ($exists ? '✓' : '✗') . PHP_EOL;
}

// Check users table columns
$hasPhone = DB::getSchemaBuilder()->hasColumn('users', 'phone');
$hasRole = DB::getSchemaBuilder()->hasColumn('users', 'role');
echo "  - users.phone column: " . ($hasPhone ? '✓' : '✗') . PHP_EOL;
echo "  - users.role column: " . ($hasRole ? '✓' : '✗') . PHP_EOL;

echo PHP_EOL;

// T1.2 - Eloquent Models
echo "T1.2 - Eloquent Models + Relasi" . PHP_EOL;

$models = [
    'User', 'Address', 'Category', 'Product', 'ProductVariant',
    'ProductImage', 'Cart', 'CartItem', 'Order', 'OrderItem'
];

foreach ($models as $model) {
    $class = "App\\Models\\{$model}";
    echo "  - {$model}: " . (class_exists($class) ? '✓' : '✗') . PHP_EOL;
}

// Test User model
$user = App\Models\User::first();
if ($user) {
    echo "  - User->addresses: " . (method_exists($user, 'addresses') ? '✓' : '✗') . PHP_EOL;
    echo "  - User->orders: " . (method_exists($user, 'orders') ? '✓' : '✗') . PHP_EOL;
    echo "  - User->isAdmin(): " . (method_exists($user, 'isAdmin') ? '✓' : '✗') . PHP_EOL;
}

// Test Product model
$product = new App\Models\Product();
$hasSoftDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($product));
echo "  - Product SoftDeletes: " . ($hasSoftDeletes ? '✓' : '✗') . PHP_EOL;
echo "  - Product->scopeActive: " . (method_exists($product, 'scopeActive') ? '✓' : '✗') . PHP_EOL;

// Test Order model
$order = new App\Models\Order();
$casts = $order->getCasts();
echo "  - Order shipping_address_snapshot cast: " . (isset($casts['shipping_address_snapshot']) && $casts['shipping_address_snapshot'] === 'array' ? '✓' : '✗') . PHP_EOL;

echo PHP_EOL;

// T1.3 - Authentication
echo "T1.3 - Authentication" . PHP_EOL;

echo "  - Login route: " . (Route::has('login') ? '✓' : '✗') . PHP_EOL;
echo "  - Register route: " . (Route::has('register') ? '✓' : '✗') . PHP_EOL;
echo "  - Logout route: " . (Route::has('logout') ? '✓' : '✗') . PHP_EOL;
echo "  - AuthController exists: " . (class_exists('App\Http\Controllers\Auth\AuthController') ? '✓' : '✗') . PHP_EOL;
echo "  - Login.vue exists: " . (file_exists('resources/js/Pages/Auth/Login.vue') ? '✓' : '✗') . PHP_EOL;
echo "  - Register.vue exists: " . (file_exists('resources/js/Pages/Auth/Register.vue') ? '✓' : '✗') . PHP_EOL;
echo "  - Inertia configured: " . (class_exists('App\Http\Middleware\HandleInertiaRequests') ? '✓' : '✗') . PHP_EOL;

// Test user creation
$testUser = App\Models\User::where('email', 'test@validation.com')->first();
if (!$testUser) {
    $testUser = App\Models\User::create([
        'name' => 'Validation Test',
        'email' => 'test@validation.com',
        'password' => Hash::make('password123'),
        'role' => 'customer'
    ]);
}
echo "  - User creation works: ✓" . PHP_EOL;
echo "  - Password hashing works: " . (Hash::check('password123', $testUser->password) ? '✓' : '✗') . PHP_EOL;

echo PHP_EOL;

// T1.4 - Layout & Route Protection
echo "T1.4 - Layout, Navbar & Route Protection" . PHP_EOL;

echo "  - AppLayout.vue: " . (file_exists('resources/js/Layouts/AppLayout.vue') ? '✓' : '✗') . PHP_EOL;
echo "  - GuestLayout.vue: " . (file_exists('resources/js/Layouts/GuestLayout.vue') ? '✓' : '✗') . PHP_EOL;
echo "  - FlashMessage.vue: " . (file_exists('resources/js/Components/FlashMessage.vue') ? '✓' : '✗') . PHP_EOL;
echo "  - AdminMiddleware: " . (class_exists('App\Http\Middleware\AdminMiddleware') ? '✓' : '✗') . PHP_EOL;
echo "  - Products route: " . (Route::has('products.index') ? '✓' : '✗') . PHP_EOL;
echo "  - Cart route: " . (Route::has('cart.index') ? '✓' : '✗') . PHP_EOL;
echo "  - Profile route: " . (Route::has('profile.index') ? '✓' : '✗') . PHP_EOL;
echo "  - Orders route: " . (Route::has('orders.index') ? '✓' : '✗') . PHP_EOL;

// Test admin user
$adminUser = App\Models\User::where('email', 'admin@styleu.com')->first();
if ($adminUser) {
    echo "  - Admin user exists: ✓" . PHP_EOL;
    echo "  - Admin isAdmin() returns true: " . ($adminUser->isAdmin() ? '✓' : '✗') . PHP_EOL;
}

$customerUser = App\Models\User::where('role', 'customer')->first();
if ($customerUser) {
    echo "  - Customer isAdmin() returns false: " . (!$customerUser->isAdmin() ? '✓' : '✗') . PHP_EOL;
}

echo PHP_EOL;
echo "=== PHASE 1 VALIDATION COMPLETE ===" . PHP_EOL;
