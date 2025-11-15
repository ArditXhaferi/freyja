<template>
    <aside class="hidden w-64 flex-col border-r border-[#dfe9f5] bg-white/90 px-6 py-8 lg:flex">
        <div class="flex items-center gap-3">
            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-[#8fc9ff] to-[#4da0ff] text-white font-semibold">
                EB
            </div>
            <div>
                <p class="text-lg font-semibold text-[#0f325a]">Espoo Advisor</p>
                <p class="text-xs text-slate-400">Business Desk</p>
            </div>
        </div>
        <nav class="mt-10 space-y-6">
            <div>
                <p class="text-xs uppercase tracking-widest text-slate-400">Menu</p>
                <div class="mt-3 space-y-2">
                    <Link
                        v-for="item in computedMenu"
                        :key="item.label"
                        :href="item.href"
                        class="flex w-full items-center gap-3 rounded-2xl px-3 py-2 text-sm font-medium transition"
                        :class="item.active ? 'bg-[#e3f2ff] text-[#0e3f73]' : 'text-slate-500 hover:bg-slate-50'"
                    >
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-50 text-slate-500">
                            <BaseIcon :name="item.icon" class="h-5 w-5" />
                        </span>
                        <span>{{ item.label }}</span>
                    </Link>
                </div>
            </div>
            <div>
                <p class="text-xs uppercase tracking-widest text-slate-400">General</p>
                <div class="mt-3 space-y-2">
                    <button class="flex w-full items-center gap-3 rounded-2xl px-3 py-2 text-sm font-medium text-slate-500 transition hover:bg-slate-50">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-50 text-slate-500">
                            <BaseIcon name="help" class="h-5 w-5" />
                        </span>
                        Help
                    </button>
                    <form method="POST" action="/logout">
                        <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                        <button type="submit" class="flex w-full items-center gap-3 rounded-2xl px-3 py-2 text-sm font-medium text-slate-500 transition hover:bg-slate-50">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-50 text-slate-500">
                                <BaseIcon name="logout" class="h-5 w-5" />
                            </span>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>
        <div class="mt-auto rounded-2xl bg-gradient-to-br from-[#b6e0ff] to-[#7fc3ff] p-4 text-sm text-[#05355f]">
            <p class="text-xs uppercase tracking-widest text-[#05355f]/70">Mobile app</p>
            <p class="mt-2 text-base font-semibold">Support on the go</p>
            <p class="mt-1 text-xs text-[#05355f]/70">Stay connected to every founder conversation from anywhere.</p>
            <button class="mt-4 w-full rounded-xl bg-white/90 py-2 text-sm font-semibold text-[#0d4f8b] shadow">Download</button>
        </div>
    </aside>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import BaseIcon from './BaseIcon.vue';

const props = defineProps({
    advisor: {
        type: Object,
        required: true
    },
    active: {
        type: String,
        default: 'Dashboard'
    }
});

const computedMenu = computed(() => [
    { label: 'Dashboard', icon: 'dashboard', href: '/advisor/dashboard', active: props.active === 'Dashboard' },
    { label: 'Calendar', icon: 'calendar', href: '/advisor/calendar', active: props.active === 'Calendar' },
    { label: 'Network', icon: 'team', href: '/advisor/network', active: props.active === 'Network' },
    {
        label: 'Meeting Requests',
        icon: 'tasks',
        href: '/advisor/meeting-requests',
        active: props.active === 'Meeting Requests'
    }
]);
</script>


