<template>
    <aside class="hidden w-64 flex-col border-r border-[#d6e4dc] bg-white/95 px-6 py-8 lg:flex">
        <div class="flex items-center gap-3">
            <img src="/storage/logo (2).png" alt="App logo" class="h-9 w-auto" />
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
                        :class="item.active ? 'bg-[#d4efe3] text-[#205274]' : 'text-slate-500 hover:bg-[#f4faf7]'"
                    >
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#f4faf7] text-[#205274]">
                            <BaseIcon :name="item.icon" class="h-5 w-5" />
                        </span>
                        <span>{{ item.label }}</span>
                    </Link>
                </div>
            </div>
            <div>
                <p class="text-xs uppercase tracking-widest text-slate-400">General</p>
                <div class="mt-3 space-y-2">
                    <button class="flex w-full items-center gap-3 rounded-2xl px-3 py-2 text-sm font-medium text-slate-500 transition hover:bg-[#f4faf7]">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#f4faf7] text-[#205274]">
                            <BaseIcon name="help" class="h-5 w-5" />
                        </span>
                        Help
                    </button>
                    <Link
                        href="/advisor/settings"
                        class="flex w-full items-center gap-3 rounded-2xl px-3 py-2 text-sm font-medium text-slate-500 transition hover:bg-[#f4faf7]"
                    >
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#f4faf7] text-[#205274]">
                            <BaseIcon name="settings" class="h-5 w-5" />
                        </span>
                        Settings
                    </Link>
                    <form method="POST" action="/logout">
                        <input type="hidden" name="_token" :value="$page.props.csrf_token" />
                        <button type="submit" class="flex w-full items-center gap-3 rounded-2xl px-3 py-2 text-sm font-medium text-slate-500 transition hover:bg-[#f4faf7]">
                            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#f4faf7] text-[#205274]">
                                <BaseIcon name="logout" class="h-5 w-5" />
                            </span>
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </nav>
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


