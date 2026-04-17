<script setup>
import { ref, onMounted } from 'vue';

const emit = defineEmits(['loaded']);

const isLoading = ref(true);

onMounted(() => {
    // Simulate loading time
    setTimeout(() => {
        isLoading.value = false;
        emit('loaded');
    }, 1500); // 1.5 seconds loading
});
</script>

<template>
    <Transition
        enter-active-class="transition-opacity duration-500"
        enter-from-class="opacity-100"
        enter-to-class="opacity-0"
        leave-active-class="transition-opacity duration-500"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <div v-if="isLoading" class="fixed inset-0 bg-black z-[9999] flex items-center justify-center">
            <div class="text-center">
                <img 
                    src="/images/logo.png" 
                    alt="MARKETTTMARKETTT" 
                    class="h-16 w-auto mx-auto mb-4 animate-pulse"
                />
                <div class="flex gap-2 justify-center">
                    <div class="w-2 h-2 bg-white rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                    <div class="w-2 h-2 bg-white rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                    <div class="w-2 h-2 bg-white rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                </div>
            </div>
        </div>
    </Transition>
</template>
