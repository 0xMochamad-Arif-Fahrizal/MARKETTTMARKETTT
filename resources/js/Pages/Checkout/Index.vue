<script setup>
import { ref, computed, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

const props = defineProps({
    cart: Object,
    addresses: Array,
    midtrans_client_key: String,
});

// Step management (only 2 steps now: Address and Payment)
const currentStep = ref(1);
const steps = [
    { number: 1, title: 'ADDRESS' },
    { number: 2, title: 'PAYMENT' },
];

// Address selection
const selectedAddressId = ref(props.addresses.find(a => a.is_default)?.id || null);

// Payment
const processingPayment = ref(false);

const selectedAddress = computed(() => {
    return props.addresses.find(a => a.id === selectedAddressId.value);
});

// Free shipping - no cost
const shippingCost = 0;

const total = computed(() => {
    return props.cart.subtotal + shippingCost;
});

const canProceedToStep2 = computed(() => {
    return selectedAddressId.value !== null;
});

const selectAddress = (addressId) => {
    selectedAddressId.value = addressId;
};

const goToStep = (step) => {
    if (step === 2 && !canProceedToStep2.value) return;
    currentStep.value = step;
};

const nextStep = () => {
    if (currentStep.value === 1 && canProceedToStep2.value) {
        currentStep.value = 2;
    }
};

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--;
    }
};

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
};

const processPayment = async () => {
    if (!selectedAddressId.value) {
        alert('Please select a shipping address');
        return;
    }

    processingPayment.value = true;

    try {
        const response = await axios.post('/checkout/process', {
            address_id: selectedAddressId.value,
        });

        const { snap_token, order_number } = response.data;

        // Open Midtrans Snap
        window.snap.pay(snap_token, {
            onSuccess: function(result) {
                router.visit(`/checkout/success/${order_number}`);
            },
            onPending: function(result) {
                router.visit(`/checkout/success/${order_number}`);
            },
            onError: function(result) {
                alert('Payment failed. Please try again.');
                processingPayment.value = false;
            },
            onClose: function() {
                processingPayment.value = false;
            }
        });

    } catch (error) {
        alert(error.response?.data?.error || 'An error occurred');
        processingPayment.value = false;
    }
};

// Load Midtrans Snap script
onMounted(() => {
    const script = document.createElement('script');
    script.src = props.midtrans_client_key.startsWith('SB-') 
        ? 'https://app.sandbox.midtrans.com/snap/snap.js'
        : 'https://app.midtrans.com/snap/snap.js';
    script.setAttribute('data-client-key', props.midtrans_client_key);
    document.head.appendChild(script);
});

</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-black text-white">
            <!-- Header -->
            <div>
                <div class="max-w-7xl mx-auto px-4 py-6">
                    <h1 class="font-['OCR_A'] text-4xl uppercase tracking-tight">CHECKOUT</h1>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 py-8">
                <!-- Progress Indicator -->
                <div class="mb-8">
                    <div class="flex items-center justify-center md:justify-start">
                        <div v-for="(step, index) in steps" :key="step.number" class="flex items-center">
                            <!-- Step Circle -->
                            <div class="flex flex-col items-center">
                                <div 
                                    class="w-10 h-10 flex items-center justify-center border-2 transition-colors"
                                    :class="{
                                        'border-white bg-white text-black': currentStep === step.number,
                                        'border-[#999999] text-[#999999]': currentStep > step.number,
                                        'border-[#333333] text-[#333333]': currentStep < step.number
                                    }"
                                >
                                    <span class="font-['OCR_A'] text-lg">{{ step.number }}</span>
                                </div>
                                <span 
                                    class="mt-2 text-xs uppercase tracking-wide font-['OCR_A']"
                                    :class="{
                                        'text-white': currentStep === step.number,
                                        'text-[#999999]': currentStep > step.number,
                                        'text-[#333333]': currentStep < step.number
                                    }"
                                >
                                    {{ step.title }}
                                </span>
                            </div>

                            <!-- Connector Line -->
                            <div 
                                v-if="index < steps.length - 1" 
                                class="w-16 md:w-24 h-0.5 mx-2 transition-colors"
                                :class="{
                                    'bg-[#999999]': currentStep > step.number,
                                    'bg-[#333333]': currentStep <= step.number
                                }"
                            ></div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column: Steps Content -->
                    <div class="lg:col-span-2">
                        <!-- Step 1: Address Selection -->
                        <div v-show="currentStep === 1" class="bg-[#0f0f0f] border border-[#1a1a1a]">
                            <div class="p-6 border-b border-[#1a1a1a]">
                                <h2 class="font-['OCR_A'] text-2xl uppercase tracking-tight">SELECT SHIPPING ADDRESS</h2>
                            </div>
                            
                            <div class="p-6">
                                <div v-if="addresses.length === 0" class="text-center py-8">
                                    <p class="text-[#999999] mb-4 uppercase tracking-wide text-sm">YOU HAVE NO SAVED ADDRESSES</p>
                                    <a href="/profile/addresses" class="inline-block bg-white text-black px-8 py-4 uppercase font-['OCR_A'] text-lg tracking-tight hover:bg-[#f0f0f0] transition-colors">
                                        ADD ADDRESS
                                    </a>
                                </div>

                                <div v-else class="space-y-4">
                                    <div
                                        v-for="address in addresses"
                                        :key="address.id"
                                        @click="selectAddress(address.id)"
                                        class="border p-4 cursor-pointer transition-all"
                                        :class="selectedAddressId === address.id ? 'border-white bg-white text-black' : 'border-[#333333] bg-black hover:border-white'"
                                    >
                                        <div class="flex items-start justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-2">
                                                    <span class="font-['OCR_A'] text-lg uppercase tracking-tight" :class="selectedAddressId === address.id ? 'text-black' : 'text-white'">{{ address.label }}</span>
                                                    <span v-if="address.is_default" class="text-xs px-2 py-1 bg-[#ff0000] text-white uppercase tracking-wide">DEFAULT</span>
                                                </div>
                                                <p class="font-medium mb-1" :class="selectedAddressId === address.id ? 'text-black' : 'text-white'">{{ address.recipient_name }}</p>
                                                <p class="text-sm" :class="selectedAddressId === address.id ? 'text-black/70' : 'text-[#999999]'">
                                                    {{ address.phone }}
                                                </p>
                                                <p class="text-sm mt-2" :class="selectedAddressId === address.id ? 'text-black/70' : 'text-[#999999]'">
                                                    {{ address.address_line }}<br>
                                                    {{ address.city }}, {{ address.province }} {{ address.postal_code }}
                                                </p>
                                            </div>
                                            <div v-if="selectedAddressId === address.id" class="ml-4">
                                                <svg class="w-6 h-6 text-black" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-6 flex gap-4">
                                    <button
                                        @click="nextStep"
                                        :disabled="!canProceedToStep2"
                                        class="flex-1 bg-white text-black py-4 uppercase font-['OCR_A'] text-xl tracking-tight transition-colors disabled:opacity-30 disabled:cursor-not-allowed hover:bg-[#f0f0f0]"
                                    >
                                        CONTINUE
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Payment -->
                        <div v-show="currentStep === 2" class="bg-[#0f0f0f] border border-[#1a1a1a]">
                            <div class="p-6 border-b border-[#1a1a1a]">
                                <h2 class="font-['OCR_A'] text-2xl uppercase tracking-tight">CONFIRM & PAY</h2>
                            </div>
                            
                            <div class="p-6 space-y-6">
                                <!-- Address Summary -->
                                <div v-if="selectedAddress">
                                    <h3 class="font-['OCR_A'] text-lg uppercase tracking-tight mb-3">SHIPPING ADDRESS</h3>
                                    <div class="bg-black border border-[#333333] p-4">
                                        <p class="font-medium">{{ selectedAddress.recipient_name }}</p>
                                        <p class="text-sm text-[#999999] mt-1">{{ selectedAddress.phone }}</p>
                                        <p class="text-sm text-[#999999] mt-2">
                                            {{ selectedAddress.address_line }}<br>
                                            {{ selectedAddress.city }}, {{ selectedAddress.province }} {{ selectedAddress.postal_code }}
                                        </p>
                                    </div>
                                </div>

                                <!-- Shipping Info -->
                                <div>
                                    <h3 class="font-['OCR_A'] text-lg uppercase tracking-tight mb-3">SHIPPING</h3>
                                    <div class="bg-black border border-[#333333] p-4">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <p class="font-['OCR_A'] uppercase tracking-tight">FREE SHIPPING</p>
                                                <p class="text-sm text-[#999999] uppercase tracking-wide">ESTIMATED: 3-5 BUSINESS DAYS</p>
                                            </div>
                                            <p class="font-['OCR_A'] text-xl">FREE</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-4">
                                    <button
                                        @click="prevStep"
                                        class="flex-1 border border-white text-white py-4 uppercase font-['OCR_A'] text-xl tracking-tight hover:bg-white hover:text-black transition-colors"
                                    >
                                        BACK
                                    </button>
                                    <button
                                        @click="processPayment"
                                        :disabled="processingPayment"
                                        class="flex-1 bg-white text-black py-4 uppercase font-['OCR_A'] text-xl tracking-tight transition-colors disabled:opacity-50 disabled:cursor-not-allowed hover:bg-[#f0f0f0]"
                                    >
                                        {{ processingPayment ? 'PROCESSING...' : 'PAY NOW' }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Order Summary (Sticky) -->
                    <div class="lg:col-span-1">
                        <div class="bg-[#0f0f0f] border border-[#1a1a1a] lg:sticky lg:top-4">
                            <div class="p-6 border-b border-[#1a1a1a]">
                                <h2 class="font-['OCR_A'] text-2xl uppercase tracking-tight">ORDER SUMMARY</h2>
                            </div>
                            
                            <div class="p-6 space-y-4">
                                <!-- Cart Items -->
                                <div class="space-y-3 max-h-64 overflow-y-auto">
                                    <div v-for="item in cart.items" :key="item.id" class="flex gap-3 text-sm">
                                        <img 
                                            v-if="item.variant.product.images[0]"
                                            :src="item.variant.product.images[0].image_url" 
                                            :alt="item.variant.product.name"
                                            class="w-16 h-16 object-cover bg-[#1a1a1a]"
                                        >
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium truncate uppercase tracking-tight">{{ item.variant.product.name }}</p>
                                            <p class="text-[#999999] text-xs uppercase tracking-wide">
                                                {{ item.variant.size }}
                                                <span v-if="item.variant.color"> • {{ item.variant.color }}</span>
                                            </p>
                                            <p class="text-[#999999] text-xs uppercase tracking-wide">QTY: {{ item.quantity }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-medium">{{ formatPrice(item.price_snapshot * item.quantity) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="border-t border-[#1a1a1a] pt-4 space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-[#999999] uppercase tracking-wide">SUBTOTAL</span>
                                        <span>{{ formatPrice(cart.subtotal) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-[#999999] uppercase tracking-wide">SHIPPING</span>
                                        <span class="text-[#00ff00]">FREE</span>
                                    </div>
                                </div>

                                <div class="border-t border-[#1a1a1a] pt-4">
                                    <div class="flex justify-between items-center">
                                        <span class="font-['OCR_A'] text-xl uppercase tracking-tight">TOTAL</span>
                                        <span class="font-['OCR_A'] text-2xl">{{ formatPrice(total) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
