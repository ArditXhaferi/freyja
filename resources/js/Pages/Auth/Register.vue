<template>
    <Head title="Sign Up" />
    <div class="min-h-screen bg-[#eef5fb] flex items-center justify-center px-4 py-10">
        <div class="grid w-full max-w-5xl overflow-hidden rounded-[32px] border border-white/60 bg-white/70 shadow-2xl shadow-emerald-100/60 backdrop-blur-lg lg:grid-cols-[1.1fr_1fr]">
            <section class="relative hidden bg-gradient-to-br from-[#205274] via-[#276487] to-[#5cc094] p-10 text-white lg:flex lg:flex-col">
                <div class="flex flex-1 flex-col justify-between space-y-10">
                    <!-- Top copy -->
                    <div>
                        <p class="text-xs uppercase tracking-[0.45em] text-white/70">Espoo launch</p>
                        <h1 class="mt-6 text-4xl font-semibold leading-tight">Create your business workspace</h1>
                        <p class="mt-4 text-sm text-white/80">
                            A focused space to review founders, accept meetings, and keep their roadmap in view.
                        </p>
                    </div>

                    <!-- Beer + tooltip -->
                    <div class="flex items-center gap-4">
                        <img src="./videos/video4.gif" alt="Cheers" class="h-[13.5rem] w-[13.5rem] rounded-2xl object-cover" />
                        <div class="relative">
                            <div
                                class="max-w-[260px] rounded-2xl px-5 py-4 text-sm font-medium text-white/95 backdrop-blur-sm"
                                style="background-color: rgba(255, 255, 255, 0.16);"
                            >
                                Be ready to learn a lot together about the process of opening a business
                            </div>
                            <span
                                class="absolute left-0 top-1/2 -translate-x-2 -translate-y-1/2 h-4 w-4 rotate-45"
                                style="background-color: rgba(255, 255, 255, 0.16);"
                            ></span>
                        </div>
                    </div>

                    <!-- Bottom feature/checklist card -->
                    <div class="">
                        <div>
                            
                           
                        </div>

                        <div class="">
                         
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

                <form @submit.prevent="submit" class="mt-6 space-y-6">
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

                        <div ref="roleSectionRef">
                            <label for="role" class="text-xs font-semibold uppercase tracking-widest text-slate-400">Role</label>
                            <div
                                class="mt-2 rounded-2xl px-4 py-2.5 text-sm focus-within:border-[#205274]"
                                :class="googleRoleError ? 'border border-rose-300 bg-rose-50/70' : 'border border-slate-200 bg-[#f6f9ff]'"
                            >
                                <select
                                    id="role"
                                    v-model="form.role"
                                    required
                                    class="w-full bg-transparent text-slate-600 focus:outline-none"
                                >
                                    <option value="" disabled>Select your role</option>
                                    <option value="entrepreneur">Entrepreneur</option>
                                    <option value="advisor">Advisor</option>
                                </select>
                            </div>
                            <p v-if="googleRoleError" class="mt-1 text-xs font-medium text-rose-500">
                                {{ googleRoleError }}
                            </p>
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

                    <div
                        ref="termsSectionRef"
                        class="flex items-start gap-3 rounded-2xl p-4 text-sm"
                        :class="termsFieldError ? 'border border-rose-300 bg-rose-50/70 text-rose-600' : 'border border-slate-200 bg-[#f6f9ff] text-slate-600'"
                    >
                        <input
                            id="accepted_terms"
                            v-model="form.accepted_terms"
                            type="checkbox"
                            :aria-invalid="!!termsFieldError"
                            class="mt-1 h-4 w-4 rounded border-slate-300 text-[#205274] focus:ring-[#205274]"
                            required
                        />
                        <span>
                            By creating an account you confirm you agree to our terms and conditions and
                            <Link href="/privacy-policy" class="font-semibold text-[#205274] underline-offset-2 hover:underline">Privacy Policy</Link>.
                        </span>
                    </div>
                    <p v-if="termsFieldError" class="text-xs font-medium text-rose-500">
                        {{ termsFieldError }}
                    </p>

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
import { computed, nextTick, ref, watch } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: '',
    language: 'en',
    country_of_origin: '',
    has_business_experience: false,
    accepted_terms: false,
});

const googleRoleError = ref('');
const googleTermsError = ref('');
const roleSectionRef = ref(null);
const termsSectionRef = ref(null);

const scrollToElement = (element) => {
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'center' });
        // Add a subtle highlight effect
        element.style.transition = 'box-shadow 0.3s ease';
        element.style.boxShadow = '0 0 0 4px rgba(239, 68, 68, 0.2)';
        setTimeout(() => {
            element.style.boxShadow = '';
        }, 2000);
    }
};

const submit = () => {
    form.post('/register');
};

const continueWithGoogle = async () => {
    let hasErrors = false;
    let firstErrorElement = null;

    if (!form.role) {
        googleRoleError.value = 'Choose whether you are an entrepreneur or advisor.';
        hasErrors = true;
        if (!firstErrorElement) {
            firstErrorElement = roleSectionRef.value;
        }
    }

    if (!form.accepted_terms) {
        googleTermsError.value = 'Please agree to the terms and privacy policy before using Google sign-in.';
        hasErrors = true;
        if (!firstErrorElement) {
            firstErrorElement = termsSectionRef.value;
        }
    }

    if (hasErrors) {
        await nextTick();
        if (firstErrorElement) {
            scrollToElement(firstErrorElement);
        }
        return;
    }

    googleRoleError.value = '';
    googleTermsError.value = '';
    window.location.href = `/auth/google/redirect?role=${form.role}`;
};

watch(
    () => form.role,
    (newVal) => {
        if (newVal) {
            googleRoleError.value = '';
        }
    }
);

watch(
    () => form.accepted_terms,
    (checked) => {
        if (checked) {
            googleTermsError.value = '';
        }
    }
);

const termsFieldError = computed(() => form.errors.accepted_terms || googleTermsError.value);
</script>
