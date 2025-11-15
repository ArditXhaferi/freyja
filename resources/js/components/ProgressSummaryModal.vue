<template>
    <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center p-4 modal-overlay" @click.self="close">
        <div class="bg-white rounded-lg shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto modal-content">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between rounded-t-lg">
                <h2 class="text-2xl font-bold text-gray-800">Your Progress Summary</h2>
                <button @click="close" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <!-- Overall Progress -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Overall Progress</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Business Plan Progress -->
                        <div class="bg-blue-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Business Plan</span>
                                <span class="text-lg font-bold text-blue-600">{{ businessPlanPercentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div 
                                    class="bg-blue-600 h-3 rounded-full transition-all duration-500"
                                    :style="{ width: `${businessPlanPercentage}%` }"
                                ></div>
                            </div>
                            <p class="text-xs text-gray-600 mt-2">{{ businessPlanCompleted }} / {{ businessPlanTotal }} fields</p>
                        </div>

                        <!-- Roadmap Progress -->
                        <div class="bg-green-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Roadmap</span>
                                <span class="text-lg font-bold text-green-600">{{ roadmapPercentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div 
                                    class="bg-green-600 h-3 rounded-full transition-all duration-500"
                                    :style="{ width: `${roadmapPercentage}%` }"
                                ></div>
                            </div>
                            <p class="text-xs text-gray-600 mt-2">{{ roadmapCompleted }} / {{ roadmapTotal }} steps</p>
                        </div>

                        <!-- Overall Completion -->
                        <div class="bg-purple-50 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-medium text-gray-700">Overall</span>
                                <span class="text-lg font-bold text-purple-600">{{ overallPercentage }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-3">
                                <div 
                                    class="bg-purple-600 h-3 rounded-full transition-all duration-500"
                                    :style="{ width: `${overallPercentage}%` }"
                                ></div>
                            </div>
                            <p class="text-xs text-gray-600 mt-2">Combined progress</p>
                        </div>
                    </div>
                </div>

                <!-- Completed Achievements -->
                <div class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Completed Achievements</h3>
                    <div class="space-y-3">
                        <div 
                            v-for="achievement in achievements" 
                            :key="achievement.id"
                            class="flex items-center gap-3 bg-green-50 rounded-lg p-3 border-l-4 border-green-500"
                        >
                            <div class="text-2xl">✅</div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">{{ achievement.title }}</p>
                                <p class="text-sm text-gray-600">{{ achievement.description }}</p>
                            </div>
                            <div class="text-sm text-gray-500">{{ achievement.date }}</div>
                        </div>
                        <div v-if="achievements.length === 0" class="text-center py-8 text-gray-500">
                            <p>Keep going! Complete more steps to unlock achievements.</p>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Recommended Next Steps</h3>
                    <div class="space-y-2">
                        <div 
                            v-for="(step, index) in nextSteps" 
                            :key="index"
                            class="flex items-start gap-3 bg-blue-50 rounded-lg p-3"
                        >
                            <div class="text-blue-600 font-bold mt-1">{{ index + 1 }}.</div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-800">{{ step.title }}</p>
                                <p class="text-sm text-gray-600">{{ step.description }}</p>
                            </div>
                        </div>
                        <div v-if="nextSteps.length === 0" class="text-center py-4 text-gray-500">
                            <p>All steps completed! Great job! 🎉</p>
                        </div>
                    </div>
                </div>

                <!-- Summary Stats -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Summary Statistics</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ roadmapTotal }}</div>
                            <div class="text-xs text-gray-600">Total Steps</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-green-600">{{ roadmapCompleted }}</div>
                            <div class="text-xs text-gray-600">Completed</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-yellow-600">{{ roadmapInProgress }}</div>
                            <div class="text-xs text-gray-600">In Progress</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold text-gray-600">{{ roadmapPending }}</div>
                            <div class="text-xs text-gray-600">Pending</div>
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex justify-end">
                    <button
                        @click="close"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    businessPlan: {
        type: Object,
        default: () => ({})
    },
    roadmap: {
        type: Object,
        default: () => ({ steps: [] })
    },
    summaryData: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['close']);

const businessPlanTotal = 33; // Total business plan fields
const businessPlanCompleted = computed(() => {
    const bp = props.businessPlan || {};
    return Object.values(bp).filter(v => 
        v !== null && v !== undefined && v !== '' && 
        !(Array.isArray(v) && v.length === 0) &&
        !(typeof v === 'object' && Object.keys(v).length === 0)
    ).length;
});

const businessPlanPercentage = computed(() => {
    return Math.round((businessPlanCompleted.value / businessPlanTotal) * 100);
});

const roadmapSteps = computed(() => {
    return props.roadmap?.steps || props.roadmap?.roadmap_json?.steps || [];
});

const roadmapTotal = computed(() => roadmapSteps.value.length);
const roadmapCompleted = computed(() => roadmapSteps.value.filter(s => s.status === 'completed').length);
const roadmapInProgress = computed(() => roadmapSteps.value.filter(s => s.status === 'in_progress').length);
const roadmapPending = computed(() => roadmapSteps.value.filter(s => s.status === 'pending' || !s.status).length);

const roadmapPercentage = computed(() => {
    if (roadmapTotal.value === 0) return 0;
    return Math.round((roadmapCompleted.value / roadmapTotal.value) * 100);
});

const overallPercentage = computed(() => {
    return Math.round((businessPlanPercentage.value + roadmapPercentage.value) / 2);
});

const achievements = computed(() => {
    const achievements = [];
    const today = new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    
    if (businessPlanCompleted.value >= 10) {
        achievements.push({
            id: 'bp-10',
            title: 'Business Plan Starter',
            description: 'Filled in 10+ business plan fields',
            date: today
        });
    }
    
    if (roadmapCompleted.value >= 3) {
        achievements.push({
            id: 'rm-3',
            title: 'Roadmap Explorer',
            description: 'Completed 3+ roadmap steps',
            date: today
        });
    }
    
    if (roadmapCompleted.value >= 5) {
        achievements.push({
            id: 'rm-5',
            title: 'Progress Champion',
            description: 'Completed 5+ roadmap steps',
            date: today
        });
    }
    
    if (overallPercentage.value >= 50) {
        achievements.push({
            id: 'halfway',
            title: 'Halfway Hero',
            description: 'Reached 50% overall completion',
            date: today
        });
    }
    
    return achievements;
});

const nextSteps = computed(() => {
    const steps = [];
    const roadmapStepsList = roadmapSteps.value;
    
    // Get pending steps
    const pendingSteps = roadmapStepsList
        .filter(s => s.status === 'pending' || !s.status)
        .slice(0, 3);
    
    pendingSteps.forEach(step => {
        steps.push({
            title: step.title || 'Complete next step',
            description: step.description || 'Continue your journey'
        });
    });
    
    // If no pending steps, suggest business plan completion
    if (steps.length === 0 && businessPlanPercentage.value < 100) {
        steps.push({
            title: 'Complete Business Plan',
            description: 'Fill in remaining business plan fields to get personalized guidance'
        });
    }
    
    return steps;
});

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
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

@keyframes slideUp {
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

