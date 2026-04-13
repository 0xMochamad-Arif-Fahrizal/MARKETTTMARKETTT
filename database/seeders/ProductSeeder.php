<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories
        $tshirts = Category::firstOrCreate(
            ['slug' => 't-shirts'],
            ['name' => 'T-Shirts']
        );

        $hoodies = Category::firstOrCreate(
            ['slug' => 'hoodies'],
            ['name' => 'Hoodies']
        );

        $jackets = Category::firstOrCreate(
            ['slug' => 'jackets'],
            ['name' => 'Jackets']
        );

        // Create sample products
        $products = [
            [
                'name' => 'ALCATRAZ TEE BLACK',
                'slug' => 'alcatraz-tee-black',
                'description' => '<p>Premium heavyweight cotton t-shirt with embroidered logo. Made from 100% organic cotton.</p>',
                'category_id' => $tshirts->id,
                'base_price' => 350000,
                'status' => 'active',
                'weight_gram' => 250,
                'variants' => [
                    ['size' => 'S', 'color' => 'Black', 'color_hex' => '#000000', 'price' => 350000, 'stock' => 10, 'sku' => 'ALC-TEE-BLK-S'],
                    ['size' => 'M', 'color' => 'Black', 'color_hex' => '#000000', 'price' => 350000, 'stock' => 15, 'sku' => 'ALC-TEE-BLK-M'],
                    ['size' => 'L', 'color' => 'Black', 'color_hex' => '#000000', 'price' => 350000, 'stock' => 12, 'sku' => 'ALC-TEE-BLK-L'],
                    ['size' => 'XL', 'color' => 'Black', 'color_hex' => '#000000', 'price' => 350000, 'stock' => 8, 'sku' => 'ALC-TEE-BLK-XL'],
                ],
                'images' => [
                    ['image_url' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=800', 'sort_order' => 0],
                    ['image_url' => 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=800', 'sort_order' => 1],
                ],
            ],
            [
                'name' => 'RULES THE WORLD HOODIE',
                'slug' => 'rules-the-world-hoodie',
                'description' => '<p>Oversized heavyweight hoodie with embroidered graphics. 450GSM premium cotton blend.</p>',
                'category_id' => $hoodies->id,
                'base_price' => 750000,
                'status' => 'active',
                'weight_gram' => 650,
                'variants' => [
                    ['size' => 'M', 'color' => 'Black', 'color_hex' => '#000000', 'price' => 750000, 'stock' => 5, 'sku' => 'RTW-HOOD-BLK-M'],
                    ['size' => 'L', 'color' => 'Black', 'color_hex' => '#000000', 'price' => 750000, 'stock' => 8, 'sku' => 'RTW-HOOD-BLK-L'],
                    ['size' => 'XL', 'color' => 'Black', 'color_hex' => '#000000', 'price' => 750000, 'stock' => 6, 'sku' => 'RTW-HOOD-BLK-XL'],
                ],
                'images' => [
                    ['image_url' => 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=800', 'sort_order' => 0],
                    ['image_url' => 'https://images.unsplash.com/photo-1620799140408-edc6dcb6d633?w=800', 'sort_order' => 1],
                ],
            ],
            [
                'name' => 'GUERRILLAZ CARGO JACKET',
                'slug' => 'guerrillaz-cargo-jacket',
                'description' => '<p>Military-inspired cargo jacket with multiple pockets. Water-resistant nylon fabric.</p>',
                'category_id' => $jackets->id,
                'base_price' => 950000,
                'status' => 'active',
                'weight_gram' => 800,
                'variants' => [
                    ['size' => 'M', 'color' => 'Olive', 'color_hex' => '#556B2F', 'price' => 950000, 'stock' => 4, 'sku' => 'GRZ-JKT-OLV-M'],
                    ['size' => 'L', 'color' => 'Olive', 'color_hex' => '#556B2F', 'price' => 950000, 'stock' => 6, 'sku' => 'GRZ-JKT-OLV-L'],
                    ['size' => 'XL', 'color' => 'Olive', 'color_hex' => '#556B2F', 'price' => 950000, 'stock' => 3, 'sku' => 'GRZ-JKT-OLV-XL'],
                ],
                'images' => [
                    ['image_url' => 'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=800', 'sort_order' => 0],
                    ['image_url' => 'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=800', 'sort_order' => 1],
                ],
            ],
            [
                'name' => 'BOLO TEE WHITE',
                'slug' => 'bolo-tee-white',
                'description' => '<p>Classic white tee with screen-printed graphics. Relaxed fit, 100% cotton.</p>',
                'category_id' => $tshirts->id,
                'base_price' => 320000,
                'status' => 'active',
                'weight_gram' => 230,
                'variants' => [
                    ['size' => 'S', 'color' => 'White', 'color_hex' => '#FFFFFF', 'price' => 320000, 'stock' => 12, 'sku' => 'BOLO-TEE-WHT-S'],
                    ['size' => 'M', 'color' => 'White', 'color_hex' => '#FFFFFF', 'price' => 320000, 'stock' => 18, 'sku' => 'BOLO-TEE-WHT-M'],
                    ['size' => 'L', 'color' => 'White', 'color_hex' => '#FFFFFF', 'price' => 320000, 'stock' => 15, 'sku' => 'BOLO-TEE-WHT-L'],
                    ['size' => 'XL', 'color' => 'White', 'color_hex' => '#FFFFFF', 'price' => 320000, 'stock' => 10, 'sku' => 'BOLO-TEE-WHT-XL'],
                ],
                'images' => [
                    ['image_url' => 'https://images.unsplash.com/photo-1622445275463-afa2ab738c34?w=800', 'sort_order' => 0],
                    ['image_url' => 'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=800', 'sort_order' => 1],
                ],
            ],
            [
                'name' => 'SOLD OUT SAMPLE TEE',
                'slug' => 'sold-out-sample-tee',
                'description' => '<p>This is a sample product to test sold out state.</p>',
                'category_id' => $tshirts->id,
                'base_price' => 300000,
                'status' => 'active',
                'weight_gram' => 220,
                'variants' => [
                    ['size' => 'M', 'color' => 'Black', 'color_hex' => '#000000', 'price' => 300000, 'stock' => 0, 'sku' => 'SOLD-TEE-BLK-M'],
                    ['size' => 'L', 'color' => 'Black', 'color_hex' => '#000000', 'price' => 300000, 'stock' => 0, 'sku' => 'SOLD-TEE-BLK-L'],
                ],
                'images' => [
                    ['image_url' => 'https://images.unsplash.com/photo-1503341504253-dff4815485f1?w=800', 'sort_order' => 0],
                ],
            ],
        ];

        foreach ($products as $productData) {
            $variants = $productData['variants'];
            $images = $productData['images'];
            unset($productData['variants'], $productData['images']);

            $product = Product::create($productData);

            foreach ($variants as $variantData) {
                $product->variants()->create($variantData);
            }

            foreach ($images as $imageData) {
                $product->images()->create($imageData);
            }
        }
    }
}
