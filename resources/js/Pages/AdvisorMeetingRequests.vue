<template>
    <Head title="Meeting Requests" />
    <div class="flex min-h-screen bg-[#eef5fb] text-slate-700">
        <RequestSidebar :advisor="advisor" active="Meeting Requests" />

        <main class="flex-1 px-4 pb-10 pt-8 sm:px-8">
            <header class="rounded-3xl bg-white/90 px-6 py-5 shadow-sm shadow-blue-200/40">
                <p class="text-xs uppercase tracking-widest text-slate-400">Meetings</p>
                <h1 class="text-3xl font-semibold text-[#0f2e5a]">Pending requests</h1>
                <p class="text-sm text-slate-400">Review and schedule incoming sessions from entrepreneurs.</p>
            </header>

            <section class="mt-8 space-y-6">
                <article
                    v-for="request in requests"
                    :key="request.id"
                    class="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm shadow-blue-100/70"
                >
                    <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                        <div>
                            <p class="text-sm uppercase tracking-widest text-slate-400">Founder</p>
                            <h2 class="text-2xl font-semibold text-[#0f2e5a]">{{ request.founder }}</h2>
                            <p class="text-xs text-slate-400">{{ preferredSlot(request.preferred_date, request.preferred_time) }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <span
                                class="inline-flex items-center gap-2 rounded-full px-4 py-1 text-xs font-semibold"
                                :class="request.status === 'pending' ? 'bg-[#e1f1ff] text-[#0d4f8b]' : 'bg-[#f2f2f5] text-slate-500'"
                            >
                                <svg viewBox="0 0 20 20" class="h-4 w-4" :fill="request.status === 'pending' ? 'currentColor' : '#9ca3af'">
                                    <circle cx="10" cy="10" r="9" />
                                </svg>
                                {{ request.status === 'pending' ? 'Pending' : 'Rejected' }}
                            </span>
                            <button
                                class="rounded-full border border-slate-200 p-2 text-slate-500 transition hover:text-[#0f2e5a] cursor-pointer"
                                @click="toggleCardCollapse(request.id)"
                            >
                                <svg
                                    viewBox="0 0 24 24"
                                    class="h-4 w-4 transition-transform duration-200"
                                    :class="cardCollapsed[request.id] ? '' : 'rotate-180'"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="1.6"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M6 9l6 6 6-6" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <transition name="accordion">
                        <div v-show="!cardCollapsed[request.id]" class="overflow-hidden">
                            <p class="mt-4 text-sm text-slate-500 whitespace-pre-line">{{ request.message || 'No additional context provided.' }}</p>

                            <section class="mt-6 grid gap-4 lg:grid-cols-[minmax(0,3fr)_minmax(0,1fr)]">
                                <div class="rounded-3xl border border-slate-100 bg-gradient-to-br from-[#edf6ff] via-[#f8fbff] to-[#eaf2ff] p-5 shadow-inner shadow-blue-100/60">
                                    <header class="flex flex-col gap-1">
                                        <div>
                                            <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Business brief</p>
                                            <h3 class="text-lg font-semibold text-[#0f2e5a]">
                                                {{ request.business?.name || 'Unknown business' }}
                                            </h3>
                                            <p class="text-xs text-slate-500">
                                                Stage: {{ request.business?.stage ? request.business.stage : 'Not provided' }}
                                            </p>
                                        </div>
                                        <p class="text-[11px] text-slate-400">
                                            Scroll or expand sections below to review the founder’s prep notes.
                                        </p>
                                    </header>
                                    <div class="mt-4 space-y-3">
                                        <div class="rounded-2xl border border-white/60 bg-white/80 px-3 py-2.5">
                                            <div class="flex items-center justify-between text-[11px] font-semibold uppercase tracking-widest text-slate-400">
                                                <span>Business summary</span>
                                                <button
                                                    type="button"
                                                    class="text-[10px] font-semibold text-[#0f4f8b]"
                                                    @click="toggleDetail(request.id, 'summary')"
                                                >
                                                    {{ getDetailState(request.id).summary ? 'Show less' : 'View more' }}
                                                </button>
                                            </div>
                                            <p
                                                class="mt-1.5 text-[13px] leading-relaxed text-slate-600 transition-all"
                                                :class="getDetailState(request.id).summary ? '' : 'max-h-16 overflow-hidden text-ellipsis'"
                                            >
                                                {{ request.business?.summary || 'No summary available.' }}
                                            </p>
                                        </div>

                                        <div class="rounded-2xl border border-white/60 bg-white/80 px-3 py-2.5">
                                            <div class="flex items-center justify-between text-[11px] font-semibold uppercase tracking-widest text-slate-400">
                                                <span>Target customers</span>
                                                <button
                                                    type="button"
                                                    class="text-[10px] font-semibold text-[#0f4f8b]"
                                                    @click="toggleDetail(request.id, 'targets')"
                                                >
                                                    {{ getDetailState(request.id).targets ? 'Show less' : 'View more' }}
                                                </button>
                                            </div>
                                            <p
                                                class="mt-1.5 text-[13px] leading-relaxed text-slate-600 transition-all"
                                                :class="getDetailState(request.id).targets ? '' : 'max-h-16 overflow-hidden text-ellipsis'"
                                            >
                                                {{ request.business?.targets || 'Founder has not defined their target customers yet.' }}
                                            </p>
                                        </div>

                                        <div class="rounded-2xl border border-white/60 bg-white/80 px-3 py-2.5">
                                            <div class="flex items-center justify-between text-[11px] font-semibold uppercase tracking-widest text-slate-400">
                                                <span>Questions for advisor</span>
                                                <button
                                                    type="button"
                                                    class="text-[10px] font-semibold text-[#0f4f8b]"
                                                    @click="toggleDetail(request.id, 'questions')"
                                                >
                                                    {{ getDetailState(request.id).questions ? 'Show less' : 'View more' }}
                                                </button>
                                            </div>
                                            <p
                                                class="mt-1.5 text-[13px] leading-relaxed text-slate-600 transition-all"
                                                :class="getDetailState(request.id).questions ? '' : 'max-h-16 overflow-hidden text-ellipsis'"
                                            >
                                                {{ request.business?.questions || 'No advisor questions submitted.' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm shadow-blue-100/70 space-y-3">
                                    <header>
                                        <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Founder roadmap</p>
                                        <p class="text-[11px] text-slate-400">Visual overview of milestones shared by the entrepreneur.</p>
                                    </header>
                                    <div class="space-y-3">
                                        <div
                                            v-if="request.roadmaps?.length"
                                            class="relative rounded-2xl border border-slate-100 bg-[#f2f8ff] p-3"
                                        >
                                            <BusinessRoadmapFlow
                                                :roadmap="request.roadmaps[0]"
                                                :height="140"
                                                :columns="2"
                                                preview
                                            />
                                            <div class="pointer-events-none absolute inset-0 rounded-2xl bg-gradient-to-r from-[#eef5fb]/5 to-white/40"></div>
                                            <button
                                                class="absolute inset-0 rounded-2xl transition hover:bg-white/30"
                                                aria-label="Expand roadmap"
                                                @click="openRoadmapModal(request)"
                                            ></button>
                                        </div>
                                        <p v-else class="rounded-2xl bg-[#f7f9fc] px-4 py-6 text-center text-sm text-slate-400">
                                            This founder hasn’t created a roadmap yet.
                                        </p>
                                    </div>
                                </div>
                            </section>

                            <div v-if="request.status === 'pending'" class="mt-6 flex flex-col gap-4 rounded-2xl border border-slate-100 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <button
                                        class="flex h-12 min-w-[7rem] flex-1 items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-[#8fc9ff] to-[#4da0ff] text-white shadow transition hover:scale-105"
                                        :disabled="processing === request.id"
                                        @click="openScheduleModal(request)"
                                    >
                                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M5 12l5 5L20 7" />
                                        </svg>
                                        <span class="text-sm font-semibold">Accept</span>
                                    </button>
                                    <button
                                        class="flex h-12 min-w-[7rem] flex-1 items-center justify-center gap-2 rounded-2xl border border-slate-200 text-slate-500 shadow-sm transition hover:bg-slate-50"
                                        :disabled="rejecting === request.id"
                                        @click="reject(request.id)"
                                    >
                                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M6 6l12 12M6 18 18 6" />
                                        </svg>
                                        <span class="text-sm font-semibold">Reject</span>
                                    </button>
                                </div>
                                <p class="text-sm text-slate-500">
                                    Use the check to accept and set a meeting slot, or the X to politely decline.
                                </p>
                            </div>
                            <div v-else class="mt-6 rounded-2xl border border-dashed border-slate-200 bg-slate-50/60 p-4 text-sm text-slate-500">
                                This request has been rejected and will be hidden automatically after the preferred date passes.
                            </div>
                        </div>
                    </transition>
                </article>
                <p v-if="!requests.length" class="text-center text-sm text-slate-400">No pending requests at the moment.</p>
            </section>
        </main>
        <transition name="fade">
            <div
                v-if="roadmapModal.open"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
            >
                <div class="relative max-h-[90vh] w-full max-w-5xl overflow-y-auto rounded-3xl bg-white p-6 shadow-2xl">
                    <header class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Founder roadmap</p>
                            <h3 class="text-2xl font-semibold text-[#0f2e5a]">{{ roadmapModal.title }}</h3>
                        </div>
                        <button
                            class="rounded-full bg-slate-100 p-2 text-slate-500 hover:text-[#0f2e5a]"
                            @click="closeRoadmapModal"
                        >
                            ✕
                        </button>
                    </header>
                    <div class="mt-6 space-y-6">
                        <BusinessRoadmapFlow
                            v-for="flow in roadmapModal.flows"
                            :key="flow.id"
                            :roadmap="flow"
                            :height="360"
                            :columns="3"
                        />
                    </div>
                </div>
            </div>
        </transition>

        <transition name="fade">
            <div
                v-if="scheduleModal.open"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
            >
                <div class="relative w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl">
                    <header class="flex items-center justify-between">
                        <div>
                            <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Schedule with</p>
                            <h3 class="text-2xl font-semibold text-[#0f2e5a]">{{ scheduleModal.founder }}</h3>
                        </div>
                        <button
                            class="rounded-full bg-slate-100 p-2 text-slate-500 hover:text-[#0f2e5a]"
                            @click="closeScheduleModal"
                        >
                            ✕
                        </button>
                    </header>
                    <div v-if="scheduleModal.requestId" class="mt-5 space-y-4">
                        <div class="grid gap-4 md:grid-cols-2">
                            <SoftDatePicker
                                v-model="forms[scheduleModal.requestId].scheduled_date"
                                label="Preferred date"
                                :min-date="today"
                            />
                            <SoftTimePicker
                                v-model="forms[scheduleModal.requestId].scheduled_time"
                                label="Preferred time"
                                :interval="30"
                            />
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-400">Location</label>
                            <div class="mt-1 flex items-center gap-3 rounded-2xl border border-slate-200 bg-[#f6f9ff] px-4 py-3 text-sm text-slate-600 transition focus-within:border-[#4da0ff]">
                                <svg viewBox="0 0 24 24" class="h-4 w-4 text-[#4da0ff]" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 21s6-5.686 6-11a6 6 0 10-12 0c0 5.314 6 11 6 11z" />
                                    <circle cx="12" cy="10" r="2.5" />
                                </svg>
                                <input
                                    type="text"
                                    placeholder="Online meeting link or physical location"
                                    v-model="forms[scheduleModal.requestId].location"
                                    class="flex-1 bg-transparent text-sm text-slate-600 placeholder:text-slate-400 focus:outline-none"
                                />
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-semibold uppercase tracking-widest text-slate-400">Optional notes</label>
                            <div class="mt-1 rounded-2xl border border-slate-200 bg-[#f6f9ff] px-4 py-3 text-sm text-slate-600 transition focus-within:border-[#4da0ff]">
                                <textarea
                                    rows="2"
                                    placeholder="Agenda / optional notes"
                                    v-model="forms[scheduleModal.requestId].agenda"
                                    class="w-full bg-transparent text-sm text-slate-600 placeholder:text-slate-400 focus:outline-none"
                                ></textarea>
                            </div>
                        </div>
                        <button
                            class="flex w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-[#8fc9ff] to-[#4da0ff] px-4 py-3 text-sm font-semibold text-white shadow transition hover:scale-[1.01]"
                            :disabled="processing === scheduleModal.requestId"
                            @click="accept(scheduleModal.requestId)"
                        >
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M5 12l5 5L20 7" />
                            </svg>
                            {{ processing === scheduleModal.requestId ? 'Scheduling...' : 'Confirm meeting' }}
                        </button>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { reactive, ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import RequestSidebar from '../components/RequestSidebar.vue';
import BusinessRoadmapFlow from '../components/BusinessRoadmapFlow.vue';
import SoftDatePicker from '../components/SoftDatePicker.vue';
import SoftTimePicker from '../components/SoftTimePicker.vue';

const props = defineProps({
    advisor: {
        type: Object,
        required: true
    },
    requests: {
        type: Array,
        default: () => []
    }
});

const forms = reactive({});
const detailStates = reactive({});
const cardCollapsed = reactive({});
const roadmapModal = reactive({
    open: false,
    flows: [],
    title: ''
});
const scheduleModal = reactive({
    open: false,
    requestId: null,
    founder: ''
});
const today = new Date().toISOString().slice(0, 10);

const ensureDetailState = (id) => {
    if (!detailStates[id]) {
        detailStates[id] = {
            summary: false,
            targets: false,
            questions: false
        };
    }
    return detailStates[id];
};

const toggleDetail = (id, key) => {
    const state = ensureDetailState(id);
    state[key] = !state[key];
};

const getDetailState = (id) => ensureDetailState(id);

props.requests.forEach((request) => {
    ensureDetailState(request.id);
    if (cardCollapsed[request.id] === undefined) {
        cardCollapsed[request.id] = false;
    }
    if (request.status === 'pending') {
        forms[request.id] = {
            scheduled_date: '',
            scheduled_time: '',
            location: '',
            agenda: ''
        };
    }
});

const openRoadmapModal = (request) => {
    if (!request.roadmaps?.length) {
        return;
    }
    roadmapModal.open = true;
    roadmapModal.flows = request.roadmaps;
    roadmapModal.title = request.business?.name || 'Founder roadmap';
};

const closeRoadmapModal = () => {
    roadmapModal.open = false;
    roadmapModal.flows = [];
    roadmapModal.title = '';
};

const toggleCardCollapse = (id) => {
    cardCollapsed[id] = !cardCollapsed[id];
};

const openScheduleModal = (request) => {
    scheduleModal.open = true;
    scheduleModal.requestId = request.id;
    scheduleModal.founder = request.founder;
};

const closeScheduleModal = () => {
    scheduleModal.open = false;
    scheduleModal.requestId = null;
    scheduleModal.founder = '';
};

const processing = ref(null);
const rejecting = ref(null);

const preferredSlot = (date, time) => {
    if (!date) return 'No preference';
    const display = time ? `${date} ${time}` : date;
    try {
        const formatted = new Date(`${date}T${time ?? '09:00'}`);
        return formatted.toLocaleString(undefined, {
            weekday: 'short',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch {
        return display;
    }
};

const accept = (requestId) => {
    const form = forms[requestId];
    if (!form.scheduled_date || !form.scheduled_time) {
        window.alert('Please select both a date and a time.');
        return;
    }
    processing.value = requestId;
    router.post(`/advisor/meeting-requests/${requestId}/accept`, form, {
        preserveScroll: true,
        onFinish: () => {
            processing.value = null;
            closeScheduleModal();
        }
    });
};

const reject = (requestId) => {
    rejecting.value = requestId;
    router.post(`/advisor/meeting-requests/${requestId}/reject`, {}, {
        preserveScroll: true,
        onFinish: () => {
            rejecting.value = null;
        }
    });
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.accordion-enter-active,
.accordion-leave-active {
    transition: max-height 0.35s ease, opacity 0.25s ease;
}
.accordion-enter-from,
.accordion-leave-to {
    max-height: 0;
    opacity: 0;
}
.accordion-enter-to,
.accordion-leave-from {
    max-height: 2000px;
    opacity: 1;
}
</style>

