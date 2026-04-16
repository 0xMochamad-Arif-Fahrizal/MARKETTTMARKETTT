<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
    selectedSlug: {
        type: String,
        default: null,
    },
});

const emit = defineEmits(['category-click']);

const handleCategoryClick = (category) => {
    if (category.is_active) {
        emit('category-click', category);
    }
};
</script>

<template>
    <nav class="space-y-2">
        <div
            v-for="category in categories"
            :key="category.id"
            class="font-['OCR_A'] text-sm uppercase tracking-wide"
        >
            <Link
                v-if="category.is_active"
                :href="`/products?category=${category.slug}`"
                class="block py-2 px-3 transition-colors duration-200"
                :class="{
                    'bg-[#CCFF00] text-black': selectedSlug === category.slug,
                    'text-white hover:text-[#CCFF00] hover:bg-[#CCFF00]/10': selectedSlug !== category.slug
                }"
                @click="handleCategoryClick(category)"
            >
                {{ category.name }}
            </Link>
            <span
                v-else
                class="block py-2 px-3 text-[#666666] line-through cursor-default"
            >
                {{ category.name }}
            </span>
        </div>
    </nav>
</template>
