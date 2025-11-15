<template>
    <div 
        class="fixed top-20 right-8 z-40 resource-card"
        :style="{ marginTop: `${index * 20}px` }"
    >
        <div class="bg-white rounded-lg shadow-xl border-l-4 border-blue-500 p-5 min-w-[320px] max-w-[400px] slide-in-right">
            <div class="flex items-start justify-between mb-3">
                <div class="flex items-start gap-3 flex-1">
                    <div class="text-3xl flex-shrink-0">{{ resource.icon }}</div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-bold px-2 py-1 rounded bg-blue-100 text-blue-800 uppercase">
                                {{ resource.category }}
                            </span>
                        </div>
                        <h3 class="font-bold text-gray-800 text-lg mb-1">{{ resource.title }}</h3>
                        <p class="text-sm text-gray-600 line-clamp-2">{{ resource.preview || resource.description }}</p>
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
            
            <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200">
                <a 
                    :href="resource.url" 
                    target="_blank"
                    rel="noopener noreferrer"
                    class="flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium text-sm transition-colors"
                >
                    <span>View Resource</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';

const props = defineProps({
    resource: {
        type: Object,
        required: true
    },
    index: {
        type: Number,
        default: 0
    }
});

const emit = defineEmits(['dismiss']);

const dismiss = () => {
    emit('dismiss', props.resource);
};

// Auto-dismiss after 30 seconds
onMounted(() => {
    setTimeout(() => {
        dismiss();
    }, 30000);
});
</script>

<style scoped>
.resource-card {
    animation: slideInRight 0.5s ease-out;
}

.slide-in-right {
    animation: slideInRight 0.5s ease-out;
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(100px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

