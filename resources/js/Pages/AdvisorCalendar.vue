<template>
    <Head title="Advisor Calendar" />
    <div class="flex min-h-screen bg-[#f3f9ff] text-slate-700">
        <RequestSidebar :advisor="advisor" active="Calendar" />

        <main class="flex-1 px-4 pb-12 pt-8 sm:px-10">
            <div class="flex flex-wrap items-center justify-between gap-4 rounded-3xl bg-white px-6 py-4 shadow-md shadow-blue-100/60">
                <div>
                    <p class="text-xs uppercase tracking-[0.5em] text-slate-400">Calendar</p>
                    <h1 class="text-3xl font-semibold text-[#0f2e5a]">Upcoming schedules</h1>
                    <p class="text-sm text-slate-500">Track your meetings in a single glance.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button class="rounded-2xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-500" @click="setToday">
                        Today
                    </button>
                </div>
            </div>

            <section class="mt-8 grid gap-6 lg:grid-cols-[320px,1fr]">
                <div class="rounded-3xl bg-white/90 p-6 shadow-lg shadow-blue-100/70">
                    <h2 class="text-lg font-semibold text-[#0f2e5a]">Upcoming events</h2>
                    <p class="text-xs text-slate-400">Don't miss scheduled meetings</p>
                    <div class="mt-6 space-y-4">
                        <article
                            v-for="event in upcomingList"
                            :key="event.id"
                            class="rounded-2xl bg-[#f0f8ff] p-4 text-sm text-[#0f2e5a] shadow-sm shadow-blue-50"
                        >
                            <p class="text-xs font-semibold uppercase tracking-widest text-[#1c75c5]">
                                {{ event.timeRange }}
                            </p>
                            <p class="mt-1 text-base font-semibold text-[#0f2e5a]">{{ event.title }}</p>
                            <p class="text-xs text-slate-500">{{ event.subtitle }}</p>
                        </article>
                        <p v-if="!upcomingList.length" class="text-sm text-slate-400">No upcoming meetings.</p>
                    </div>
                    <div class="mt-6 rounded-2xl bg-gradient-to-br from-[#5ad4ff] to-[#4669f6] p-4 text-white shadow">
                        <p class="text-xs uppercase tracking-widest text-white/70">Conversion history</p>
                        <p class="mt-2 text-base font-semibold">Week to week performance</p>
                        <button class="mt-3 rounded-2xl bg-white/90 px-4 py-2 text-xs font-semibold text-[#3b45d5]">
                            See more
                        </button>
                    </div>
                </div>

                <div class="rounded-3xl bg-white p-6 shadow-xl shadow-blue-100/70">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="rounded-full bg-[#e1f2ff] px-4 py-1 text-xs font-semibold text-[#0d4f8b]">
                                {{ monthYearLabel }}
                            </div>
                            <div class="hidden rounded-full bg-[#eef5ff] text-xs font-semibold text-[#5d6d85] lg:flex">
                                <button
                                    v-for="mode in ['Month', 'Week', 'Day']"
                                    :key="mode"
                                    class="rounded-full px-4 py-1 transition"
                                    :class="viewMode === mode ? 'bg-white text-[#0d4f8b] shadow-sm' : ''"
                                    @click="viewMode = mode"
                                >
                                    {{ mode }}
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="rounded-full border border-slate-200 p-2" @click="prevMonth">
                                <BaseIcon name="chevron-left" class="h-4 w-4 text-slate-500" />
                            </button>
                            <button class="rounded-full border border-slate-200 p-2" @click="nextMonth">
                                <BaseIcon name="chevron-right" class="h-4 w-4 text-slate-500" />
                            </button>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-7 gap-3 text-center text-xs font-semibold uppercase tracking-widest text-slate-400">
                        <span v-for="day in weekDays" :key="day">{{ day }}</span>
                    </div>

                    <div class="mt-4 grid grid-cols-7 gap-3">
                        <div
                            v-for="day in calendarDays"
                            :key="day.iso"
                            class="rounded-2xl border border-transparent bg-[#f2f8ff] p-3 text-left transition"
                            :class="{
                                'bg-white border-blue-200 shadow-sm': day.isToday,
                                'opacity-50': !day.isCurrentMonth
                            }"
                        >
                            <p class="text-sm font-semibold text-[#0f2e5a]">{{ day.date }}</p>
                            <div class="mt-2 space-y-2">
                                <div
                                    v-for="event in day.events"
                                    :key="event.id"
                                    class="rounded-xl px-3 py-2 text-xs font-semibold text-[#0f2e5a]"
                                    :class="event.color"
                                >
                                    <p>{{ event.title }}</p>
                                    <p class="text-[10px] font-normal">{{ event.time }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</template>

<script setup>
import { computed, reactive, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import RequestSidebar from '../components/RequestSidebar.vue';
import BaseIcon from '../components/BaseIcon.vue';

const props = defineProps({
    advisor: {
        type: Object,
        required: true
    },
    meetings: {
        type: Array,
        default: () => []
    }
});

const viewMode = ref('Month');
const currentDate = ref(new Date());

const weekDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

const meetingsByDate = computed(() => {
    const map = {};
    props.meetings.forEach((meeting) => {
        if (!meeting.scheduled_at) return;
        const date = new Date(meeting.scheduled_at);
        const iso = date.toISOString().slice(0, 10);
        if (!map[iso]) {
            map[iso] = [];
        }
        map[iso].push({
            id: meeting.id,
            title: meeting.founder,
            time: date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
            color: colorForEvent(map[iso].length),
            subtitle: meeting.agenda || meeting.location || 'Meeting',
            rawDate: date
        });
    });
    return map;
});

const upcomingList = computed(() =>
    Object.values(meetingsByDate.value)
        .flat()
        .sort((a, b) => a.rawDate - b.rawDate)
        .slice(0, 3)
        .map((event) => ({
            id: event.id,
            timeRange: event.rawDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
            title: event.title,
            subtitle: event.subtitle
        }))
);

const monthYearLabel = computed(() =>
    currentDate.value.toLocaleDateString(undefined, {
        month: 'long',
        year: 'numeric'
    })
);

const calendarDays = computed(() => {
    const days = [];
    const year = currentDate.value.getFullYear();
    const month = currentDate.value.getMonth();

    const firstOfMonth = new Date(year, month, 1);
    const startOffset = (firstOfMonth.getDay() + 6) % 7; // Monday first
    const startDate = new Date(firstOfMonth);
    startDate.setDate(startDate.getDate() - startOffset);

    for (let i = 0; i < 42; i++) {
        const date = new Date(startDate);
        date.setDate(startDate.getDate() + i);
        const iso = date.toISOString().slice(0, 10);
        days.push({
            iso,
            date: date.getDate(),
            isCurrentMonth: date.getMonth() === month,
            isToday: isSameDate(date, new Date()),
            events: (meetingsByDate.value[iso] || []).map((event) => ({
                ...event,
                color: event.color
            }))
        });
    }

    return days;
});

const prevMonth = () => {
    const date = new Date(currentDate.value);
    date.setMonth(date.getMonth() - 1);
    currentDate.value = date;
};

const nextMonth = () => {
    const date = new Date(currentDate.value);
    date.setMonth(date.getMonth() + 1);
    currentDate.value = date;
};

const setToday = () => {
    currentDate.value = new Date();
};

function isSameDate(a, b) {
    return (
        a.getFullYear() === b.getFullYear() &&
        a.getMonth() === b.getMonth() &&
        a.getDate() === b.getDate()
    );
}

function colorForEvent(index) {
    const palette = ['bg-[#dff4ff]', 'bg-[#cfeaff]', 'bg-[#e6f9ff]', 'bg-[#d2f1ff]'];
    return palette[index % palette.length];
}
</script>


