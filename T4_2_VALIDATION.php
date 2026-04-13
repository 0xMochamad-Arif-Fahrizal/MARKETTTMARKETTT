<?php

/**
 * T4.2 VALIDATION SCRIPT - Admin Order Management (Filament)
 * 
 * This script validates the implementation of T4.2:
 * - OrderResource exists with proper configuration
 * - ItemsRelationManager is registered
 * - All text is in English
 * - Proper form sections and fields
 * - Table columns with badges and formatting
 * - Filters (status, date range)
 * - Bulk actions (Mark as Processing, Mark as Shipped)
 * - Navigation badge shows pending payment count
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== T4.2 VALIDATION: Admin Order Management (Filament) ===\n\n";

$checks = [
    'passed' => 0,
    'failed' => 0,
    'total' => 0
];

function check($description, $condition, &$checks) {
    $checks['total']++;
    if ($condition) {
        $checks['passed']++;
        echo "✓ {$description}\n";
        return true;
    } else {
        $checks['failed']++;
        echo "✗ {$description}\n";
        return false;
    }
}

// 1. Check OrderResource exists
echo "1. Checking OrderResource file...\n";
$orderResourcePath = __DIR__ . '/app/Filament/Resources/OrderResource.php';
check('OrderResource.php exists', file_exists($orderResourcePath), $checks);

if (file_exists($orderResourcePath)) {
    $content = file_get_contents($orderResourcePath);
    
    // Check model binding
    check('OrderResource uses Order model', 
        str_contains($content, "protected static ?string \$model = Order::class"), 
        $checks);
    
    // Check navigation
    check('OrderResource has navigation icon', 
        str_contains($content, "protected static ?string \$navigationIcon"), 
        $checks);
    
    check('OrderResource has navigation group', 
        str_contains($content, "protected static ?string \$navigationGroup = 'Orders'"), 
        $checks);
    
    // Check form sections
    check('Form has Order Information section', 
        str_contains($content, "Forms\Components\Section::make('Order Information')"), 
        $checks);
    
    check('Form has Shipping Information section', 
        str_contains($content, "Forms\Components\Section::make('Shipping Information')"), 
        $checks);
    
    check('Form has Order Summary section', 
        str_contains($content, "Forms\Components\Section::make('Order Summary')"), 
        $checks);
    
    // Check form fields
    check('Form has order_number field', 
        str_contains($content, "->make('order_number')"), 
        $checks);
    
    check('Form has status select', 
        str_contains($content, "->make('status')") && str_contains($content, "->options(["), 
        $checks);
    
    check('Form has tracking_number field', 
        str_contains($content, "->make('tracking_number')"), 
        $checks);
    
    // Check table columns
    check('Table has order_number column', 
        str_contains($content, "TextColumn::make('order_number')"), 
        $checks);
    
    check('Table has customer column', 
        str_contains($content, "TextColumn::make('user.name')"), 
        $checks);
    
    check('Table has status badge column', 
        str_contains($content, "TextColumn::make('status')") && str_contains($content, "->badge()"), 
        $checks);
    
    check('Table has total column with money format', 
        str_contains($content, "TextColumn::make('total')") && str_contains($content, "->money('IDR')"), 
        $checks);
    
    check('Table has payment_method column', 
        str_contains($content, "TextColumn::make('payment_method')"), 
        $checks);
    
    check('Table has shipping_courier column', 
        str_contains($content, "TextColumn::make('shipping_courier')"), 
        $checks);
    
    check('Table has tracking_number column', 
        str_contains($content, "TextColumn::make('tracking_number')"), 
        $checks);
    
    // Check filters
    check('Table has status filter', 
        str_contains($content, "SelectFilter::make('status')"), 
        $checks);
    
    check('Status filter is multiple', 
        str_contains($content, "->multiple()"), 
        $checks);
    
    check('Table has date range filter', 
        str_contains($content, "Filter::make('created_at')"), 
        $checks);
    
    // Check bulk actions
    check('Table has mark_as_processing bulk action', 
        str_contains($content, "BulkAction::make('mark_as_processing')"), 
        $checks);
    
    check('Table has mark_as_shipped bulk action', 
        str_contains($content, "BulkAction::make('mark_as_shipped')"), 
        $checks);
    
    // Check ItemsRelationManager registration
    check('ItemsRelationManager is registered in getRelations()', 
        str_contains($content, "RelationManagers\ItemsRelationManager::class"), 
        $checks);
    
    // Check navigation badge
    check('Navigation badge shows pending payment count', 
        str_contains($content, "public static function getNavigationBadge()") && 
        str_contains($content, "where('status', 'pending_payment')"), 
        $checks);
    
    // Check all text is in English (no Indonesian)
    $indonesianWords = ['Bayar', 'Lanjut', 'Kembali', 'Pesanan', 'Pembayaran', 'Pengiriman', 'Alamat', 'Kurir', 'Produk', 'Jumlah', 'Harga'];
    $hasIndonesian = false;
    foreach ($indonesianWords as $word) {
        if (str_contains($content, $word)) {
            $hasIndonesian = true;
            break;
        }
    }
    check('All text is in English (no Indonesian words)', !$hasIndonesian, $checks);
}

echo "\n";

// 2. Check ItemsRelationManager
echo "2. Checking ItemsRelationManager...\n";
$itemsRelationManagerPath = __DIR__ . '/app/Filament/Resources/OrderResource/RelationManagers/ItemsRelationManager.php';
check('ItemsRelationManager.php exists', file_exists($itemsRelationManagerPath), $checks);

if (file_exists($itemsRelationManagerPath)) {
    $content = file_get_contents($itemsRelationManagerPath);
    
    check('ItemsRelationManager extends RelationManager', 
        str_contains($content, 'extends RelationManager'), 
        $checks);
    
    check('ItemsRelationManager has items relationship', 
        str_contains($content, "protected static string \$relationship = 'items'"), 
        $checks);
    
    check('Table has product image column', 
        str_contains($content, "ImageColumn::make"), 
        $checks);
    
    check('Table has product name column', 
        str_contains($content, "TextColumn::make('variant.product.name')"), 
        $checks);
    
    check('Table has size column', 
        str_contains($content, "TextColumn::make('variant.size')"), 
        $checks);
    
    check('Table has quantity column', 
        str_contains($content, "TextColumn::make('quantity')"), 
        $checks);
    
    check('Table has unit_price column', 
        str_contains($content, "TextColumn::make('unit_price')"), 
        $checks);
    
    check('Table has total column with calculation', 
        str_contains($content, "TextColumn::make('total')") && 
        str_contains($content, "->getStateUsing(fn (\$record) => \$record->unit_price * \$record->quantity)"), 
        $checks);
}

echo "\n";

// 3. Check ViewOrder page
echo "3. Checking ViewOrder page...\n";
$viewOrderPath = __DIR__ . '/app/Filament/Resources/OrderResource/Pages/ViewOrder.php';
check('ViewOrder.php exists', file_exists($viewOrderPath), $checks);

if (file_exists($viewOrderPath)) {
    $content = file_get_contents($viewOrderPath);
    
    check('ViewOrder extends ViewRecord', 
        str_contains($content, 'extends ViewRecord'), 
        $checks);
    
    check('ViewOrder has EditAction in header', 
        str_contains($content, 'Actions\EditAction::make()'), 
        $checks);
}

echo "\n";

// 4. Check database for orders
echo "4. Checking database...\n";
try {
    $orderCount = \App\Models\Order::count();
    check("Orders table is accessible (found {$orderCount} orders)", true, $checks);
    
    if ($orderCount > 0) {
        $pendingCount = \App\Models\Order::where('status', 'pending_payment')->count();
        check("Found {$pendingCount} pending payment orders", true, $checks);
        
        $sampleOrder = \App\Models\Order::with(['user', 'items.variant.product'])->first();
        check('Order relationships work (user, items, variant, product)', 
            $sampleOrder && $sampleOrder->user && $sampleOrder->items->count() > 0, 
            $checks);
    }
} catch (\Exception $e) {
    check('Database connection', false, $checks);
    echo "   Error: " . $e->getMessage() . "\n";
}

echo "\n";

// Summary
echo "=== VALIDATION SUMMARY ===\n";
echo "Total checks: {$checks['total']}\n";
echo "Passed: {$checks['passed']} ✓\n";
echo "Failed: {$checks['failed']} ✗\n";
$percentage = $checks['total'] > 0 ? round(($checks['passed'] / $checks['total']) * 100, 1) : 0;
echo "Success rate: {$percentage}%\n\n";

if ($checks['failed'] === 0) {
    echo "🎉 ALL CHECKS PASSED! T4.2 implementation is complete.\n";
} else {
    echo "⚠️  Some checks failed. Please review the implementation.\n";
}

echo "\n=== MANUAL TESTING INSTRUCTIONS ===\n";
echo "1. Visit http://styleu.test/admin\n";
echo "2. Login with admin credentials (admin@styleu.com / admin123)\n";
echo "3. Click 'Orders' in the sidebar\n";
echo "4. Verify:\n";
echo "   - Order list displays with all columns\n";
echo "   - Status badges have correct colors\n";
echo "   - Navigation badge shows pending payment count\n";
echo "   - Filters work (status, date range)\n";
echo "   - Click 'View' on an order\n";
echo "   - Verify order details display correctly\n";
echo "   - Verify 'Order Items' tab shows items with images\n";
echo "   - Click 'Edit' and update tracking number\n";
echo "   - Select multiple orders and use bulk actions\n";
echo "   - Verify all text is in English\n";
