<template>
    <Head title="Privacy Policy" />
    <div class="min-h-screen bg-[#eef5fb] px-4 py-16">
        <div class="mx-auto flex max-w-5xl flex-col gap-10">
            <header class="rounded-[32px] border border-white/60 bg-white/90 p-10 shadow-2xl shadow-emerald-100/60 backdrop-blur-lg">
                <p class="text-xs uppercase tracking-[0.45em] text-slate-400">Legal</p>
                <h1 class="mt-4 text-4xl font-semibold text-[#0b1f30]">Privacy Policy</h1>
                <p class="mt-4 text-base text-slate-600">
                    This Privacy Policy explains how Espoo Launch (“we”, “us”, “our”) collects, uses, shares, and protects your
                    information when you use our advisor platform, AI assistant, and related services. We follow global privacy
                    frameworks including GDPR and CCPA, as well as Google OAuth and analytics requirements.
                </p>
                <div class="mt-6 flex flex-wrap gap-4 text-sm text-slate-500">
                    <span class="rounded-full border border-[#d6e4ef] bg-[#f7fbff] px-4 py-1 font-semibold text-[#205274]">
                        Last updated: {{ lastUpdated }}
                    </span>
                </div>
            </header>

            <section class="grid gap-6 rounded-[32px] border border-white/60 bg-white/90 p-10 shadow-xl shadow-emerald-50 backdrop-blur">
                <div class="rounded-2xl border border-[#d4efe3] bg-[#f3fbf7] p-6">
                    <h2 class="text-lg font-semibold text-[#205274]">Summary of How We Handle Data</h2>
                    <p class="mt-2 text-sm text-slate-600">We collect only the data we need to deliver the platform:</p>
                    <ul class="mt-3 list-disc space-y-2 pl-5 text-sm text-slate-600">
                        <li>Account data you provide (name, email, password, role, language)</li>
                        <li>Business context fields you choose to share (roadmap data, meeting prep fields, founder details)</li>
                        <li>Authentication metadata (session cookies, CSRF tokens, Google OAuth IDs)</li>
                        <li>Usage analytics (page interactions, device/browser info) via privacy-focused metrics</li>
                        <li>Conversation data with our AI agent — full transcripts are stored and linked to your account</li>
                    </ul>
                </div>

                <div v-for="section in sections" :key="section.title" class="space-y-3">
                    <h3 class="text-xl font-semibold text-[#205274]">{{ section.title }}</h3>
                    <p v-if="section.description" class="text-sm leading-relaxed text-slate-600">
                        {{ section.description }}
                    </p>
                    <ul v-if="section.list" class="mt-2 list-disc space-y-2 pl-5 text-sm text-slate-600">
                        <li v-for="item in section.list" :key="item">{{ item }}</li>
                    </ul>
                </div>
            </section>

            <section class="rounded-[32px] border border-white/60 bg-white p-8 shadow-xl shadow-emerald-50">
                <h3 class="text-xl font-semibold text-[#205274]">Your Choices & Rights</h3>
                <div class="mt-4 grid gap-4 text-sm text-slate-600 md:grid-cols-2">
                    <div class="rounded-2xl border border-[#d6e4ef] bg-[#f7fbff] p-5">
                        <h4 class="font-semibold text-[#205274]">Access & Portability</h4>
                        <p class="mt-2">Request a copy of your data or ask us to export it in a portable format.</p>
                    </div>
                    <div class="rounded-2xl border border-[#d6e4ef] bg-[#f7fbff] p-5">
                        <h4 class="font-semibold text-[#205274]">Correction & Deletion</h4>
                        <p class="mt-2">Update profile data anytime. Contact us to delete your account and stored transcripts.</p>
                    </div>
                    <div class="rounded-2xl border border-[#d6e4ef] bg-[#f7fbff] p-5">
                        <h4 class="font-semibold text-[#205274]">AI Conversation Recordings</h4>
                        <p class="mt-2">
                            We record every interaction with the AI agent (audio, text, metadata). By using the assistant you
                            consent to these recordings being stored, reviewed, and shared with human advisors to improve the
                            service.
                        </p>
                    </div>
                    <div class="rounded-2xl border border-[#d6e4ef] bg-[#f7fbff] p-5">
                        <h4 class="font-semibold text-[#205274]">Opt-outs</h4>
                        <p class="mt-2">
                            You can opt out of marketing email, disable analytics cookies, or revoke Google access at any time.
                        </p>
                    </div>
                </div>
            </section>

            <footer class="rounded-[32px] border border-[#d4efe3] bg-[#f3fbf7] p-8 text-center text-sm text-slate-600">
                <p>Need to review our Terms and Conditions?</p>
                <p class="mt-2">
                    <Link href="/register" class="font-semibold text-[#205274] underline-offset-2 hover:underline">Return to sign up</Link>
                </p>
            </footer>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';

const lastUpdated = 'November 15, 2025';

const sections = [
    {
        title: '1. Information We Collect',
        list: [
            'Identity data: name, email, role, language, company details you provide.',
            'Authentication data: hashed passwords, session cookies, CSRF tokens, Google OAuth IDs.',
            'Usage data: device/browser info, pages visited, interaction timestamps.',
            'AI assistant data: voice/text transcripts, audio files, roadmap context shared during conversations.',
            'Support data: messages you send to our team, attachments, feedback notes.',
        ],
    },
    {
        title: '2. How We Collect Data',
        list: [
            'Registration and profile forms on the platform.',
            'Cookies and similar technologies that keep you signed in and secure.',
            'Google services (OAuth, analytics) when you connect your account.',
            'Our AI assistant, which stores full transcripts and metadata for every session.',
            'System logs that automatically capture errors and performance metrics.',
        ],
    },
    {
        title: '3. Why We Use Your Data',
        list: [
            'Authenticate you, personalize dashboards, and route you to the correct role experience.',
            'Match founders with advisors, generate meeting briefs, and produce PDFs you request.',
            'Improve the AI agent and roadmap recommendations using anonymized insights.',
            'Send important account notices, reminders, and security alerts.',
            'Comply with legal obligations, prevent abuse, and enforce platform policies.',
        ],
    },
    {
        title: '4. Sharing & Third Parties',
        description:
            'We only share data with partners that help us deliver the service, under confidentiality agreements:',
        list: [
            'Infrastructure & hosting providers (e.g., AWS, Laravel Forge).',
            'Analytics and error monitoring (privacy-friendly tools, Google analytics if enabled).',
            'Authentication partners (Google Socialite for OAuth login).',
            'Advisors matched to you, who can access the roadmap data you provide.',
            'Law enforcement or regulators when required to comply with the law.',
        ],
    },
    {
        title: '5. Cookies & Tracking',
        description:
            'We use strictly necessary cookies for login sessions, security, and load balancing. Optional analytics cookies record anonymized usage patterns to improve UX. You can adjust preferences in your browser or disable analytics in settings.',
    },
    {
        title: '6. Data Retention & Security',
        list: [
            'We retain account data while you have an active profile, then delete or anonymize it after closure unless a longer period is required by law.',
            'AI assistant transcripts are stored until you request deletion, because they improve answers and advisor hand-offs.',
            'Passwords are hashed, TLS is enforced, and we use least-privilege access controls for staff.',
            'We regularly review vendors, perform backups, and monitor for suspicious activity.',
        ],
    },
    {
        title: '7. International Transfers',
        description:
            'Data may be processed in the EU, US, or other regions where our trusted vendors operate. When data leaves the EU/EEA we rely on Standard Contractual Clauses or equivalent safeguards.',
    },
    {
        title: '8. Children’s Data',
        description:
            'Our services are not directed to children under 16, and we do not knowingly collect their data. If you believe we have collected information about a child, contact us and we will delete it.',
    },
    {
        title: '9. Changes to This Policy',
        description:
            'We may update this document to reflect product or legal changes. We will notify you via email or in-app notice before material updates take effect.',
    },
];
</script>

