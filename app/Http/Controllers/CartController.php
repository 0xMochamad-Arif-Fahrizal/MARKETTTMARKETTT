<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * Get cart item count
     */
    public function count(Request $request)
    {
        $cart = $this->cartService->getCart($request);
        $totals = $this->cartService->getTotal($cart);

        return response()->json([
            'count' => $totals['item_count'],
        ]);
    }

    /**
     * Display the cart
     */
    public function index(Request $request)
    {
        $cart = $this->cartService->getCart($request);
        $totals = $this->cartService->getTotal($cart);

        return Inertia::render('Cart/Index', [
            'cart' => [
                'items' => $totals['items']->filter(function ($item) {
                    // Filter out items with missing variant or product
                    return $item->variant && $item->variant->product;
                })->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'quantity' => $item->quantity,
                        'price_snapshot' => $item->price_snapshot,
                        'variant' => [
                            'id' => $item->variant->id,
                            'size' => $item->variant->size,
                            'color' => $item->variant->color,
                            'color_hex' => $item->variant->color_hex,
                            'price' => $item->variant->price,
                            'stock' => $item->variant->stock,
                            'sku' => $item->variant->sku,
                            'product' => [
                                'id' => $item->variant->product->id,
                                'name' => $item->variant->product->name,
                                'slug' => $item->variant->product->slug,
                                'images' => $item->variant->product->images->map(function ($img) {
                                    return [
                                        'image_url' => $img->image_url,
                                        'sort_order' => $img->sort_order,
                                    ];
                                }),
                            ],
                        ],
                    ];
                })->values(),
                'subtotal' => $totals['subtotal'],
                'item_count' => $totals['item_count'],
            ],
        ]);
    }

    /**
     * Add item to cart
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            $cart = $this->cartService->getCart($request);
            $this->cartService->addItem($cart, $validated['variant_id'], $validated['quantity']);

            return back()->with('success', 'Product added to cart');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, CartItem $item)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        try {
            // Verify item belongs to current cart
            $cart = $this->cartService->getCart($request);
            if ($item->cart_id !== $cart->id) {
                abort(403);
            }

            $this->cartService->updateItem($item, $validated['quantity']);

            return back()->with('success', 'Cart updated');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Remove item from cart
     */
    public function destroy(Request $request, CartItem $item)
    {
        // Verify item belongs to current cart
        $cart = $this->cartService->getCart($request);
        if ($item->cart_id !== $cart->id) {
            abort(403);
        }

        $this->cartService->removeItem($item);

        return back()->with('success', 'Item removed from cart');
    }
}
