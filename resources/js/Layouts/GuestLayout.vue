<script setup>
import FlashMessage from '@/Components/FlashMessage.vue';
</script>

<template>
    <div class="min-h-screen bg-black text-white">
        <FlashMessage />
        
        <div class="flex items-center justify-center min-h-screen px-4">
            <slot />
        </div>
    </div>
</template>
