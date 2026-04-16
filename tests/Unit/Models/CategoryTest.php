<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_has_display_order_in_fillable_array()
    {
        $category = new Category();
        $this->assertContains('display_order', $category->getFillable());
    }

    /** @test */
    public function ordered_scope_sorts_by_display_order_then_name()
    {
        // Create categories with different display orders
        Category::create(['name' => 'Zebra', 'slug' => 'zebra', 'display_order' => 2]);
        Category::create(['name' => 'Alpha', 'slug' => 'alpha', 'display_order' => 1]);
        Category::create(['name' => 'Beta', 'slug' => 'beta', 'display_order' => 1]);
        Category::create(['name' => 'Gamma', 'slug' => 'gamma', 'display_order' => 0]);

        $orderedCategories = Category::ordered()->get();

        // Should be ordered by display_order ASC, then name ASC
        $this->assertEquals('Gamma', $orderedCategories[0]->name); // display_order 0
        $this->assertEquals('Alpha', $orderedCategories[1]->name); // display_order 1, alphabetically first
        $this->assertEquals('Beta', $orderedCategories[2]->name);  // display_order 1, alphabetically second
        $this->assertEquals('Zebra', $orderedCategories[3]->name); // display_order 2
    }

    /** @test */
    public function active_products_count_returns_correct_count()
    {
        $category = Category::create(['name' => 'Test Category', 'slug' => 'test-category', 'display_order' => 0]);

        // Create active products
        Product::create([
            'name' => 'Product 1',
            'slug' => 'product-1',
            'category_id' => $category->id,
            'base_price' => 100,
            'status' => 'active',
            'weight_gram' => 100,
        ]);

        Product::create([
            'name' => 'Product 2',
            'slug' => 'product-2',
            'category_id' => $category->id,
            'base_price' => 200,
            'status' => 'active',
            'weight_gram' => 200,
        ]);

        $this->assertEquals(2, $category->activeProductsCount());
    }

    /** @test */
    public function active_products_count_excludes_inactive_products()
    {
        $category = Category::create(['name' => 'Test Category', 'slug' => 'test-category', 'display_order' => 0]);

        // Create active product
        Product::create([
            'name' => 'Active Product',
            'slug' => 'active-product',
            'category_id' => $category->id,
            'base_price' => 100,
            'status' => 'active',
            'weight_gram' => 100,
        ]);

        // Create non-active products (draft and archived)
        Product::create([
            'name' => 'Draft Product',
            'slug' => 'draft-product',
            'category_id' => $category->id,
            'base_price' => 200,
            'status' => 'draft',
            'weight_gram' => 200,
        ]);

        Product::create([
            'name' => 'Archived Product',
            'slug' => 'archived-product',
            'category_id' => $category->id,
            'base_price' => 300,
            'status' => 'archived',
            'weight_gram' => 300,
        ]);

        $this->assertEquals(1, $category->activeProductsCount());
    }

    /** @test */
    public function cache_is_cleared_when_category_is_saved()
    {
        Cache::put('sidebar_categories', 'test_data', 60);
        $this->assertTrue(Cache::has('sidebar_categories'));

        $category = Category::create(['name' => 'New Category', 'slug' => 'new-category', 'display_order' => 0]);

        $this->assertFalse(Cache::has('sidebar_categories'));
    }

    /** @test */
    public function cache_is_cleared_when_category_is_updated()
    {
        $category = Category::create(['name' => 'Test Category', 'slug' => 'test-category', 'display_order' => 0]);

        Cache::put('sidebar_categories', 'test_data', 60);
        $this->assertTrue(Cache::has('sidebar_categories'));

        $category->update(['name' => 'Updated Category']);

        $this->assertFalse(Cache::has('sidebar_categories'));
    }

    /** @test */
    public function cache_is_cleared_when_category_is_deleted()
    {
        $category = Category::create(['name' => 'Test Category', 'slug' => 'test-category', 'display_order' => 0]);

        Cache::put('sidebar_categories', 'test_data', 60);
        $this->assertTrue(Cache::has('sidebar_categories'));

        $category->delete();

        $this->assertFalse(Cache::has('sidebar_categories'));
    }
}
