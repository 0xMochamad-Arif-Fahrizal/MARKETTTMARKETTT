<script setup>
import { ref, watch, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const show = ref(false);
const message = ref('');
const type = ref('info');

const bgColor = computed(() => {
    switch (type.value) {
        case 'success':
            return 'bg-white text-black';
        case 'error':
            return 'bg-[#ff3333] text-white';
        case 'info':
            return 'bg-[#0f0f0f] text-white border border-[#1a1a1a]';
        default:
            return 'bg-[#0f0f0f] text-white border border-[#1a1a1a]';
    }
});

watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) {
            message.value = flash.success;
            type.value = 'success';
            show.value = true;
            autoDismiss();
        } else if (flash?.error) {
            message.value = flash.error;
            type.value = 'error';
            show.value = true;
            autoDismiss();
        } else if (flash?.info) {
            message.value = flash.info;
            type.value = 'info';
            show.value = true;
            autoDismiss();
        }
    },
    { deep: true, immediate: true }
);

const autoDismiss = () => {
    setTimeout(() => {
        show.value = false;
    }, 4000);
};

const dismiss = () => {
    show.value = false;
};
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="translate-x-full opacity-0"
        enter-to-class="translate-x-0 opacity-100"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="translate-x-0 opacity-100"
        leave-to-class="translate-x-full opacity-0"
    >
        <div
            v-if="show"
            :class="[bgColor, 'fixed top-4 right-4 z-[100] px-6 py-4 max-w-sm shadow-lg']"
        >
            <div class="flex items-start justify-between">
                <p class="text-sm uppercase tracking-wide font-medium">{{ message }}</p>
                <button
                    @click="dismiss"
                    class="ml-4 text-current hover:opacity-70 transition-opacity"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </Transition>
</template>
