<template>
    <Head title="Features" />
    <div class="min-h-screen bg-[#eef5fb] px-4 py-16">
        <div class="mx-auto flex max-w-5xl flex-col gap-10">
            <header class="rounded-[32px] border border-white/60 bg-white/90 p-10 shadow-2xl shadow-emerald-100/60 backdrop-blur-lg">
                <p class="text-xs uppercase tracking-[0.45em] text-slate-400">Platform Overview</p>
                <h1 class="mt-4 text-4xl font-semibold text-[#0b1f30]">All Features</h1>
                <p class="mt-4 text-base text-slate-600">
                    Espoo Launch provides a comprehensive digital business advisory platform designed to empower
                    entrepreneurs and support advisors. Explore all the features we offer to help you succeed in
                    starting and growing your business.
                </p>
                <div class="mt-6 flex flex-wrap gap-4 text-sm text-slate-500">
                    <span class="rounded-full border border-[#d6e4ef] bg-[#f7fbff] px-4 py-1 font-semibold text-[#205274]">
                        Updated: {{ lastUpdated }}
                    </span>
                </div>
            </header>

            <!-- For Entrepreneurs Section -->
            <section class="grid gap-6 rounded-[32px] border border-white/60 bg-white/90 p-10 shadow-xl shadow-emerald-50 backdrop-blur">
                <div class="rounded-2xl border border-[#d4efe3] bg-[#f3fbf7] p-6">
                    <h2 class="text-2xl font-semibold text-[#205274]">For Entrepreneurs</h2>
                    <p class="mt-2 text-sm text-slate-600">
                        Everything you need to start and grow your business, from idea to launch and beyond.
                    </p>
                </div>

                <div v-for="feature in entrepreneurFeatures" :key="feature.title" class="space-y-3">
                    <h3 class="text-xl font-semibold text-[#205274]">{{ feature.title }}</h3>
                    <p v-if="feature.description" class="text-sm leading-relaxed text-slate-600">
                        {{ feature.description }}
                    </p>
                    <ul v-if="feature.list" class="mt-2 list-disc space-y-2 pl-5 text-sm text-slate-600">
                        <li v-for="item in feature.list" :key="item">{{ item }}</li>
                    </ul>
                </div>
            </section>

            <!-- For Advisors Section -->
            <section class="grid gap-6 rounded-[32px] border border-white/60 bg-white/90 p-10 shadow-xl shadow-emerald-50 backdrop-blur">
                <div class="rounded-2xl border border-[#d4efe3] bg-[#f3fbf7] p-6">
                    <h2 class="text-2xl font-semibold text-[#205274]">For Advisors</h2>
                    <p class="mt-2 text-sm text-slate-600">
                        Powerful tools to manage your advisory practice and help entrepreneurs succeed.
                    </p>
                </div>

                <div v-for="feature in advisorFeatures" :key="feature.title" class="space-y-3">
                    <h3 class="text-xl font-semibold text-[#205274]">{{ feature.title }}</h3>
                    <p v-if="feature.description" class="text-sm leading-relaxed text-slate-600">
                        {{ feature.description }}
                    </p>
                    <ul v-if="feature.list" class="mt-2 list-disc space-y-2 pl-5 text-sm text-slate-600">
                        <li v-for="item in feature.list" :key="item">{{ item }}</li>
                    </ul>
                </div>
            </section>

            <!-- Core Platform Features -->
            <section class="rounded-[32px] border border-white/60 bg-white p-8 shadow-xl shadow-emerald-50">
                <h3 class="text-xl font-semibold text-[#205274]">Core Platform Features</h3>
                <div class="mt-4 grid gap-4 text-sm text-slate-600 md:grid-cols-2">
                    <div class="rounded-2xl border border-[#d6e4ef] bg-[#f7fbff] p-5">
                        <h4 class="font-semibold text-[#205274]">AI-Powered Assistance</h4>
                        <p class="mt-2">
                            Intelligent voice and text conversations with our AI assistant that understands your business
                            context and provides tailored guidance.
                        </p>
                    </div>
                    <div class="rounded-2xl border border-[#d6e4ef] bg-[#f7fbff] p-5">
                        <h4 class="font-semibold text-[#205274]">Personalized Roadmaps</h4>
                        <p class="mt-2">
                            Customized startup checklists and roadmaps that adapt to your background, business type, and
                            goals.
                        </p>
                    </div>
                    <div class="rounded-2xl border border-[#d6e4ef] bg-[#f7fbff] p-5">
                        <h4 class="font-semibold text-[#205274]">Multi-language Support</h4>
                        <p class="mt-2">
                            Available in English, Finnish, and Swedish to serve Espoo's diverse international
                            community.
                        </p>
                    </div>
                    <div class="rounded-2xl border border-[#d6e4ef] bg-[#f7fbff] p-5">
                        <h4 class="font-semibold text-[#205274]">Secure Authentication</h4>
                        <p class="mt-2">
                            Email-based registration and Google OAuth integration for quick and secure access to the
                            platform.
                        </p>
                    </div>
                </div>
            </section>

            <footer class="rounded-[32px] border border-[#d4efe3] bg-[#f3fbf7] p-8 text-center text-sm text-slate-600">
                <p>Ready to get started?</p>
                <p class="mt-2">
                    <Link href="/register" class="font-semibold text-[#205274] underline-offset-2 hover:underline">
                        Create your account
                    </Link>
                    <span class="mx-2">or</span>
                    <Link href="/login" class="font-semibold text-[#205274] underline-offset-2 hover:underline">
                        Sign in
                    </Link>
                </p>
            </footer>
        </div>
    </div>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';

const lastUpdated = 'November 15, 2025';

const entrepreneurFeatures = [
    {
        title: '1. Voice & Text AI Assistant',
        description:
            'Have natural conversations with our AI assistant through voice or text. Get instant answers to your business questions, receive guidance on startup procedures, and access information tailored to your specific situation.',
        list: [
            'Voice conversations with real-time transcription',
            'Text chat for quick questions and detailed inquiries',
            'Context-aware responses based on your business profile',
            'Full conversation history saved for future reference',
            'Multi-language support for international entrepreneurs',
        ],
    },
    {
        title: '2. Personalized Roadmap Builder',
        description:
            'Get a customized step-by-step roadmap that guides you through starting your business. The roadmap adapts to your background, business type, country of origin, and experience level.',
        list: [
            'Interactive roadmap visualization with progress tracking',
            'Steps automatically tailored to your context (EU/non-EU, newcomer status, etc.)',
            'Mark steps as complete to track your progress',
            'Get notifications and reminders for important milestones',
            'Dynamic roadmap updates based on your interactions',
        ],
    },
    {
        title: '3. Meeting Preparation Wizard',
        description:
            'Prepare thoroughly for your advisory meetings with structured guidance. Create meeting briefs, compile your questions, and organize your business information.',
        list: [
            'Business summary generation from your profile data',
            'Target customer analysis and identification',
            'Questions for advisors organized by topic',
            'Meeting prep documents formatted for easy sharing',
            'PDF export of your meeting preparation materials',
        ],
    },
    {
        title: '4. Meeting Request System',
        description:
            'Easily request meetings with specialized advisors based on your needs. Connect with experts who can help with specific aspects of your business journey.',
        list: [
            'Browse available advisors by specialization',
            'Request meetings with specific advisors',
            'Track meeting request status',
            'Receive notifications when advisors respond',
            'View meeting history and scheduled appointments',
        ],
    },
    {
        title: '5. Business Plan Builder',
        description:
            'Collect and organize all your business information in one place. Build comprehensive business plans with structured data collection and storage.',
        list: [
            'Step-by-step business plan data collection',
            'Industry-specific fields and templates',
            'Business idea refinement tools',
            'SWOT analysis framework',
            'Product and service description templates',
            'Market analysis and competitor research sections',
        ],
    },
    {
        title: '6. Resources Hub',
        description:
            'Access curated resources, guides, and links relevant to starting a business in Finland. Find official forms, documentation, and helpful articles organized by category.',
        list: [
            'Categorized resource library',
            'Official forms and documentation links',
            'Guides for different business stages',
            'Language-specific resources',
            'Searchable resource database',
        ],
    },
];

const advisorFeatures = [
    {
        title: '1. Comprehensive Dashboard',
        description:
            'Get a complete overview of your advisory practice at a glance. See pending requests, upcoming meetings, reminders, and key metrics all in one place.',
        list: [
            'Real-time overview of meeting requests',
            'Upcoming meetings calendar widget',
            'Reminder notifications and alerts',
            'Quick access to most urgent tasks',
            'Personalized welcome interface',
        ],
    },
    {
        title: '2. Meeting Request Management',
        description:
            'Efficiently manage incoming meeting requests from entrepreneurs. Review business information, accept or reject requests, and organize your advisory schedule.',
        list: [
            'View detailed business profiles and meeting prep materials',
            'Accept or reject meeting requests with one click',
            'Generate PDF briefs of entrepreneur information',
            'Access meeting preparation documents',
            'Track meeting request history',
        ],
    },
    {
        title: '3. Calendar & Scheduling',
        description:
            'Manage your advisory schedule with an integrated calendar system. View upcoming meetings, schedule availability, and stay organized.',
        list: [
            'Visual calendar with all scheduled meetings',
            'Upcoming meetings display',
            'Meeting details and entrepreneur information',
            'Quick access to meeting preparation materials',
            'Schedule management tools',
        ],
    },
    {
        title: '4. Network Visualization',
        description:
            'Explore your network of entrepreneurs and businesses through an interactive network graph. Understand connections and discover patterns.',
        list: [
            'Interactive network graph visualization',
            'View all businesses you are connected with',
            'Discover business similarities and patterns',
            'Search and filter businesses by various criteria',
            'Visual representation of your advisory network',
        ],
    },
    {
        title: '5. Reminder System',
        description:
            'Never miss important deadlines or follow-ups. Get organized reminders for meetings, follow-ups, and administrative tasks.',
        list: [
            'Customizable reminder notifications',
            'Follow-up task tracking',
            'Important deadline alerts',
            'Reminder acknowledgment system',
            'Priority-based reminder organization',
        ],
    },
    {
        title: '6. Settings & Preferences',
        description:
            'Customize your platform experience with comprehensive settings. Control notifications, preferences, and account settings.',
        list: [
            'Notification preferences and pause controls',
            'Dark mode toggle',
            'Account management options',
            'Profile and preference settings',
            'Privacy and security controls',
        ],
    },
];
</script>

