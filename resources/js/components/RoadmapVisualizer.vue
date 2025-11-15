<template>
    <div class="w-full">
        <!-- Cute Header -->
        <div v-if="roadmap?.title || roadmap?.roadmap_json?.title" class="mb-4 bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 rounded-2xl p-4 shadow-lg">
            <div class="flex items-center gap-3">
                <div class="text-3xl">🗺️</div>
                <h2 class="text-xl font-bold text-white">
                    {{ roadmap.title || roadmap.roadmap_json?.title }}
                </h2>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="!roadmap || (!roadmap.steps && (!roadmap.roadmap_json || !roadmap.roadmap_json.steps)) || (roadmap.steps && roadmap.steps.length === 0) || (roadmap.roadmap_json && roadmap.roadmap_json.steps && roadmap.roadmap_json.steps.length === 0)" 
             class="border-2 border-dashed border-blue-300 bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl p-8 text-center">
            <div class="text-5xl mb-3">🗺️</div>
            <p class="font-bold text-gray-700">No Roadmap Yet</p>
            <p class="text-sm text-gray-500 mt-1">Start a voice session to build your roadmap!</p>
        </div>

        <!-- Cute Roadmap Cards -->
        <div v-else class="space-y-3">
            <div
                v-for="(step, index) in animatedSteps"
                :key="step.id || step._stepId || index"
                :class="[
                    'rounded-2xl p-4 transition-all duration-300 cursor-pointer shadow-md hover:shadow-xl hover:scale-[1.02]',
                    step.status === 'completed' 
                        ? 'bg-gradient-to-br from-green-100 to-emerald-200 border-2 border-green-300' 
                        : step.status === 'in_progress'
                        ? 'bg-gradient-to-br from-blue-100 to-purple-200 border-2 border-blue-300'
                        : 'bg-gradient-to-br from-gray-50 to-blue-50 border-2 border-gray-200',
                    step.isNewlyAdded ? 'animate-pulse border-yellow-400' : ''
                ]"
                @click="handleStepClick(step)"
            >
                <div class="flex items-start gap-4">
                    <!-- Cute Step Number Badge -->
                    <div :class="[
                        'w-12 h-12 rounded-full flex items-center justify-center text-lg font-bold shadow-lg flex-shrink-0 transition-all hover:scale-110',
                        step.status === 'completed' 
                            ? 'bg-gradient-to-br from-green-400 to-emerald-500 text-white' 
                            : step.status === 'in_progress'
                            ? 'bg-gradient-to-br from-blue-400 to-purple-500 text-white animate-pulse'
                            : 'bg-gradient-to-br from-gray-300 to-gray-400 text-gray-700'
                    ]">
                        <span v-if="step.status === 'completed'" class="text-2xl">✓</span>
                        <span v-else-if="step.status === 'in_progress'" class="text-xl">⟳</span>
                        <span v-else>{{ step.order || index + 1 }}</span>
                    </div>

                    <!-- Step Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <h3 :class="[
                                'font-bold text-base',
                                step.status === 'completed' ? 'text-green-800' : step.status === 'in_progress' ? 'text-blue-800' : 'text-gray-700'
                            ]">
                                {{ step.title || `Step ${step.order || index + 1}` }}
                            </h3>
                            <span :class="[
                                'px-3 py-1 rounded-full text-xs font-bold shadow-sm',
                                step.status === 'completed' 
                                    ? 'bg-green-300 text-green-800' 
                                    : step.status === 'in_progress'
                                    ? 'bg-blue-300 text-blue-800'
                                    : 'bg-gray-200 text-gray-600'
                            ]">
                                {{ (step.status || 'pending').replace('_', ' ').toUpperCase() }}
                            </span>
                            <span v-if="step.isNewlyAdded" class="px-3 py-1 rounded-full bg-gradient-to-r from-yellow-300 to-orange-300 text-yellow-900 text-xs font-bold shadow-sm animate-bounce">
                                ✨ NEW
                            </span>
                        </div>
                        
                        <p v-if="step.description" :class="[
                            'text-sm leading-relaxed line-clamp-2',
                            step.status === 'completed' ? 'text-green-700' : step.status === 'in_progress' ? 'text-blue-700' : 'text-gray-600'
                        ]">
                            {{ step.description }}
                        </p>

                        <!-- Cute Progress Bar for In Progress -->
                        <div v-if="step.status === 'in_progress'" class="mt-3 pt-3 border-t border-blue-200">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 bg-white/60 rounded-full overflow-hidden">
                                    <div 
                                        class="h-full bg-gradient-to-r from-blue-400 to-purple-400 rounded-full transition-all duration-500"
                                        style="width: 50%"
                                    ></div>
                                </div>
                                <span class="text-xs font-bold text-blue-700">50%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    roadmap: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['stepUpdate']);

const animatedSteps = ref([]);
const previousStepIds = ref(new Set());

watch(() => props.roadmap, (newRoadmap) => {
    if (!newRoadmap) {
        animatedSteps.value = [];
        previousStepIds.value = new Set();
        return;
    }
    
    const allSteps = newRoadmap.steps || (newRoadmap.roadmap_json && newRoadmap.roadmap_json.steps) || [];
    
    if (!allSteps || allSteps.length === 0) {
        animatedSteps.value = [];
        previousStepIds.value = new Set();
        return;
    }

    const roadmapSteps = allSteps.filter(step => !step.isQuestion);
    
    if (roadmapSteps.length === 0) {
        animatedSteps.value = [];
        previousStepIds.value = new Set();
        return;
    }

    const currentStepIds = new Set(roadmapSteps.map(s => s.id || `${s.order}_${s.title}`));
    const newlyAddedIds = new Set();
    
    roadmapSteps.forEach(step => {
        const stepId = step.id || `${step.order}_${step.title}`;
        if (!previousStepIds.value.has(stepId)) {
            newlyAddedIds.add(stepId);
        }
    });

    animatedSteps.value = roadmapSteps.map((step) => {
        const stepId = step.id || `${step.order}_${step.title}`;
        return {
            ...step,
            isNewlyAdded: newlyAddedIds.has(stepId),
            _stepId: stepId
        };
    });
    
    previousStepIds.value = currentStepIds;
}, { immediate: true, deep: true });

const handleStepClick = (step) => {
    emit('stepUpdate', step);
};
</script>

<style scoped>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
