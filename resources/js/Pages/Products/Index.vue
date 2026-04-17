<script setup>
import { ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import Sidebar from '@/Components/Sidebar.vue';
import CategoryList from '@/Components/CategoryList.vue';
import ShippingPolicyModal from '@/Components/ShippingPolicyModal.vue';
import LoadingScreen from '@/Components/LoadingScreen.vue';

const props = defineProps({
    products: Object,
    categories: Array,
    selectedCategory: Object,
    filters: Object,
});

const mobileMenuOpen = ref(false);
const showShippingModal = ref(false);
const showLoading = ref(false);
const contentVisible = ref(false);

const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
};

const handleLoadingComplete = () => {
    contentVisible.value = true;
};

onMounted(() => {
    // Check if user just logged in or signed up (from session or URL param)
    const urlParams = new URLSearchParams(window.location.search);
    const fromAuth = urlParams.get('from_auth') === 'true' || sessionStorage.getItem('show_loading') === 'true';
    
    if (fromAuth) {
        showLoading.value = true;
        sessionStorage.removeItem('show_loading');
        
        // Remove from_auth param from URL
        if (urlParams.has('from_auth')) {
            urlParams.delete('from_auth');
            const newUrl = window.location.pathname + (urlParams.toString() ? '?' + urlParams.toString() : '');
            window.history.replaceState({}, '', newUrl);
        }
    } else {
        contentVisible.value = true;
    }
});
</script>

<template>
    <LoadingScreen v-if="showLoading" @loaded="handleLoadingComplete" />
    
    <Transition
        enter-active-class="transition-opacity duration-1000"
        enter-from-class="opacity-0"
        enter-to-class="opacity-100"
    >
        <AppLayout v-if="contentVisible" @toggle-mobile-menu="toggleMobileMenu" :mobile-menu-open="mobileMenuOpen">
            <div class="min-h-screen bg-black flex">
            <!-- Sidebar - Desktop -->
            <Sidebar 
                :categories="categories"
                :selected-category-slug="filters?.category || null"
                :is-open="false"
                class="hidden lg:flex"
            />
            
            <!-- Sidebar - Mobile Overlay -->
            <Sidebar 
                v-if="mobileMenuOpen"
                :categories="categories"
                :selected-category-slug="filters?.category || null"
                :is-open="true"
                @close="mobileMenuOpen = false"
                class="lg:hidden"
            />
            
            <!-- Main Content -->
            <div class="flex-1 lg:ml-60 w-full">
                <!-- Header -->
                <div v-if="selectedCategory">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                        <h1 class="text-4xl md:text-5xl font-['OCR_A'] uppercase tracking-tight mb-2">
                            {{ selectedCategory.name }}
                        </h1>
                        <div class="flex items-center justify-between">
                            <p class="text-sm text-[#999999] uppercase tracking-wide">
                                {{ products.total }} PRODUCTS
                            </p>
                            <button 
                                @click="router.get('/products')"
                                class="text-sm text-[#CCFF00] hover:text-white uppercase tracking-wide transition-colors"
                            >
                                CLEAR FILTER
                            </button>
                        </div>
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
    </Transition>
</template>
