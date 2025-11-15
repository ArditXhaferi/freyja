<template>
    <div class="w-full">
        <!-- Header -->
        <div v-if="roadmap?.title || roadmap?.roadmap_json?.title || hasRoadmapSteps" class="mb-4 bg-[#012169] rounded-lg p-4 shadow-lg">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-xl font-bold text-white">
                    {{ roadmap.title || roadmap.roadmap_json?.title || 'My Startup Roadmap' }}
                </h2>
                <button
                    v-if="hasRoadmapSteps"
                    @click="handleRebuild"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all font-semibold shadow-lg flex items-center gap-2 text-sm"
                    title="Rebuild your roadmap from scratch"
                >
                    <i class="fa-solid fa-rotate-right"></i>
                    <span>Rebuild Roadmap</span>
                </button>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="!roadmap || (!roadmap.steps && (!roadmap.roadmap_json || !roadmap.roadmap_json.steps)) || (roadmap.steps && roadmap.steps.length === 0) || (roadmap.roadmap_json && roadmap.roadmap_json.steps && roadmap.roadmap_json.steps.length === 0)" 
             class="border border-dashed border-white/30 bg-[#011135] rounded-lg p-8 text-center">
            <p class="font-bold text-white">No Roadmap Yet</p>
            <p class="text-sm text-white/70 mt-1">Start a voice session to build your roadmap!</p>
        </div>

        <!-- Roadmap Cards -->
        <div v-else class="space-y-3">
            <div
                v-for="(step, index) in animatedSteps"
                :key="step.id || step._stepId || index"
                :class="[
                    'rounded-lg p-4 transition-all duration-300 cursor-pointer shadow-md hover:shadow-lg border',
                    step.status === 'completed' 
                        ? 'bg-[#011135] border-green-500/50' 
                        : step.status === 'in_progress'
                        ? 'bg-[#011135] border-[#012169]'
                        : 'bg-[#011135] border-white/20',
                    step.isNewlyAdded ? 'animate-pulse border-white' : ''
                ]"
                @click="handleStepClick(step)"
            >
                <div class="flex items-start gap-4">
                    <!-- Step Number Badge -->
                    <div :class="[
                        'w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0 transition-all border',
                        step.status === 'completed' 
                            ? 'bg-green-600 text-white border-green-400' 
                            : step.status === 'in_progress'
                            ? 'bg-[#012169] text-white border-white/30 animate-pulse'
                            : 'bg-[#012169] text-white border-white/20'
                    ]">
                        <span v-if="step.status === 'completed'" class="text-lg">✓</span>
                        <span v-else-if="step.status === 'in_progress'" class="text-sm">⟳</span>
                        <span v-else>{{ step.order || index + 1 }}</span>
                    </div>

                    <!-- Step Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <h3 class="font-bold text-base text-white">
                                {{ step.title || `Step ${step.order || index + 1}` }}
                            </h3>
                            <span :class="[
                                'px-3 py-1 rounded-full text-xs font-semibold',
                                step.status === 'completed' 
                                    ? 'bg-green-600/30 text-green-300 border border-green-500/50' 
                                    : step.status === 'in_progress'
                                    ? 'bg-[#012169] text-white border border-white/30'
                                    : 'bg-white/10 text-white/70 border border-white/20'
                            ]">
                                {{ (step.status || 'pending').replace('_', ' ').toUpperCase() }}
                            </span>
                            <span v-if="step.isNewlyAdded" class="px-3 py-1 rounded-full bg-white/20 text-white text-xs font-semibold border border-white/30">
                                NEW
                            </span>
                        </div>
                        
                        <p v-if="step.description" class="text-sm leading-relaxed line-clamp-2 text-white/80">
                            {{ step.description }}
                        </p>

                        <!-- Resources Section -->
                        <div v-if="step.resources && step.resources.length > 0" class="mt-3 pt-3 border-t border-white/20">
                            <div class="flex items-center gap-2 mb-2">
                                <span class="text-xs font-semibold text-white/70">Resources:</span>
                            </div>
                            <div class="space-y-2">
                                <a
                                    v-for="(resource, resIndex) in step.resources"
                                    :key="resIndex"
                                    :href="resource.url"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="block bg-[#012169] rounded-lg p-2 border border-white/20 hover:border-white/40 hover:bg-[#011135] transition-all group"
                                >
                                    <div class="flex items-start gap-2">
                                        <span class="text-white/60 text-sm group-hover:text-white">🔗</span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-white group-hover:text-white/90">
                                                {{ resource.title }}
                                            </p>
                                            <p v-if="resource.description" class="text-xs text-white/60 mt-0.5 line-clamp-1">
                                                {{ resource.description }}
                                            </p>
                                            <p class="text-xs text-white/50 mt-1 truncate">
                                                {{ resource.url }}
                                            </p>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>

                        <!-- Progress Bar for In Progress -->
                        <div v-if="step.status === 'in_progress'" class="mt-3 pt-3 border-t border-white/20">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-2 bg-white/10 rounded-full overflow-hidden">
                                    <div 
                                        class="h-full bg-[#012169] rounded-full transition-all duration-500"
                                        style="width: 50%"
                                    ></div>
                                </div>
                                <span class="text-xs font-semibold text-white/70">50%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';

const props = defineProps({
    roadmap: {
        type: Object,
        default: null
    }
});

const emit = defineEmits(['stepUpdate', 'rebuild']);

const hasRoadmapSteps = computed(() => {
    if (!props.roadmap) return false;
    const allSteps = props.roadmap.steps || (props.roadmap.roadmap_json && props.roadmap.roadmap_json.steps) || [];
    const roadmapSteps = allSteps.filter(step => !step.isQuestion);
    return roadmapSteps.length > 0;
});

const handleRebuild = () => {
    emit('rebuild');
};

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
