<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_fetches_categories_with_product_counts()
    {
        $user = User::factory()->create();
        
        // Create categories with different product counts
        $categoryWithProducts = Category::factory()->create([
            'name' => 'T-Shirts',
            'slug' => 't-shirts',
            'display_order' => 1,
        ]);
        
        $categoryWithoutProducts = Category::factory()->create([
            'name' => 'Hoodies',
            'slug' => 'hoodies',
            'display_order' => 2,
        ]);

        // Create active products for first category
        Product::factory()->count(3)->create([
            'category_id' => $categoryWithProducts->id,
            'status' => 'active',
        ]);

        // Create draft product (should not be counted)
        Product::factory()->create([
            'category_id' => $categoryWithProducts->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->get('/products');

        $response->assertStatus(200);
        
        $props = $response->viewData('page')['props'];
        $categories = $props['categories'];
        
        // Verify category with products
        $tShirtsCategory = collect($categories)->firstWhere('slug', 't-shirts');
        $this->assertEquals(3, $tShirtsCategory['products_count']);
        $this->assertTrue($tShirtsCategory['is_active']);
        
        // Verify category without products
        $hoodiesCategory = collect($categories)->firstWhere('slug', 'hoodies');
        $this->assertEquals(0, $hoodiesCategory['products_count']);
        $this->assertFalse($hoodiesCategory['is_active']);
    }

    /** @test */
    public function it_orders_categories_by_display_order_then_name()
    {
        $user = User::factory()->create();
        
        Category::factory()->create([
            'name' => 'Zebra',
            'slug' => 'zebra',
            'display_order' => 2,
        ]);
        
        Category::factory()->create([
            'name' => 'Alpha',
            'slug' => 'alpha',
            'display_order' => 1,
        ]);
        
        Category::factory()->create([
            'name' => 'Beta',
            'slug' => 'beta',
            'display_order' => 1,
        ]);

        $response = $this->actingAs($user)->get('/products');
        
        $props = $response->viewData('page')['props'];
        $categories = $props['categories'];
        
        // Should be ordered: Alpha (1), Beta (1), Zebra (2)
        $this->assertEquals('alpha', $categories[0]['slug']);
        $this->assertEquals('beta', $categories[1]['slug']);
        $this->assertEquals('zebra', $categories[2]['slug']);
    }

    /** @test */
    public function it_caches_categories()
    {
        $user = User::factory()->create();
        Cache::flush();
        
        $category = Category::factory()->create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        // First request should cache
        $this->actingAs($user)->get('/products');
        
        // Verify cache exists
        $this->assertTrue(Cache::has('sidebar_categories'));
        
        // Verify cached data structure
        $cachedCategories = Cache::get('sidebar_categories');
        $this->assertNotNull($cachedCategories);
        $this->assertCount(1, $cachedCategories);
        $this->assertEquals('test-category', $cachedCategories[0]['slug']);
    }

    /** @test */
    public function it_only_counts_active_products()
    {
        $user = User::factory()->create();
        
        $category = Category::factory()->create([
            'name' => 'Test Category',
            'slug' => 'test-category',
        ]);

        // Create 2 active products
        Product::factory()->count(2)->create([
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        // Create 3 draft products (should not be counted)
        Product::factory()->count(3)->create([
            'category_id' => $category->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($user)->get('/products');
        
        $props = $response->viewData('page')['props'];
        $categories = $props['categories'];
        $testCategory = collect($categories)->firstWhere('slug', 'test-category');
        
        // Should only count the 2 active products
        $this->assertEquals(2, $testCategory['products_count']);
        $this->assertTrue($testCategory['is_active']);
    }

    /** @test */
    public function it_filters_products_by_category_slug()
    {
        $user = User::factory()->create();
        
        $tshirtCategory = Category::factory()->create([
            'name' => 'T-Shirts',
            'slug' => 't-shirts',
        ]);
        
        $hoodieCategory = Category::factory()->create([
            'name' => 'Hoodies',
            'slug' => 'hoodies',
        ]);

        // Create products for each category
        $tshirtProducts = Product::factory()->count(3)->create([
            'category_id' => $tshirtCategory->id,
            'status' => 'active',
        ]);

        $hoodieProducts = Product::factory()->count(2)->create([
            'category_id' => $hoodieCategory->id,
            'status' => 'active',
        ]);

        // Filter by t-shirts category
        $response = $this->actingAs($user)->get('/products?category=t-shirts');
        
        $response->assertStatus(200);
        $props = $response->viewData('page')['props'];
        
        // Should only return t-shirt products
        $this->assertCount(3, $props['products']['data']);
        
        // Verify all returned products belong to t-shirts category
        foreach ($props['products']['data'] as $product) {
            $this->assertEquals($tshirtCategory->id, $product['category_id']);
        }
    }

    /** @test */
    public function it_passes_selected_category_to_view()
    {
        $user = User::factory()->create();
        
        $category = Category::factory()->create([
            'name' => 'T-Shirts',
            'slug' => 't-shirts',
        ]);

        Product::factory()->create([
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get('/products?category=t-shirts');
        
        $props = $response->viewData('page')['props'];
        
        // Should have selectedCategory in props
        $this->assertNotNull($props['selectedCategory']);
        $this->assertEquals('T-Shirts', $props['selectedCategory']['name']);
        $this->assertEquals('t-shirts', $props['selectedCategory']['slug']);
    }

    /** @test */
    public function it_handles_invalid_category_slug_gracefully()
    {
        $user = User::factory()->create();
        
        $category = Category::factory()->create([
            'name' => 'T-Shirts',
            'slug' => 't-shirts',
        ]);

        Product::factory()->count(5)->create([
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        // Request with invalid category slug
        $response = $this->actingAs($user)->get('/products?category=invalid-slug');
        
        $response->assertStatus(200);
        $props = $response->viewData('page')['props'];
        
        // Should return no products (invalid category has no products)
        $this->assertCount(0, $props['products']['data']);
        
        // selectedCategory should be null
        $this->assertNull($props['selectedCategory']);
    }

    /** @test */
    public function it_returns_null_selected_category_when_no_filter()
    {
        $user = User::factory()->create();
        
        $category = Category::factory()->create([
            'name' => 'T-Shirts',
            'slug' => 't-shirts',
        ]);

        Product::factory()->create([
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get('/products');
        
        $props = $response->viewData('page')['props'];
        
        // selectedCategory should be null when no filter
        $this->assertNull($props['selectedCategory']);
    }

    /** @test */
    public function it_includes_category_slug_in_filters()
    {
        $user = User::factory()->create();
        
        $category = Category::factory()->create([
            'name' => 'T-Shirts',
            'slug' => 't-shirts',
        ]);

        Product::factory()->create([
            'category_id' => $category->id,
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->get('/products?category=t-shirts');
        
        $props = $response->viewData('page')['props'];
        
        // filters should include category slug
        $this->assertEquals('t-shirts', $props['filters']['category']);
    }
}
