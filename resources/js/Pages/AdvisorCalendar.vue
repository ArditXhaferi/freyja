<template>
    <Head title="Advisor Calendar" />
    <div class="flex min-h-screen bg-[#f2f7f5] text-slate-700">
        <RequestSidebar :advisor="advisor" active="Calendar" />

        <main class="flex-1 px-4 pb-12 pt-8 sm:px-10">
            <div class="flex flex-wrap items-center justify-between gap-4 rounded-3xl bg-white px-6 py-4 shadow-md shadow-emerald-100/60 border border-[#c3d7de]">
                <div>
                    <p class="text-xs uppercase tracking-[0.5em] text-slate-400">Calendar</p>
                    <h1 class="text-3xl font-semibold text-[#205274]">Upcoming schedules</h1>
                    <p class="text-sm text-slate-500">Track your meetings in a single glance.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button class="rounded-2xl border border-[#5cc094] bg-[#205274] px-4 py-2 text-sm font-semibold text-white hover:bg-[#1a425d]" @click="setToday">
                        Today
                    </button>
                </div>
            </div>

            <section class="mt-8 grid gap-6 lg:grid-cols-[320px,1fr]">
                <div class="rounded-3xl bg-white/90 p-6 shadow-lg shadow-emerald-100/70 border border-[#c3d7de]">
                    <h2 class="text-lg font-semibold text-[#205274]">Upcoming events</h2>
                    <p class="text-xs text-slate-400">Don't miss scheduled meetings</p>
                    <div class="mt-6 space-y-4">
                        <article
                            v-for="event in upcomingList"
                            :key="event.id"
                            class="rounded-2xl border border-[#c3d7de] bg-[#f2f8f5] p-4 text-sm text-[#205274] shadow-sm shadow-emerald-50 transition hover:shadow-md cursor-pointer"
                            @click="openMeetingModal(event)"
                        >
                            <p class="text-xs font-semibold uppercase tracking-widest text-[#5cc094]">
                                {{ event.timeRange }}
                            </p>
                            <p class="mt-1 text-base font-semibold text-[#205274]">{{ event.title }}</p>
                            <p class="text-xs text-slate-500 line-clamp-2">{{ event.subtitle }}</p>
                        </article>
                        <p v-if="!upcomingList.length" class="text-sm text-slate-400">No upcoming meetings.</p>
                    </div>
                    <div class="mt-6 rounded-2xl bg-gradient-to-br from-[#205274] to-[#5cc094] p-4 text-white shadow">
                        <p class="text-xs uppercase tracking-widest text-white/80">Conversion history</p>
                        <p class="mt-2 text-base font-semibold">Week to week performance</p>
                        <button class="mt-3 rounded-2xl bg-[#205274] border border-[#5cc094] px-4 py-2 text-xs font-semibold text-white hover:bg-[#1a425d]">
                            See more
                        </button>
                    </div>
                </div>

                <div class="rounded-3xl bg-white p-6 shadow-xl shadow-emerald-100/70 border border-[#c3d7de]">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="rounded-full bg-[#e0f5ec] px-4 py-1 text-xs font-semibold text-[#205274]">
                                {{ monthYearLabel }}
                            </div>
                            <div class="hidden rounded-full bg-[#e0f5ec] text-xs font-semibold text-[#205274] lg:flex">
                                <button
                                    v-for="mode in ['Month', 'Week', 'Day']"
                                    :key="mode"
                                    class="rounded-full px-4 py-1 transition"
                                    :class="viewMode === mode ? 'bg-white text-[#205274] shadow-sm' : ''"
                                    @click="viewMode = mode"
                                >
                                    {{ mode }}
                                </button>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button class="rounded-full border border-[#c3d7de] p-2" @click="prevMonth">
                                <BaseIcon name="chevron-left" class="h-4 w-4 text-[#205274]" />
                            </button>
                            <button class="rounded-full border border-[#c3d7de] p-2" @click="nextMonth">
                                <BaseIcon name="chevron-right" class="h-4 w-4 text-[#205274]" />
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
                            class="rounded-2xl border border-transparent bg-[#f2f7f5] p-3 text-left transition"
                            :class="{
                                'bg-white border-[#5cc094] shadow-sm': day.isToday,
                                'opacity-50': !day.isCurrentMonth
                            }"
                        >
                            <p class="text-sm font-semibold text-[#205274]">{{ day.date }}</p>
                            <div class="mt-2 space-y-2">
                                <div
                                    v-for="event in day.events"
                                    :key="event.id"
                                    class="rounded-xl px-3 py-2 text-xs font-semibold text-[#205274] transition hover:scale-[1.02] hover:shadow cursor-pointer"
                                    :class="event.color"
                                    @click="openMeetingModal(event)"
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

    <transition name="fade">
        <div
            v-if="selectedMeeting"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
        >
                    <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl">
                <header class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.5em] text-slate-400">Meeting details</p>
                        <h2 class="mt-1 text-2xl font-semibold text-[#0f2e5a]">{{ selectedMeeting.title }}</h2>
                    </div>
                    <button
                        class="rounded-full bg-slate-100 p-2 text-slate-500 hover:text-[#0f2e5a]"
                        @click="closeMeetingModal"
                    >
                        ✕
                    </button>
                </header>
                    <div class="mt-4 space-y-4 text-sm text-slate-600">
                    <div class="rounded-2xl bg-[#f6f9ff] px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Schedule</p>
                        <p class="text-base text-[#0f2e5a]">
                            {{ selectedMeeting.rawDate.toLocaleDateString(undefined, { weekday: 'long', month: 'short', day: 'numeric' }) }}
                        </p>
                        <p class="text-sm text-slate-500">
                            {{ selectedMeeting.rawDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }) }}
                        </p>
                    </div>
                    <div class="rounded-2xl border border-dashed border-slate-200 px-4 py-3">
                        <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Agenda / Notes</p>
                        <p class="mt-1 text-sm text-slate-600">
                            {{ selectedMeeting.subtitle || 'No agenda provided.' }}
                        </p>
                    </div>
                    <div class="rounded-2xl bg-gradient-to-r from-[#205274] to-[#5cc094] px-4 py-4 text-white shadow">
                        <p class="text-xs font-semibold uppercase tracking-widest text-white/70">Google Meet</p>
                        <p class="mt-2 text-base font-semibold">You can join through the Google Meet link</p>
                        <a
                            :href="selectedMeeting.meetLink"
                            target="_blank"
                            rel="noopener"
                            class="mt-3 inline-flex items-center gap-2 rounded-2xl bg-[#205274] border border-[#5cc094] px-4 py-2 text-sm font-semibold text-white transition hover:bg-[#1a425d]"
                        >
                            {{ selectedMeeting.meetLink }}
                            <svg
                                viewBox="0 0 24 24"
                                class="h-4 w-4"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.6"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            >
                                <path d="M7 17L17 7" />
                                <path d="M7 7h10v10" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </transition>
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
            ...event,
            timeRange: event.rawDate.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
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

const selectedMeeting = ref(null);

const generateMeetLink = () => {
    const chars = 'abcdefghijklmnopqrstuvwxyz';
    const segment = () =>
        Array.from({ length: 3 })
            .map(() => chars[Math.floor(Math.random() * chars.length)])
            .join('');
    return `https://meet.google.com/${segment()}-${segment()}-${segment()}`;
};

const openMeetingModal = (event) => {
    selectedMeeting.value = {
        ...event,
        meetLink: generateMeetLink()
    };
};

const closeMeetingModal = () => {
    selectedMeeting.value = null;
};
</script>


