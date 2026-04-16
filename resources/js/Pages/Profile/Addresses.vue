<script setup>
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    addresses: Array,
});

const showModal = ref(false);
const editingAddress = ref(null);

const form = useForm({
    label: '',
    recipient_name: '',
    phone: '',
    address_line: '',
    city: '',
    province: '',
    postal_code: '',
    latitude: null,
    longitude: null,
    is_default: false,
});

const openModal = (address = null) => {
    if (address) {
        editingAddress.value = address;
        form.label = address.label;
        form.recipient_name = address.recipient_name;
        form.phone = address.phone;
        form.address_line = address.address_line;
        form.city = address.city;
        form.province = address.province;
        form.postal_code = address.postal_code;
        form.latitude = address.latitude;
        form.longitude = address.longitude;
        form.is_default = address.is_default;
    } else {
        editingAddress.value = null;
        form.reset();
    }
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    editingAddress.value = null;
    form.reset();
};

const submit = () => {
    if (editingAddress.value) {
        form.patch(`/profile/addresses/${editingAddress.value.id}`, {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post('/profile/addresses', {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteAddress = (address) => {
    if (confirm('Delete this address?')) {
        router.delete(`/profile/addresses/${address.id}`);
    }
};

const setDefault = (address) => {
    router.post(`/profile/addresses/${address.id}/set-default`);
};
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-black text-white">
            <!-- Header -->
            <div>
                <div class="max-w-7xl mx-auto px-4 py-6">
                    <div class="flex items-center justify-between">
                        <h1 class="font-['OCR_A'] text-4xl uppercase tracking-tight">My Addresses</h1>
                        <button
                            @click="openModal()"
                            class="bg-white text-black px-6 py-3 uppercase font-['OCR_A'] text-lg tracking-tight hover:bg-[#f0f0f0] transition-colors"
                        >
                            Add Address
                        </button>
                    </div>
                </div>
            </div>

            <div class="max-w-7xl mx-auto px-4 py-8">
                <div v-if="addresses.length === 0" class="text-center py-16">
                    <p class="text-[#999999] text-lg mb-6">You don't have any addresses yet</p>
                    <button
                        @click="openModal()"
                        class="bg-white text-black px-8 py-4 uppercase font-['OCR_A'] text-xl tracking-tight hover:bg-[#f0f0f0] transition-colors"
                    >
                        Add First Address
                    </button>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div
                        v-for="address in addresses"
                        :key="address.id"
                        class="bg-[#0f0f0f] border border-[#222222] p-6"
                    >
                        <div class="flex items-start justify-between mb-4">
                            <div>
                                <div class="flex items-center gap-2 mb-2">
                                    <h3 class="font-['OCR_A'] text-xl uppercase">{{ address.label }}</h3>
                                    <span v-if="address.is_default" class="text-xs px-2 py-1 bg-white text-black uppercase">Primary Address</span>
                                </div>
                                <p class="font-medium mb-1">{{ address.recipient_name }}</p>
                                <p class="text-sm text-[#999999]">{{ address.phone }}</p>
                            </div>
                        </div>

                        <p class="text-sm text-[#999999] mb-4">
                            {{ address.address_line }}<br>
                            {{ address.city }}, {{ address.province }} {{ address.postal_code }}
                        </p>

                        <div v-if="address.latitude && address.longitude" class="text-xs text-[#666666] mb-4">
                            📍 {{ address.latitude }}, {{ address.longitude }}
                        </div>
                        <div v-else class="text-xs text-[#ff0000] mb-4">
                            ⚠ No coordinates yet (required for shipping calculation)
                        </div>

                        <div class="flex gap-2 pt-4 border-t border-[#1a1a1a]">
                            <button
                                @click="openModal(address)"
                                class="flex-1 bg-white text-black py-2 uppercase font-['OCR_A'] text-sm tracking-tight hover:bg-[#f0f0f0] transition-colors"
                            >
                                Edit
                            </button>
                            <button
                                v-if="!address.is_default"
                                @click="setDefault(address)"
                                class="flex-1 border border-white text-white py-2 uppercase font-['OCR_A'] text-sm tracking-tight hover:bg-white hover:text-black transition-colors"
                            >
                                Set as Primary
                            </button>
                            <button
                                @click="deleteAddress(address)"
                                class="px-4 border border-[#ff0000] text-[#ff0000] py-2 uppercase font-['OCR_A'] text-sm tracking-tight hover:bg-[#ff0000] hover:text-white transition-colors"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black/80" @click="closeModal"></div>
                
                <div class="relative bg-[#0f0f0f] border border-[#222222] w-full max-w-2xl">
                    <div class="p-6 border-b border-[#1a1a1a]">
                        <h2 class="font-['OCR_A'] text-2xl uppercase tracking-tight">
                            {{ editingAddress ? 'Edit Address' : 'Add Address' }}
                        </h2>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs text-[#999999] mb-2 uppercase tracking-wide">Label</label>
                                <input
                                    v-model="form.label"
                                    type="text"
                                    placeholder="Home, Office, etc"
                                    class="w-full bg-black border border-[#333333] px-4 py-3 text-white focus:outline-none focus:border-white transition-colors"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block text-xs text-[#999999] mb-2 uppercase tracking-wide">Recipient Name</label>
                                <input
                                    v-model="form.recipient_name"
                                    type="text"
                                    class="w-full bg-black border border-[#333333] px-4 py-3 text-white focus:outline-none focus:border-white transition-colors"
                                    required
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm text-[#999999] mb-2 uppercase tracking-wide">Phone Number</label>
                            <input
                                v-model="form.phone"
                                type="tel"
                                class="w-full bg-black border border-[#1a1a1a] px-4 py-3 text-white focus:outline-none focus:border-white"
                                required
                            >
                        </div>

                        <div>
                            <label class="block text-sm text-[#999999] mb-2 uppercase tracking-wide">Full Address</label>
                            <textarea
                                v-model="form.address_line"
                                rows="3"
                                class="w-full bg-black border border-[#1a1a1a] px-4 py-3 text-white focus:outline-none focus:border-white resize-none"
                                required
                            ></textarea>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm text-[#999999] mb-2 uppercase tracking-wide">City</label>
                                <input
                                    v-model="form.city"
                                    type="text"
                                    class="w-full bg-black border border-[#1a1a1a] px-4 py-3 text-white focus:outline-none focus:border-white"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block text-sm text-[#999999] mb-2 uppercase tracking-wide">Province</label>
                                <input
                                    v-model="form.province"
                                    type="text"
                                    class="w-full bg-black border border-[#1a1a1a] px-4 py-3 text-white focus:outline-none focus:border-white"
                                    required
                                >
                            </div>

                            <div>
                                <label class="block text-sm text-[#999999] mb-2 uppercase tracking-wide">Postal Code</label>
                                <input
                                    v-model="form.postal_code"
                                    type="text"
                                    class="w-full bg-black border border-[#1a1a1a] px-4 py-3 text-white focus:outline-none focus:border-white"
                                    required
                                >
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-[#999999] mb-2 uppercase tracking-wide">Latitude (optional)</label>
                                <input
                                    v-model="form.latitude"
                                    type="number"
                                    step="any"
                                    placeholder="-7.2575"
                                    class="w-full bg-black border border-[#1a1a1a] px-4 py-3 text-white focus:outline-none focus:border-white"
                                >
                            </div>

                            <div>
                                <label class="block text-sm text-[#999999] mb-2 uppercase tracking-wide">Longitude (optional)</label>
                                <input
                                    v-model="form.longitude"
                                    type="number"
                                    step="any"
                                    placeholder="112.7521"
                                    class="w-full bg-black border border-[#1a1a1a] px-4 py-3 text-white focus:outline-none focus:border-white"
                                >
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <input
                                v-model="form.is_default"
                                type="checkbox"
                                id="is_default"
                                class="w-4 h-4"
                            >
                            <label for="is_default" class="text-sm uppercase tracking-wide cursor-pointer">
                                Set as default address
                            </label>
                        </div>

                        <div class="flex gap-4 pt-4">
                            <button
                                type="button"
                                @click="closeModal"
                                class="flex-1 border border-white text-white py-3 uppercase font-['OCR_A'] text-lg tracking-tight hover:bg-white hover:text-black transition-colors"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="flex-1 bg-white text-black py-3 uppercase font-['OCR_A'] text-lg tracking-tight hover:bg-[#f0f0f0] transition-colors disabled:opacity-50"
                            >
                                {{ editingAddress ? 'Update' : 'Save' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
