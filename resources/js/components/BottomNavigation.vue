<template>
    <nav class="fixed bottom-0 left-0 right-0 z-50 bg-[#011135] border-t border-white/20 shadow-2xl">
        <div class="max-w-6xl mx-auto px-2">
            <div class="flex items-center justify-around py-2">
                <!-- Business Info -->
                <button
                    @click="navigate('business')"
                    :class="[
                        'flex flex-col items-center justify-center gap-1 px-3 py-2 rounded-lg transition-all duration-300',
                        activeTab === 'business' 
                            ? 'bg-[#012169] text-white' 
                            : 'text-white/70 hover:text-white hover:bg-white/10'
                    ]"
                >
                    <div class="text-xl">{{ activeTab === 'business' ? '📋' : '📄' }}</div>
                    <span class="text-xs font-semibold">Business</span>
                </button>

                <!-- Roadmap -->
                <button
                    @click="navigate('roadmap-tab')"
                    :class="[
                        'flex flex-col items-center justify-center gap-1 px-3 py-2 rounded-lg transition-all duration-300',
                        activeTab === 'roadmap-tab' 
                            ? 'bg-[#012169] text-white' 
                            : 'text-white/70 hover:text-white hover:bg-white/10'
                    ]"
                >
                    <div class="text-xl">{{ activeTab === 'roadmap-tab' ? '🗺️' : '📍' }}</div>
                    <span class="text-xs font-semibold">Roadmap</span>
                </button>

                <!-- Manual Add -->
                <button
                    @click="navigate('add')"
                    :class="[
                        'flex flex-col items-center justify-center gap-1 px-3 py-2 rounded-lg transition-all duration-300',
                        activeTab === 'add' 
                            ? 'bg-[#012169] text-white' 
                            : 'text-white/70 hover:text-white hover:bg-white/10'
                    ]"
                >
                    <div class="text-xl">{{ activeTab === 'add' ? '➕' : '✏️' }}</div>
                    <span class="text-xs font-semibold">Add</span>
                </button>

                <!-- Home -->
                <button
                    @click="navigate('roadmap')"
                    :class="[
                        'flex flex-col items-center justify-center gap-1 px-3 py-2 rounded-lg transition-all duration-300',
                        activeTab === 'roadmap' 
                            ? 'bg-[#012169] text-white' 
                            : 'text-white/70 hover:text-white hover:bg-white/10'
                    ]"
                >
                    <div class="text-xl">{{ activeTab === 'roadmap' ? '🏠' : '🏠' }}</div>
                    <span class="text-xs font-semibold">Home</span>
                </button>

                <!-- Advisors -->
                <button
                    @click="navigate('advisors')"
                    :class="[
                        'flex flex-col items-center justify-center gap-1 px-3 py-2 rounded-lg transition-all duration-300',
                        activeTab === 'advisors' 
                            ? 'bg-[#012169] text-white' 
                            : 'text-white/70 hover:text-white hover:bg-white/10'
                    ]"
                >
                    <div class="text-xl">{{ activeTab === 'advisors' ? '👥' : '💼' }}</div>
                    <span class="text-xs font-semibold">Advisors</span>
                </button>

                <!-- Calendar -->
                <button
                    @click="navigate('calendar')"
                    :class="[
                        'flex flex-col items-center justify-center gap-1 px-3 py-2 rounded-lg transition-all duration-300',
                        activeTab === 'calendar' 
                            ? 'bg-[#012169] text-white' 
                            : 'text-white/70 hover:text-white hover:bg-white/10'
                    ]"
                >
                    <div class="text-xl">{{ activeTab === 'calendar' ? '📅' : '🗓️' }}</div>
                    <span class="text-xs font-semibold">Calendar</span>
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

