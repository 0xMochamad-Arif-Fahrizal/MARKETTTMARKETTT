<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

const props = defineProps({
    orders: Array,
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

    // Load Midtrans Snap script
    const script = document.createElement('script');
    script.src = props.midtrans_client_key.startsWith('SB-') 
        ? 'https://app.sandbox.midtrans.com/snap/snap.js'
        : 'https://app.midtrans.com/snap/snap.js';
    script.setAttribute('data-client-key', props.midtrans_client_key);
    document.head.appendChild(script);
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
        month: 'short',
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
    // Payment expires 24 hours after order creation
    const created = new Date(createdAt).getTime();
    const expiry = created + (24 * 60 * 60 * 1000); // 24 hours
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

    const warning = remaining < (5 * 60 * 1000); // Warning if less than 5 minutes

    return {
        expired: false,
        text: `${hours}j ${minutes}m ${seconds}d`,
        warning: warning,
    };
};

const retryPayment = async (orderNumber) => {
    if (processingPayment.value) return;

    processingPayment.value = true;

    try {
        const response = await axios.post(`/orders/${orderNumber}/retry-payment`);
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
        alert(error.response?.data?.error || 'An error occurred');
        processingPayment.value = false;
    }
};

const cancelOrder = (orderNumber) => {
    if (confirm('Cancel this order?')) {
        router.post(`/orders/${orderNumber}/cancel`);
    }
};

const pendingOrders = computed(() => {
    return props.orders.filter(o => o.status === 'pending_payment');
});

const otherOrders = computed(() => {
    return props.orders.filter(o => o.status !== 'pending_payment');
});
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-black text-white">
            <!-- Header -->
            <div class="border-b border-[#1a1a1a]">
                <div class="max-w-7xl mx-auto px-4 py-6">
                    <h1 class="font-['OCR_A'] text-4xl uppercase tracking-tight">My Orders</h1>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 py-8">
                <!-- Empty State -->
                <div v-if="orders.length === 0" class="text-center py-16">
                    <div class="mb-6">
                        <svg class="w-24 h-24 mx-auto text-[#333333]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="square" stroke-linejoin="miter" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <p class="text-[#999999] text-lg uppercase tracking-wide mb-6">No orders yet</p>
                    <Link 
                        href="/products" 
                        class="inline-block bg-white text-black px-8 py-4 uppercase font-['OCR_A'] text-xl tracking-tight hover:bg-[#f0f0f0] transition-colors"
                    >
                        Start Shopping
                    </Link>
                </div>

                <!-- Orders List -->
                <div v-else class="space-y-8">
                    <!-- Pending Payment Orders -->
                    <div v-if="pendingOrders.length > 0">
                        <h2 class="font-['OCR_A'] text-2xl uppercase tracking-tight mb-4">Pending Payment</h2>
                        <div class="space-y-4">
                            <div
                                v-for="order in pendingOrders"
                                :key="order.id"
                                class="bg-[#0f0f0f] border border-[#222222] hover:border-[#333333] transition-colors"
                            >
                                <!-- Order Header -->
                                <div class="p-6 border-b border-[#1a1a1a]">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                        <div>
                                            <p class="text-[#999999] text-xs uppercase tracking-wide mb-1">Order Number</p>
                                            <Link 
                                                :href="`/orders/${order.order_number}`"
                                                class="font-['OCR_A'] text-2xl uppercase tracking-wide hover:text-[#999999] transition-colors"
                                            >
                                                {{ order.order_number }}
                                            </Link>
                                            <p class="text-[#999999] text-sm mt-1">{{ formatDate(order.created_at) }}</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-[#999999] text-xs uppercase tracking-wide mb-2">Time Remaining</p>
                                            <div 
                                                class="font-['OCR_A'] text-3xl tracking-tight"
                                                :class="getTimeRemaining(order.created_at).warning ? 'text-[#ff0000]' : 'text-white'"
                                            >
                                                {{ getTimeRemaining(order.created_at).text }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Items -->
                                <div class="p-6 border-b border-[#1a1a1a]">
                                    <div class="space-y-3">
                                        <div 
                                            v-for="item in order.items.slice(0, 2)" 
                                            :key="item.id" 
                                            class="flex gap-4"
                                        >
                                            <img 
                                                v-if="item.variant.product.images[0]"
                                                :src="item.variant.product.images[0].image_url" 
                                                :alt="item.variant.product.name"
                                                class="w-16 h-16 object-cover bg-[#1a1a1a]"
                                            >
                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium uppercase tracking-tight truncate">{{ item.variant.product.name }}</p>
                                                <p class="text-sm text-[#999999] uppercase tracking-wide">
                                                    {{ item.variant.size }}
                                                    <span v-if="item.variant.color"> • {{ item.variant.color }}</span>
                                                </p>
                                                <p class="text-sm text-[#999999]">Qty: {{ item.quantity }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-medium">{{ formatPrice(item.unit_price * item.quantity) }}</p>
                                            </div>
                                        </div>
                                        <p v-if="order.items.length > 2" class="text-sm text-[#999999] uppercase tracking-wide">
                                            +{{ order.items.length - 2 }} more items
                                        </p>
                                    </div>
                                </div>

                                <!-- Order Footer -->
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="text-[#999999] uppercase tracking-wide text-sm">Total Payment</span>
                                        <span class="font-['OCR_A'] text-2xl">{{ formatPrice(order.total) }}</span>
                                    </div>
                                    <div class="flex gap-4">
                                        <button
                                            @click="cancelOrder(order.order_number)"
                                            class="flex-1 border border-[#ff0000] text-[#ff0000] py-3 uppercase font-['OCR_A'] text-lg tracking-tight hover:bg-[#ff0000] hover:text-white transition-colors"
                                        >
                                            Cancel
                                        </button>
                                        <button
                                            @click="retryPayment(order.order_number)"
                                            :disabled="processingPayment || getTimeRemaining(order.created_at).expired"
                                            class="flex-1 bg-white text-black py-3 uppercase font-['OCR_A'] text-lg tracking-tight hover:bg-[#f0f0f0] transition-colors disabled:opacity-30 disabled:cursor-not-allowed"
                                        >
                                            {{ getTimeRemaining(order.created_at).expired ? 'Expired' : 'Continue Payment' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Other Orders -->
                    <div v-if="otherOrders.length > 0">
                        <h2 class="font-['OCR_A'] text-2xl uppercase tracking-tight mb-4">Order History</h2>
                        <div class="space-y-4">
                            <div
                                v-for="order in otherOrders"
                                :key="order.id"
                                class="bg-[#0f0f0f] border border-[#222222] hover:border-[#333333] transition-colors"
                            >
                                <!-- Order Header -->
                                <div class="p-6 border-b border-[#1a1a1a]">
                                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                                        <div>
                                            <p class="text-[#999999] text-xs uppercase tracking-wide mb-1">Order Number</p>
                                            <Link 
                                                :href="`/orders/${order.order_number}`"
                                                class="font-['OCR_A'] text-2xl uppercase tracking-wide hover:text-[#999999] transition-colors"
                                            >
                                                {{ order.order_number }}
                                            </Link>
                                            <p class="text-[#999999] text-sm mt-1">{{ formatDate(order.created_at) }}</p>
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

                                <!-- Order Items -->
                                <div class="p-6 border-b border-[#1a1a1a]">
                                    <div class="space-y-3">
                                        <div 
                                            v-for="item in order.items.slice(0, 2)" 
                                            :key="item.id" 
                                            class="flex gap-4"
                                        >
                                            <img 
                                                v-if="item.variant.product.images[0]"
                                                :src="item.variant.product.images[0].image_url" 
                                                :alt="item.variant.product.name"
                                                class="w-16 h-16 object-cover bg-[#1a1a1a]"
                                            >
                                            <div class="flex-1 min-w-0">
                                                <p class="font-medium uppercase tracking-tight truncate">{{ item.variant.product.name }}</p>
                                                <p class="text-sm text-[#999999] uppercase tracking-wide">
                                                    {{ item.variant.size }}
                                                    <span v-if="item.variant.color"> • {{ item.variant.color }}</span>
                                                </p>
                                                <p class="text-sm text-[#999999]">Qty: {{ item.quantity }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-medium">{{ formatPrice(item.unit_price * item.quantity) }}</p>
                                            </div>
                                        </div>
                                        <p v-if="order.items.length > 2" class="text-sm text-[#999999] uppercase tracking-wide">
                                            +{{ order.items.length - 2 }} more items
                                        </p>
                                    </div>
                                </div>

                                <!-- Order Footer -->
                                <div class="p-6">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[#999999] uppercase tracking-wide text-sm">Total Payment</span>
                                        <span class="font-['OCR_A'] text-2xl">{{ formatPrice(order.total) }}</span>
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
