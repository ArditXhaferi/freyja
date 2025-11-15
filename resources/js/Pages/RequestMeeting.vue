<template>
    <Head title="Request Meeting" />
    <div class="min-h-screen bg-[#eef5fb] py-12 px-4">
        <div class="mx-auto max-w-3xl rounded-3xl bg-white/95 p-8 shadow-lg shadow-blue-100/60">
            <div class="mb-6">
                <p class="text-xs uppercase tracking-[0.4em] text-slate-400">Schedule</p>
                <h1 class="text-3xl font-semibold text-[#0f2e5a]">Request a meeting</h1>
                <p class="mt-2 text-sm text-slate-500">
                    Share a preferred date &amp; time. The Espoo advisor will confirm or suggest a new slot.
                </p>
            </div>
            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label class="text-xs font-semibold uppercase tracking-widest text-slate-400">Advisor</label>
                    <select v-model="form.advisor_id" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-600 focus:border-[#4da0ff] focus:outline-none">
                        <option disabled value="">Select advisor</option>
                        <option v-for="advisor in advisors" :key="advisor.id" :value="advisor.id">
                            {{ advisor.name }} ({{ advisor.email }})
                        </option>
                    </select>
                    <p v-if="form.errors.advisor_id" class="mt-1 text-xs text-rose-500">{{ form.errors.advisor_id }}</p>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-slate-400">Preferred date</label>
                        <input type="date" v-model="form.preferred_date" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-600 focus:border-[#4da0ff] focus:outline-none" />
                        <p v-if="form.errors.preferred_date" class="mt-1 text-xs text-rose-500">{{ form.errors.preferred_date }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold uppercase tracking-widest text-slate-400">Preferred time</label>
                        <input type="time" v-model="form.preferred_time" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-600 focus:border-[#4da0ff] focus:outline-none" />
                        <p v-if="form.errors.preferred_time" class="mt-1 text-xs text-rose-500">{{ form.errors.preferred_time }}</p>
                    </div>
                </div>
                <div>
                    <label class="text-xs font-semibold uppercase tracking-widest text-slate-400">Context for advisor</label>
                    <textarea v-model="form.message" rows="4" class="mt-2 w-full rounded-2xl border border-slate-200 px-4 py-3 text-sm text-slate-600 focus:border-[#4da0ff] focus:outline-none" placeholder="Share what you would like to cover in the meeting."></textarea>
                    <p v-if="form.errors.message" class="mt-1 text-xs text-rose-500">{{ form.errors.message }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <button type="submit" class="rounded-2xl bg-gradient-to-r from-[#8fc9ff] to-[#4da0ff] px-6 py-3 text-sm font-semibold text-white shadow transition hover:opacity-90" :disabled="form.processing">
                        {{ form.processing ? 'Sending...' : 'Send request' }}
                    </button>
                    <Link href="/advisor/dashboard" class="text-sm font-semibold text-slate-500 hover:text-[#0f2e5a]">Back to dashboard</Link>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    advisors: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    advisor_id: '',
    preferred_date: '',
    preferred_time: '',
    message: ''
});

const submit = () => {
    form.post('/meetings/request');
};
</script>


