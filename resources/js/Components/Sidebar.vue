<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
    selectedCategorySlug: {
        type: String,
        default: null,
    },
    isOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close']);

const showShippingModal = ref(false);

</script>

<template>
    <!-- Backdrop for mobile overlay -->
    <div
        v-if="isOpen"
        @click="emit('close')"
        class="lg:hidden fixed inset-0 bg-black z-50"
    ></div>

    <!-- Sidebar Container -->
    <aside 
        class="fixed left-0 top-0 h-screen bg-black flex flex-col p-6 transition-transform duration-300 border-r border-[#1a1a1a]"
        :class="{
            'translate-x-0 z-50 w-full': isOpen,
            '-translate-x-full lg:translate-x-0 lg:z-40 lg:w-60': !isOpen,
            'hidden lg:flex lg:w-60': !isOpen
        }"
    >
        <!-- Logo Section - Always visible on desktop, visible when open on mobile -->
        <div class="mb-8">
            <Link href="/products" class="block">
                <img 
                    src="/images/logo.png" 
                    alt="MARKETTTMARKETTT" 
                    class="h-12 w-auto cursor-pointer"
                />
            </Link>
        </div>

        <!-- Category List Slot -->
        <nav class="flex-1 overflow-y-auto">
            <slot name="categories" :categories="categories" :selected-slug="selectedCategorySlug">
                <!-- Default category rendering if no slot provided -->
                <div class="space-y-2">
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
                                'bg-[#CCFF00] text-black': selectedCategorySlug === category.slug,
                                'text-white hover:text-[#CCFF00] hover:bg-[#CCFF00]/10': selectedCategorySlug !== category.slug
                            }"
                            @click="emit('close')"
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
                </div>
            </slot>
        </nav>

        <!-- Instagram Link -->
        <div class="mt-6 mb-4">
            <a
                href="https://instagram.com/marketttmarkettt"
                target="_blank"
                rel="noopener noreferrer"
                class="flex items-center gap-2 text-white hover:text-[#CCFF00] transition-colors duration-200 font-['OCR_A'] text-sm uppercase"
            >
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                </svg>
                <span>INSTAGRAM</span>
            </a>
        </div>

        <!-- Shipping Policy Button -->
        <div class="mb-4">
            <button
                @click="showShippingModal = true"
                class="w-full text-left text-white hover:text-[#CCFF00] transition-colors duration-200 font-['OCR_A'] text-sm uppercase tracking-wide"
            >
                SHIPPING POLICY
            </button>
        </div>
    </aside>

    <!-- Shipping Policy Modal Placeholder -->
    <!-- This will be implemented in a subsequent task -->
    <Teleport to="body" v-if="showShippingModal">
        <div class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4" @click="showShippingModal = false">
            <div class="bg-black border-2 border-white max-w-2xl w-full p-8" @click.stop>
                <h2 class="text-white font-['OCR_A'] text-2xl uppercase mb-6">SHIPPING POLICY</h2>
                <div class="text-white font-['OCR_A'] text-sm space-y-4 uppercase">
                    <p>WE ONLY SHIP WITHIN INDONESIA</p>
                    <p>ALL ORDERS ARE PROCESSED AND SHIPPED WITHIN 5–10 WORKING DAYS</p>
                    <p>UNLESS A PRE-ORDER SHIP DATE IS SPECIFIED</p>
                    <p>WE DO NOT ACCEPT ORDERS FROM OUTSIDE INDONESIA</p>
                    <p>PAYMENTS CANNOT BE COMPLETED BY CUSTOMERS LOCATED OUTSIDE INDONESIA</p>
                    <p>ANY ORDERS IDENTIFIED AS BEING FROM OUTSIDE INDONESIA WILL BE AUTOMATICALLY CANCELLED AND NOT PROCESSED</p>
                    <p>SHIPPING WILL NOT BE PROCESSED FOR ANY DESTINATIONS OUTSIDE INDONESIA</p>
                    <p>IF AN INVALID ORDER IS PLACED FROM OUTSIDE INDONESIA, ANY ASSOCIATED COSTS OR FEES WILL BE THE RESPONSIBILITY OF THE CUSTOMER</p>
                    <p>PLEASE REFER TO OUR TERMS OF SALE FOR FURTHER INFORMATION</p>
                </div>
                <button
                    @click="showShippingModal = false"
                    class="mt-6 bg-[#CCFF00] text-black px-6 py-2 font-['OCR_A'] uppercase hover:bg-[#CCFF00]/80 transition-colors duration-200"
                >
                    CLOSE
                </button>
            </div>
        </div>
    </Teleport>
</template>
