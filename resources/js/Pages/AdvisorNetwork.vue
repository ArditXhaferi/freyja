<template>
    <Head title="Advisor Network" />
    <div class="flex min-h-screen bg-[#f2f7f5] text-slate-700">
        <RequestSidebar :advisor="advisor" active="Network" />
        <AdvisorBottomNav active="Network" />

        <main class="flex-1 px-4 pb-12 pt-8 sm:px-10">
            <header class="rounded-3xl bg-white/95 px-6 py-5 shadow-sm shadow-emerald-100/40 border border-[#c3d7de]">
                <p class="text-xs uppercase tracking-[0.5em] text-slate-400">Network</p>
                <h1 class="text-3xl font-semibold text-[#205274]">Entrepreneur companies</h1>
                <p class="text-sm text-slate-500">Browse every founder in your queue and keep their context close.</p>
            </header>

            <div class="mt-8 flex items-center gap-2 rounded-full border border-[#c3d7de] bg-white px-5 py-3 shadow-sm shadow-emerald-100/60">
                <BaseIcon name="search" class="h-5 w-5 text-[#205274]" />
                <input
                    v-model="searchTerm"
                    type="text"
                    placeholder="Search founders, companies, or stages"
                    class="w-full bg-transparent text-sm text-slate-600 placeholder:text-slate-400 focus:outline-none"
                />
            </div>

            <!-- Companies list -->
            <section class="mt-8 grid gap-6 lg:grid-cols-2 xl:grid-cols-3">
                <article
                    v-for="company in filteredCompanies"
                    :key="company.id"
                    class="flex flex-col rounded-3xl border border-[#c3d7de] bg-white p-5 shadow-md shadow-emerald-100/60 transition hover:-translate-y-0.5 hover:shadow-lg"
                >
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Founder</p>
                            <h3 class="text-xl font-semibold text-[#205274]">{{ company.name }}</h3>
                            <p class="text-sm text-slate-500">{{ company.company }}</p>
                        </div>
                        <span class="rounded-full bg-[#e0f5ec] px-3 py-1 text-xs font-semibold text-[#205274]">
                            {{ company.stage || 'Early stage' }}
                        </span>
                    </div>

                    <div class="mt-4 space-y-3 text-sm text-slate-600">
                        <p class="line-clamp-3 text-[13px] leading-relaxed text-slate-600">
                            {{ company.summary }}
                        </p>
                        <div class="rounded-2xl bg-[#f2f7f5] px-4 py-3 text-xs text-slate-500">
                            <p class="font-semibold text-[#205274]">Ideal customer</p>
                            <p class="line-clamp-2">{{ company.target_customers || 'Not defined yet.' }}</p>
                        </div>
                        <p class="text-xs text-slate-400">
                            Updated {{ formatRelativeTime(company.updated_at) }}
                        </p>
                    </div>

                    <button
                        class="mt-4 inline-flex items-center justify-center rounded-2xl bg-[#205274] border border-[#5cc094] px-4 py-2 text-sm font-semibold text-white shadow hover:bg-[#1a425d]"
                        @click="openModal(company)"
                    >
                        View company
                    </button>
                </article>

                <p v-if="!filteredCompanies.length" class="rounded-3xl border border-dashed border-[#c3d7de] bg-white/80 p-6 text-center text-sm text-slate-500">
                    No companies match your current search. Try a different keyword.
                </p>
            </section>

            <!-- Network graph -->
            <section class="mt-8 rounded-3xl border border-[#c3d7de] bg-white p-5 shadow-sm shadow-emerald-100/70">
                <header class="flex items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-[0.3em] text-slate-400">Advisor network</p>
                        <h2 class="text-lg font-semibold text-[#205274]">Matches graph</h2>
                    </div>
                </header>
                <div class="mt-4 h-[320px] rounded-2xl bg-[#f2f7f5] p-2">
                    <v-network-graph
                        :nodes="graphNodes"
                        :edges="graphEdges"
                        :layouts="graphLayouts"
                        :configs="graphConfig"
                        class="h-full w-full"
                    />
                </div>
                <p class="mt-2 text-[11px] text-slate-500">
                    Advisor node appears in the center. Thicker links indicate stronger or mutual matches.
                </p>
            </section>
        </main>
    </div>

    <transition name="fade">
        <div
            v-if="modal.open"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4"
        >
            <div class="w-full max-w-3xl rounded-3xl bg-white p-6 shadow-2xl border border-[#c3d7de]">
                <header class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Company profile</p>
                        <h3 class="text-3xl font-semibold text-[#205274]">{{ modal.company?.company }}</h3>
                        <p class="text-sm text-slate-500">{{ modal.company?.name }} · {{ modal.company?.email }}</p>
                    </div>
                    <button class="rounded-full bg-slate-100 p-2 text-slate-500 hover:text-[#0f2e5a]" @click="closeModal">
                        ✕
                    </button>
                </header>

                <div class="mt-6 grid gap-6 lg:grid-cols-[2fr,1fr]">
                    <div class="space-y-4 rounded-2xl border border-[#c3d7de] bg-[#f7f9fc] p-5">
                        <h4 class="text-sm font-semibold uppercase tracking-[0.3em] text-slate-400">Business summary</h4>
                        <p class="text-sm leading-relaxed text-slate-600">
                            {{ modal.company?.summary }}
                        </p>
                        <div class="rounded-2xl bg-white px-4 py-3">
                            <p class="text-xs font-semibold uppercase tracking-widest text-slate-400">Stage</p>
                            <p class="text-sm text-slate-600">{{ modal.company?.stage || 'Not specified' }}</p>
                        </div>
                    </div>

                        <div class="space-y-4 rounded-2xl border border-[#c3d7de] bg-white p-5">
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
                        <div class="rounded-2xl bg-[#f2f7f5] px-4 py-3 text-xs text-slate-500">
                            Preferred language: <span class="font-semibold text-[#205274]">{{ modal.company?.language || 'Not shared' }}</span>
                            <br />
                            Country of origin: <span class="font-semibold text-[#205274]">{{ modal.company?.country || 'Not shared' }}</span>
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
import { defineConfigs } from 'v-network-graph';
import RequestSidebar from '../components/RequestSidebar.vue';
import AdvisorBottomNav from '../components/AdvisorBottomNav.vue';
import BaseIcon from '../components/BaseIcon.vue';

const props = defineProps({
    advisor: {
        type: Object,
        required: true
    },
    companies: {
        type: Array,
        default: () => []
    },
    networkEdges: {
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

// v-network-graph data
const graphNodes = computed(() => {
    const nodes = {};

    if (props.advisor?.id) {
        nodes[String(props.advisor.id)] = {
            name: props.advisor.name,
            type: 'advisor'
        };
    }

    props.companies.forEach((company) => {
        nodes[String(company.id)] = {
            name: company.company || company.name,
            type: 'company'
        };
    });

    return nodes;
});

const graphEdges = computed(() => {
    const edges = {};
    const fromServer = props.networkEdges || [];

    // If we have explicit edges from the backend, use them.
    if (fromServer.length > 0 && props.advisor?.id) {
        fromServer.forEach((edge) => {
            edges[edge.id] = {
                source: String(edge.from),
                target: String(edge.to),
                strength: edge.strength ?? 1
            };
        });
        return edges;
    }

    // Fallback: connect advisor to every company so edges are always visible.
    if (props.advisor?.id) {
        const centerId = String(props.advisor.id);
        (props.companies || []).forEach((company) => {
            const toId = String(company.id);
            const id = `edge-fallback-${centerId}-${toId}`;
            edges[id] = {
                source: centerId,
                target: toId,
                strength: 1
            };
        });
    }

    return edges;
});

const graphLayouts = computed(() => {
    const layouts = { nodes: {} };
    const centerId = props.advisor?.id ? String(props.advisor.id) : null;

    // Center advisor node
    if (centerId) {
        layouts.nodes[centerId] = { x: 0, y: 0 };
    }

    // Place all companies in a circle around the advisor
    const companiesForLayout = props.companies || [];
    const count = companiesForLayout.length;
    if (count === 0) {
        return layouts;
    }

    const radius = 220; // slightly larger radius so nodes are not too close

    companiesForLayout.forEach((company, index) => {
        const id = String(company.id);
        const angle = (index / count) * 2 * Math.PI;
        layouts.nodes[id] = {
            x: radius * Math.cos(angle),
            y: radius * Math.sin(angle)
        };
    });

    return layouts;
});

const graphConfig = defineConfigs({
    view: {
        zoomMin: 0.5,
        zoomMax: 1.6
    },
    node: {
        normal: {
            radius: (node) => (node.type === 'advisor' ? 18 : 12),
            // Advisor stays solid primary; businesses get a stronger secondary fill so they don't blend into the background
            color: (node) => (node.type === 'advisor' ? '#205274' : '#5cc094'),
            borderColor: (node) => (node.type === 'advisor' ? '#5cc094' : '#205274'),
            borderWidth: 2
        },
        label: {
            visible: true,
            fontSize: 10,
            color: '#205274'
        },
        draggable: false
    },
    edge: {
        normal: {
            width: (edge) => 1 + (edge.strength ?? 1) * 1.5,
            color: '#5cc094'
        }
    },
    layout: {
        maxIteration: 0
    }
});
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

