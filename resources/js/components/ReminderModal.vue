<template>
    <transition name="fade">
        <div v-if="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm">
            <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl shadow-blue-200/80">
                <header class="flex items-center justify-between">
                    <div>
                        <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Reminders</p>
                        <h3 class="text-2xl font-semibold text-[#0f2e5a]">Upcoming meetings</h3>
                    </div>
                    <button class="rounded-full bg-slate-100 p-2 text-slate-500 hover:text-slate-700" @click="$emit('close')">
                        ✕
                    </button>
                </header>
                <section class="mt-5 max-h-72 space-y-3 overflow-y-auto pr-2">
                    <p v-if="error" class="rounded-2xl bg-rose-50 px-4 py-2 text-xs font-semibold text-rose-600">
                        {{ error }}
                    </p>
                    <p v-else-if="loading" class="rounded-2xl bg-slate-50 px-4 py-2 text-xs font-semibold text-slate-500">
                        Loading reminders...
                    </p>
                    <article
                        v-for="reminder in reminders"
                        :key="reminder.id"
                        class="rounded-2xl border border-slate-100 bg-[#f2f8ff] p-4 text-sm text-[#0f2e5a]"
                    >
                        <p class="font-semibold">{{ reminder.message }}</p>
                        <p class="text-xs text-slate-500">
                            {{ formatDate(reminder.remind_at) }}
                        </p>
                    </article>
                    <p v-if="!loading && !error && !reminders.length" class="text-center text-sm text-slate-400">No reminders yet.</p>
                </section>
                <footer class="mt-6 flex items-center justify-between">
                    <button
                        class="text-sm font-semibold text-slate-500 hover:text-[#0f2e5a]"
                        @click="$emit('close')"
                    >
                        Close
                    </button>
                    <button
                        v-if="reminders.length"
                        class="rounded-2xl bg-gradient-to-r from-[#8fc9ff] to-[#4da0ff] px-4 py-2 text-sm font-semibold text-white shadow"
                        @click="$emit('acknowledge')"
                    >
                        Mark as read
                    </button>
                </footer>
            </div>
        </div>
    </transition>
</template>

<script setup>
const props = defineProps({
    open: {
        type: Boolean,
        default: false
    },
    reminders: {
        type: Array,
        default: () => []
    },
    loading: {
        type: Boolean,
        default: false
    },
    error: {
        type: String,
        default: null
    }
});

const formatDate = (value) => {
    if (!value) return '';
    try {
        const date = new Date(value);
        return date.toLocaleString(undefined, {
            weekday: 'short',
            month: 'short',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (e) {
        return value;
    }
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


