<template>
    <div class="bg-yellow-50 border-l-4 border-yellow-500 rounded-lg p-4 shadow-md document-request-card">
        <div class="flex items-start justify-between mb-2">
            <div class="flex items-start gap-3 flex-1">
                <div class="text-2xl flex-shrink-0">📋</div>
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <span class="text-xs font-bold px-2 py-1 rounded bg-yellow-100 text-yellow-800 uppercase">
                            {{ request.type }}
                        </span>
                        <span v-if="request.required" class="text-xs font-medium text-red-600">Required</span>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-1">{{ request.title }}</h3>
                    <p class="text-sm text-gray-600">{{ request.description }}</p>
                </div>
            </div>
            <button 
                @click="dismiss"
                class="text-gray-400 hover:text-gray-600 transition-colors flex-shrink-0 ml-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <div class="flex items-center gap-3 mt-4 pt-4 border-t border-yellow-200">
            <button
                @click="markProvided"
                class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium"
            >
                Mark as Provided
            </button>
            <button
                @click="dismiss"
                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors text-sm"
            >
                Dismiss
            </button>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';

const props = defineProps({
    request: {
        type: Object,
        required: true
    },
    index: {
        type: Number,
        default: 0
    }
});

const emit = defineEmits(['dismiss', 'provided']);

const dismiss = () => {
    emit('dismiss', props.request);
};

const markProvided = () => {
    emit('provided', props.request);
    // Auto-dismiss after marking as provided
    setTimeout(() => {
        dismiss();
    }, 500);
};

// Auto-dismiss after 60 seconds if not required
onMounted(() => {
    if (!props.request.required) {
        setTimeout(() => {
            dismiss();
        }, 60000);
    }
});
</script>

<style scoped>
.document-request-card {
    animation: slideInUp 0.4s ease-out;
}

@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

