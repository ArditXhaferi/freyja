<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-end justify-center modal-overlay" @click.self="close">
        <div class="bg-white rounded-t-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto modal-content">
            <!-- Header -->
            <div class="sticky top-0 bg-gradient-to-r from-pink-400 to-red-400 p-6 rounded-t-3xl flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="text-3xl">📅</div>
                    <h2 class="text-2xl font-bold text-white">Calendar</h2>
                </div>
                <button @click="close" class="text-white hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Content -->
            <div class="p-6">
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">🗓️</div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2">Your Calendar</h3>
                    <p class="text-gray-600 mb-6">View and manage your meetings and important dates</p>
                    <div class="bg-gradient-to-br from-pink-50 to-red-50 rounded-2xl p-6 border-2 border-pink-200">
                        <p class="text-sm text-gray-600">Coming soon! You'll be able to view your calendar and schedule meetings here.</p>
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

