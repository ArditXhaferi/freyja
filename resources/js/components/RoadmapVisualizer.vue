<template>
    <div class="w-full">
        <!-- Header -->
        <div v-if="roadmap?.title || roadmap?.roadmap_json?.title" class="mb-4 bg-[#5cc094] rounded-lg p-4">
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-bold text-white">
                    {{ roadmap.title || roadmap.roadmap_json?.title }}
                </h2>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="!roadmap || (!roadmap.steps && (!roadmap.roadmap_json || !roadmap.roadmap_json.steps)) || (roadmap.steps && roadmap.steps.length === 0) || (roadmap.roadmap_json && roadmap.roadmap_json.steps && roadmap.roadmap_json.steps.length === 0)" 
             class="border border-dashed border-gray-300 bg-white rounded-lg p-8 text-center">
            <p class="font-bold text-gray-900">No Roadmap Yet</p>
            <p class="text-sm text-gray-600 mt-1">Start a voice session to build your roadmap!</p>
        </div>

        <!-- Roadmap Cards -->
        <div v-else class="space-y-3">
            <div
                v-for="(step, index) in animatedSteps"
                :key="step.id || step._stepId || index"
                :class="[
                    'rounded-lg p-4 cursor-pointer border',
                    step.status === 'completed' 
                        ? 'bg-white border-[#5cc094]' 
                        : step.status === 'in_progress'
                        ? 'bg-white border-gray-300'
                        : 'bg-white border-gray-200'
                ]"
                @click="handleStepClick(step)"
            >
                <div class="flex items-start gap-4">
                    <!-- Step Number Badge -->
                    <div :class="[
                        'w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0 border',
                        step.status === 'completed' 
                            ? 'bg-[#5cc094] text-white border-[#5cc094]' 
                            : step.status === 'in_progress'
                            ? 'bg-blue-500 text-white border-blue-300'
                            : 'bg-gray-300 text-gray-900 border-gray-400'
                    ]">
                        <span v-if="step.status === 'completed'" class="text-lg">✓</span>
                        <span v-else-if="step.status === 'in_progress'" class="text-sm">⟳</span>
                        <span v-else>{{ step.order || index + 1 }}</span>
                    </div>

                    <!-- Step Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <h3 class="font-bold text-base text-gray-900">
                                {{ step.title || `Step ${step.order || index + 1}` }}
                            </h3>
                            <span :class="[
                                'px-3 py-1 rounded-full text-xs font-semibold',
                                step.status === 'completed' 
                                    ? 'bg-[#5cc094]/30 text-[#5cc094] border border-[#5cc094]/50' 
                                    : step.status === 'in_progress'
                                    ? 'bg-blue-500 text-white border border-blue-300'
                                    : 'bg-gray-100 text-gray-700 border border-gray-300'
                            ]">
                                {{ (step.status || 'pending').replace('_', ' ').toUpperCase() }}
                            </span>
                        </div>
                        
                        <p v-if="step.description" class="text-sm leading-relaxed line-clamp-2 text-gray-700">
                            {{ step.description }}
                        </p>

                        <!-- Resources Section -->
                        <div v-if="step.resources && step.resources.length > 0" class="mt-3 pt-3 border-t border-gray-300">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs font-semibold text-gray-600">Resources:</span>
                            </div>
                            <div class="space-y-2">
                                <a
                                    v-for="(resource, resIndex) in step.resources"
                                    :key="resIndex"
                                    :href="resource.url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="block bg-gray-50 rounded-lg p-2 border border-gray-200 hover:border-gray-400 hover:bg-gray-100 group"
                                >
                                    <div class="flex items-start gap-2">
                                        <span class="text-gray-500 text-sm group-hover:text-gray-700">🔗</span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-gray-900 group-hover:text-gray-700">
                                                {{ resource.title }}
                                            </p>
                                            <p v-if="resource.description" class="text-xs text-gray-600 mt-0.5 line-clamp-1">
                                                {{ resource.description }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1 truncate">
                                                {{ resource.url }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Progress Bar for In Progress -->
                        <div v-if="step.status === 'in_progress'" class="mt-3 pt-3 border-t border-gray-300">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div 
                                        class="h-full bg-[#5cc094] rounded-full"
                                        style="width: 50%"
                                    ></div>
                                </div>
                                <span class="text-xs font-semibold text-gray-600">50%</span>
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
