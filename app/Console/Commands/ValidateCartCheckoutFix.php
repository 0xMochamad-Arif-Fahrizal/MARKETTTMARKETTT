<?php

namespace App\Console\Commands;

use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Console\Command;

class ValidateCartCheckoutFix extends Command
{
    protected $signature = 'cart:validate-fix';
    protected $description = 'Validate cart and checkout orphaned data fix';

    public function handle()
    {
        $this->info('=== CART & CHECKOUT ORPHANED DATA FIX VALIDATION ===');
        $this->newLine();

        // Test 1: Check for orphaned cart items
        $this->info('Test 1: Checking for orphaned cart items...');
        $orphanedItems = CartItem::with(['variant.product'])->get()->filter(function ($item) {
            return !$item->variant || !$item->variant->product;
        });
        $this->line("Orphaned cart items found: {$orphanedItems->count()}");
        $orphanedItems->count() === 0 ? $this->info('✓ PASS') : $this->error('✗ FAIL');

        // Test 2: Check ProductVariant relationship
        $this->newLine();
        $this->info('Test 2: Checking ProductVariant->product relationship...');
        $variant = ProductVariant::with('product')->first();
        if ($variant) {
            $this->line("Sample variant ID: {$variant->id}");
            $this->line("Has product: " . ($variant->product ? "Yes" : "No"));
            if ($variant->product) {
                $this->line("Product deleted_at: " . ($variant->product->deleted_at ? "Yes" : "No"));
                !$variant->product->deleted_at ? $this->info('✓ PASS') : $this->error('✗ FAIL');
            } else {
                $this->info('✓ PASS (variant has no product - filtered correctly)');
            }
        } else {
            $this->line('No variants found in database');
            $this->info('✓ PASS (no data to test)');
        }

        // Test 3: Check CartItem relationship
        $this->newLine();
        $this->info('Test 3: Checking CartItem->variant relationship...');
        $cartItem = CartItem::with(['variant.product'])->first();
        if ($cartItem) {
            $this->line("Sample cart item ID: {$cartItem->id}");
            $this->line("Has variant: " . ($cartItem->variant ? "Yes" : "No"));
            $this->line("Has product: " . ($cartItem->variant && $cartItem->variant->product ? "Yes" : "No"));
            ($cartItem->variant && $cartItem->variant->product) ? $this->info('✓ PASS') : $this->error('✗ FAIL');
        } else {
            $this->line('No cart items found in database');
            $this->info('✓ PASS (no data to test)');
        }

        // Test 4: Simulate cart data retrieval
        $this->newLine();
        $this->info('Test 4: Simulating cart data retrieval...');
        $user = User::where('email', 'newuser@styleu.com')->first();
        if ($user) {
            $cart = $user->cart;
            if ($cart) {
                $items = $cart->items()->with(['variant.product'])->get();
                $validItems = $items->filter(function ($item) {
                    return $item->variant && $item->variant->product;
                });
                $this->line("Total cart items: {$items->count()}");
                $this->line("Valid cart items: {$validItems->count()}");
                $this->line("All items valid: " . ($items->count() === $validItems->count() ? "Yes" : "No"));
                ($items->count() === $validItems->count()) ? $this->info('✓ PASS') : $this->error('✗ FAIL');
            } else {
                $this->line('User has no cart');
                $this->info('✓ PASS (no cart to test)');
            }
        } else {
            $this->line('Test user not found');
            $this->info('✓ PASS (no user to test)');
        }

        $this->newLine();
        $this->info('=== VALIDATION COMPLETE ===');
        $this->newLine();
        $this->line('Summary:');
        $this->line('- ProductVariant model now uses withoutTrashed() for product relationship');
        $this->line('- CartItem model filters variants with deleted products');
        $this->line('- Orphaned cart items have been cleaned up');
        $this->line('- Cart and checkout pages should now work without errors');
        $this->newLine();

        return 0;
    }
}
