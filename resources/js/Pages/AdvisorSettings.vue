<template>
    <Head title="Settings" />
    <div class="flex min-h-screen bg-[#f2f7f5] text-slate-700">
        <RequestSidebar :advisor="advisor" active="Settings" />
        <AdvisorBottomNav active="Settings" />

        <main class="flex-1 px-4 pb-12 pt-8 sm:px-10">
            <header class="rounded-3xl bg-white/95 px-6 py-5 shadow-sm shadow-emerald-100/60 border border-[#5cc094]">
                <p class="text-xs uppercase tracking-widest text-slate-400">General</p>
                <h1 class="text-3xl font-semibold text-[#205274]">Settings</h1>
                <p class="text-sm text-slate-500">
                    Control how your advisor workspace behaves — notifications, appearance, and account safety.
                </p>
            </header>

            <section class="mt-8 space-y-6 max-w-2xl">
                <!-- Main settings list -->
                <div class="rounded-3xl border border-[#5cc094] bg-white p-4 shadow-sm shadow-emerald-100/70">
                    <p class="px-1 text-xs uppercase tracking-widest text-slate-400">General</p>
                    <div class="mt-2 divide-y divide-slate-100">
                        <!-- Reminders toggle -->
                        <div class="flex items-center justify-between gap-4 px-2 py-3">
                            <div>
                                <p class="text-sm font-semibold text-[#205274]">Reminders</p>
                                <p class="text-xs text-slate-500">Show in-app notifications</p>
                            </div>
                            <button
                                type="button"
                                class="relative inline-flex h-6 w-11 items-center rounded-full border border-[#5cc094] transition-colors"
                                :class="settings.pauseReminders ? 'bg-white' : 'bg-[#205274]'"
                                @click="toggle('pauseReminders')"
                            >
                                <span
                                    class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"
                                    :class="settings.pauseReminders ? 'translate-x-1' : 'translate-x-5'"
                                />
                            </button>
                        </div>

                        <!-- Dark mode toggle -->
                        <div class="flex items-center justify-between gap-4 px-2 py-3">
                            <div>
                                <p class="text-sm font-semibold text-[#205274]">Dark mode</p>
                                <p class="text-xs text-slate-500">Use dark theme</p>
                            </div>
                            <button
                                type="button"
                                class="relative inline-flex h-6 w-11 items-center rounded-full border border-[#5cc094] transition-colors"
                                :class="settings.darkMode ? 'bg-[#205274]' : 'bg-white'"
                                @click="toggle('darkMode')"
                            >
                                <span
                                    class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform"
                                    :class="settings.darkMode ? 'translate-x-5' : 'translate-x-1'"
                                />
                            </button>
                        </div>

                        <!-- Delete account row -->
                        <div class="flex items-center justify-between gap-4 px-2 py-3">
                            <div>
                                <p class="text-sm font-semibold text-red-600">Delete account</p>
                                <p class="text-xs text-slate-500">Remove this advisor profile</p>
                            </div>
                            <button
                                type="button"
                                class="inline-flex items-center justify-center rounded-2xl bg-red-600 px-3 py-1.5 text-[11px] font-semibold text-white shadow hover:bg-red-700"
                                @click="confirmDelete"
                            >
                                Delete
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Profile summary -->
                <div class="rounded-3xl border border-[#5cc094] bg-white p-5 shadow-sm shadow-emerald-100/70">
                    <p class="text-xs uppercase tracking-widest text-slate-400">Profile</p>
                    <h2 class="mt-1 text-lg font-semibold text-[#205274]">{{ advisor.name }}</h2>
                    <p class="text-xs text-slate-500">{{ advisor.email }}</p>

                    <div class="mt-4 flex items-center gap-3 rounded-2xl bg-[#f2f7f5] px-4 py-3 text-xs text-slate-600">
                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gradient-to-br from-[#5cc094] to-[#205274] text-white text-sm font-semibold">
                            {{ advisorInitials }}
                        </div>
                        <div>
                            <p class="font-semibold text-[#205274]">Advisor workspace</p>
                            <p class="text-[11px] text-slate-500">Settings apply only to this advisor account.</p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
</template>

<script setup>
import { computed, reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import RequestSidebar from '../components/RequestSidebar.vue';
import AdvisorBottomNav from '../components/AdvisorBottomNav.vue';

const props = defineProps({
    advisor: {
        type: Object,
        required: true
    }
});

const settings = reactive({
    pauseReminders: false,
    darkMode: false
});

const advisorInitials = computed(() => {
    if (!props.advisor?.name) return 'AD';
    return props.advisor.name
        .split(' ')
        .map((part) => part[0])
        .join('')
        .slice(0, 2)
        .toUpperCase();
});

const toggle = (key) => {
    settings[key] = !settings[key];
};

const confirmDelete = () => {
    if (!window.confirm('Are you sure you want to delete your advisor account? This cannot be undone.')) {
        return;
    }
    // Placeholder: wire up to a real route if/when backend deletion is implemented
    router.post('/advisor/delete-account', {}, { preserveScroll: true });
};
</script>


