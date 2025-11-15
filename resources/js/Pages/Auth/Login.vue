<template>
    <Head title="Login" />
    <div class="min-h-screen bg-[#eef5fb] flex items-center justify-center px-4 py-10">
        <div class="grid w-full max-w-5xl overflow-hidden rounded-[32px] border border-white/60 bg-white/70 shadow-2xl shadow-emerald-100/60 backdrop-blur-lg lg:grid-cols-[1.1fr_1fr]">
            <section class="relative hidden bg-gradient-to-br from-[#205274] via-[#276487] to-[#5cc094] p-10 text-white lg:flex lg:flex-col lg:justify-between">
                <div>
                    <p class="text-xs uppercase tracking-[0.5em] text-white/70">Espoo Launch</p>
                    <h1 class="mt-6 text-4xl font-semibold leading-tight">Advisor Portal</h1>
                    <p class="mt-4 text-sm text-white/80">
                        Manage founder requests, review their roadmap context, and keep your calendar in sync —
                        all from a single calm workspace.
                    </p>
                </div>
                <div class="space-y-4">
                    <div class="rounded-3xl border border-white/20 bg-white/10 p-5 backdrop-blur-sm">
                        <p class="text-sm uppercase tracking-[0.4em] text-white/60">Why advisors use this</p>
                        <p class="mt-2 text-lg font-semibold">One place for all founder conversations</p>
                        <p class="text-sm text-white/70">See briefs, requests, and roadmaps in a single calm view.</p>
                    </div>
                    <div class="flex items-center gap-3 text-xs uppercase tracking-[0.5em] text-white/60">
                        <span class="h-[2px] flex-1 bg-white/40"></span>
                        Secure access
                        <span class="h-[2px] flex-1 bg-white/40"></span>
                    </div>
                </div>
            </section>

            <section class="p-8 sm:p-10 bg-white">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.45em] text-slate-400">Welcome back</p>
                    <h2 class="text-3xl font-semibold text-[#205274]">Sign in to continue</h2>
                    <p class="text-sm text-slate-500">Use your Espoo advisor credentials to access the workspace.</p>
                </div>

                <form @submit.prevent="submit" class="mt-8 space-y-6">
                    <div v-if="Object.keys($page.props.errors).length" class="rounded-2xl border border-rose-100 bg-rose-50/80 p-4 text-sm text-rose-600">
                        {{ Object.values($page.props.errors)[0] }}
                    </div>

                    <div>
                        <label for="email" class="text-xs font-semibold uppercase tracking-widest text-slate-400">Email address</label>
                        <div class="mt-2 rounded-2xl border border-slate-200 bg-[#f6f9ff] px-4 py-3 text-sm text-slate-600 focus-within:border-[#205274]">
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
                        <div class="mt-2 rounded-2xl border border-slate-200 bg-[#f6f9ff] px-4 py-3 text-sm text-slate-600 focus-within:border-[#205274]">
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                required
                                class="w-full bg-transparent placeholder:text-slate-400 focus:outline-none"
                                placeholder="Enter your password"
                            />
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm text-slate-500">
                        <label class="flex items-center gap-2">
                            <input
                                id="remember"
                                v-model="form.remember"
                                type="checkbox"
                                class="h-4 w-4 rounded border-slate-300 text-[#205274] focus:ring-[#205274]"
                            />
                            Remember me
                        </label>
                        <Link href="/" class="text-[#205274] hover:text-[#5cc094]">Need help?</Link>
                    </div>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="w-full rounded-2xl bg-gradient-to-r from-[#205274] to-[#5cc094] py-3 text-center text-sm font-semibold text-white shadow-lg shadow-emerald-200 transition hover:scale-[1.01] disabled:opacity-60"
                    >
                        {{ form.processing ? 'Signing in...' : 'Sign in' }}
                    </button>

                    <p class="text-center text-sm text-slate-500">
                        Don’t have an account?
                        <Link href="/register" class="font-semibold text-[#205274] hover:text-[#5cc094]">Create one</Link>
                    </p>
                </form>
            </section>
        </div>
    </div>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

