<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-end justify-center modal-overlay" @click.self="close">
        <div class="bg-white rounded-t-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto modal-content">
            <!-- Header -->
            <div class="sticky top-0 bg-gradient-to-r from-green-400 to-emerald-400 p-6 rounded-t-3xl flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="text-3xl">➕</div>
                    <h2 class="text-2xl font-bold text-white">Add Manually</h2>
                </div>
                <button @click="close" class="text-white hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="p-6">
                <div class="space-y-4">
                    <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl p-4 border-2 border-blue-200">
                        <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="text-xl">📝</span>
                            Add Business Plan Field
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">Coming soon! You'll be able to manually add business plan information here.</p>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-4 border-2 border-green-200">
                        <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="text-xl">🗺️</span>
                            Add Roadmap Step
                        </h3>
                        <p class="text-sm text-gray-600 mb-4">Coming soon! You'll be able to manually add roadmap steps here.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
const props = defineProps({
    show: {
        type: Boolean,
        default: false
    }
});

const emit = defineEmits(['close']);

const close = () => {
    emit('close');
};
</script>

<style scoped>
.modal-overlay {
    background-color: rgba(0, 0, 0, 0.5);
    animation: fadeIn 0.2s ease-out;
}

.modal-content {
    animation: slideUp 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from {
        transform: translateY(100%);
    }
    to {
        transform: translateY(0);
    }
}
</style>

