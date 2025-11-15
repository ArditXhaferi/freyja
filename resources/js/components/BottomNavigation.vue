<template>
    <nav class="fixed bottom-0 left-0 right-0 z-50 bg-white border-t-2 border-blue-200 shadow-2xl">
        <div class="max-w-6xl mx-auto px-2">
            <div class="flex items-center justify-around py-2">
                <!-- Business Info -->
                <button
                    @click="navigate('business')"
                    :class="[
                        'flex flex-col items-center justify-center gap-1 px-3 py-2 rounded-xl transition-all duration-300',
                        activeTab === 'business' 
                            ? 'bg-gradient-to-br from-purple-400 to-pink-400 text-white scale-110' 
                            : 'text-gray-600 hover:bg-blue-50'
                    ]"
                >
                    <div class="text-2xl">{{ activeTab === 'business' ? '📋' : '📄' }}</div>
                    <span class="text-xs font-bold">Business</span>
                </button>

                <!-- Roadmap -->
                <button
                    @click="navigate('roadmap-tab')"
                    :class="[
                        'flex flex-col items-center justify-center gap-1 px-3 py-2 rounded-xl transition-all duration-300',
                        activeTab === 'roadmap-tab' 
                            ? 'bg-gradient-to-br from-blue-400 to-purple-400 text-white scale-110' 
                            : 'text-gray-600 hover:bg-blue-50'
                    ]"
                >
                    <div class="text-2xl">{{ activeTab === 'roadmap-tab' ? '🗺️' : '📍' }}</div>
                    <span class="text-xs font-bold">Roadmap</span>
                </button>

                <!-- Manual Add -->
                <button
                    @click="navigate('add')"
                    :class="[
                        'flex flex-col items-center justify-center gap-1 px-3 py-2 rounded-xl transition-all duration-300',
                        activeTab === 'add' 
                            ? 'bg-gradient-to-br from-green-400 to-emerald-400 text-white scale-110' 
                            : 'text-gray-600 hover:bg-green-50'
                    ]"
                >
                    <div class="text-2xl">{{ activeTab === 'add' ? '➕' : '✏️' }}</div>
                    <span class="text-xs font-bold">Add</span>
                </button>

                <!-- Home -->
                <button
                    @click="navigate('roadmap')"
                    :class="[
                        'flex flex-col items-center justify-center gap-1 px-3 py-2 rounded-xl transition-all duration-300',
                        activeTab === 'roadmap' 
                            ? 'bg-gradient-to-br from-orange-400 to-yellow-400 text-white scale-110' 
                            : 'text-gray-600 hover:bg-orange-50'
                    ]"
                >
                    <div class="text-2xl">{{ activeTab === 'roadmap' ? '🐻' : '🏠' }}</div>
                    <span class="text-xs font-bold">Home</span>
                </button>

                <!-- Advisors -->
                <button
                    @click="navigate('advisors')"
                    :class="[
                        'flex flex-col items-center justify-center gap-1 px-3 py-2 rounded-xl transition-all duration-300',
                        activeTab === 'advisors' 
                            ? 'bg-gradient-to-br from-yellow-400 to-orange-400 text-white scale-110' 
                            : 'text-gray-600 hover:bg-yellow-50'
                    ]"
                >
                    <div class="text-2xl">{{ activeTab === 'advisors' ? '👥' : '💼' }}</div>
                    <span class="text-xs font-bold">Advisors</span>
                </button>

                <!-- Calendar -->
                <button
                    @click="navigate('calendar')"
                    :class="[
                        'flex flex-col items-center justify-center gap-1 px-3 py-2 rounded-xl transition-all duration-300',
                        activeTab === 'calendar' 
                            ? 'bg-gradient-to-br from-pink-400 to-red-400 text-white scale-110' 
                            : 'text-gray-600 hover:bg-pink-50'
                    ]"
                >
                    <div class="text-2xl">{{ activeTab === 'calendar' ? '📅' : '🗓️' }}</div>
                    <span class="text-xs font-bold">Calendar</span>
                </button>
            </div>
        </div>
    </nav>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    activeTab: {
        type: String,
        default: 'roadmap'
    }
});

const emit = defineEmits(['navigate']);

const navigate = (tab) => {
    emit('navigate', tab);
};
</script>

<style scoped>
/* Smooth transitions for cute animations */
</style>

