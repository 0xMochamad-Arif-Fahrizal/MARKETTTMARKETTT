<script setup>
import { ref, computed, onMounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import FlashMessage from '@/Components/FlashMessage.vue';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const cartItemCount = ref(0);

const mobileMenuOpen = ref(false);
const userMenuOpen = ref(false);

// Fetch cart count
const fetchCartCount = async () => {
    // Only fetch if user is authenticated
    if (!user.value) {
        cartItemCount.value = 0;
        return;
    }
    
    try {
        const response = await fetch('/cart/count');
        const data = await response.json();
        cartItemCount.value = data.count;
    } catch (error) {
        console.error('Failed to fetch cart count:', error);
    }
};

onMounted(() => {
    fetchCartCount();
    
    // Listen for cart updates
    router.on('success', () => {
        fetchCartCount();
    });
});

const logout = () => {
    router.post('/logout');
};

const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
};

const toggleUserMenu = () => {
    userMenuOpen.value = !userMenuOpen.value;
};
</script>

<template>
    <div class="min-h-screen bg-black text-white">
        <FlashMessage />
        
        <!-- Navbar -->
        <nav class="bg-transparent border-t-4 border-[#CCFF00] sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Left Side: Hamburger/X Button (Mobile Only) -->
                    <div class="flex items-center gap-4">
                        <button
                            @click="$emit('toggle-mobile-menu')"
                            class="lg:hidden p-2 text-[#CCFF00] hover:text-white transition-colors"
                            aria-label="Toggle menu"
                        >
                            <!-- Hamburger icon when closed -->
                            <svg v-if="!$attrs.mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <!-- X icon when open -->
                            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Right Side -->
                    <div class="flex items-center space-x-6">
                        <!-- Cart Icon (only for authenticated users) -->
                        <Link v-if="user" href="/cart" class="relative hover:text-gray-300 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span 
                                v-if="cartItemCount > 0" 
                                class="absolute -top-2 -right-2 bg-white text-black text-xs font-bold w-5 h-5 flex items-center justify-center"
                            >
                                {{ cartItemCount }}
                            </span>
                        </Link>

                        <!-- User Menu (Desktop) - Authenticated -->
                        <div v-if="user" class="hidden md:block relative">
                            <button 
                                @click="toggleUserMenu"
                                class="text-sm uppercase tracking-wide hover:text-gray-300 transition-colors flex items-center space-x-2"
                            >
                                <span>{{ user.name }}</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>

                            <!-- Dropdown -->
                            <div 
                                v-if="userMenuOpen"
                                @click.away="userMenuOpen = false"
                                class="absolute right-0 mt-2 w-48 bg-[#0f0f0f] border border-[#1a1a1a]"
                            >
                                <Link 
                                    href="/profile" 
                                    class="block px-4 py-3 text-sm uppercase tracking-wide hover:bg-[#1a1a1a] transition-colors"
                                >
                                    PROFILE
                                </Link>
                                <Link 
                                    href="/profile/addresses" 
                                    class="block px-4 py-3 text-sm uppercase tracking-wide hover:bg-[#1a1a1a] transition-colors"
                                >
                                    ADDRESSES
                                </Link>
                                <Link 
                                    href="/orders" 
                                    class="block px-4 py-3 text-sm uppercase tracking-wide hover:bg-[#1a1a1a] transition-colors"
                                >
                                    ORDERS
                                </Link>
                                <button 
                                    @click="logout"
                                    class="w-full text-left px-4 py-3 text-sm uppercase tracking-wide hover:bg-[#1a1a1a] transition-colors"
                                >
                                    LOGOUT
                                </button>
                            </div>
                        </div>

                        <!-- Login Button (Guest) -->
                        <Link 
                            v-else
                            href="/login" 
                            class="hidden md:block text-sm uppercase tracking-wide hover:text-gray-300 transition-colors"
                        >
                            LOGIN
                        </Link>

                        <!-- Mobile Menu Button -->
                        <button 
                            @click="toggleMobileMenu"
                            class="md:hidden"
                        >
                            <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            </svg>
                            <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div 
                v-if="mobileMenuOpen"
                class="md:hidden border-t border-[#1a1a1a] bg-black"
            >
                <div class="px-4 py-4 space-y-3">
                    <!-- Authenticated User Menu -->
                    <div v-if="user">
                        <div class="text-xs text-[#999999] uppercase mb-2">{{ user.name }}</div>
                        <Link 
                            href="/profile" 
                            class="block text-sm uppercase tracking-wide hover:text-gray-300 transition-colors mb-2"
                        >
                            PROFILE
                        </Link>
                        <Link 
                            href="/profile/addresses" 
                            class="block text-sm uppercase tracking-wide hover:text-gray-300 transition-colors mb-2"
                        >
                            ADDRESSES
                        </Link>
                        <Link 
                            href="/orders" 
                            class="block text-sm uppercase tracking-wide hover:text-gray-300 transition-colors mb-2"
                        >
                            ORDERS
                        </Link>
                        <button 
                            @click="logout"
                            class="text-sm uppercase tracking-wide hover:text-gray-300 transition-colors"
                        >
                            LOGOUT
                        </button>
                    </div>
                    
                    <!-- Guest Menu -->
                    <div v-else>
                        <Link 
                            href="/login" 
                            class="block text-sm uppercase tracking-wide hover:text-gray-300 transition-colors"
                        >
                            LOGIN
                        </Link>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            <slot />
        </main>
    </div>
</template>
