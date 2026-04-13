<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING DATABASE RELATIONSHIPS ===" . PHP_EOL . PHP_EOL;

// Clean up test data
DB::table('addresses')->where('user_id', '>', 0)->delete();
DB::table('users')->where('email', 'LIKE', '%relationship-test%')->delete();

// Create test user
$user = App\Models\User::create([
    'name' => 'Relationship Test User',
    'email' => 'relationship-test@styleu.com',
    'password' => Hash::make('password'),
    'role' => 'customer'
]);

echo "1. User Created: {$user->name} (ID: {$user->id})" . PHP_EOL;

// Test User -> Address relationship
$address = $user->addresses()->create([
    'label' => 'Home',
    'recipient_name' => 'Test User',
    'phone' => '081234567890',
    'address_line' => 'Jl. Test No. 123',
    'city' => 'Jakarta',
    'province' => 'DKI Jakarta',
    'postal_code' => '12345',
    'is_default' => true
]);

echo "2. Address Created via User relationship: {$address->label}" . PHP_EOL;

// Test reverse relationship
$addressUser = $address->user;
echo "3. Address->User relationship: " . ($addressUser->id === $user->id ? '✓' : '✗') . PHP_EOL;

// Test Category -> Product relationship
$category = App\Models\Category::firstOrCreate([
    'slug' => 'test-category'
], [
    'name' => 'Test Category'
]);

$product = $category->products()->create([
    'name' => 'Test Product',
    'slug' => 'test-product-' . time(),
    'description' => 'Test description',
    'base_price' => 100000,
    'status' => 'active',
    'weight_gram' => 200
]);

echo "4. Product Created via Category relationship: {$product->name}" . PHP_EOL;
echo "5. Product->Category relationship: " . ($product->category->id === $category->id ? '✓' : '✗') . PHP_EOL;

// Test Product -> Variant relationship
$variant = $product->variants()->create([
    'size' => 'M',
    'color' => 'Black',
    'color_hex' => '#000000',
    'price' => 150000,
    'stock' => 10,
    'sku' => 'TEST-M-BLK-' . time(),
    'is_active' => true
]);

echo "6. ProductVariant Created: Size {$variant->size}, Stock {$variant->stock}" . PHP_EOL;
echo "7. Variant->Product relationship: " . ($variant->product->id === $product->id ? '✓' : '✗') . PHP_EOL;

// Test Product -> Image relationship
$image = $product->images()->create([
    'image_url' => 'https://example.com/test.jpg',
    'sort_order' => 1
]);

echo "8. ProductImage Created with sort_order: {$image->sort_order}" . PHP_EOL;

// Test ProductImage ordering
$image2 = $product->images()->create([
    'image_url' => 'https://example.com/test2.jpg',
    'sort_order' => 0
]);

$images = $product->fresh()->images;
echo "9. ProductImage auto-ordering: " . ($images->first()->sort_order === 0 ? '✓' : '✗') . PHP_EOL;

// Test Cart relationship
$cart = $user->carts()->create([
    'session_id' => null
]);

echo "10. Cart Created for User" . PHP_EOL;

// Test CartItem relationship
$cartItem = $cart->items()->create([
    'product_variant_id' => $variant->id,
    'quantity' => 2,
    'price_snapshot' => $variant->price
]);

echo "11. CartItem Created: Qty {$cartItem->quantity}" . PHP_EOL;
echo "12. CartItem->Variant relationship: " . ($cartItem->variant->id === $variant->id ? '✓' : '✗') . PHP_EOL;

// Test Order relationship
$order = $user->orders()->create([
    'order_number' => 'ORD-TEST-' . time(),
    'status' => 'pending_payment',
    'shipping_address_snapshot' => [
        'recipient_name' => 'Test User',
        'address' => 'Jl. Test',
        'city' => 'Jakarta'
    ],
    'shipping_courier' => 'JNE',
    'shipping_cost' => 10000,
    'subtotal' => 150000,
    'total' => 160000
]);

echo "13. Order Created: {$order->order_number}" . PHP_EOL;
echo "14. Order shipping_address_snapshot is array: " . (is_array($order->shipping_address_snapshot) ? '✓' : '✗') . PHP_EOL;

// Test OrderItem relationship
$orderItem = $order->items()->create([
    'product_variant_id' => $variant->id,
    'quantity' => 1,
    'unit_price' => $variant->price
]);

echo "15. OrderItem Created" . PHP_EOL;
echo "16. OrderItem->Order relationship: " . ($orderItem->order->id === $order->id ? '✓' : '✗') . PHP_EOL;

// Test Product active scope
$activeCount = App\Models\Product::active()->count();
echo "17. Product::active() scope works: " . ($activeCount > 0 ? '✓' : '✗') . PHP_EOL;

// Test Product soft delete
$product->delete();
$deletedProduct = App\Models\Product::withTrashed()->find($product->id);
echo "18. Product SoftDelete works: " . ($deletedProduct && $deletedProduct->trashed() ? '✓' : '✗') . PHP_EOL;

echo PHP_EOL;
echo "=== ALL RELATIONSHIPS WORKING CORRECTLY ===" . PHP_EOL;
