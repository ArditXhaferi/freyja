<template>
    <Head title="Advisor Dashboard" />
    <div class="flex min-h-screen bg-[#eef5fb] text-slate-700">
        <RequestSidebar :advisor="advisor" active="Dashboard" />

        <main class="flex-1 px-4 pb-10 pt-8 sm:px-8">
            <!-- header -->
            <header class="rounded-3xl px-6 py-5">
                <div class="flex flex-col gap-3">
                    <div class="flex items-center justify-between gap-6">
                        <!-- Left: beer + tooltip -->
                        <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="relative flex items-center gap-3">
                                <img :key="beerFrameKey" :src="beerGif" alt="Cheers" class="h-[65px] w-[65px] rounded-2xl object-cover ring-4 ring-[#ecf5ff]" />
                                <div class="tooltip-wrapper" aria-live="polite">
                                    <transition name="fade-scale">
                                        <div
                                            v-if="showTooltip"
                                            class="tooltip-beer relative rounded-2xl px-4 py-2 text-sm font-semibold text-[#0f2e5a] shadow-lg"
                                        >
                                            <span class="typewriter">{{ welcomeText }}</span>
                                            <span class="tooltip-tail"></span>
                                        </div>
                                    </transition>
                                </div>
                            </div>
                        </div>

                        <!-- Right: navbar controls (fixed width) -->
                        <div class="flex justify-end w-[360px] shrink-0">
                            <div
                                class="flex w-full items-center justify-end gap-3 rounded-3xl border border-slate-100 bg-white px-4 py-2 shadow-sm shadow-blue-100"
                            >
                                <!-- Reminders / notifications -->
                                <button
                                    type="button"
                                    @click="handleReminderClick"
                                    class="relative inline-flex items-center justify-center rounded-full border border-slate-100 bg-white px-3 py-2 text-slate-600 shadow-sm hover:bg-slate-50"
                                >
                                    <BaseIcon name="bell" class="h-4 w-4 text-[#0f2e5a]" />
                                    <span
                                        v-if="reminderCount"
                                        class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-[#ff4d6a] text-[10px] font-bold text-white"
                                    >
                                        {{ reminderCount }}
                                    </span>
                                </button>

                                <!-- Small icon container (e.g. for quick actions) -->
                                <button
                                    type="button"
                                    class="hidden sm:inline-flex h-9 w-9 items-center justify-center rounded-full border border-slate-100 bg-white text-slate-500 shadow-sm hover:bg-slate-50"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="h-4 w-4"
                                    >
                                        <rect x="3" y="4" width="18" height="17" rx="3" />
                                        <path d="M3 9h18" />
                                        <path d="M9 3v3" />
                                        <path d="M15 3v3" />
                                    </svg>
                                </button>

                                <!-- Profile chip -->
                                <div
                                    class="hidden sm:flex items-center gap-2 rounded-full border border-slate-100 bg-white px-3 py-1 text-xs shadow-sm shadow-blue-50"
                                >
                                    <div class="h-7 w-7 rounded-full bg-gradient-to-br from-[#4da0ff] to-[#0f2e5a]" />
                                    <div class="flex flex-col leading-tight">
                                        <span class="font-semibold text-[#0f2e5a] truncate max-w-[120px]">
                                            {{ advisor.name }}
                                        </span>
                                        <span class="text-[10px] uppercase tracking-wide text-slate-400">Advisor</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <p class="text-xs uppercase tracking-widest text-slate-400">Dashboard</p>
                </div>
            </header>

            <!-- stats cards -->
            <section class="mt-8 grid gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div v-for="card in summaryCards" :key="card.label" class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm shadow-blue-100/70">
                    <div class="flex items-center justify-between text-slate-400 text-xs uppercase tracking-widest">
                        <span>{{ card.label }}</span>
                        <span>↗</span>
                    </div>
                    <p class="mt-3 text-3xl font-semibold text-[#0e3f73]">{{ card.value }}</p>
                    <p class="text-xs text-slate-400">{{ card.subtext }}</p>
                </div>
            </section>

            <section class="mt-8 grid gap-6 xl:grid-cols-3">
                <div class="space-y-6 xl:col-span-2">
                    <div class="grid gap-6 lg:grid-cols-2">
                        <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm shadow-blue-100/70">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs uppercase tracking-widest text-slate-400">Lead analytics</p>
                                    <h2 class="text-xl font-semibold text-[#0f2e5a]">Weekly submissions</h2>
                                </div>
                                <span class="rounded-full bg-[#e1f1ff] px-3 py-1 text-xs font-semibold text-[#0d4f8b]">+12%</span>
                            </div>
                            <div class="mt-6 flex items-end gap-3">
                                <div v-for="point in analytics" :key="point.label" class="flex flex-1 flex-col items-center gap-2">
                                    <div class="flex w-full items-end justify-center rounded-xl bg-[#e7f2fb] p-1">
                                        <div class="w-6 rounded-xl bg-gradient-to-t from-[#4da0ff] to-[#8fc9ff]" :style="{ height: point.percentage + '%' }"></div>
                                    </div>
                                    <p class="text-xs font-semibold text-slate-500">{{ point.label }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm shadow-blue-100/70">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs uppercase tracking-widest text-slate-400">Reminders</p>
                                    <h2 class="text-xl font-semibold text-[#0f2e5a]">Upcoming session</h2>
                                </div>
                                <span class="rounded-full bg-[#e1f1ff] px-3 py-1 text-xs font-semibold text-[#0d4f8b]">
                                    {{ formatDate(latestMeeting && latestMeeting.submitted_at) }}
                                </span>
                            </div>
                            <div class="mt-5 rounded-2xl border border-slate-100 bg-[#f6fbff] p-4 text-sm text-[#0f3061]">
                                <p class="text-lg font-semibold">
                                    {{ latestMeeting ? latestMeeting.founder : 'No sessions scheduled' }}
                                </p>
                                <p class="text-sm text-slate-500">
                                    {{ latestMeeting ? latestMeeting.business_summary : 'Once a meeting is submitted, it will display here.' }}
                                </p>
                                <button class="mt-4 w-full rounded-2xl bg-gradient-to-r from-[#8fc9ff] to-[#4da0ff] py-2 text-sm font-semibold text-white shadow">
                                    Start Meeting
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm shadow-blue-100/70">
                        <header class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                            <div>
                                <p class="text-xs uppercase tracking-widest text-slate-400">Meetings</p>
                                <h2 class="text-xl font-semibold text-[#0f2e5a]">Pending requests</h2>
                            </div>
                            <Link href="/advisor/meeting-requests" class="inline-flex items-center justify-center rounded-2xl border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-500 hover:text-[#0f2e5a]">
                                View all
                            </Link>
                        </header>
                        <div class="mt-5 space-y-5">
                            <article
                                v-for="request in meetingRequestsList.slice(0, 2)"
                                :key="request.id"
                                class="rounded-2xl border border-slate-100 px-4 py-4 shadow-sm shadow-blue-50"
                            >
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-semibold text-[#0f2e5a]">{{ request.founder }}</p>
                                        <p class="text-xs text-slate-400">{{ formatPreferredSlot(request.preferred_date, request.preferred_time) }}</p>
                                        <p class="mt-2 text-sm text-slate-500 text-left">
                                            {{ request.message || 'No additional context provided.' }}
                                        </p>
                                    </div>
                                    <span class="inline-flex rounded-full bg-[#e1f1ff] px-3 py-1 text-xs font-semibold text-[#0d4f8b]">New</span>
                                </div>
                                <Link href="/advisor/meeting-requests" class="mt-4 inline-flex text-xs font-semibold text-[#0d4f8b] hover:underline">
                                    Open request
                                </Link>
                            </article>
                            <p v-if="!meetingRequestsList.length" class="text-sm text-slate-400">No meeting requests waiting.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm shadow-blue-100/70">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs uppercase tracking-widest text-slate-400">Projects</p>
                                <h2 class="text-xl font-semibold text-[#0f2e5a]">Active requests</h2>
                            </div>
                            <button class="rounded-2xl border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-500">+ New</button>
                        </div>
                        <div class="mt-4 space-y-4">
                            <article v-for="project in projectList" :key="project.title" class="rounded-2xl border border-slate-100 px-4 py-3">
                                <p class="text-sm font-semibold text-[#0f2e5a]">{{ project.title }}</p>
                                <p class="text-xs text-slate-400">Status: {{ project.status }}</p>
                                <p class="text-xs text-slate-400">Requested: {{ project.due || 'N/A' }}</p>
                            </article>
                            <p v-if="!projectList.length" class="text-sm text-slate-400">No investor interest submitted yet.</p>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm shadow-blue-100/70">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs uppercase tracking-widest text-slate-400">Schedule</p>
                                <h2 class="text-xl font-semibold text-[#0f2e5a]">Upcoming meetings</h2>
                            </div>
                            <Link href="/meetings/request" class="rounded-2xl border border-slate-200 px-4 py-2 text-xs font-semibold text-slate-500 hover:text-[#0f2e5a]">
                                New request
                            </Link>
                        </div>
                        <div class="mt-4 space-y-4">
                            <article v-for="meeting in upcomingMeetingsList" :key="meeting.id" class="rounded-2xl border border-slate-100 px-4 py-3">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-[#0f2e5a]">{{ meeting.founder }}</p>
                                        <p class="text-xs text-slate-400">{{ formatDate(meeting.scheduled_at) }}</p>
                                    </div>
                                    <span :class="statusTagClass(meeting.status)" class="capitalize">{{ meeting.status }}</span>
                                </div>
                                <p class="mt-2 text-xs text-slate-400">Location: {{ meeting.location || 'Virtual / TBD' }}</p>
                            </article>
                            <p v-if="!upcomingMeetingsList.length" class="text-sm text-slate-400">No meetings scheduled yet.</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
        <ReminderModal
            :open="showReminderModal"
            :reminders="reminders"
            :loading="reminderLoading"
            :error="reminderError"
            @close="showReminderModal = false"
            @acknowledge="acknowledgeReminders"
        />
    </div>
</template>

<script setup>
import { computed, reactive, ref, onMounted, onBeforeUnmount } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import RequestSidebar from '../components/RequestSidebar.vue';
import BaseIcon from '../components/BaseIcon.vue';
import ReminderModal from '../components/ReminderModal.vue';

const props = defineProps({
    advisor: {
        type: Object,
        required: true
    },
    hero: {
        type: Object,
        required: true
    },
    stats: {
        type: Object,
        default: () => ({})
    },
    meetingLeads: {
        type: Array,
        default: () => []
    },
    interestRequests: {
        type: Array,
        default: () => []
    },
    analytics: {
        type: Array,
        default: () => []
    },
    projectList: {
        type: Array,
        default: () => []
    },
    latestMeeting: {
        type: Object,
        default: null
    },
    progress: {
        type: Object,
        default: () => ({})
    },
    meetingRequests: {
        type: Array,
        default: () => []
    },
    upcomingMeetings: {
        type: Array,
        default: () => []
    }
});

const beerGif = '/storage/beer.gif';
const beerGifDuration = 2000; // ms
const showTooltip = ref(false);
const beerFrameKey = ref(0);
const welcomeText = ref('');
let beerInterval = null;
let tooltipTimeout = null;
let typewriterTimeout = null;
const fullWelcomeText = computed(() => `Welcome back, ${props.advisor.name}!`);

const animateWelcomeText = () => {
    // reset text
    welcomeText.value = '';

    const text = fullWelcomeText.value;
    let index = 0;
    const step = beerGifDuration / Math.max(text.length, 1);

    // clear any previous timer
    if (typewriterTimeout) {
        clearTimeout(typewriterTimeout);
        typewriterTimeout = null;
    }

    const typeNext = () => {
        welcomeText.value = text.slice(0, index + 1);
        index += 1;

        if (index < text.length) {
            typewriterTimeout = setTimeout(typeNext, step);
        }
    };

    // start immediately with the first character
    typewriterTimeout = setTimeout(typeNext, 0);
};

const restartBeerLoop = () => {
    beerFrameKey.value += 1; // restart GIF
    showTooltip.value = true;
    animateWelcomeText();

    if (tooltipTimeout) {
        clearTimeout(tooltipTimeout);
    }

    tooltipTimeout = setTimeout(() => {
        showTooltip.value = false;
    }, beerGifDuration);
};

const summaryCards = computed(() => [
    {
        label: 'Total leads',
        value: props.stats.activeLeads ?? 0,
        subtext: 'Submitted prep forms'
    },
    {
        label: 'Accepted intros',
        value: props.progress.completed ?? 0,
        subtext: 'Approved investor matches'
    },
    {
        label: 'Meeting requests',
        value: props.meetingRequests?.length ?? 0,
        subtext: 'Awaiting confirmation'
    },
    {
        label: 'Active sessions',
        value: props.stats.voiceSessionsToday ?? 0,
        subtext: 'Voice calls today'
    }
]);

const meetingRequestsList = computed(() => props.meetingRequests ?? []);
const upcomingMeetingsList = computed(() => props.upcomingMeetings ?? []);

const requestForms = reactive({});
const processingRequest = ref(null);
const rejectingRequest = ref(null);

const reminders = ref([]);
const showReminderModal = ref(false);
const reminderLoading = ref(false);
const reminderCount = ref(0);
const reminderError = ref(null);

const getRequestForm = (id) => {
    if (!requestForms[id]) {
        requestForms[id] = {
            scheduled_date: '',
            scheduled_time: '',
            location: '',
            agenda: ''
        };
    }
    return requestForms[id];
};

const pendingCount = computed(() => props.interestRequests.filter((r) => r.status === 'pending').length);

const stageTagClass = (stage) => {
    const base = 'rounded-full px-3 py-1 text-xs font-semibold capitalize';
    const colors = {
        idea: 'bg-sky-50 text-sky-600 border border-sky-100',
        planning: 'bg-indigo-50 text-indigo-600 border border-indigo-100',
        launched: 'bg-emerald-50 text-emerald-600 border border-emerald-100'
    };
    return `${base} ${colors[stage] ?? 'bg-slate-100 text-slate-500 border border-slate-200'}`;
};

const statusTagClass = (status) => {
    const base = 'rounded-full px-3 py-1 text-xs font-semibold capitalize';
    const colors = {
        pending: 'bg-[#fff4d7] text-[#c57a00]',
        accepted: 'bg-[#e0fbec] text-[#0d884b]',
        rejected: 'bg-[#ffe1e6] text-[#c22d4d]',
        complete: 'bg-[#e0fbec] text-[#0d884b]',
        failed: 'bg-[#ffe1e6] text-[#c22d4d]'
    };
    return `${base} ${colors[status] ?? 'bg-slate-100 text-slate-500'}`;
};

const formatPreferredSlot = (date, time) => {
    if (!date) {
        return 'No preference';
    }
    try {
        const iso = `${date}T${time ?? '09:00'}`;
        const formatted = new Date(iso);
        return formatted.toLocaleString(undefined, {
            weekday: 'short',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch {
        return `${date} ${time ?? ''}`.trim();
    }
};

const formatDate = (value) => {
    if (!value) {
        return '—';
    }
    try {
        const date = new Date(value);
        return date.toLocaleString(undefined, {
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch {
        return value;
    }
};

const handleAcceptRequest = (request) => {
    const form = getRequestForm(request.id);

    if (!form.scheduled_date || !form.scheduled_time) {
        window.alert('Select both date and time to schedule this meeting.');
        return;
    }

    processingRequest.value = request.id;

    router.post(`/advisor/meeting-requests/${request.id}/accept`, form, {
        preserveScroll: true,
        onFinish: () => {
            processingRequest.value = null;
            requestForms[request.id] = {
                scheduled_date: '',
                scheduled_time: '',
                location: '',
                agenda: ''
            };
        }
    });
};

const handleRejectRequest = (requestId) => {
    rejectingRequest.value = requestId;
    router.post(`/advisor/meeting-requests/${requestId}/reject`, {}, {
        preserveScroll: true,
        onFinish: () => {
            rejectingRequest.value = null;
            delete requestForms[requestId];
        }
    });
};

const syncReminders = async () => {
    try {
        reminderLoading.value = true;
        reminderError.value = null;
        const { data } = await axios.get('/advisor/reminders');
        reminders.value = data.reminders || [];
        reminderCount.value = reminders.value.length;
    } catch (error) {
        reminderError.value = 'Failed to load reminders';
        console.error(error);
    } finally {
        reminderLoading.value = false;
    }
};

const handleReminderClick = async () => {
    showReminderModal.value = true;
    await syncReminders();
};

const acknowledgeReminders = async () => {
    const ids = reminders.value.map((reminder) => reminder.id);
    if (!ids.length) {
        showReminderModal.value = false;
        return;
    }

    try {
        await axios.post('/advisor/reminders/acknowledge', { ids });
        reminders.value = [];
        reminderCount.value = 0;
        showReminderModal.value = false;
        reminderError.value = null;
    } catch (error) {
        console.error('Failed to acknowledge reminders', error);
        reminderError.value = 'Failed to update reminders';
    }
};

onMounted(() => {
    restartBeerLoop();
    beerInterval = setInterval(restartBeerLoop, beerGifDuration);
    syncReminders();
});

onBeforeUnmount(() => {
    if (beerInterval) {
        clearInterval(beerInterval);
    }
    if (tooltipTimeout) {
        clearTimeout(tooltipTimeout);
    }
    if (typewriterTimeout) {
        clearTimeout(typewriterTimeout);
    }
});

const statusPillClass = (status) => {
    const mapping = {
        'In Progress': 'bg-[#e1f1ff] text-[#0d4f8b]',
        Planning: 'bg-[#fef6d8] text-[#b38600]',
        Launched: 'bg-[#e0fbec] text-[#0d884b]',
        Pending: 'bg-[#ffe1e6] text-[#b8324f]'
    };
    return mapping[status] ?? 'bg-slate-100 text-slate-500';
};
</script>

<style scoped>
@keyframes tooltipFloat {
    0% {
        opacity: 0;
        transform: translateY(-8px) scale(0.95);
    }
    10% {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
    90% {
        opacity: 1;
    }
    100% {
        opacity: 0;
        transform: translateY(-8px) scale(0.95);
    }
}

.fade-scale-enter-active,
.fade-scale-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}
.fade-scale-enter-from,
.fade-scale-leave-to {
    opacity: 0;
    transform: translateY(-6px) scale(0.95);
}

.tooltip-wrapper {
    display: flex;
    align-items: center;
}

.tooltip-beer {
    pointer-events: none;
    max-width: 260px;
    white-space: nowrap;
    background-color: #d4e7ff;
}

.tooltip-tail {
    position: absolute;
    left: -2px;
    top: 50%;
    transform: translateY(-50%) rotate(45deg);
    width: 12px;
    height: 12px;
    background-color: #d4e7ff;
}
</style>


