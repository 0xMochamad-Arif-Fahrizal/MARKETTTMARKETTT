<script setup>
import { onMounted, onUnmounted } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        required: true,
    },
});

const emit = defineEmits(['close']);

const handleEscape = (e) => {
    if (e.key === 'Escape' && props.show) {
        emit('close');
    }
};

onMounted(() => {
    document.addEventListener('keydown', handleEscape);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleEscape);
});
</script>

<template>
    <Teleport to="body">
        <div 
            v-if="show" 
            class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4"
            @click="$emit('close')"
        >
            <div 
                class="bg-black border-2 border-white max-w-2xl w-full p-8 max-h-[90vh] overflow-y-auto"
                @click.stop
            >
                <h2 class="text-white font-['OCR_A'] text-2xl uppercase mb-6">
                    SHIPPING POLICY
                </h2>
                
                <div class="text-white font-['OCR_A'] uppercase space-y-4 text-sm leading-relaxed">
                    <p>WE ONLY SHIP WITHIN INDONESIA</p>
                    <p>ALL ORDERS ARE PROCESSED AND SHIPPED WITHIN 5–10 WORKING DAYS</p>
                    <p>UNLESS A PRE-ORDER SHIP DATE IS SPECIFIED</p>
                    <p>WE DO NOT ACCEPT ORDERS FROM OUTSIDE INDONESIA</p>
                    <p>PAYMENTS CANNOT BE COMPLETED BY CUSTOMERS LOCATED OUTSIDE INDONESIA</p>
                    <p>ANY ORDERS IDENTIFIED AS BEING FROM OUTSIDE INDONESIA WILL BE AUTOMATICALLY CANCELLED AND NOT PROCESSED</p>
                    <p>SHIPPING WILL NOT BE PROCESSED FOR ANY DESTINATIONS OUTSIDE INDONESIA</p>
                    <p>IF AN INVALID ORDER IS PLACED FROM OUTSIDE INDONESIA, ANY ASSOCIATED COSTS OR FEES WILL BE THE RESPONSIBILITY OF THE CUSTOMER</p>
                    <p>PLEASE REFER TO OUR TERMS OF SALE FOR FURTHER INFORMATION</p>
                </div>
                
                <button 
                    @click="$emit('close')"
                    class="mt-8 bg-[#CCFF00] text-black font-['OCR_A'] uppercase px-6 py-2 hover:bg-[#CCFF00]/80 transition-colors"
                >
                    CLOSE
                </button>
            </div>
        </div>
    </Teleport>
</template>
