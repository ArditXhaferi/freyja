<template>
    <nav class="fixed bottom-0 left-0 right-0 z-40 bg-[#205274] border-t border-white/10 shadow-2xl lg:hidden">
        <div class="mx-auto flex max-w-5xl items-center justify-around px-2 py-2">
            <Link
                href="/advisor/dashboard"
                :class="navItemClass('Dashboard')"
            >
                <BaseIcon name="dashboard" class="h-5 w-5" />
                <span class="text-[11px] font-semibold">Home</span>
            </Link>

            <Link
                href="/advisor/calendar"
                :class="navItemClass('Calendar')"
            >
                <BaseIcon name="calendar" class="h-5 w-5" />
                <span class="text-[11px] font-semibold">Calendar</span>
            </Link>

            <Link
                href="/advisor/network"
                :class="navItemClass('Network')"
            >
                <BaseIcon name="team" class="h-5 w-5" />
                <span class="text-[11px] font-semibold">Network</span>
            </Link>

            <Link
                href="/advisor/meeting-requests"
                :class="navItemClass('Meeting Requests')"
            >
                <BaseIcon name="tasks" class="h-5 w-5" />
                <span class="text-[11px] font-semibold">Requests</span>
            </Link>

            <Link
                href="/advisor/settings"
                :class="navItemClass('Settings')"
            >
                <BaseIcon name="settings" class="h-5 w-5" />
                <span class="text-[11px] font-semibold">Settings</span>
            </Link>
        </div>
    </nav>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import BaseIcon from './BaseIcon.vue';

const props = defineProps({
    active: {
        type: String,
        default: 'Dashboard'
    }
});

const navItemClass = (label) =>
    computed(() => {
        const isActive = props.active === label;
        return [
            'flex flex-1 flex-col items-center justify-center gap-1 rounded-2xl px-2 py-1 transition-all duration-200',
            isActive ? 'bg-white text-[#205274]' : 'text-white/80 hover:bg-white/10 hover:text-white'
        ];
    }).value;
</script>




