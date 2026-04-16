<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

const props = defineProps({
    order: Object,
    midtrans_client_key: String,
});

const now = ref(Date.now());
const processingPayment = ref(false);

// Update current time every second for countdown
let interval;
onMounted(() => {
    interval = setInterval(() => {
        now.value = Date.now();
    }, 1000);

    // Load Midtrans Snap script if pending payment
    if (props.order.status === 'pending_payment') {
        const script = document.createElement('script');
        script.src = props.midtrans_client_key.startsWith('SB-') 
            ? 'https://app.sandbox.midtrans.com/snap/snap.js'
            : 'https://app.midtrans.com/snap/snap.js';
        script.setAttribute('data-client-key', props.midtrans_client_key);
        document.head.appendChild(script);
    }
});

onUnmounted(() => {
    if (interval) {
        clearInterval(interval);
    }
});

const formatPrice = (price) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(price);
};

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getStatusBadge = (status) => {
    const badges = {
        'pending_payment': { text: 'Pending Payment', class: 'bg-[#92400E] text-[#FEF3C7]' },
        'paid': { text: 'Paid', class: 'bg-[#1e3a5f] text-[#93C5FD]' },
        'processing': { text: 'Processing', class: 'bg-[#3b0764] text-[#DDD6FE]' },
        'shipped': { text: 'Shipped', class: 'bg-[#14532d] text-[#86EFAC]' },
        'delivered': { text: 'Delivered', class: 'bg-[#166534] text-[#DCFCE7]' },
        'cancelled': { text: 'Cancelled', class: 'bg-[#7f1d1d] text-[#FECACA]' },
        'payment_failed': { text: 'Payment Failed', class: 'bg-[#7f1d1d] text-[#FECACA]' },
    };
    return badges[status] || { text: status, class: 'bg-[#999999] text-white' };
};

const getPaymentExpiry = (createdAt) => {
    const created = new Date(createdAt).getTime();
    const expiry = created + (24 * 60 * 60 * 1000);
    return expiry;
};

const getTimeRemaining = (createdAt) => {
    const expiry = getPaymentExpiry(createdAt);
    const remaining = expiry - now.value;
    
    if (remaining <= 0) {
        return { expired: true, text: 'Expired', warning: false };
    }

    const hours = Math.floor(remaining / (1000 * 60 * 60));
    const minutes = Math.floor((remaining % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((remaining % (1000 * 60)) / 1000);

    const warning = remaining < (5 * 60 * 1000);

    return {
        expired: false,
        text: `${hours}j ${minutes}m ${seconds}d`,
        warning: warning,
    };
};

const retryPayment = async () => {
    if (processingPayment.value) return;

    processingPayment.value = true;

    try {
        const response = await axios.post(`/orders/${props.order.order_number}/retry-payment`);
        const { snap_token } = response.data;

        window.snap.pay(snap_token, {
            onSuccess: function(result) {
                router.reload();
            },
            onPending: function(result) {
                router.reload();
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
        alert(error.response?.data?.error || 'Terjadi kesalahan');
        processingPayment.value = false;
    }
};

const cancelOrder = () => {
    if (confirm('Cancel this order?')) {
        router.post(`/orders/${props.order.order_number}/cancel`);
    }
};
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-black text-white">
            <!-- Header -->
            <div>
                <div class="max-w-7xl mx-auto px-4 py-6">
                    <Link href="/orders" class="text-[#999999] hover:text-white transition-colors uppercase tracking-wide text-sm mb-2 inline-block">
                        ← Back to Orders
                    </Link>
                    <h1 class="font-['OCR_A'] text-4xl uppercase tracking-tight">Order Details</h1>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 py-8">
                <!-- Countdown Timer for Pending Payment -->
                <div v-if="order.status === 'pending_payment'" class="bg-[#0f0f0f] border border-[#222222] p-8 mb-6 text-center">
                    <p class="text-[#999999] uppercase tracking-wide text-sm mb-4">Complete Payment Within</p>
                    <div 
                        class="font-['OCR_A'] text-6xl tracking-tight mb-6"
                        :class="getTimeRemaining(order.created_at).warning ? 'text-[#ff0000]' : 'text-white'"
                    >
                        {{ getTimeRemaining(order.created_at).text }}
                    </div>
                    <div class="flex gap-4 max-w-md mx-auto">
                        <button
                            @click="cancelOrder"
                            class="flex-1 border border-[#ff0000] text-[#ff0000] py-4 uppercase font-['OCR_A'] text-xl tracking-tight hover:bg-[#ff0000] hover:text-white transition-colors"
                        >
                            Cancel
                        </button>
                        <button
                            @click="retryPayment"
                            :disabled="processingPayment || getTimeRemaining(order.created_at).expired"
                            class="flex-1 bg-white text-black py-4 uppercase font-['OCR_A'] text-xl tracking-tight hover:bg-[#f0f0f0] transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                        >
                            {{ getTimeRemaining(order.created_at).expired ? 'Expired' : 'Continue Payment' }}
                        </button>
                    </div>
                </div>

                <!-- Order Details Card -->
                <div class="bg-[#0f0f0f] border border-[#222222] mb-6">
                    <div class="p-6 border-b border-[#1a1a1a]">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <p class="text-[#999999] text-sm uppercase tracking-wide mb-1">Order Number</p>
                                <p class="font-['OCR_A'] text-3xl uppercase tracking-wide">{{ order.order_number }}</p>
                            </div>
                            <div>
                                <span 
                                    class="inline-block px-4 py-2 text-sm uppercase tracking-wide"
                                    :class="getStatusBadge(order.status).class"
                                >
                                    {{ getStatusBadge(order.status).text }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 space-y-6">
                        <!-- Order Info -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="font-['OCR_A'] text-lg uppercase tracking-tight mb-3">Shipping Address</h3>
                                <div class="text-sm space-y-1">
                                    <p class="font-medium">{{ order.shipping_address_snapshot.recipient_name }}</p>
                                    <p class="text-[#999999]">{{ order.shipping_address_snapshot.phone }}</p>
                                    <p class="text-[#999999] mt-2">
                                        {{ order.shipping_address_snapshot.address_line }}<br>
                                        {{ order.shipping_address_snapshot.city }}, {{ order.shipping_address_snapshot.province }} {{ order.shipping_address_snapshot.postal_code }}
                                    </p>
                                </div>
                            </div>

                            <div>
                                <h3 class="font-['OCR_A'] text-lg uppercase tracking-tight mb-3">Shipping</h3>
                                <div class="text-sm space-y-1">
                                    <p class="font-medium">{{ order.shipping_courier }}</p>
                                    <p class="text-[#999999]">Cost: {{ formatPrice(order.shipping_cost) }}</p>
                                    <p v-if="order.tracking_number" class="text-[#999999] mt-2">
                                        Tracking: <span class="font-['OCR_A'] text-[#999999]">{{ order.tracking_number }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div>
                            <h3 class="font-['OCR_A'] text-lg uppercase tracking-tight mb-3">Order Items</h3>
                            <div class="space-y-3">
                                <div v-for="item in order.items" :key="item.id" class="flex gap-4 pb-3 border-b border-[#1a1a1a] last:border-0">
                                    <img 
                                        v-if="item.variant.product.images[0]"
                                        :src="item.variant.product.images[0].image_url" 
                                        :alt="item.variant.product.name"
                                        class="w-[60px] h-[60px] object-cover bg-[#1a1a1a]"
                                    >
                                    <div class="flex-1">
                                        <p class="font-medium uppercase tracking-tight">{{ item.variant.product.name }}</p>
                                        <p class="text-sm text-[#999999] uppercase tracking-wide">
                                            {{ item.variant.size }}
                                            <span v-if="item.variant.color"> • {{ item.variant.color }}</span>
                                        </p>
                                        <p class="text-sm text-[#999999] mt-1">Qty: {{ item.quantity }} × {{ formatPrice(item.unit_price) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-['OCR_A'] text-xl">{{ formatPrice(item.unit_price * item.quantity) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="border-t border-[#1a1a1a] pt-4">
                            <div class="space-y-2 mb-4">
                                <div class="flex justify-between text-sm">
                                    <span class="text-[#999999] uppercase tracking-wide">Subtotal</span>
                                    <span>{{ formatPrice(order.subtotal) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-[#999999] uppercase tracking-wide">Shipping</span>
                                    <span>{{ formatPrice(order.shipping_cost) }}</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center pt-4 border-t border-[#1a1a1a]">
                                <span class="font-['OCR_A'] text-2xl uppercase tracking-tight">Total</span>
                                <span class="font-['OCR_A'] text-3xl">{{ formatPrice(order.total) }}</span>
                            </div>
                        </div>

                        <!-- Order Date -->
                        <div class="text-center text-sm text-[#999999] uppercase tracking-wide">
                            Created on {{ formatDate(order.created_at) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
