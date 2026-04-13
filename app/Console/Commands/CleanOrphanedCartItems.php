<?php

namespace App\Console\Commands;

use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Console\Command;

class CleanOrphanedCartItems extends Command
{
    protected $signature = 'cart:clean-orphaned';
    protected $description = 'Remove cart items with deleted products or variants';

    public function handle()
    {
        $this->info('Cleaning orphaned cart items...');

        // Get all cart items
        $cartItems = CartItem::with(['variant.product'])->get();
        $deletedCount = 0;

        foreach ($cartItems as $item) {
            // Check if variant or product is missing/deleted
            if (!$item->variant || !$item->variant->product) {
                $item->delete();
                $deletedCount++;
            }
        }

        $this->info("Cleaned up {$deletedCount} orphaned cart items.");
        
        return 0;
    }
}
