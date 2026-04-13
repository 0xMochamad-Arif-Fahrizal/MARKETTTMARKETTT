<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    user: Object,
});

const showPasswordForm = ref(false);

// Profile form
const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone || '',
});

// Password form
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updateProfile = () => {
    profileForm.patch('/profile', {
        preserveScroll: true,
        onSuccess: () => {
            // Success handled by flash message
        },
    });
};

const updatePassword = () => {
    passwordForm.patch('/profile/password', {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
            showPasswordForm.value = false;
        },
    });
};
</script>

<template>
    <AppLayout>
        <div class="min-h-screen bg-black text-white">
            <!-- Header -->
            <div class="border-b border-[#1a1a1a]">
                <div class="max-w-7xl mx-auto px-4 py-6">
                    <h1 class="font-['OCR_A'] text-4xl uppercase tracking-tight">My Profile</h1>
                </div>
            </div>

            <div class="max-w-4xl mx-auto px-4 py-8">
                <div class="space-y-6">
                    <!-- Profile Information Section -->
                    <div class="bg-[#0f0f0f] border border-[#222222]">
                        <div class="p-6 border-b border-[#1a1a1a]">
                            <h2 class="font-['OCR_A'] text-2xl uppercase tracking-tight">Profile Information</h2>
                            <p class="text-sm text-[#999999] mt-1">Update your profile information and email address.</p>
                        </div>

                        <form @submit.prevent="updateProfile" class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs text-[#999999] mb-2 uppercase tracking-wide">Full Name</label>
                                <input
                                    v-model="profileForm.name"
                                    type="text"
                                    class="w-full bg-black border border-[#333333] px-4 py-3 text-white focus:outline-none focus:border-white transition-colors"
                                    required
                                >
                                <p v-if="profileForm.errors.name" class="text-[#ff0000] text-xs mt-1">{{ profileForm.errors.name }}</p>
                            </div>

                            <div>
                                <label class="block text-xs text-[#999999] mb-2 uppercase tracking-wide">Email</label>
                                <input
                                    v-model="profileForm.email"
                                    type="email"
                                    class="w-full bg-black border border-[#333333] px-4 py-3 text-white focus:outline-none focus:border-white transition-colors"
                                    required
                                >
                                <p v-if="profileForm.errors.email" class="text-[#ff0000] text-xs mt-1">{{ profileForm.errors.email }}</p>
                            </div>

                            <div>
                                <label class="block text-xs text-[#999999] mb-2 uppercase tracking-wide">Phone Number</label>
                                <input
                                    v-model="profileForm.phone"
                                    type="tel"
                                    placeholder="08xxxxxxxxxx"
                                    class="w-full bg-black border border-[#333333] px-4 py-3 text-white focus:outline-none focus:border-white transition-colors"
                                >
                                <p v-if="profileForm.errors.phone" class="text-[#ff0000] text-xs mt-1">{{ profileForm.errors.phone }}</p>
                            </div>

                            <div class="pt-4">
                                <button
                                    type="submit"
                                    :disabled="profileForm.processing"
                                    class="bg-white text-black px-8 py-3 uppercase font-['OCR_A'] text-lg tracking-tight hover:bg-[#f0f0f0] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    Save
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Password Section -->
                    <div class="bg-[#0f0f0f] border border-[#222222]">
                        <div class="p-6 border-b border-[#1a1a1a]">
                            <h2 class="font-['OCR_A'] text-2xl uppercase tracking-tight">Change Password</h2>
                            <p class="text-sm text-[#999999] mt-1">Ensure your account is using a strong password.</p>
                        </div>

                        <div v-if="!showPasswordForm" class="p-6">
                            <button
                                @click="showPasswordForm = true"
                                class="border border-white text-white px-8 py-3 uppercase font-['OCR_A'] text-lg tracking-tight hover:bg-white hover:text-black transition-colors"
                            >
                                Change Password
                            </button>
                        </div>

                        <form v-else @submit.prevent="updatePassword" class="p-6 space-y-4">
                            <div>
                                <label class="block text-xs text-[#999999] mb-2 uppercase tracking-wide">Current Password</label>
                                <input
                                    v-model="passwordForm.current_password"
                                    type="password"
                                    class="w-full bg-black border border-[#333333] px-4 py-3 text-white focus:outline-none focus:border-white transition-colors"
                                    required
                                >
                                <p v-if="passwordForm.errors.current_password" class="text-[#ff0000] text-xs mt-1">{{ passwordForm.errors.current_password }}</p>
                            </div>

                            <div>
                                <label class="block text-xs text-[#999999] mb-2 uppercase tracking-wide">New Password</label>
                                <input
                                    v-model="passwordForm.password"
                                    type="password"
                                    class="w-full bg-black border border-[#333333] px-4 py-3 text-white focus:outline-none focus:border-white transition-colors"
                                    required
                                >
                                <p v-if="passwordForm.errors.password" class="text-[#ff0000] text-xs mt-1">{{ passwordForm.errors.password }}</p>
                            </div>

                            <div>
                                <label class="block text-xs text-[#999999] mb-2 uppercase tracking-wide">Confirm New Password</label>
                                <input
                                    v-model="passwordForm.password_confirmation"
                                    type="password"
                                    class="w-full bg-black border border-[#333333] px-4 py-3 text-white focus:outline-none focus:border-white transition-colors"
                                    required
                                >
                            </div>

                            <div class="flex gap-4 pt-4">
                                <button
                                    type="button"
                                    @click="showPasswordForm = false; passwordForm.reset();"
                                    class="border border-white text-white px-8 py-3 uppercase font-['OCR_A'] text-lg tracking-tight hover:bg-white hover:text-black transition-colors"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="submit"
                                    :disabled="passwordForm.processing"
                                    class="bg-white text-black px-8 py-3 uppercase font-['OCR_A'] text-lg tracking-tight hover:bg-[#f0f0f0] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    Save Password
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Addresses Section -->
                    <div class="bg-[#0f0f0f] border border-[#222222]">
                        <div class="p-6 border-b border-[#1a1a1a]">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="font-['OCR_A'] text-2xl uppercase tracking-tight">Shipping Addresses</h2>
                                    <p class="text-sm text-[#999999] mt-1">Manage your shipping addresses.</p>
                                </div>
                                <Link
                                    href="/profile/addresses"
                                    class="border border-white text-white px-6 py-2 uppercase font-['OCR_A'] text-sm tracking-tight hover:bg-white hover:text-black transition-colors"
                                >
                                    Manage Addresses
                                </Link>
                            </div>
                        </div>

                        <div class="p-6">
                            <p class="text-sm text-[#999999]">
                                Click "Manage Addresses" to add, edit, or delete your shipping addresses.
                            </p>
                        </div>
                    </div>

                    <!-- Orders Section -->
                    <div class="bg-[#0f0f0f] border border-[#222222]">
                        <div class="p-6 border-b border-[#1a1a1a]">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h2 class="font-['OCR_A'] text-2xl uppercase tracking-tight">My Orders</h2>
                                    <p class="text-sm text-[#999999] mt-1">View your order history.</p>
                                </div>
                                <Link
                                    href="/orders"
                                    class="border border-white text-white px-6 py-2 uppercase font-['OCR_A'] text-sm tracking-tight hover:bg-white hover:text-black transition-colors"
                                >
                                    View Orders
                                </Link>
                            </div>
                        </div>

                        <div class="p-6">
                            <p class="text-sm text-[#999999]">
                                Click "View Orders" to see all your orders, including shipping status and payment history.
                            </p>
                        </div>
                    </div>

                    <!-- Account Info -->
                    <div class="bg-[#0f0f0f] border border-[#222222]">
                        <div class="p-6">
                            <h3 class="font-['OCR_A'] text-lg uppercase tracking-tight mb-4">Account Information</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between py-2 border-b border-[#1a1a1a]">
                                    <span class="text-[#999999] uppercase tracking-wide">Account Status</span>
                                    <span class="text-white">{{ user.role === 'admin' ? 'Administrator' : 'Customer' }}</span>
                                </div>
                                <div class="flex justify-between py-2 border-b border-[#1a1a1a]">
                                    <span class="text-[#999999] uppercase tracking-wide">Member Since</span>
                                    <span class="text-white">{{ new Date(user.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
