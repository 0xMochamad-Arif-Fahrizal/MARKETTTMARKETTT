<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    product: Object,
});

const selectedImageIndex = ref(0);
const selectedVariant = ref(null);
const quantity = ref(1);
const sizeGuideOpen = ref(false);
const isTransitioning = ref(false);

const images = computed(() => props.product.images || []);
const variants = computed(() => props.product.variants || []);

const availableSizes = computed(() => {
    return [...new Set(variants.value.map(v => v.size))];
});

const availableColors = computed(() => {
    if (!selectedVariant.value) return [];
    const size = selectedVariant.value.size;
    return variants.value.filter(v => v.size === size);
});

const canGoPrev = computed(() => selectedImageIndex.value > 0);
const canGoNext = computed(() => selectedImageIndex.value < images.value.length - 1);

const goToPrevImage = () => {
    if (canGoPrev.value && !isTransitioning.value) {
        isTransitioning.value = true;
        setTimeout(() => {
            selectedImageIndex.value--;
            setTimeout(() => {
                isTransitioning.value = false;
            }, 400);
        }, 50);
    }
};

const goToNextImage = () => {
    if (canGoNext.value && !isTransitioning.value) {
        isTransitioning.value = true;
        setTimeout(() => {
            selectedImageIndex.value++;
            setTimeout(() => {
                isTransitioning.value = false;
            }, 400);
        }, 50);
    }
};

const selectSize = (size) => {
    const variant = variants.value.find(v => v.size === size && v.stock > 0);
    if (variant) {
        selectedVariant.value = variant;
    }
};

const selectColor = (variant) => {
    selectedVariant.value = variant;
};

const isOutOfStock = computed(() => {
    return !selectedVariant.value || selectedVariant.value.stock === 0;
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
};

const addToCart = () => {
    if (!selectedVariant.value || isOutOfStock.value) return;
    
    router.post('/cart/items', {
        variant_id: selectedVariant.value.id,
        quantity: quantity.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Reset quantity after adding
            quantity.value = 1;
        },
    });
};

// Auto-select first available variant
if (variants.value.length > 0) {
    selectedVariant.value = variants.value.find(v => v.stock > 0) || variants.value[0];
}
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-black">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12">
                    <!-- Image Gallery -->
                    <div>
                        <!-- Main Image with Navigation Arrows -->
                        <div class="relative aspect-[3/4] bg-[#0f0f0f] mb-4 overflow-hidden group">
                            <!-- All images stacked with fade transition -->
                            <div
                                v-for="(image, index) in images"
                                :key="image.id"
                                class="absolute inset-0 transition-opacity duration-500 ease-in-out"
                                :class="{ 'opacity-0': selectedImageIndex !== index, 'opacity-100': selectedImageIndex === index }"
                            >
                                <img
                                    :src="image.image_url"
                                    :alt="`${product.name} ${index + 1}`"
                                    class="w-full h-full object-cover"
                                />
                            </div>

                            <!-- Previous Arrow -->
                            <button
                                v-if="canGoPrev"
                                @click="goToPrevImage"
                                class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-black/50 hover:bg-black/80 text-white flex items-center justify-center transition-all duration-300 opacity-0 group-hover:opacity-100 z-10"
                                aria-label="Previous image"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>

                            <!-- Next Arrow -->
                            <button
                                v-if="canGoNext"
                                @click="goToNextImage"
                                class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-black/50 hover:bg-black/80 text-white flex items-center justify-center transition-all duration-300 opacity-0 group-hover:opacity-100 z-10"
                                aria-label="Next image"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>

                            <!-- Image Counter -->
                            <div class="absolute bottom-4 right-4 bg-black/50 text-white px-3 py-1 text-xs uppercase tracking-wide z-10">
                                {{ selectedImageIndex + 1 }} / {{ images.length }}
                            </div>
                        </div>

                        <!-- Thumbnail Row -->
                        <div v-if="images.length > 1" class="grid grid-cols-4 gap-2">
                            <button
                                v-for="(image, index) in images"
                                :key="image.id"
                                @click="selectedImageIndex = index"
                                class="aspect-square bg-[#0f0f0f] border-2 transition-colors"
                                :class="selectedImageIndex === index ? 'border-white' : 'border-[#1a1a1a] hover:border-[#999999]'"
                            >
                                <img
                                    :src="image.image_url"
                                    :alt="`${product.name} ${index + 1}`"
                                    class="w-full h-full object-cover"
                                />
                            </button>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div>
                        <!-- Category -->
                        <p v-if="product.category" class="text-xs text-[#999999] uppercase tracking-wide mb-2">
                            {{ product.category.name }}
                        </p>

                        <!-- Product Name -->
                        <h1 class="text-4xl md:text-5xl font-['OCR_A'] uppercase tracking-tight mb-4">
                            {{ product.name }}
                        </h1>

                        <!-- Price -->
                        <p class="text-2xl font-['OCR_A'] uppercase tracking-tight mb-8">
                            {{ selectedVariant ? formatPrice(selectedVariant.price) : formatPrice(product.base_price) }}
                        </p>

                        <!-- Size Selection -->
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-xs uppercase tracking-wide text-[#999999]">
                                    SELECT SIZE
                                </label>
                                <button
                                    @click="sizeGuideOpen = !sizeGuideOpen"
                                    class="text-xs uppercase tracking-wide text-white hover:text-[#999999] transition-colors"
                                >
                                    SIZE GUIDE
                                </button>
                            </div>
                            <div class="grid grid-cols-4 gap-2">
                                <button
                                    v-for="size in availableSizes"
                                    :key="size"
                                    @click="selectSize(size)"
                                    :disabled="!variants.find(v => v.size === size && v.stock > 0)"
                                    class="py-3 text-sm uppercase tracking-wide transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                                    :class="selectedVariant?.size === size 
                                        ? 'bg-white text-black' 
                                        : 'bg-black border border-[#1a1a1a] text-white hover:border-white'"
                                >
                                    {{ size }}
                                </button>
                            </div>
                        </div>

                        <!-- Size Guide Accordion -->
                        <div v-if="sizeGuideOpen" class="mb-6 p-4 bg-[#0f0f0f] border border-[#1a1a1a]">
                            <h3 class="text-sm uppercase tracking-wide mb-3">SIZE GUIDE</h3>
                            <div class="text-xs text-[#999999] space-y-2">
                                <p>XS: Chest 86-91cm</p>
                                <p>S: Chest 91-96cm</p>
                                <p>M: Chest 96-101cm</p>
                                <p>L: Chest 101-106cm</p>
                                <p>XL: Chest 106-111cm</p>
                                <p>XXL: Chest 111-116cm</p>
                            </div>
                        </div>

                        <!-- Color Selection (if multiple colors for selected size) -->
                        <div v-if="availableColors.length > 1" class="mb-6">
                            <label class="block text-xs uppercase tracking-wide text-[#999999] mb-3">
                                SELECT COLOR
                            </label>
                            <div class="flex gap-2">
                                <button
                                    v-for="variant in availableColors"
                                    :key="variant.id"
                                    @click="selectColor(variant)"
                                    :disabled="variant.stock === 0"
                                    class="w-12 h-12 border-2 transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                                    :class="selectedVariant?.id === variant.id ? 'border-white' : 'border-[#1a1a1a] hover:border-[#999999]'"
                                    :style="{ backgroundColor: variant.color_hex || '#000' }"
                                    :title="variant.color"
                                />
                            </div>
                        </div>

                        <!-- Stock Status -->
                        <div class="mb-6">
                            <p v-if="isOutOfStock" class="text-sm uppercase tracking-wide text-[#ff0000]">
                                OUT OF STOCK
                            </p>
                            <p v-else-if="selectedVariant && selectedVariant.stock < 5" class="text-sm uppercase tracking-wide text-[#ff0000]">
                                ONLY {{ selectedVariant.stock }} LEFT
                            </p>
                            <p v-else class="text-sm uppercase tracking-wide text-[#999999]">
                                IN STOCK
                            </p>
                        </div>

                        <!-- Add to Cart Button -->
                        <button
                            @click="addToCart"
                            :disabled="isOutOfStock"
                            class="w-full py-4 mb-4 uppercase tracking-wide text-sm font-medium transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                            :class="isOutOfStock 
                                ? 'bg-[#0f0f0f] border border-[#1a1a1a] text-[#999999]' 
                                : 'bg-white text-black hover:bg-[#999999]'"
                        >
                            {{ isOutOfStock ? 'OUT OF STOCK' : 'ADD TO CART' }}
                        </button>

                        <!-- Product Description -->
                        <div v-if="product.description" class="mt-8 pt-8 border-t border-[#1a1a1a]">
                            <h3 class="text-sm uppercase tracking-wide mb-4">PRODUCT DETAILS</h3>
                            <div 
                                class="text-sm text-[#999999] leading-relaxed prose prose-invert max-w-none"
                                v-html="product.description"
                            />
                        </div>

                        <!-- Product Info -->
                        <div class="mt-8 pt-8 border-t border-[#1a1a1a] space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-[#999999] uppercase tracking-wide">SKU</span>
                                <span class="uppercase tracking-wide">{{ selectedVariant?.sku || 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-[#999999] uppercase tracking-wide">WEIGHT</span>
                                <span class="uppercase tracking-wide">{{ product.weight_gram }}G</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
