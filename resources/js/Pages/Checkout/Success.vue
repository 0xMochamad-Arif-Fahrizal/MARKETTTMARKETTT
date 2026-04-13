<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    order: Object,
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
        'pending_payment': { text: 'Pending Payment', class: 'bg-[#999999]' },
        'paid': { text: 'Paid', class: 'bg-[#00ff00] text-black' },
        'processing': { text: 'Processing', class: 'bg-[#ffff00] text-black' },
        'shipped': { text: 'Shipped', class: 'bg-[#00ffff] text-black' },
        'delivered': { text: 'Delivered', class: 'bg-[#00ff00] text-black' },
        'cancelled': { text: 'Cancelled', class: 'bg-[#ff0000]' },
        'payment_failed': { text: 'Payment Failed', class: 'bg-[#ff0000]' },
    };
    return badges[status] || { text: status, class: 'bg-[#999999]' };
};
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-black text-white">
            <div class="max-w-4xl mx-auto px-4 py-12">
                <!-- Success Icon -->
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-20 h-20 border-4 border-white mb-4">
                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="square" stroke-linejoin="miter" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h1 class="font-['OCR_A'] text-5xl uppercase tracking-tight mb-2">Order Successful</h1>
                    <p class="text-[#999999] uppercase tracking-wide">Thank you for your order</p>
                </div>

                <!-- Order Details Card -->
                <div class="bg-[#0f0f0f] border border-[#1a1a1a] mb-6">
                    <div class="p-6 border-b border-[#1a1a1a]">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <p class="text-[#999999] text-sm uppercase tracking-wide mb-1">Order Number</p>
                                <p class="font-['OCR_A'] text-3xl uppercase tracking-tight">{{ order.order_number }}</p>
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
                                    <p class="text-[#999999]">Biaya: {{ formatPrice(order.shipping_cost) }}</p>
                                    <p v-if="order.tracking_number" class="text-[#999999] mt-2">
                                        Resi: {{ order.tracking_number }}
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
                                        class="w-20 h-20 object-cover bg-[#1a1a1a]"
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
                                    <span class="text-[#999999] uppercase tracking-wide">Ongkir</span>
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
                            Dibuat pada {{ formatDate(order.created_at) }}
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col md:flex-row gap-4">
                    <Link 
                        href="/orders" 
                        class="flex-1 text-center border border-white text-white py-4 uppercase font-['OCR_A'] text-xl tracking-tight hover:bg-white hover:text-black transition-colors"
                    >
                        View All Orders
                    </Link>
                    <Link 
                        href="/products" 
                        class="flex-1 text-center bg-white text-black py-4 uppercase font-['OCR_A'] text-xl tracking-tight hover:bg-[#f0f0f0] transition-colors"
                    >
                        Continue Shopping
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
