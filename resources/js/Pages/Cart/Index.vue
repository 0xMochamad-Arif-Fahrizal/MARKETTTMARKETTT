<script setup>
import { ref } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    cart: Object,
});

const updatingItem = ref(null);

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
};

const updateQuantity = (item, newQuantity) => {
    if (newQuantity < 1) return;
    if (newQuantity > item.variant.stock) {
        alert(`Stok tidak mencukupi. Tersedia: ${item.variant.stock}`);
        return;
    }

    updatingItem.value = item.id;
    
    router.patch(`/cart/items/${item.id}`, {
        quantity: newQuantity,
    }, {
        preserveScroll: true,
        onFinish: () => {
            updatingItem.value = null;
        },
    });
};

const removeItem = (item) => {
    if (!confirm('Remove this item from cart?')) return;

    router.delete(`/cart/items/${item.id}`, {
        preserveScroll: true,
    });
};

const proceedToCheckout = () => {
    router.get('/checkout');
};
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-black">
            <!-- Header -->
            <div class="border-b border-[#1a1a1a]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <h1 class="text-4xl md:text-5xl font-heading uppercase tracking-tight mb-2">
                        SHOPPING CART
                    </h1>
                    <p class="text-sm text-[#999999] uppercase tracking-wide">
                        {{ cart.item_count }} {{ cart.item_count === 1 ? 'ITEM' : 'ITEMS' }}
                    </p>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Empty Cart -->
                <div v-if="cart.items.length === 0" class="text-center py-16">
                    <p class="text-[#999999] uppercase tracking-wide text-sm mb-6">
                        YOUR CART IS EMPTY
                    </p>
                    <Link
                        href="/products"
                        class="inline-block px-8 py-4 bg-white text-black uppercase tracking-wide text-sm font-medium hover:bg-[#999999] transition-colors"
                    >
                        CONTINUE SHOPPING
                    </Link>
                </div>

                <!-- Cart Items -->
                <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Items List -->
                    <div class="lg:col-span-2 space-y-4">
                        <div
                            v-for="item in cart.items"
                            :key="item.id"
                            class="bg-black border border-[#1a1a1a] p-4 sm:p-6"
                        >
                            <div class="flex gap-4">
                                <!-- Product Image -->
                                <Link
                                    :href="`/products/${item.variant.product.slug}`"
                                    class="flex-shrink-0 w-24 h-32 sm:w-32 sm:h-40 bg-black"
                                >
                                    <img
                                        v-if="item.variant.product.images[0]"
                                        :src="item.variant.product.images[0].image_url"
                                        :alt="item.variant.product.name"
                                        class="w-full h-full object-cover"
                                    />
                                </Link>

                                <!-- Product Details -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between gap-4 mb-3">
                                        <div class="flex-1 min-w-0">
                                            <Link
                                                :href="`/products/${item.variant.product.slug}`"
                                                class="block font-heading text-lg uppercase tracking-tight hover:text-[#999999] transition-colors mb-1"
                                            >
                                                {{ item.variant.product.name }}
                                            </Link>
                                            <p class="text-xs text-[#999999] uppercase tracking-wide">
                                                SIZE: {{ item.variant.size }}
                                                <span v-if="item.variant.color"> / {{ item.variant.color }}</span>
                                            </p>
                                        </div>
                                        
                                        <!-- Remove Button (Desktop) -->
                                        <button
                                            @click="removeItem(item)"
                                            class="hidden sm:block text-[#999999] hover:text-white transition-colors"
                                            title="Remove"
                                        >
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Price & Quantity -->
                                    <div class="flex items-end justify-between gap-4">
                                        <!-- Quantity Adjuster -->
                                        <div class="flex items-center border border-[#1a1a1a]">
                                            <button
                                                @click="updateQuantity(item, item.quantity - 1)"
                                                :disabled="item.quantity <= 1 || updatingItem === item.id"
                                                class="px-3 py-2 text-white hover:bg-[#1a1a1a] transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" />
                                                </svg>
                                            </button>
                                            <input
                                                :value="item.quantity"
                                                @change="updateQuantity(item, parseInt($event.target.value))"
                                                type="number"
                                                min="1"
                                                :max="item.variant.stock"
                                                class="w-12 py-2 text-center bg-black border-x border-[#1a1a1a] text-white text-sm focus:outline-none"
                                                :disabled="updatingItem === item.id"
                                            />
                                            <button
                                                @click="updateQuantity(item, item.quantity + 1)"
                                                :disabled="item.quantity >= item.variant.stock || updatingItem === item.id"
                                                class="px-3 py-2 text-white hover:bg-[#1a1a1a] transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                            </button>
                                        </div>

                                        <!-- Price -->
                                        <div class="text-right">
                                            <p class="text-sm font-heading uppercase tracking-tight">
                                                {{ formatPrice(item.price_snapshot * item.quantity) }}
                                            </p>
                                            <p v-if="item.quantity > 1" class="text-xs text-[#999999]">
                                                {{ formatPrice(item.price_snapshot) }} each
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Stock Warning -->
                                    <p v-if="item.variant.stock < 5" class="mt-2 text-xs text-[#ff0000] uppercase tracking-wide">
                                        ONLY {{ item.variant.stock }} LEFT IN STOCK
                                    </p>

                                    <!-- Remove Button (Mobile) -->
                                    <button
                                        @click="removeItem(item)"
                                        class="sm:hidden mt-3 text-xs text-[#999999] hover:text-white transition-colors uppercase tracking-wide"
                                    >
                                        REMOVE
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-[#0f0f0f] border border-[#1a1a1a] p-6 sticky top-24">
                            <h2 class="text-xl font-heading uppercase tracking-tight mb-6">
                                ORDER SUMMARY
                            </h2>

                            <div class="space-y-4 mb-6 pb-6 border-b border-[#1a1a1a]">
                                <div class="flex justify-between text-sm">
                                    <span class="text-[#999999] uppercase tracking-wide">SUBTOTAL</span>
                                    <span class="uppercase tracking-wide">{{ formatPrice(cart.subtotal) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-[#999999] uppercase tracking-wide">SHIPPING</span>
                                    <span class="text-[#999999] uppercase tracking-wide text-xs">CALCULATED AT CHECKOUT</span>
                                </div>
                            </div>

                            <div class="flex justify-between mb-6">
                                <span class="text-lg font-heading uppercase tracking-tight">TOTAL</span>
                                <span class="text-lg font-heading uppercase tracking-tight">{{ formatPrice(cart.subtotal) }}</span>
                            </div>

                            <button
                                @click="proceedToCheckout"
                                class="w-full py-4 bg-white text-black uppercase tracking-wide text-sm font-medium hover:bg-[#999999] transition-colors mb-4"
                            >
                                PROCEED TO CHECKOUT
                            </button>

                            <Link
                                href="/products"
                                class="block w-full py-4 text-center bg-black border border-[#1a1a1a] text-white uppercase tracking-wide text-sm font-medium hover:border-white transition-colors"
                            >
                                CONTINUE SHOPPING
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
