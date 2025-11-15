<template>
    <div class="relative" ref="wrapperRef">
        <label class="text-xs font-semibold uppercase tracking-widest text-slate-400">
            {{ label }}
        </label>
        <button
            type="button"
            class="mt-2 flex w-full items-center justify-between rounded-2xl border border-slate-200 bg-[#f6f9ff] px-4 py-3 text-left text-sm text-slate-600 transition hover:border-[#4da0ff]"
            @click.stop="toggleOpen"
        >
            <div class="flex items-center gap-3">
                <svg viewBox="0 0 24 24" class="h-4 w-4 text-[#4da0ff]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" />
                    <line x1="16" y1="2" x2="16" y2="6" />
                    <line x1="8" y1="2" x2="8" y2="6" />
                    <line x1="3" y1="10" x2="21" y2="10" />
                </svg>
                <span class="font-medium" :class="modelValue ? 'text-slate-700' : 'text-slate-400'">
                    {{ displayDate }}
                </span>
            </div>
            <svg
                viewBox="0 0 24 24"
                class="h-4 w-4 text-slate-500 transition-transform duration-200"
                :class="state.open ? 'rotate-180' : ''"
                fill="none"
                stroke="currentColor"
                stroke-width="1.6"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path d="M6 9l6 6 6-6" />
            </svg>
        </button>

        <transition name="soft-dropdown">
            <div
                v-if="state.open"
                class="soft-picker-panel absolute z-30 mt-2 w-full rounded-2xl border border-slate-100 bg-white/95 p-4 shadow-lg shadow-blue-100/70 backdrop-blur"
            >
                <div class="flex items-center justify-between text-sm text-slate-600">
                    <button
                        type="button"
                        class="rounded-xl border border-slate-200 p-2 text-slate-500 transition hover:border-[#4da0ff] hover:text-[#0f2e5a]"
                        @click="shiftMonth(-1)"
                    >
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 18l-6-6 6-6" />
                        </svg>
                    </button>
                    <div class="text-sm font-semibold text-[#0f2e5a]">
                        {{ monthLabel }}
                    </div>
                    <button
                        type="button"
                        class="rounded-xl border border-slate-200 p-2 text-slate-500 transition hover:border-[#4da0ff] hover:text-[#0f2e5a]"
                        @click="shiftMonth(1)"
                    >
                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 6l6 6-6 6" />
                        </svg>
                    </button>
                </div>
                <div class="mt-3 grid grid-cols-7 gap-1 text-center text-[11px] font-semibold uppercase tracking-widest text-slate-400">
                    <span v-for="dow in daysOfWeek" :key="dow">{{ dow }}</span>
                </div>
                <div class="mt-2 grid grid-cols-7 gap-1">
                    <button
                        v-for="day in calendarDays"
                        :key="day.key"
                        type="button"
                        class="flex h-9 items-center justify-center rounded-xl text-sm transition"
                        :class="dayClasses(day)"
                        :disabled="day.isDisabled"
                        @click="selectDate(day.date)"
                    >
                        {{ day.label }}
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { computed, reactive, ref, watch, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
    modelValue: {
        type: String,
        default: ''
    },
    label: {
        type: String,
        default: 'Select date'
    },
    minDate: {
        type: String,
        default: ''
    }
});

const emit = defineEmits(['update:modelValue']);

const state = reactive({
    focusedMonth: props.modelValue ? new Date(props.modelValue) : new Date(),
    open: false
});
const wrapperRef = ref(null);

const daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

const monthLabel = computed(() =>
    state.focusedMonth.toLocaleDateString(undefined, { month: 'long', year: 'numeric' })
);

const startOfWeek = (date) => {
    const day = date.getDay();
    const diff = (day === 0 ? -6 : 1) - day; // convert to Monday as first day
    const target = new Date(date);
    target.setDate(date.getDate() + diff);
    target.setHours(0, 0, 0, 0);
    return target;
};

const buildCalendar = () => {
    const firstOfMonth = new Date(state.focusedMonth.getFullYear(), state.focusedMonth.getMonth(), 1);
    const calendarStart = startOfWeek(firstOfMonth);
    const days = [];

    for (let i = 0; i < 42; i++) {
        const current = new Date(calendarStart);
        current.setDate(calendarStart.getDate() + i);
        const isCurrentMonth = current.getMonth() === state.focusedMonth.getMonth();
        const key = current.toISOString();
        days.push({
            key,
            date: current,
            label: current.getDate(),
            isCurrentMonth,
            isToday: isSameDay(current, new Date()),
            isSelected: props.modelValue ? isSameDay(current, new Date(props.modelValue)) : false,
            isDisabled: props.minDate ? current < new Date(props.minDate) : false
        });
    }
    return days;
};

const calendarDays = computed(() => buildCalendar());

function isSameDay(a, b) {
    return a.getFullYear() === b.getFullYear() && a.getMonth() === b.getMonth() && a.getDate() === b.getDate();
}

const shiftMonth = (value) => {
    state.focusedMonth = new Date(state.focusedMonth.getFullYear(), state.focusedMonth.getMonth() + value, 1);
};

const selectDate = (date) => {
    emit('update:modelValue', date.toISOString().slice(0, 10));
    state.open = false;
};

watch(
    () => props.modelValue,
    (value) => {
        if (value) {
            state.focusedMonth = new Date(value);
        }
    }
);

const displayDate = computed(() => {
    if (!props.modelValue) return 'Select date';
    const date = new Date(props.modelValue);
    return date.toLocaleDateString(undefined, {
        weekday: 'short',
        month: 'short',
        day: 'numeric'
    });
});

const toggleOpen = () => {
    state.open = !state.open;
};

const handleClickOutside = (event) => {
    if (wrapperRef.value && !wrapperRef.value.contains(event.target)) {
        state.open = false;
    }
};

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});

const dayClasses = (day) => {
    if (!day.isCurrentMonth) {
        return 'text-slate-300';
    }
    if (day.isSelected) {
        return 'bg-gradient-to-r from-[#8fc9ff] to-[#4da0ff] text-white font-semibold shadow';
    }
    if (day.isToday) {
        return 'border border-[#4da0ff] text-[#0f2e5a]';
    }
    return 'text-slate-600 hover:bg-[#e4f0ff]';
};
</script>

<style scoped>
.soft-picker-panel {
    animation: softFade 0.2s ease;
}

.soft-dropdown-enter-from,
.soft-dropdown-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}

.soft-dropdown-enter-active,
.soft-dropdown-leave-active {
    transition: opacity 0.18s ease, transform 0.18s ease;
}

@keyframes softFade {
    from {
        opacity: 0;
        transform: translateY(-4px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

