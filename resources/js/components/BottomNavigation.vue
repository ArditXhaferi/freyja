<template>
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-[#fff5f5] border-t border-gray-200 shadow-2xl safe-area-inset-bottom">
        <div class="max-w-6xl mx-auto px-1 sm:px-2">
            <div class="flex items-center justify-around py-1.5 sm:py-2">
                <!-- Business Info -->
                <button
                    @click="navigate('business')"
                    :class="[
                        'flex flex-col items-center justify-center gap-0 sm:gap-1 px-2 sm:px-3 py-2 sm:py-2.5 rounded-lg transition-all duration-300 flex-1 min-w-0',
                        activeTab === 'business' 
                            ? 'bg-gray-100 text-gray-900' 
                            : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                    ]"
                >
                    <i :class="activeTab === 'business' ? 'fa-solid fa-clipboard-list' : 'fa-regular fa-file'" class="text-lg sm:text-xl"></i>
                    <span class="hidden sm:inline text-xs font-semibold truncate w-full text-center">Business</span>
                </button>

                <!-- Roadmap -->
                <button
                    @click="navigate('roadmap-tab')"
                    :class="[
                        'flex flex-col items-center justify-center gap-0 sm:gap-1 px-2 sm:px-3 py-2 sm:py-2.5 rounded-lg transition-all duration-300 flex-1 min-w-0',
                        activeTab === 'roadmap-tab' 
                            ? 'bg-gray-100 text-gray-900' 
                            : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                    ]"
                >
                    <i :class="activeTab === 'roadmap-tab' ? 'fa-solid fa-map' : 'fa-solid fa-map-pin'" class="text-lg sm:text-xl"></i>
                    <span class="hidden sm:inline text-xs font-semibold truncate w-full text-center">Roadmap</span>
                </button>

                <!-- Manual Add -->
                <button
                    @click="navigate('add')"
                    :class="[
                        'flex flex-col items-center justify-center gap-0 sm:gap-1 px-2 sm:px-3 py-2 sm:py-2.5 rounded-lg transition-all duration-300 flex-1 min-w-0',
                        activeTab === 'add' 
                            ? 'bg-gray-100 text-gray-900' 
                            : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                    ]"
                >
                    <i :class="activeTab === 'add' ? 'fa-solid fa-plus' : 'fa-solid fa-pen'" class="text-lg sm:text-xl"></i>
                    <span class="hidden sm:inline text-xs font-semibold truncate w-full text-center">Add</span>
                </button>

                <!-- Home -->
                <button
                    @click="navigate('roadmap')"
                    :class="[
                        'flex flex-col items-center justify-center gap-0 sm:gap-1 px-2 sm:px-3 py-2 sm:py-2.5 rounded-lg transition-all duration-300 flex-1 min-w-0',
                        activeTab === 'roadmap' 
                            ? 'bg-gray-100 text-gray-900' 
                            : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                    ]"
                >
                    <i class="fa-solid fa-house text-lg sm:text-xl"></i>
                    <span class="hidden sm:inline text-xs font-semibold truncate w-full text-center">Home</span>
                </button>

                <!-- Advisors -->
                <button
                    @click="navigate('advisors')"
                    :class="[
                        'flex flex-col items-center justify-center gap-0 sm:gap-1 px-2 sm:px-3 py-2 sm:py-2.5 rounded-lg transition-all duration-300 flex-1 min-w-0',
                        activeTab === 'advisors' 
                            ? 'bg-gray-100 text-gray-900' 
                            : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                    ]"
                >
                    <i :class="activeTab === 'advisors' ? 'fa-solid fa-users' : 'fa-solid fa-briefcase'" class="text-lg sm:text-xl"></i>
                    <span class="hidden sm:inline text-xs font-semibold truncate w-full text-center">Advisors</span>
                </button>

                <!-- Calendar -->
                <button
                    @click="navigate('calendar')"
                    :class="[
                        'flex flex-col items-center justify-center gap-0 sm:gap-1 px-2 sm:px-3 py-2 sm:py-2.5 rounded-lg transition-all duration-300 flex-1 min-w-0',
                        activeTab === 'calendar' 
                            ? 'bg-gray-100 text-gray-900' 
                            : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                    ]"
                >
                    <i :class="activeTab === 'calendar' ? 'fa-solid fa-calendar-days' : 'fa-regular fa-calendar'" class="text-lg sm:text-xl"></i>
                    <span class="hidden sm:inline text-xs font-semibold truncate w-full text-center">Calendar</span>
                </button>

                <!-- Network -->
                <button
                    @click="navigate('network')"
                    :class="[
                        'flex flex-col items-center justify-center gap-0 sm:gap-1 px-2 sm:px-3 py-2 sm:py-2.5 rounded-lg transition-all duration-300 flex-1 min-w-0',
                        activeTab === 'network' 
                            ? 'bg-gray-100 text-gray-900' 
                            : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50'
                    ]"
                >
                    <i :class="activeTab === 'network' ? 'fa-solid fa-network-wired' : 'fa-solid fa-building'" class="text-lg sm:text-xl"></i>
                    <span class="hidden sm:inline text-xs font-semibold truncate w-full text-center">Network</span>
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

