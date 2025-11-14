<template>
    <div class="w-full">
        <div v-if="!roadmap || !roadmap.steps || roadmap.steps.length === 0" 
             class="flex items-center justify-center p-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
            <div class="text-center">
                <div class="text-4xl mb-4">🗺️</div>
                <p class="text-gray-600 text-lg">No roadmap steps yet</p>
                <p class="text-gray-500 text-sm mt-2">Start a voice session to build your roadmap!</p>
            </div>
        </div>

        <div v-else>
            <div v-if="roadmap.title" class="mb-6">
                <h2 class="text-2xl font-bold text-gray-800">{{ roadmap.title }}</h2>
                <p class="text-gray-600 mt-1">Your personalized startup roadmap</p>
            </div>

            <div class="space-y-4">
                <div
                    v-for="(step, index) in animatedSteps"
                    :key="step.id || index"
                    :class="[
                        'relative bg-white rounded-lg shadow-md p-6 border-l-4 cursor-pointer',
                        'transform transition-all duration-500 ease-out hover:shadow-lg hover:scale-[1.02]',
                        getStatusColor(step.status)
                    ]"
                    :style="{
                        animationDelay: `${step.animationDelay}ms`,
                        opacity: 0,
                        animation: 'fadeInUp 0.6s ease-out forwards'
                    }"
                    @click="handleStepClick(step)"
                >
                    <div class="flex items-start gap-4">
                        <div :class="[
                            'flex-shrink-0 w-12 h-12 rounded-full text-white',
                            'flex items-center justify-center font-bold text-lg shadow-md',
                            'transition-transform duration-300 hover:scale-110',
                            getStatusColor(step.status)
                        ]">
                            {{ getStatusIcon(step.status) }}
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-xl font-semibold text-gray-800">
                                    {{ step.title || `Step ${step.order || index + 1}` }}
                                </h3>
                                <span :class="[
                                    'px-3 py-1 rounded-full text-xs font-medium',
                                    step.status === 'completed' ? 'bg-green-100 text-green-800' : '',
                                    step.status === 'in_progress' ? 'bg-blue-100 text-blue-800' : '',
                                    step.status === 'pending' ? 'bg-gray-100 text-gray-800' : ''
                                ]">
                                    {{ (step.status || 'pending').replace('_', ' ') }}
                                </span>
                            </div>
                            
                            <p v-if="step.description" class="text-gray-600 leading-relaxed">
                                {{ step.description }}
                            </p>

                            <div class="mt-3 flex items-center gap-2">
                                <span class="text-xs text-gray-500 font-medium">
                                    Step {{ step.order || index + 1 }} of {{ roadmap.steps.length }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div v-if="step.status === 'in_progress'" class="mt-4 pt-4 border-t border-gray-200">
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div 
                                    class="h-full bg-blue-500 rounded-full transition-all duration-500"
                                    style="width: 50%"
                                />
                            </div>
                            <span class="text-xs text-gray-600">In progress</span>
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

const emit = defineEmits(['stepUpdate']);

const animatedSteps = ref([]);

watch(() => props.roadmap, (newRoadmap) => {
    if (!newRoadmap || !newRoadmap.steps) {
        animatedSteps.value = [];
        return;
    }

    animatedSteps.value = newRoadmap.steps.map((step, index) => ({
        ...step,
        animationDelay: index * 100,
    }));
}, { immediate: true });

const getStatusColor = (status) => {
    switch (status) {
        case 'completed':
            return 'border-green-500';
        case 'in_progress':
            return 'border-blue-500';
        case 'pending':
        default:
            return 'border-gray-400';
    }
};

const getStatusIcon = (status) => {
    switch (status) {
        case 'completed':
            return '✓';
        case 'in_progress':
            return '⟳';
        case 'pending':
        default:
            return '○';
    }
};

const handleStepClick = (step) => {
    emit('stepUpdate', step);
};
</script>

<style scoped>
@keyframes fadeInUp {
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

