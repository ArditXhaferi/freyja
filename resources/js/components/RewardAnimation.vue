<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center reward-overlay" @click="dismiss">
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4 reward-content" @click.stop>
            <!-- Confetti Canvas -->
            <canvas ref="confettiCanvas" class="absolute inset-0 pointer-events-none"></canvas>
            
            <!-- Content -->
            <div class="relative z-10 text-center">
                <!-- Celebration Icon -->
                <div class="text-7xl mb-4 animate-bounce">🎉</div>
                
                <!-- Title -->
                <h2 class="text-3xl font-bold text-gray-800 mb-2">{{ reward.title || 'Congratulations!' }}</h2>
                
                <!-- Message -->
                <p class="text-lg text-gray-600 mb-6">{{ reward.message || 'You\'ve completed a step!' }}</p>
                
                <!-- Progress Bar -->
                <div v-if="showProgress" class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700">Overall Progress</span>
                        <span class="text-sm font-bold text-blue-600">{{ progressPercentage }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                        <div 
                            class="bg-gradient-to-r from-blue-500 to-green-500 h-4 rounded-full transition-all duration-1000 progress-bar-fill"
                            :style="{ width: `${progressPercentage}%` }"
                        ></div>
                    </div>
                </div>
                
                <!-- XP Gain Display -->
                <div v-if="reward.xpGained" class="mb-4">
                    <div class="inline-flex items-center gap-2 bg-gradient-to-r from-green-400 to-emerald-500 text-white px-6 py-3 rounded-full shadow-lg animate-pulse">
                        <span class="text-2xl">⭐</span>
                        <span class="font-bold text-xl">+{{ reward.xpGained }} XP</span>
                    </div>
                </div>

                <!-- Achievement Badge -->
                <div v-if="reward.achievement" class="mb-6">
                    <div class="inline-flex items-center gap-2 bg-gradient-to-r from-yellow-400 to-orange-500 text-white px-6 py-3 rounded-full shadow-lg">
                        <span class="text-2xl">🏆</span>
                        <span class="font-bold">{{ reward.achievement }}</span>
                    </div>
                </div>
                
                <!-- Step Info -->
                <div v-if="reward.step" class="bg-blue-50 rounded-lg p-4 mb-6">
                    <p class="text-sm text-gray-600 mb-1">Completed Step:</p>
                    <p class="font-semibold text-gray-800">{{ reward.step.title || reward.step }}</p>
                </div>
                
                <!-- Close Button -->
                <button
                    @click="dismiss"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                >
                    Awesome!
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted } from 'vue';
import confetti from 'canvas-confetti';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    reward: {
        type: Object,
        default: () => ({
            title: 'Congratulations!',
            message: 'You\'ve completed a step!',
            step: null,
            achievement: null,
            progress: 0
        })
    }
});

const emit = defineEmits(['close']);

const confettiCanvas = ref(null);
const showProgress = ref(true);
const progressPercentage = ref(props.reward.progress || 0);
let confettiInterval = null;

const triggerConfetti = () => {
    if (!confettiCanvas.value) return;
    
    const canvas = confettiCanvas.value;
    const myConfetti = confetti.create(canvas, {
        resize: true,
        useWorker: true
    });
    
    // Burst confetti
    myConfetti({
        particleCount: 100,
        spread: 70,
        origin: { y: 0.6 }
    });
    
    // Continuous confetti for 3 seconds
    const duration = 3000;
    const animationEnd = Date.now() + duration;
    
    confettiInterval = setInterval(() => {
        const timeLeft = animationEnd - Date.now();
        
        if (timeLeft <= 0) {
            clearInterval(confettiInterval);
            return;
        }
        
        const particleCount = 50 * (timeLeft / duration);
        myConfetti({
            particleCount: Math.min(particleCount, 50),
            angle: 60,
            spread: 55,
            origin: { x: 0 },
            colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
        });
        myConfetti({
            particleCount: Math.min(particleCount, 50),
            angle: 120,
            spread: 55,
            origin: { x: 1 },
            colors: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
        });
    }, 250);
};

const animateProgress = () => {
    const target = props.reward.progress || 0;
    const start = 0;
    const duration = 1500;
    const startTime = Date.now();
    
    const animate = () => {
        const elapsed = Date.now() - startTime;
        const progress = Math.min(elapsed / duration, 1);
        
        // Easing function (ease-out)
        const easeOut = 1 - Math.pow(1 - progress, 3);
        progressPercentage.value = Math.round(start + (target - start) * easeOut);
        
        if (progress < 1) {
            requestAnimationFrame(animate);
        }
    };
    
    animate();
};

watch(() => props.show, (newVal) => {
    if (newVal) {
        // Reset progress
        progressPercentage.value = 0;
        
        // Trigger confetti
        setTimeout(() => {
            triggerConfetti();
        }, 100);
        
        // Animate progress bar
        setTimeout(() => {
            animateProgress();
        }, 500);
        
        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            dismiss();
        }, 5000);
    } else {
        // Clean up confetti
        if (confettiInterval) {
            clearInterval(confettiInterval);
            confettiInterval = null;
        }
    }
});

const dismiss = () => {
    if (confettiInterval) {
        clearInterval(confettiInterval);
        confettiInterval = null;
    }
    emit('close');
};

onUnmounted(() => {
    if (confettiInterval) {
        clearInterval(confettiInterval);
    }
});
</script>

<style scoped>
.reward-overlay {
    background-color: rgba(0, 0, 0, 0.6);
    animation: fadeIn 0.3s ease-out;
}

.reward-content {
    animation: scaleIn 0.4s ease-out;
    position: relative;
}

.progress-bar-fill {
    background: linear-gradient(90deg, #3b82f6, #10b981, #f59e0b);
    background-size: 200% 100%;
    animation: shimmer 2s infinite;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

@keyframes shimmer {
    0% {
        background-position: -200% 0;
    }
    100% {
        background-position: 200% 0;
    }
}
</style>

