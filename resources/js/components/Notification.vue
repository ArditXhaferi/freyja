<template>
    <Transition name="notification">
        <div
            v-if="visible"
            :class="[
                'fixed top-20 right-4 z-50 max-w-sm w-full shadow-2xl rounded-lg p-4 flex items-start gap-3',
                type === 'success' 
                    ? 'bg-[#5cc094] text-white'
                    : type === 'error'
                    ? 'bg-red-500 text-white'
                    : type === 'info'
                    ? 'bg-blue-500 text-white'
                    : 'bg-gray-800 text-white'
            ]"
        >
            <div class="flex-shrink-0">
                <i v-if="type === 'success'" class="fa-solid fa-check-circle text-xl"></i>
                <i v-else-if="type === 'error'" class="fa-solid fa-exclamation-circle text-xl"></i>
                <i v-else-if="type === 'info'" class="fa-solid fa-info-circle text-xl"></i>
                <i v-else class="fa-solid fa-bell text-xl"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm leading-tight">{{ message }}</p>
            </div>
            <button
                @click="close"
                class="flex-shrink-0 text-white/80 hover:text-white transition-colors"
            >
                <i class="fa-solid fa-times text-sm"></i>
            </button>
        </div>
    </Transition>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue';

const props = defineProps({
    message: {
        type: String,
        required: true
    },
    type: {
        type: String,
        default: 'success',
        validator: (value) => ['success', 'error', 'info', 'warning'].includes(value)
    },
    duration: {
        type: Number,
        default: 3000
    }
});

const emit = defineEmits(['close']);

const visible = ref(false);
let timeoutId = null;

const show = () => {
    visible.value = true;
    
    if (timeoutId) {
        clearTimeout(timeoutId);
    }
    
    timeoutId = setTimeout(() => {
        close();
    }, props.duration);
};

const close = () => {
    visible.value = false;
    if (timeoutId) {
        clearTimeout(timeoutId);
        timeoutId = null;
    }
    emit('close');
};

onMounted(() => {
    show();
});

defineExpose({
    show,
    close
});
</script>

<style scoped>
.notification-enter-active {
    transition: all 0.3s ease-out;
}

.notification-leave-active {
    transition: all 0.3s ease-in;
}

.notification-enter-from {
    opacity: 0;
    transform: translateX(100%);
}

.notification-leave-to {
    opacity: 0;
    transform: translateX(100%);
}
</style>

