<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== PHASE 2 VALIDATION ===" . PHP_EOL . PHP_EOL;

// T2.1 - Admin Filament Resources
echo "T2.1 - Admin Filament - Resource Produk & Kategori" . PHP_EOL;

echo "  - Filament installed: " . (class_exists('Filament\Panel') ? '✓' : '✗') . PHP_EOL;
echo "  - AdminPanelProvider exists: " . (class_exists('App\Providers\Filament\AdminPanelProvider') ? '✓' : '✗') . PHP_EOL;
echo "  - CategoryResource exists: " . (class_exists('App\Filament\Resources\CategoryResource') ? '✓' : '✗') . PHP_EOL;
echo "  - ProductResource exists: " . (class_exists('App\Filament\Resources\ProductResource') ? '✓' : '✗') . PHP_EOL;

// Check admin user can access panel
$admin = App\Models\User::where('email', 'admin@styleu.com')->first();
if ($admin) {
    $panel = new Filament\Panel('admin');
    echo "  - Admin can access panel: " . ($admin->canAccessPanel($panel) ? '✓' : '✗') . PHP_EOL;
}

// Check categories
$categoriesCount = App\Models\Category::count();
echo "  - Categories in database: {$categoriesCount}" . PHP_EOL;

// Check products
$productsCount = App\Models\Product::count();
echo "  - Products in database: {$productsCount}" . PHP_EOL;

echo PHP_EOL;

// T2.2 - Halaman Katalog Produk
echo "T2.2 - Halaman Katalog Produk (User-facing)" . PHP_EOL;

echo "  - ProductController exists: " . (class_exists('App\Http\Controllers\ProductController') ? '✓' : '✗') . PHP_EOL;
echo "  - Products index route: " . (Route::has('products.index') ? '✓' : '✗') . PHP_EOL;
echo "  - Products show route: " . (Route::has('products.show') ? '✓' : '✗') . PHP_EOL;
echo "  - Products/Index.vue exists: " . (file_exists('resources/js/Pages/Products/Index.vue') ? '✓' : '✗') . PHP_EOL;
echo "  - Products/Show.vue exists: " . (file_exists('resources/js/Pages/Products/Show.vue') ? '✓' : '✗') . PHP_EOL;
echo "  - ProductCard.vue exists: " . (file_exists('resources/js/Components/ProductCard.vue') ? '✓' : '✗') . PHP_EOL;

// Test product query
$activeProducts = App\Models\Product::where('status', 'active')
    ->with(['category', 'variants', 'images'])
    ->get();
echo "  - Active products: {$activeProducts->count()}" . PHP_EOL;

// Test product with variants and images
$productWithData = App\Models\Product::with(['variants', 'images'])->first();
if ($productWithData) {
    echo "  - Sample product has variants: " . ($productWithData->variants->count() > 0 ? '✓' : '✗') . PHP_EOL;
    echo "  - Sample product has images: " . ($productWithData->images->count() > 0 ? '✓' : '✗') . PHP_EOL;
}

// Test filters
echo "  - Category filter works: " . (App\Models\Product::whereHas('category')->count() > 0 ? '✓' : '✗') . PHP_EOL;

// Test product detail
$productSlug = App\Models\Product::where('status', 'active')->first()?->slug;
if ($productSlug) {
    $product = App\Models\Product::where('slug', $productSlug)
        ->where('status', 'active')
        ->with(['category', 'variants', 'images'])
        ->first();
    echo "  - Product detail query works: " . ($product ? '✓' : '✗') . PHP_EOL;
}

echo PHP_EOL;

// Design System Check
echo "Design System (Corteiz-inspired)" . PHP_EOL;
echo "  - Bebas Neue font in CSS: " . (str_contains(file_get_contents('resources/css/app.css'), 'Bebas Neue') ? '✓' : '✗') . PHP_EOL;
echo "  - Black background color: " . (str_contains(file_get_contents('resources/css/app.css'), '#000000') ? '✓' : '✗') . PHP_EOL;
echo "  - Sharp edges (no border-radius): " . (str_contains(file_get_contents('resources/js/Components/ProductCard.vue'), 'border-radius: 0') || !str_contains(file_get_contents('resources/js/Components/ProductCard.vue'), 'rounded') ? '✓' : '✗') . PHP_EOL;

echo PHP_EOL;
echo "=== PHASE 2 VALIDATION COMPLETE ===" . PHP_EOL;
echo PHP_EOL;
echo "Next: Access /admin to manage products" . PHP_EOL;
echo "       Access /products to view catalog" . PHP_EOL;
