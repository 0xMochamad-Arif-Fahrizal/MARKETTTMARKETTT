<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    /**
     * Display the homepage.
     */
    public function index()
    {
        // Get featured products (latest 8 active products)
        $featuredProducts = Product::with(['images', 'category', 'variants'])
            ->where('status', 'active')
            ->latest()
            ->take(8)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->price,
                    'category' => $product->category ? [
                        'name' => $product->category->name,
                        'slug' => $product->category->slug,
                    ] : null,
                    'images' => $product->images->map(fn($img) => [
                        'image_url' => $img->image_url,
                        'sort_order' => $img->sort_order,
                    ])->sortBy('sort_order')->values(),
                    'in_stock' => $product->variants->sum('stock') > 0,
                ];
            });

        // Get all categories with product count
        $categories = Category::withCount(['products' => function ($query) {
            $query->where('status', 'active');
        }])
            ->get()
            ->filter(function ($category) {
                return $category->products_count > 0;
            })
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'products_count' => $category->products_count,
                ];
            })
            ->values();

        return Inertia::render('Home/Index', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
        ]);
    }
}
