<template>
    <svg
        v-if="icon"
        :viewBox="icon.viewBox"
        :width="size"
        :height="size"
        fill="none"
        stroke="currentColor"
        :stroke-width="icon.strokeWidth ?? 1.6"
        aria-hidden="true"
        class="inline-block"
    >
        <path
            v-for="(path, index) in icon.paths"
            :key="`${name}-${index}`"
            :d="path.d"
            :fill="path.fill ?? 'none'"
            :stroke="path.stroke ?? 'currentColor'"
            :stroke-linecap="path.linecap ?? 'round'"
            :stroke-linejoin="path.linejoin ?? 'round'"
        />
    </svg>
</template>

<script setup>
const iconPaths = {
    dashboard: {
        viewBox: '0 0 24 24',
        paths: [
            { d: 'M3.5 3.5h7v7h-7z' },
            { d: 'M13.5 3.5h7v7h-7z' },
            { d: 'M3.5 13.5h7v7h-7z' },
            { d: 'M13.5 13.5h7v7h-7z' }
        ]
    },
    calendar: {
        viewBox: '0 0 24 24',
        paths: [
            { d: 'M7 3v4' },
            { d: 'M17 3v4' },
            { d: 'M4 8h16v12H4z' },
            { d: 'M8 12h4' },
            { d: 'M14 12h4' }
        ]
    },
    tasks: {
        viewBox: '0 0 24 24',
        paths: [
            { d: 'M10 6h10' },
            { d: 'M10 12h10' },
            { d: 'M10 18h10' },
            { d: 'M4 6l2 2 3-4' },
            { d: 'M4 12l2 2 3-4' },
            { d: 'M4 18l2 2 3-4' }
        ]
    },
    analytics: {
        viewBox: '0 0 24 24',
        paths: [
            { d: 'M4 20h17' },
            { d: 'M7 20V11' },
            { d: 'M12 20V4' },
            { d: 'M17 20V8' },
            { d: 'M21 20v-5' }
        ]
    },
    team: {
        viewBox: '0 0 24 24',
        paths: [
            { d: 'M8 8a3 3 0 1 1 0 6 3 3 0 0 1 0-6z' },
            { d: 'M16 9a3 3 0 1 1 0 6' },
            { d: 'M4 20a4 4 0 0 1 8 0' },
            { d: 'M14 20a4 4 0 0 1 6 0' }
        ]
    },
    help: {
        viewBox: '0 0 24 24',
        paths: [
            { d: 'M12 6a4 4 0 0 1 4 4c0 2-2 2.5-2 4.5' },
            { d: 'M12 18h.01' },
            { d: 'M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z' }
        ]
    },
    logout: {
        viewBox: '0 0 24 24',
        paths: [
            { d: 'M15 7 20 12l-5 5' },
            { d: 'M20 12H9' },
            { d: 'M4 19V5h5' }
        ]
    },
    search: {
        viewBox: '0 0 24 24',
        paths: [
            { d: 'm21 21-4.35-4.35' },
            { d: 'M10.5 4.5a6 6 0 1 1 0 12 6 6 0 0 1 0-12z' }
        ]
    },
    filter: {
        viewBox: '0 0 24 24',
        paths: [
            { d: 'M4 6h16' },
            { d: 'M7 12h10' },
            { d: 'M10 18h4' }
        ]
    },
    'chevron-left': {
        viewBox: '0 0 24 24',
        paths: [{ d: 'M15 18l-6-6 6-6' }]
    },
    'chevron-right': {
        viewBox: '0 0 24 24',
        paths: [{ d: 'M9 6l6 6-6 6' }]
    },
    bell: {
        viewBox: '0 0 24 24',
        paths: [
            {
                d: 'M15 17h5l-1.4-1.4A2 2 0 0 1 18 14V10a6 6 0 0 0-12 0v4a2 2 0 0 1-.6 1.4L4 17h5'
            },
            { d: 'M9 17a3 3 0 0 0 6 0' }
        ]
    }
};

const props = defineProps({
    name: {
        type: String,
        required: true
    },
    size: {
        type: [Number, String],
        default: 20
    }
});

const icon = iconPaths[props.name];
</script>


