<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
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

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
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

        $categories = Category::withCount('products')->get();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
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
