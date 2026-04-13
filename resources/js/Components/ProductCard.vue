<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    product: Object,
});

const currentImageIndex = ref(0);
const isHovering = ref(false);

const images = computed(() => props.product.images || []);
const primaryImage = computed(() => images.value[0]?.image_url || '');
const secondaryImage = computed(() => images.value[1]?.image_url || primaryImage.value);

const displayImage = computed(() => {
    return isHovering.value && images.value.length > 1 ? secondaryImage.value : primaryImage.value;
});

const minPrice = computed(() => {
    if (!props.product.variants || props.product.variants.length === 0) {
        return props.product.base_price;
    }
    return Math.min(...props.product.variants.map(v => v.price));
});

const isOutOfStock = computed(() => {
    if (!props.product.variants || props.product.variants.length === 0) return true;
    return props.product.variants.every(v => v.stock === 0);
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
    <Link 
        :href="`/products/${product.slug}`"
        class="group block bg-black"
        @mouseenter="isHovering = true"
        @mouseleave="isHovering = false"
    >
        <!-- Image Container -->
        <div class="relative aspect-[3/4] bg-black overflow-hidden">
            <img
                v-if="displayImage"
                :src="displayImage"
                :alt="product.name"
                class="w-full h-full object-cover transition-opacity duration-300"
                :class="{ 'opacity-0': !displayImage }"
            />
            
            <!-- Out of Stock Badge -->
            <div 
                v-if="isOutOfStock"
                class="absolute top-0 left-0 right-0 bg-[#ff0000] text-white text-xs uppercase tracking-wide py-2 text-center font-medium"
            >
                SOLD OUT
            </div>
        </div>

        <!-- Product Info -->
        <div class="pt-3 pb-4 px-3 bg-black">
            <h3 class="font-heading text-sm uppercase tracking-tight text-white mb-1 line-clamp-1">
                {{ product.name }}
            </h3>
            <p class="text-xs text-[#999999] uppercase tracking-wide">
                {{ formatPrice(minPrice) }}
            </p>
        </div>
    </Link>
</template>
