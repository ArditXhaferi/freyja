<template>
    <Head title="Sign Up" />
    <div class="min-h-screen bg-[#eef5fb] flex items-center justify-center px-4 py-10">
        <div class="grid w-full max-w-5xl overflow-hidden rounded-[32px] border border-white/60 bg-white/70 shadow-2xl shadow-blue-100/60 backdrop-blur-lg lg:grid-cols-[1.1fr_1fr]">
            <section class="relative hidden bg-gradient-to-br from-[#4da0ff] via-[#5fb2ff] to-[#0f4f8b] p-10 text-white lg:flex lg:flex-col lg:justify-between">
                <div class="space-y-8">
                    <div>
                        <p class="text-xs uppercase tracking-[0.45em] text-white/70">Espoo launch</p>
                        <h1 class="mt-6 text-4xl font-semibold leading-tight">Create your advisor workspace</h1>
                        <p class="mt-4 text-sm text-white/80">
                            Unlock an operating system for guiding new businesses entering Espoo. Share guidance, review roadmaps,
                            and keep every meeting aligned with their growth journey.
                        </p>
                    </div>

                    <div class="grid gap-4 rounded-3xl border border-white/20 bg-white/10 p-5 backdrop-blur-sm">
                        <div>
                            <p class="text-xs uppercase tracking-[0.5em] text-white/60">What you get</p>
                            <ul class="mt-3 space-y-3 text-sm text-white/85">
                                <li class="flex items-start gap-3">
                                    <span class="mt-1 h-1.5 w-1.5 rounded-full bg-white/80"></span>
                                    Unified dashboard with founder briefs, reminders, and roadmap previews.
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="mt-1 h-1.5 w-1.5 rounded-full bg-white/80"></span>
                                    Meeting request queue with structured context from each entrepreneur.
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="mt-1 h-1.5 w-1.5 rounded-full bg-white/80"></span>
                                    Calendar view with automatic reminders (1 week, 3 days, 1 day, 1 hour).
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="mt-1 h-1.5 w-1.5 rounded-full bg-white/80"></span>
                                    Voice roadmap insights to understand every founder’s latest milestones.
                                </li>
                            </ul>
                        </div>

                        <div class="rounded-2xl border border-white/20 bg-white/5 p-4">
                            <p class="text-xs uppercase tracking-[0.5em] text-white/60">Advisor checklist</p>
                            <ol class="mt-3 space-y-2 text-sm text-white/80">
                                <li>1 · Choose your role (advisor or entrepreneur).</li>
                                <li>2 · Share your preferred language and origin.</li>
                                <li>3 · Tell us if you have prior business experience.</li>
                                <li>4 · Activate your account and start reviewing leads.</li>
                            </ol>
                        </div>
                    </div>

                    <div class="space-y-3 rounded-3xl border border-white/20 bg-white/10 p-5 backdrop-blur-sm">
                        <p class="text-xs uppercase tracking-[0.5em] text-white/60">Trusted by</p>
                        <div class="text-2xl font-semibold">45+ advisors · 180+ founders</div>
                        <p class="text-sm text-white/80">
                            “Espoo Launch keeps me looped in on every founder’s progress before we even jump on a call.”
                        </p>
                        <p class="text-xs uppercase tracking-[0.5em] text-white/60">Secure onboarding · SOC2-ready</p>
                    </div>
                </div>
            </section>

            <section class="p-8 sm:p-10 bg-white">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.45em] text-slate-400">Let’s get started</p>
                    <h2 class="text-3xl font-semibold text-[#0f2e5a]">Create your account</h2>
                    <p class="text-sm text-slate-500">Tell us who you are so we can tailor the workspace to your role.</p>
                </div>

                <form @submit.prevent="submit" class="mt-8 space-y-6">
                    <div v-if="Object.keys($page.props.errors).length" class="rounded-2xl border border-rose-100 bg-rose-50/80 p-4 text-sm text-rose-600">
                        <ul class="space-y-1">
                            <li v-for="(error, key) in $page.props.errors" :key="key">{{ error }}</li>
                        </ul>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="sm:col-span-2">
                            <label for="name" class="text-xs font-semibold uppercase tracking-widest text-slate-400">Full name</label>
                            <div class="mt-2 rounded-2xl border border-slate-200 bg-[#f6f9ff] px-4 py-3 text-sm focus-within:border-[#4da0ff]">
                                <input
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    class="w-full bg-transparent placeholder:text-slate-400 focus:outline-none"
                                    placeholder="Sanna Virtanen"
                                />
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="email" class="text-xs font-semibold uppercase tracking-widest text-slate-400">Email address</label>
                            <div class="mt-2 rounded-2xl border border-slate-200 bg-[#f6f9ff] px-4 py-3 text-sm focus-within:border-[#4da0ff]">
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    class="w-full bg-transparent placeholder:text-slate-400 focus:outline-none"
                                    placeholder="you@example.com"
                                />
                            </div>
                        </div>

                        <div>
                            <label for="password" class="text-xs font-semibold uppercase tracking-widest text-slate-400">Password</label>
                            <div class="mt-2 rounded-2xl border border-slate-200 bg-[#f6f9ff] px-4 py-3 text-sm focus-within:border-[#4da0ff]">
                                <input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    required
                                    class="w-full bg-transparent placeholder:text-slate-400 focus:outline-none"
                                    placeholder="Create a password"
                                />
                            </div>
                        </div>
                        <div>
                            <label for="password_confirmation" class="text-xs font-semibold uppercase tracking-widest text-slate-400">Confirm password</label>
                            <div class="mt-2 rounded-2xl border border-slate-200 bg-[#f6f9ff] px-4 py-3 text-sm focus-within:border-[#4da0ff]">
                                <input
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    type="password"
                                    required
                                    class="w-full bg-transparent placeholder:text-slate-400 focus:outline-none"
                                    placeholder="Repeat password"
                                />
                            </div>
                        </div>

                        <div>
                            <label for="role" class="text-xs font-semibold uppercase tracking-widest text-slate-400">Role</label>
                            <div class="mt-2 rounded-2xl border border-slate-200 bg-[#f6f9ff] px-4 py-2.5 text-sm focus-within:border-[#4da0ff]">
                                <select
                                    id="role"
                                    v-model="form.role"
                                    class="w-full bg-transparent text-slate-600 focus:outline-none"
                                >
                                    <option value="entrepreneur">Entrepreneur</option>
                                    <option value="advisor">Advisor</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="language" class="text-xs font-semibold uppercase tracking-widest text-slate-400">Preferred language</label>
                            <div class="mt-2 rounded-2xl border border-slate-200 bg-[#f6f9ff] px-4 py-2.5 text-sm focus-within:border-[#4da0ff]">
                                <select
                                    id="language"
                                    v-model="form.language"
                                    class="w-full bg-transparent text-slate-600 focus:outline-none"
                                >
                                    <option value="en">English</option>
                                    <option value="fi">Finnish</option>
                                    <option value="sv">Swedish</option>
                                </select>
                            </div>
                        </div>

                        <div class="sm:col-span-2">
                            <label for="country_of_origin" class="text-xs font-semibold uppercase tracking-widest text-slate-400">Country of origin (optional)</label>
                            <div class="mt-2 rounded-2xl border border-slate-200 bg-[#f6f9ff] px-4 py-3 text-sm focus-within:border-[#4da0ff]">
                                <input
                                    id="country_of_origin"
                                    v-model="form.country_of_origin"
                                    type="text"
                                    class="w-full bg-transparent placeholder:text-slate-400 focus:outline-none"
                                    placeholder="e.g., Finland, USA"
                                />
                            </div>
                        </div>
                    </div>

                    <label class="flex items-start gap-3 rounded-2xl border border-slate-200 bg-[#f6f9ff] p-4 text-sm text-slate-600">
                        <input
                            id="has_business_experience"
                            v-model="form.has_business_experience"
                            type="checkbox"
                            value="1"
                            class="mt-1 h-4 w-4 rounded border-slate-300 text-[#4da0ff] focus:ring-[#4da0ff]"
                        />
                        <span>
                            I have previous business experience
                            <span class="block text-xs text-slate-400">Helps us adapt the onboarding flow to your background.</span>
                        </span>
                    </label>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-2xl bg-gradient-to-r from-[#8fc9ff] to-[#4da0ff] py-3 text-center text-sm font-semibold text-white shadow-lg shadow-blue-200 transition hover:scale-[1.01] disabled:opacity-60"
                    >
                        {{ form.processing ? 'Creating account...' : 'Create account' }}
                    </button>

                    <p class="text-center text-sm text-slate-500">
                        Already have an account?
                        <Link href="/login" class="font-semibold text-[#0f4f8b] hover:text-[#4da0ff]">Sign in</Link>
                    </p>
                </form>
            </section>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'entrepreneur',
    language: 'en',
    country_of_origin: '',
    has_business_experience: false
});

const submit = () => {
    form.post('/register');
};
</script>
