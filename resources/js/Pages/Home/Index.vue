<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    featuredProducts: Array,
    categories: Array,
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
};
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-black text-white">
            <!-- Hero Section -->
            <div class="border-b border-[#1a1a1a]">
                <div class="max-w-7xl mx-auto px-4 py-16 md:py-24">
                    <div class="max-w-3xl">
                        <h1 class="font-['OCR_A'] text-6xl md:text-8xl uppercase tracking-tight leading-none mb-6">
                            Style<br>Redefined
                        </h1>
                        <p class="text-lg md:text-xl text-[#999999] mb-8 max-w-xl">
                            Latest fashion collection with minimalist design and premium quality. Find your style.
                        </p>
                        <Link
                            href="/products"
                            class="inline-block bg-white text-black px-8 py-4 uppercase font-['OCR_A'] text-xl tracking-tight hover:bg-[#f0f0f0] transition-colors"
                        >
                            Shop Now
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Categories Section -->
            <div v-if="categories.length > 0" class="border-b border-[#1a1a1a]">
                <div class="max-w-7xl mx-auto px-4 py-12">
                    <h2 class="font-['OCR_A'] text-4xl uppercase tracking-tight mb-8">Categories</h2>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <Link
                            v-for="category in categories"
                            :key="category.id"
                            :href="`/products?category=${category.slug}`"
                            class="bg-[#0f0f0f] border border-[#222222] p-6 hover:border-white transition-colors group"
                        >
                            <h3 class="font-['OCR_A'] text-2xl uppercase tracking-tight mb-2 group-hover:text-[#999999] transition-colors">
                                {{ category.name }}
                            </h3>
                            <p class="text-sm text-[#999999] uppercase tracking-wide">
                                {{ category.products_count }} Products
                            </p>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Featured Products Section -->
            <div class="max-w-7xl mx-auto px-4 py-12">
                <div class="flex items-center justify-between mb-8">
                    <h2 class="font-['OCR_A'] text-4xl uppercase tracking-tight">Latest Products</h2>
                    <Link
                        href="/products"
                        class="text-sm uppercase tracking-wide text-[#999999] hover:text-white transition-colors"
                    >
                        View All →
                    </Link>
                </div>

                <div v-if="featuredProducts.length === 0" class="text-center py-16">
                    <p class="text-[#999999] text-lg">No products available yet</p>
                </div>

                <div v-else class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <Link
                        v-for="product in featuredProducts"
                        :key="product.id"
                        :href="`/products/${product.slug}`"
                        class="group"
                    >
                        <!-- Product Image -->
                        <div class="relative aspect-square bg-[#0f0f0f] mb-3 overflow-hidden">
                            <img
                                v-if="product.images[0]"
                                :src="product.images[0].image_url"
                                :alt="product.name"
                                class="absolute inset-0 w-full h-full object-cover transition-opacity duration-300"
                                :class="product.images[1] ? 'group-hover:opacity-0' : ''"
                            >
                            <img
                                v-if="product.images[1]"
                                :src="product.images[1].image_url"
                                :alt="product.name"
                                class="absolute inset-0 w-full h-full object-cover opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                            >
                            <div
                                v-if="!product.in_stock"
                                class="absolute inset-0 bg-black/80 flex items-center justify-center"
                            >
                                <span class="text-white uppercase tracking-wide text-sm">Out of Stock</span>
                            </div>
                        </div>

                        <!-- Product Info -->
                        <div>
                            <p v-if="product.category" class="text-xs text-[#999999] uppercase tracking-wide mb-1">
                                {{ product.category.name }}
                            </p>
                            <h3 class="font-medium uppercase tracking-tight mb-1 group-hover:text-[#999999] transition-colors">
                                {{ product.name }}
                            </h3>
                            <p class="font-['OCR_A'] text-xl">
                                {{ formatPrice(product.price) }}
                            </p>
                        </div>
                    </Link>
                </div>
            </div>

            <!-- CTA Section -->
            <div class="border-t border-[#1a1a1a]">
                <div class="max-w-7xl mx-auto px-4 py-16 text-center">
                    <h2 class="font-['OCR_A'] text-5xl md:text-6xl uppercase tracking-tight mb-6">
                        Ready to Shop?
                    </h2>
                    <p class="text-lg text-[#999999] mb-8 max-w-2xl mx-auto">
                        Explore our complete collection and find the perfect products for your style.
                    </p>
                    <Link
                        href="/products"
                        class="inline-block bg-white text-black px-8 py-4 uppercase font-['OCR_A'] text-xl tracking-tight hover:bg-[#f0f0f0] transition-colors"
                    >
                        View All Products
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
