<template>
    <div class="rounded-3xl border border-slate-100 bg-white p-4 shadow-inner shadow-blue-100/70">
        <div
            class="overflow-hidden rounded-2xl border border-slate-100 bg-[#f6fbff]"
            :style="{ height: `${height}px` }"
        >
            <VueFlow
                :nodes="nodes"
                :edges="edges"
                :fit-view-on-init="true"
                class="h-full"
                :zoom-on-double-click="false"
                :zoom-on-scroll="!preview"
                :pan-on-scroll="false"
                :pan-on-drag="false"
                :zoom-on-pinch="!preview"
                :min-zoom="preview ? 0.25 : 0.5"
                :max-zoom="preview ? 0.8 : 1.5"
            >
                <template #node-default="nodeProps">
                    <div
                        class="rounded-xl border border-white/40 px-2 py-1.5 text-[10px] font-semibold text-[#0f2e5a] shadow"
                        :class="nodeColor(nodeProps.data.status)"
                        :style="{ width: `${nodeWidth}px` }"
                    >
                        <p class="text-[12px] font-semibold leading-tight">
                            {{ nodeProps.data.label }}
                        </p>
                        <p class="text-[10px] text-[#1f2a37]/70 line-clamp-2">
                            {{ nodeProps.data.description || 'No details provided' }}
                        </p>
                    </div>
                </template>
            </VueFlow>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { VueFlow } from '@vue-flow/core';
import '@vue-flow/core/dist/style.css';

const props = defineProps({
    roadmap: {
        type: Object,
        required: true
    },
    height: {
        type: Number,
        default: 220
    },
    columns: {
        type: Number,
        default: 3
    },
    preview: {
        type: Boolean,
        default: false
    }
});

const nodeWidth = computed(() => (props.preview ? 90 : 170));

const nodes = computed(() => {
    const steps = props.roadmap?.steps ?? [];
    const total = steps.length || 1;
    const previewColumns = Math.max(1, Math.ceil(Math.sqrt(total)));
    const baseColumns = Math.max(1, Math.min(total, props.columns));
    const columns = props.preview ? previewColumns : baseColumns;
    const horizontalGap = props.preview ? 120 : 220;
    const verticalGap = props.preview ? 100 : 150;
    const rows = Math.max(1, Math.ceil(total / columns));
    const gridWidth = (columns - 1) * horizontalGap;
    const gridHeight = (rows - 1) * verticalGap;

    return steps.map((step, index) => {
        const col = index % columns;
        const row = Math.floor(index / columns);
        return {
            id: String(step.id ?? index + 1),
            position: {
                x: col * horizontalGap - gridWidth / 2,
                y: row * verticalGap - gridHeight / 2
            },
            data: {
                label: step.title ?? `Step ${index + 1}`,
                description: step.description,
                status: step.status ?? 'pending'
            },
            draggable: false,
            selectable: false,
            style: {
                border: '1px solid rgba(15,74,139,0.08)',
                background: '#ffffff',
                width: nodeWidth.value,
                pointerEvents: 'none'
            }
        };
    });
});

const edges = computed(() => {
    const steps = props.roadmap?.steps ?? [];
    return steps.slice(1).map((step, index) => ({
        id: `e-${steps[index].id ?? index + 1}-${step.id ?? index + 2}`,
        source: String(steps[index].id ?? index + 1),
        target: String(step.id ?? index + 2),
        type: 'smoothstep',
        animated: true,
        style: {
            stroke: '#8fc9ff',
            strokeWidth: 2
        }
    }));
});

const nodeColor = (status) => {
    switch (status) {
        case 'completed':
            return 'bg-[#e8f6ef]';
        case 'in_progress':
            return 'bg-[#e3f2ff]';
        default:
            return 'bg-[#f9fbff]';
    }
};
</script>


