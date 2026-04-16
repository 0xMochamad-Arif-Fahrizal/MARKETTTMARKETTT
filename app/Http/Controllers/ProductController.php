<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::query()
            ->where('status', 'active')
            ->with(['category', 'variants' => function ($query) {
                $query->where('is_active', true);
            }, 'images' => function ($query) {
                $query->orderBy('sort_order');
            }]);

        // Filter by category slug
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by size (check variants)
        if ($request->filled('size')) {
            $query->whereHas('variants', function ($q) use ($request) {
                $q->where('size', $request->size)
                  ->where('is_active', true);
            });
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('base_price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('base_price', '<=', $request->max_price);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->paginate(20)->withQueryString();

        // Fetch categories with product counts and cache for 5 minutes
        $categories = Cache::remember('sidebar_categories', 300, function () {
            return Category::ordered()
                ->withCount(['products' => function ($query) {
                    $query->where('status', 'active');
                }])
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'name' => $category->name,
                        'slug' => $category->slug,
                        'products_count' => $category->products_count,
                        'is_active' => $category->products_count > 0,
                    ];
                });
        });

        // Fetch selected category object when filtering
        $selectedCategory = null;
        if ($request->filled('category')) {
            $selectedCategory = Category::where('slug', $request->category)->first();
        }

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'filters' => [
                'category' => $request->category,
                'size' => $request->size,
                'min_price' => $request->min_price,
                'max_price' => $request->max_price,
                'search' => $request->search,
            ],
        ]);
    }

    /**
     * Display the specified product.
     */
    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('status', 'active')
            ->with([
                'category',
                'variants' => function ($query) {
                    $query->where('is_active', true)->orderBy('size');
                },
                'images' => function ($query) {
                    $query->orderBy('sort_order');
                }
            ])
            ->firstOrFail();

        return Inertia::render('Products/Show', [
            'product' => $product,
        ]);
    }
}
