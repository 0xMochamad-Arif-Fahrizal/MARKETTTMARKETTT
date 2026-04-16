<script setup>
import { useForm } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const passwordsMatch = computed(() => {
    if (!form.password || !form.password_confirmation) return true;
    return form.password === form.password_confirmation;
});

const submit = () => {
    if (!passwordsMatch.value) {
        return;
    }
    
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
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
                <p class="text-sm text-[#999999] uppercase tracking-wide">CREATE YOUR ACCOUNT</p>
            </div>
            
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        required
                        placeholder="FULL NAME"
                        class="w-full px-4 py-3 bg-[#0f0f0f] border border-[#1a1a1a] text-white placeholder-[#999999] focus:outline-none focus:border-white transition-colors uppercase text-sm tracking-wide"
                    />
                    <div v-if="form.errors.name" class="mt-2 text-[#ff3333] text-xs uppercase tracking-wide">
                        {{ form.errors.name }}
                    </div>
                </div>
                
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
                        placeholder="PASSWORD (MIN 8 CHARACTERS)"
                        class="w-full px-4 py-3 bg-[#0f0f0f] border border-[#1a1a1a] text-white placeholder-[#999999] focus:outline-none focus:border-white transition-colors uppercase text-sm tracking-wide"
                    />
                    <div v-if="form.errors.password" class="mt-2 text-[#ff3333] text-xs uppercase tracking-wide">
                        {{ form.errors.password }}
                    </div>
                </div>
                
                <div>
                    <input
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        required
                        placeholder="CONFIRM PASSWORD"
                        class="w-full px-4 py-3 bg-[#0f0f0f] border border-[#1a1a1a] text-white placeholder-[#999999] focus:outline-none focus:border-white transition-colors uppercase text-sm tracking-wide"
                    />
                    <div v-if="!passwordsMatch" class="mt-2 text-[#ff3333] text-xs uppercase tracking-wide">
                        PASSWORDS DO NOT MATCH
                    </div>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing || !passwordsMatch"
                    class="w-full py-3 bg-white text-black font-medium uppercase tracking-wide hover:bg-[#999999] transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    REGISTER
                </button>

                <div class="text-center">
                    <Link href="/login" class="text-sm text-[#999999] hover:text-white transition-colors uppercase tracking-wide">
                        ALREADY HAVE AN ACCOUNT? SIGN IN
                    </Link>
                </div>
            </form>
        </div>
    </GuestLayout>
</template>
