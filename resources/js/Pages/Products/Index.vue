<script setup>
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-black">
            <!-- Header -->
            <div class="border-b border-[#1a1a1a]">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                    <h1 class="text-4xl md:text-5xl font-heading uppercase tracking-tight mb-2">
                        COLLECTION
                    </h1>
                    <p class="text-sm text-[#999999] uppercase tracking-wide">
                        {{ products.total }} PRODUCTS
                    </p>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <!-- Empty State -->
                <div v-if="products.data.length === 0" class="text-center py-16">
                    <p class="text-[#999999] uppercase tracking-wide text-sm">
                        NO PRODUCTS FOUND
                    </p>
                </div>

                <!-- Products Grid -->
                <div v-else>
                    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        <ProductCard
                            v-for="product in products.data"
                            :key="product.id"
                            :product="product"
                        />
                    </div>

                    <!-- Pagination -->
                    <div v-if="products.last_page > 1" class="mt-8 flex justify-center gap-2">
                        <button
                            v-for="page in products.last_page"
                            :key="page"
                            @click="router.get(products.path + '?page=' + page)"
                            class="px-4 py-2 text-sm uppercase tracking-wide transition-colors"
                            :class="products.current_page === page 
                                ? 'bg-white text-black' 
                                : 'bg-black border border-[#1a1a1a] text-white hover:border-white'"
                        >
                            {{ page }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
