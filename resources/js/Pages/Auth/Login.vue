<script setup>
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <img 
                        src="/images/logo.png" 
                        alt="STYLEU" 
                        class="h-20 w-auto"
                        @error="$event.target.style.display='none'; $event.target.nextElementSibling.style.display='block'"
                    />
                    <h1 class="text-5xl font-['OCR_A'] uppercase tracking-tight" style="display: none;">STYLEU</h1>
                </div>
                <p class="text-sm text-[#999999] uppercase tracking-wide">SIGN IN TO YOUR ACCOUNT</p>
            </div>
            
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        placeholder="EMAIL ADDRESS"
                        class="w-full px-4 py-3 bg-[#0f0f0f] border border-[#1a1a1a] text-white placeholder-[#999999] focus:outline-none focus:border-white transition-colors uppercase text-sm tracking-wide"
                    />
                    <div v-if="form.errors.email" class="mt-2 text-[#ff3333] text-xs uppercase tracking-wide">
                        {{ form.errors.email }}
                    </div>
                </div>

                <div>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        placeholder="PASSWORD"
                        class="w-full px-4 py-3 bg-[#0f0f0f] border border-[#1a1a1a] text-white placeholder-[#999999] focus:outline-none focus:border-white transition-colors uppercase text-sm tracking-wide"
                    />
                </div>

                <div class="flex items-center">
                    <input
                        id="remember"
                        v-model="form.remember"
                        type="checkbox"
                        class="w-4 h-4 bg-[#0f0f0f] border-[#1a1a1a] text-white focus:ring-0 focus:ring-offset-0"
                    />
                    <label for="remember" class="ml-2 text-sm text-[#999999] uppercase tracking-wide">
                        REMEMBER ME
                    </label>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full py-3 bg-white text-black font-medium uppercase tracking-wide hover:bg-[#999999] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    SIGN IN
                </button>

                <div class="text-center">
                    <Link href="/register" class="text-sm text-[#999999] hover:text-white transition-colors uppercase tracking-wide">
                        DON'T HAVE AN ACCOUNT? REGISTER
                    </Link>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
