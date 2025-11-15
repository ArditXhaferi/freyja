<template>
    <Head title="Sign Up" />
    <div class="min-h-screen bg-[#eef5fb] flex items-center justify-center px-4 py-10">
        <div class="grid w-full max-w-5xl overflow-hidden rounded-[32px] border border-white/60 bg-white/70 shadow-2xl shadow-emerald-100/60 backdrop-blur-lg lg:grid-cols-[1.1fr_1fr]">
            <section class="relative hidden bg-gradient-to-br from-[#205274] via-[#276487] to-[#5cc094] p-10 text-white lg:flex lg:flex-col">
                <div class="flex flex-1 flex-col justify-between space-y-10">
                    <!-- Top copy -->
                    <div>
                        <p class="text-xs uppercase tracking-[0.45em] text-white/70">Espoo launch</p>
                        <h1 class="mt-6 text-4xl font-semibold leading-tight">Create your advisor workspace</h1>
                        <p class="mt-4 text-sm text-white/80">
                            A focused space to review founders, accept meetings, and keep their roadmap in view.
                        </p>
                    </div>

                    <!-- Bottom feature/checklist card -->
                    <div class="grid gap-4 rounded-3xl border border-white/20 bg-white/10 p-5 backdrop-blur-sm">
                        <div>
                            <p class="text-xs uppercase tracking-[0.5em] text-white/60">What you get</p>
                            <ul class="mt-3 space-y-2 text-sm text-white/85">
                                <li class="flex items-start gap-3">
                                    <span class="mt-1 h-1.5 w-1.5 rounded-full bg-white/80"></span>
                                    Clean dashboard with founder briefs and reminders.
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="mt-1 h-1.5 w-1.5 rounded-full bg-white/80"></span>
                                    Meeting requests with structured context.
                                </li>
                                <li class="flex items-start gap-3">
                                    <span class="mt-1 h-1.5 w-1.5 rounded-full bg-white/80"></span>
                                    Roadmap previews for every founder.
                                </li>
                            </ul>
                        </div>

                        <div class="rounded-2xl border border-white/20 bg-white/5 p-4">
                            <p class="text-xs uppercase tracking-[0.5em] text-white/60">Advisor checklist</p>
                            <ol class="mt-3 space-y-2 text-sm text-white/80">
                                <li>1 · Fill in your basic details.</li>
                                <li>2 · Choose your role and language.</li>
                                <li>3 · Start reviewing founder requests.</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <section class="p-8 sm:p-10 bg-white">
                <div class="space-y-2">
                        <p class="text-xs uppercase tracking-[0.45em] text-slate-400">Let’s get started</p>
                        <h2 class="text-3xl font-semibold text-[#205274]">Create your account</h2>
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
                            <div class="mt-2 rounded-2xl border border-slate-200 bg-[#f6f9ff] px-4 py-3 text-sm focus-within:border-[#205274]">
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
                            <div class="mt-2 rounded-2xl border border-slate-200 bg-[#f6f9ff] px-4 py-3 text-sm focus-within:border-[#205274]">
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
                            <div class="mt-2 rounded-2xl border border-slate-200 bg-[#f6f9ff] px-4 py-3 text-sm focus-within:border-[#205274]">
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
                            <div class="mt-2 rounded-2xl border border-slate-200 bg-[#f6f9ff] px-4 py-3 text-sm focus-within:border-[#205274]">
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
                            <div class="mt-2 rounded-2xl border border-slate-200 bg-[#f6f9ff] px-4 py-2.5 text-sm focus-within:border-[#205274]">
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
                            <div class="mt-2 rounded-2xl border border-slate-200 bg-[#f6f9ff] px-4 py-2.5 text-sm focus-within:border-[#205274]">
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
                            <div class="mt-2 rounded-2xl border border-slate-200 bg-[#f6f9ff] px-4 py-3 text-sm focus-within:border-[#205274]">
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
                            class="mt-1 h-4 w-4 rounded border-slate-300 text-[#205274] focus:ring-[#205274]"
                        />
                        <span>
                            I have previous business experience
                            <span class="block text-xs text-slate-400">Helps us adapt the onboarding flow to your background.</span>
                        </span>
                    </label>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-2xl bg-gradient-to-r from-[#205274] to-[#5cc094] py-3 text-center text-sm font-semibold text-white shadow-lg shadow-emerald-200 transition hover:scale-[1.01] disabled:opacity-60"
                    >
                        {{ form.processing ? 'Creating account...' : 'Create account' }}
                    </button>

                    <p class="text-center text-sm text-slate-500">
                        Already have an account?
                        <Link href="/login" class="font-semibold text-[#205274] hover:text-[#5cc094]">Sign in</Link>
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
