<template>
    <div>
        <label class="text-xs font-semibold uppercase tracking-widest text-slate-400">
            {{ label }}
        </label>
        <div class="mt-2 flex items-center gap-3 rounded-2xl border border-slate-200 bg-[#f6f9ff] px-4 py-3 text-sm text-slate-600 transition focus-within:border-[#4da0ff]">
            <svg viewBox="0 0 24 24" class="h-4 w-4 text-[#4da0ff]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="9" />
                <polyline points="12 7 12 12 15 14" />
            </svg>
            <input
                type="time"
                v-model="internalValue"
                class="flex-1 bg-transparent text-sm text-slate-600 placeholder:text-slate-400 focus:outline-none"
                step="300"
            />
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    label: {
        type: String,
        default: 'Select time'
    }
});

const emit = defineEmits(['update:modelValue']);

const internalValue = ref(props.modelValue || '');

watch(
    () => props.modelValue,
    (val) => {
        if (val !== internalValue.value) {
            internalValue.value = val || '';
        }
    }
);

watch(
    () => internalValue.value,
    (val) => {
        emit('update:modelValue', val);
    }
);
</script>

