<template>
    <div class="bg-gradient-to-r from-green-400 via-blue-500 to-purple-600 rounded-2xl shadow-xl p-4 mb-6 sticky top-4 z-40">
        <div class="flex items-center justify-between flex-wrap gap-4">
            <!-- Left: Level & Avatar -->
            <div class="flex items-center gap-4">
                <!-- Level Badge -->
                <div class="relative">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-lg border-4 border-yellow-400">
                        <span class="text-2xl font-bold text-gray-800">{{ level }}</span>
                    </div>
                    <div class="absolute -top-1 -right-1 bg-yellow-400 rounded-full w-6 h-6 flex items-center justify-center border-2 border-white">
                        <span class="text-xs font-bold">⭐</span>
                    </div>
                </div>
                
                <!-- XP Progress Bar -->
                <div class="flex-1 min-w-[200px] max-w-[300px]">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-bold text-white">{{ currentXP }} XP</span>
                        <span class="text-xs font-medium text-white/90">{{ xpForNextLevel }} XP to Level {{ level + 1 }}</span>
                    </div>
                    <div class="w-full bg-white/30 rounded-full h-3 overflow-hidden shadow-inner">
                        <div 
                            class="bg-gradient-to-r from-yellow-300 to-yellow-500 h-3 rounded-full transition-all duration-1000 ease-out relative overflow-hidden"
                            :style="{ width: `${xpProgressPercentage}%` }"
                        >
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-shimmer"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Streak & Daily Goal -->
            <div class="flex items-center gap-4">
                <!-- Daily Streak -->
                <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2 border-2 border-white/30">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">🔥</span>
                        <div>
                            <div class="text-xs text-white/80 font-medium">Streak</div>
                            <div class="text-lg font-bold text-white">{{ streak }} days</div>
                        </div>
                    </div>
                </div>

                <!-- Daily Goal -->
                <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-2 border-2 border-white/30">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">🎯</span>
                        <div>
                            <div class="text-xs text-white/80 font-medium">Daily Goal</div>
                            <div class="text-lg font-bold text-white">{{ dailyXP }}/{{ dailyGoal }} XP</div>
                        </div>
                    </div>
                </div>

                <!-- Hearts/Lives (Duolingo style) -->
                <div class="flex items-center gap-1">
                    <div 
                        v-for="i in 5" 
                        :key="i"
                        class="w-8 h-8 rounded-full flex items-center justify-center"
                        :class="i <= hearts ? 'bg-red-500' : 'bg-white/20'"
                    >
                        <span class="text-lg">❤️</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- XP Gain Animation -->
        <Transition name="xp-gain">
            <div v-if="showXPGain" class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-full bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg font-bold text-lg animate-bounce">
                +{{ xpGainAmount }} XP
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
    level: {
        type: Number,
        default: 1
    },
    xp: {
        type: Number,
        default: 0
    },
    streak: {
        type: Number,
        default: 0
    },
    dailyXP: {
        type: Number,
        default: 0
    },
    dailyGoal: {
        type: Number,
        default: 50
    },
    hearts: {
        type: Number,
        default: 5
    }
});

const emit = defineEmits(['level-up']);

// Calculate XP needed for each level (Duolingo-style: exponential growth)
const getXPForLevel = (level) => {
    // Level 1: 0 XP, Level 2: 50 XP, Level 3: 100 XP, etc.
    // Exponential: 50 * (level - 1) * 1.5^(level - 2)
    if (level === 1) return 0;
    return Math.floor(50 * (level - 1) * Math.pow(1.5, level - 2));
};

const currentLevelXP = computed(() => getXPForLevel(props.level));
const nextLevelXP = computed(() => getXPForLevel(props.level + 1));
const xpForNextLevel = computed(() => {
    const needed = nextLevelXP.value - props.xp;
    return Math.max(0, needed);
});
const currentXP = computed(() => props.xp);
const xpProgressPercentage = computed(() => {
    const xpInCurrentLevel = props.xp - currentLevelXP.value;
    const xpNeededForNext = nextLevelXP.value - currentLevelXP.value;
    if (xpNeededForNext === 0) return 100;
    return Math.min(100, Math.max(0, (xpInCurrentLevel / xpNeededForNext) * 100));
});

const showXPGain = ref(false);
const xpGainAmount = ref(0);
let previousXP = props.xp;

watch(() => props.xp, (newXP) => {
    const xpGained = newXP - previousXP;
    if (xpGained > 0) {
        xpGainAmount.value = xpGained;
        showXPGain.value = true;
        setTimeout(() => {
            showXPGain.value = false;
        }, 2000);
    }
    previousXP = newXP;

    // Check for level up
    if (newXP >= nextLevelXP.value && props.level < 50) {
        emit('level-up', props.level + 1);
    }
});
</script>

<style scoped>
@keyframes shimmer {
    0% {
        transform: translateX(-100%);
    }
    100% {
        transform: translateX(100%);
    }
}

.animate-shimmer {
    animation: shimmer 2s infinite;
}

.xp-gain-enter-active {
    transition: all 0.3s ease-out;
}

.xp-gain-enter-from {
    opacity: 0;
    transform: translate(-50%, -20px);
}

.xp-gain-enter-to {
    opacity: 1;
    transform: translate(-50%, -100%);
}

.xp-gain-leave-active {
    transition: all 0.3s ease-in;
}

.xp-gain-leave-from {
    opacity: 1;
    transform: translate(-50%, -100%);
}

.xp-gain-leave-to {
    opacity: 0;
    transform: translate(-50%, -120px);
}
</style>

