<template>
    <Head title="Advisor Network" />
    <div class="flex min-h-screen bg-[#eef5fb] text-slate-700">
        <RequestSidebar :advisor="advisor" active="Network" />

        <main class="flex-1 px-4 pb-12 pt-8 sm:px-10">
            <header class="rounded-3xl bg-white/95 px-6 py-5 shadow-sm shadow-blue-200/40">
                <p class="text-xs uppercase tracking-[0.5em] text-slate-400">Network</p>
                <h1 class="text-3xl font-semibold text-[#0f2e5a]">Entrepreneur companies</h1>
                <p class="text-sm text-slate-500">Browse every founder in your queue and keep their context close.</p>
            </header>

            <div class="mt-8 flex items-center gap-2 rounded-full border border-slate-100 bg-white px-5 py-3 shadow">
                <BaseIcon name="search" class="h-5 w-5 text-[#0f4f8b]" />
                <input
                    v-model="searchTerm"
                    type="text"
                    placeholder="Search founders, companies, or stages"
                    class="w-full bg-transparent text-sm text-slate-600 placeholder:text-slate-400 focus:outline-none"
                />
            </div>

            <section class="mt-8 grid gap-6 lg:grid-cols-2 xl:grid-cols-3">
                <article
                    v-for="company in filteredCompanies"
                    :key="company.id"
                    class="flex flex-col rounded-3xl border border-slate-100 bg-white p-5 shadow-md shadow-blue-100/50 transition hover:-translate-y-0.5 hover:shadow-lg"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Founder</p>
                            <h3 class="text-xl font-semibold text-[#0f2e5a]">{{ company.name }}</h3>
                            <p class="text-sm text-slate-500">{{ company.company }}</p>
                        </div>
                        <span class="rounded-full bg-[#e3f2ff] px-3 py-1 text-xs font-semibold text-[#0d4f8b]">
                            {{ company.stage || 'Early stage' }}
                        </span>
                    </div>

                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        <p class="line-clamp-3 text-[13px] leading-relaxed text-slate-600">
                            {{ company.summary }}
                        </p>
                        <div class="rounded-2xl bg-[#f7faff] px-4 py-3 text-xs text-slate-500">
                            <p class="font-semibold text-[#0f4f8b]">Ideal customer</p>
                            <p class="line-clamp-2">{{ company.target_customers || 'Not defined yet.' }}</p>
                        </div>
                        <p class="text-xs text-slate-400">
                            Updated {{ formatRelativeTime(company.updated_at) }}
                        </p>
                    </div>

                    <button
                        class="mt-4 inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-[#8fc9ff] to-[#4da0ff] px-4 py-2 text-sm font-semibold text-white shadow hover:shadow-lg"
                        @click="openModal(company)"
                    >
                        View company
                    </button>
                </article>

                <p v-if="!filteredCompanies.length" class="rounded-3xl border border-dashed border-slate-200 bg-white/80 p-6 text-center text-sm text-slate-500">
                    No companies match your current search. Try a different keyword.
                </p>
            </section>
        </main>
    </div>

    <transition name="fade">
        <div
            v-if="modal.open"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
        >
            <div class="w-full max-w-3xl rounded-3xl bg-white p-6 shadow-2xl">
                <header class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Company profile</p>
                        <h3 class="text-3xl font-semibold text-[#0f2e5a]">{{ modal.company?.company }}</h3>
                        <p class="text-sm text-slate-500">{{ modal.company?.name }} · {{ modal.company?.email }}</p>
                    </div>
                    <button class="rounded-full bg-slate-100 p-2 text-slate-500 hover:text-[#0f2e5a]" @click="closeModal">
                        ✕
                    </button>
                </header>

                <div class="mt-6 grid gap-6 lg:grid-cols-[2fr,1fr]">
                    <div class="space-y-4 rounded-2xl border border-slate-100 bg-[#f7f9fc] p-5">
                        <h4 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">Business summary</h4>
                        <p class="text-sm leading-relaxed text-slate-600">
                            {{ modal.company?.summary }}
                        </p>
                        <div class="rounded-2xl bg-white px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Stage</p>
                            <p class="text-sm text-slate-600">{{ modal.company?.stage || 'Not specified' }}</p>
                        </div>
                    </div>

                    <div class="space-y-4 rounded-2xl border border-slate-100 bg-white p-5">
                        <div>
                            <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Focus market</p>
                            <p class="mt-2 text-sm text-slate-600">
                                {{ modal.company?.target_customers || 'No target details provided.' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Key questions</p>
                            <p class="mt-2 text-sm text-slate-600">
                                {{ modal.company?.questions || 'This founder hasn’t submitted advisor questions yet.' }}
                            </p>
                        </div>
                        <div class="rounded-2xl bg-[#f6fbff] px-4 py-3 text-xs text-slate-500">
                            Preferred language: <span class="font-semibold text-[#0f4f8b]">{{ modal.company?.language || 'Not shared' }}</span>
                            <br />
                            Country of origin: <span class="font-semibold text-[#0f4f8b]">{{ modal.company?.country || 'Not shared' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
import { computed, ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import RequestSidebar from '../components/RequestSidebar.vue';
import BaseIcon from '../components/BaseIcon.vue';

const props = defineProps({
    advisor: {
        type: Object,
        required: true
    },
    companies: {
        type: Array,
        default: () => []
    }
});

const searchTerm = ref('');
const modal = ref({
    open: false,
    company: null
});

const filteredCompanies = computed(() => {
    if (!searchTerm.value) return props.companies;
    const term = searchTerm.value.toLowerCase();
    return props.companies.filter((company) =>
        [company.name, company.company, company.summary, company.stage]
            .filter(Boolean)
            .some((field) => field.toLowerCase().includes(term))
    );
});

const openModal = (company) => {
    modal.value.open = true;
    modal.value.company = company;
};

const closeModal = () => {
    modal.value.open = false;
    modal.value.company = null;
};

const formatRelativeTime = (isoString) => {
    if (!isoString) return 'recently';
    const updated = new Date(isoString);
    const diff = Date.now() - updated.getTime();
    const days = Math.round(diff / (1000 * 60 * 60 * 24));
    if (days <= 0) return 'today';
    if (days === 1) return 'yesterday';
    if (days < 30) return `${days} days ago`;
    const months = Math.floor(days / 30);
    return `${months} month${months > 1 ? 's' : ''} ago`;
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
</style>

