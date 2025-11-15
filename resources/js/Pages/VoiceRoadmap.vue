<template>
    <Head title="Voice Roadmap Builder" />
    <div class="min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50 py-6 px-4 pb-24">
        <div class="max-w-6xl mx-auto">

                    <!-- Cute Header - Eppu the Bear -->
                    <div class="mb-6 bg-gradient-to-r from-purple-400 via-pink-400 to-blue-400 rounded-3xl p-6 text-center shadow-xl">
                        <div class="text-6xl mb-3">🐻</div>
                        <h1 class="text-3xl font-bold text-white mb-2">
                            Eppu the Bear
                        </h1>
                        <p class="text-lg font-medium text-white/90">
                            Your AI Startup Coach 🚀
                        </p>
                    </div>

            <!-- Cute Error Display -->
            <div v-if="error" class="mb-4 bg-gradient-to-r from-red-300 to-pink-300 rounded-2xl p-4 shadow-lg border-2 border-red-400">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span class="text-red-500 text-xl">⚠️</span>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm text-red-700">{{ error }}</p>
                    </div>
                    <div class="ml-auto">
                        <button
                            @click="error = null"
                            class="text-red-500 hover:text-red-700"
                        >
                            ✕
                        </button>
                    </div>
                </div>
            </div>

            <!-- Cute Voice Controls -->
            <div class="bg-white rounded-2xl shadow-lg border-2 border-blue-200 p-5 mb-6">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-4">
                        <div :class="[
                            'w-3 h-3 rounded-full',
                            connectionStatus === 'connected' ? 'bg-green-500' : '',
                            connectionStatus === 'connecting' ? 'bg-yellow-500 animate-pulse' : '',
                            connectionStatus === 'disconnected' ? 'bg-gray-400' : ''
                        ]"></div>
                        <span class="text-gray-700 font-medium">
                            {{ connectionStatus === 'connected' ? 'Connected' : '' }}
                            {{ connectionStatus === 'connecting' ? 'Connecting...' : '' }}
                            {{ connectionStatus === 'disconnected' ? 'Disconnected' : '' }}
                        </span>
                        <span v-if="isListening" 
                              class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium animate-pulse">
                            🎤 Listening...
                        </span>
                        <span v-if="isSpeaking" 
                              class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm font-medium">
                            🔊 Speaking...
                        </span>
                    </div>

                    <div class="flex gap-3">
                        <button
                            v-if="!isConnected"
                            @click="handleConnect"
                            class="px-6 py-3 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all font-bold shadow-lg hover:shadow-xl transform hover:scale-105"
                        >
                            ✨ Connect
                        </button>
                        <template v-else>
                            <button
                                v-if="!isSessionActive"
                                @click="handleStartSession"
                                class="px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white rounded-xl hover:from-green-600 hover:to-emerald-700 transition-all font-bold shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2"
                            >
                                <span class="text-xl">🎤</span>
                                Start Voice Session
                            </button>
                            <button
                                v-else
                                @click="handleStopSession"
                                class="px-6 py-3 bg-gradient-to-r from-red-500 to-pink-600 text-white rounded-xl hover:from-red-600 hover:to-pink-700 transition-all font-bold shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center gap-2"
                            >
                                <span class="text-xl">⏹️</span>
                                Stop Session
                            </button>
                            <button
                                @click="handleDisconnect"
                                class="px-6 py-3 bg-gradient-to-r from-gray-500 to-gray-600 text-white rounded-xl hover:from-gray-600 hover:to-gray-700 transition-all font-bold shadow-lg hover:shadow-xl transform hover:scale-105"
                            >
                                Disconnect
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Transcript Display -->
                <div v-if="transcripts.length > 0" class="mt-6 pt-6 border-t border-gray-200">
                    <h3 class="text-sm font-semibold text-gray-700 mb-3">Conversation</h3>
                    <div class="max-h-48 overflow-y-auto space-y-2">
                        <div
                            v-for="(transcript, index) in transcripts.slice(-5)"
                            :key="index"
                            :class="[
                                'p-3 rounded-lg text-sm',
                                transcript.type === 'user' 
                                    ? 'bg-blue-50 text-blue-900 ml-8' 
                                    : 'bg-gray-50 text-gray-900 mr-8'
                            ]"
                        >
                            <span class="font-medium">
                                {{ transcript.type === 'user' ? 'You: ' : 'AI Coach: ' }}
                            </span>
                            {{ transcript.text }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Area - Home page (Eppu the Bear) -->
            <div v-if="activeTab === 'roadmap'" class="mb-6">
                <!-- Home page shows only voice agent interface -->
            </div>

            <!-- Cute Help Text -->
            <div v-if="!isConnected" class="mt-4 bg-gradient-to-r from-blue-100 to-purple-100 rounded-2xl p-4 border-2 border-blue-200 shadow-md">
                <div class="flex items-center gap-3">
                    <div class="text-2xl">💡</div>
                    <p class="text-sm font-bold text-gray-700">
                        <span class="text-blue-600">Getting Started:</span> Click "Connect" to start building your roadmap!
                    </p>
                </div>
            </div>

            <!-- Document Requests Section (shown in business tab) -->
            <div v-if="pendingDocuments.length > 0 && activeTab === 'business'" class="mt-4 bg-white rounded-2xl shadow-lg border-2 border-yellow-200 p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="text-2xl">📄</div>
                    <h3 class="text-lg font-bold text-gray-800">Document Requests</h3>
                </div>
                <div class="space-y-3">
                    <DocumentRequestCard
                        v-for="(doc, index) in pendingDocuments"
                        :key="doc.id || index"
                        :request="doc"
                        :index="index"
                        @dismiss="handleDocumentDismiss"
                        @provided="handleDocumentProvided"
                    />
                </div>
            </div>
        </div>

        <!-- Modals -->
        <MeetingPrepModal
            :show="showMeetingPrepModal"
            :business-plan="businessPlanData"
            :prep-data="meetingPrepData"
            @close="showMeetingPrepModal = false"
        />

        <ProgressSummaryModal
            :show="showProgressSummaryModal"
            :business-plan="businessPlanData"
            :roadmap="roadmap"
            :summary-data="progressSummaryData"
            @close="showProgressSummaryModal = false"
        />

        <!-- Reward Animation -->
        <RewardAnimation
            :show="showReward"
            :reward="activeReward"
            @close="showReward = false"
        />

        <!-- Resource Cards (Floating) -->
        <ResourceCard
            v-for="(resource, index) in suggestedResources"
            :key="resource.id || index"
            :resource="resource"
            :index="index"
            @dismiss="handleResourceDismiss"
        />

        <!-- Bottom Navigation -->
        <BottomNavigation 
            :active-tab="activeTab"
            @navigate="handleNavigation"
        />

        <!-- Bottom Drawer Modals for Navigation -->
        <!-- Business Info Drawer -->
        <div v-if="activeTab === 'business'" 
             class="fixed inset-0 z-40 flex items-end drawer-overlay"
             @click.self="activeTab = 'roadmap'">
            <div class="bg-white rounded-t-3xl shadow-2xl w-full max-h-[85vh] overflow-y-auto drawer-content">
                <div class="sticky top-0 bg-gradient-to-r from-purple-400 to-pink-400 p-5 flex items-center justify-between rounded-t-3xl shadow-md z-10">
                    <h2 class="text-xl font-bold text-white">💼 Business Information</h2>
                    <button @click="activeTab = 'roadmap'" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Contextual Information Section -->
                    <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-2xl shadow-lg border-2 border-indigo-200 p-5">
                        <h3 class="text-lg font-bold text-indigo-800 mb-4 flex items-center gap-2">
                            <span class="text-2xl">🌍</span>
                            Your Background Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div 
                                :ref="el => setContextualFieldRef('country_of_origin', el)"
                                :id="'contextual-field-country_of_origin'"
                                :class="[
                                    'bg-white rounded-xl p-3 border border-indigo-100 transition-all',
                                    fieldToHighlight === 'country_of_origin' ? 'field-highlight' : ''
                                ]"
                            >
                                <p class="text-xs font-semibold text-gray-500 mb-1">Country of Origin</p>
                                <p class="text-sm font-bold text-gray-800">{{ businessPlanData?.country_of_origin || 'Not provided' }}</p>
                            </div>
                            <div 
                                :ref="el => setContextualFieldRef('is_eu_resident', el)"
                                :id="'contextual-field-is_eu_resident'"
                                :class="[
                                    'bg-white rounded-xl p-3 border border-indigo-100 transition-all',
                                    fieldToHighlight === 'is_eu_resident' ? 'field-highlight' : ''
                                ]"
                            >
                                <p class="text-xs font-semibold text-gray-500 mb-1">EU Resident</p>
                                <p class="text-sm font-bold text-gray-800">
                                    <span v-if="businessPlanData?.is_eu_resident === true" class="text-green-600">✅ Yes</span>
                                    <span v-else-if="businessPlanData?.is_eu_resident === false" class="text-orange-600">❌ No</span>
                                    <span v-else class="text-gray-400">Not provided</span>
                                </p>
                            </div>
                            <div 
                                :ref="el => setContextualFieldRef('is_newcomer_to_finland', el)"
                                :id="'contextual-field-is_newcomer_to_finland'"
                                :class="[
                                    'bg-white rounded-xl p-3 border border-indigo-100 transition-all',
                                    fieldToHighlight === 'is_newcomer_to_finland' ? 'field-highlight' : ''
                                ]"
                            >
                                <p class="text-xs font-semibold text-gray-500 mb-1">Newcomer to Finland</p>
                                <p class="text-sm font-bold text-gray-800">
                                    <span v-if="businessPlanData?.is_newcomer_to_finland === true" class="text-blue-600">✅ Yes</span>
                                    <span v-else-if="businessPlanData?.is_newcomer_to_finland === false" class="text-gray-600">❌ No</span>
                                    <span v-else class="text-gray-400">Not provided</span>
                                </p>
                            </div>
                            <div 
                                :ref="el => setContextualFieldRef('has_residence_permit', el)"
                                :id="'contextual-field-has_residence_permit'"
                                :class="[
                                    'bg-white rounded-xl p-3 border border-indigo-100 transition-all',
                                    fieldToHighlight === 'has_residence_permit' ? 'field-highlight' : ''
                                ]"
                            >
                                <p class="text-xs font-semibold text-gray-500 mb-1">Residence Permit</p>
                                <p class="text-sm font-bold text-gray-800">
                                    <span v-if="businessPlanData?.has_residence_permit === true" class="text-green-600">✅ Yes</span>
                                    <span v-else-if="businessPlanData?.has_residence_permit === false" class="text-orange-600">❌ No</span>
                                    <span v-else class="text-gray-400">Not provided</span>
                                </p>
                            </div>
                            <div 
                                :ref="el => setContextualFieldRef('residence_permit_type', el)"
                                :id="'contextual-field-residence_permit_type'"
                                :class="[
                                    'bg-white rounded-xl p-3 border border-indigo-100 transition-all',
                                    fieldToHighlight === 'residence_permit_type' ? 'field-highlight' : ''
                                ]"
                            >
                                <p class="text-xs font-semibold text-gray-500 mb-1">Residence Permit Type</p>
                                <p class="text-sm font-bold text-gray-800">{{ businessPlanData?.residence_permit_type || 'Not provided' }}</p>
                            </div>
                            <div 
                                :ref="el => setContextualFieldRef('years_in_finland', el)"
                                :id="'contextual-field-years_in_finland'"
                                :class="[
                                    'bg-white rounded-xl p-3 border border-indigo-100 transition-all',
                                    fieldToHighlight === 'years_in_finland' ? 'field-highlight' : ''
                                ]"
                            >
                                <p class="text-xs font-semibold text-gray-500 mb-1">Years in Finland</p>
                                <p class="text-sm font-bold text-gray-800">{{ businessPlanData?.years_in_finland !== null && businessPlanData?.years_in_finland !== undefined ? businessPlanData.years_in_finland + ' years' : 'Not provided' }}</p>
                            </div>
                            <div 
                                :ref="el => setContextualFieldRef('has_business_experience', el)"
                                :id="'contextual-field-has_business_experience'"
                                :class="[
                                    'bg-white rounded-xl p-3 border border-indigo-100 transition-all',
                                    fieldToHighlight === 'has_business_experience' ? 'field-highlight' : ''
                                ]"
                            >
                                <p class="text-xs font-semibold text-gray-500 mb-1">Business Experience</p>
                                <p class="text-sm font-bold text-gray-800">
                                    <span v-if="businessPlanData?.has_business_experience === true" class="text-green-600">✅ Yes</span>
                                    <span v-else-if="businessPlanData?.has_business_experience === false" class="text-gray-600">❌ No</span>
                                    <span v-else class="text-gray-400">Not provided</span>
                                </p>
                            </div>
                            <div 
                                :ref="el => setContextualFieldRef('language', el)"
                                :id="'contextual-field-language'"
                                :class="[
                                    'bg-white rounded-xl p-3 border border-indigo-100 transition-all',
                                    fieldToHighlight === 'language' ? 'field-highlight' : ''
                                ]"
                            >
                                <p class="text-xs font-semibold text-gray-500 mb-1">Preferred Language</p>
                                <p class="text-sm font-bold text-gray-800">{{ businessPlanData?.language || 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Business Plan Progress -->
                    <BusinessPlanProgress 
                        :business-plan="businessPlanData"
                        :recently-answered-fields="recentlyAnsweredFields"
                        :field-to-highlight="fieldToHighlight"
                    />
                    
                    <!-- Document Requests Section -->
                    <div v-if="pendingDocuments.length > 0" class="bg-white rounded-2xl shadow-lg border-2 border-yellow-200 p-5">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="text-2xl">📄</div>
                            <h3 class="text-lg font-bold text-gray-800">Document Requests</h3>
                        </div>
                        <div class="space-y-3">
                            <DocumentRequestCard
                                v-for="(doc, index) in pendingDocuments"
                                :key="doc.id || index"
                                :request="doc"
                                :index="index"
                                @dismiss="handleDocumentDismiss"
                                @provided="handleDocumentProvided"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Manual Add Drawer -->
        <div v-if="activeTab === 'add'" 
             class="fixed inset-0 z-40 flex items-end drawer-overlay"
             @click.self="activeTab = 'roadmap'">
            <div class="bg-white rounded-t-3xl shadow-2xl w-full max-h-[85vh] overflow-y-auto drawer-content">
                <div class="sticky top-0 bg-gradient-to-r from-green-400 to-emerald-400 p-5 flex items-center justify-between rounded-t-3xl shadow-md z-10">
                    <h2 class="text-xl font-bold text-white">✍️ Manual Entry</h2>
                    <button @click="activeTab = 'roadmap'" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <!-- Manual Add content embedded directly -->
                    <div class="space-y-6">
                        <p class="text-gray-600 mb-6">Here you can manually add or update business plan fields or roadmap steps.</p>
                        <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-2xl p-4 border-2 border-blue-200">
                            <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                                <span class="text-xl">📝</span>
                                Add Business Plan Field
                            </h3>
                            <p class="text-sm text-gray-600 mb-4">Coming soon! You'll be able to manually add business plan information here.</p>
                        </div>
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-4 border-2 border-green-200">
                            <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                                <span class="text-xl">🗺️</span>
                                Add Roadmap Step
                            </h3>
                            <p class="text-sm text-gray-600 mb-4">Coming soon! You'll be able to manually add roadmap steps here.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advisors Drawer -->
        <div v-if="activeTab === 'advisors'" 
             class="fixed inset-0 z-40 flex items-end drawer-overlay"
             @click.self="activeTab = 'roadmap'">
            <div class="bg-white rounded-t-3xl shadow-2xl w-full max-h-[85vh] overflow-y-auto drawer-content">
                <div class="sticky top-0 bg-gradient-to-r from-yellow-400 to-orange-400 p-5 flex items-center justify-between rounded-t-3xl shadow-md z-10">
                    <h2 class="text-xl font-bold text-white">🤝 Your Advisors</h2>
                    <button @click="activeTab = 'roadmap'" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <!-- Advisors content embedded directly -->
                    <p class="text-gray-600 mb-6">Connect with experienced advisors who can help guide your startup journey.</p>
                    <div class="space-y-4">
                        <div class="bg-purple-50 rounded-lg p-4 border border-purple-200 flex items-center gap-4">
                            <div class="flex-shrink-0 w-12 h-12 bg-purple-300 rounded-full flex items-center justify-center text-white text-xl font-bold">A</div>
                            <div>
                                <p class="font-bold text-purple-800">Advisor Name 1</p>
                                <p class="text-sm text-gray-600">Specialization: Funding, Business Strategy</p>
                                <p class="text-xs text-gray-500">Next meeting: Oct 26, 2024</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Roadmap Drawer -->
        <div v-if="activeTab === 'roadmap-tab'" 
             class="fixed inset-0 z-40 flex items-end drawer-overlay"
             @click.self="activeTab = 'roadmap'">
            <div class="bg-white rounded-t-3xl shadow-2xl w-full max-h-[85vh] overflow-y-auto drawer-content">
                <div class="sticky top-0 bg-gradient-to-r from-blue-400 to-purple-400 p-5 flex items-center justify-between rounded-t-3xl shadow-md z-10">
                    <h2 class="text-xl font-bold text-white">🗺️ Your Roadmap</h2>
                    <button @click="activeTab = 'roadmap'" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <!-- Roadmap Visualization -->
                    <div v-if="roadmap && roadmap.steps && roadmap.steps.filter(s => !s.isQuestion).length > 0" 
                         class="bg-white rounded-2xl shadow-lg border-2 border-blue-200 p-5">
                        <RoadmapVisualizer 
                            :roadmap="roadmap"
                            @step-update="handleStepUpdate"
                        />
                    </div>
                    <div v-else class="text-center py-12">
                        <div class="text-6xl mb-4">🗺️</div>
                        <h3 class="text-xl font-bold text-gray-800 mb-2">No Roadmap Steps Yet</h3>
                        <p class="text-gray-600">Start a voice session with Eppu the Bear to create your roadmap!</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Drawer -->
        <div v-if="activeTab === 'calendar'" 
             class="fixed inset-0 z-40 flex items-end drawer-overlay"
             @click.self="activeTab = 'roadmap'">
            <div class="bg-white rounded-t-3xl shadow-2xl w-full max-h-[85vh] overflow-y-auto drawer-content">
                <div class="sticky top-0 bg-gradient-to-r from-pink-400 to-red-400 p-5 flex items-center justify-between rounded-t-3xl shadow-md z-10">
                    <h2 class="text-xl font-bold text-white">📅 Your Calendar</h2>
                    <button @click="activeTab = 'roadmap'" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <!-- Calendar content embedded directly -->
                    <p class="text-gray-600 mb-6">Manage your upcoming meetings and important dates.</p>
                    <div class="space-y-4">
                        <div class="bg-blue-50 rounded-lg p-4 border border-blue-200">
                            <p class="font-bold text-blue-800 mb-1">Meeting with Advisor A</p>
                            <p class="text-sm text-gray-600">Date: October 26, 2024, 10:00 AM</p>
                            <p class="text-xs text-gray-500">Topic: Funding Strategy</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick, computed, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import useVoiceAgent from '../composables/useVoiceAgent';
import RoadmapVisualizer from '../components/RoadmapVisualizer.vue';
import BusinessPlanProgress from '../components/BusinessPlanProgress.vue';
import MeetingPrepModal from '../components/MeetingPrepModal.vue';
import ProgressSummaryModal from '../components/ProgressSummaryModal.vue';
import ResourceCard from '../components/ResourceCard.vue';
import DocumentRequestCard from '../components/DocumentRequestCard.vue';
import RewardAnimation from '../components/RewardAnimation.vue';
import XPLevelHeader from '../components/XPLevelHeader.vue';
import BottomNavigation from '../components/BottomNavigation.vue';
import BusinessInfoModal from '../components/BusinessInfoModal.vue';
import ManualAddModal from '../components/ManualAddModal.vue';
import AdvisorsModal from '../components/AdvisorsModal.vue';
import CalendarModal from '../components/CalendarModal.vue';

const props = defineProps({
    initialRoadmap: {
        type: Object,
        default: null
    },
    initialBusinessPlan: {
        type: Object,
        default: null
    },
    userName: {
        type: String,
        default: null
    }
});

const roadmap = ref(props.initialRoadmap || { steps: [] });
const loading = ref(false);
const error = ref(null);
const transcripts = ref([]);
const isSessionActive = ref(false);
const businessPlanData = ref(props.initialBusinessPlan);
const recentlyAnsweredFields = ref(new Set()); // Track fields that were just answered

// Modal states
const showMeetingPrepModal = ref(false);
const showProgressSummaryModal = ref(false);
const meetingPrepData = ref({});
const progressSummaryData = ref({});

// Resource cards
const suggestedResources = ref([]);
let resourceIdCounter = 0;

// Document requests
const pendingDocuments = ref([]);
let documentIdCounter = 0;

// Reward animation
const showReward = ref(false);
const activeReward = ref({});

// User progress (XP, Level, Streak)
const userProgress = ref({
    level: 1,
    xp: 0,
    streak: 0,
    dailyXP: 0,
    dailyGoal: 50,
    hearts: 5
});

// Navigation state
const activeTab = ref('roadmap');
const fieldToHighlight = ref(null);
const contextualFieldRefs = ref({});
const showBusinessInfo = ref(false);

// Set refs for contextual fields
const setContextualFieldRef = (fieldKey, el) => {
    if (el) {
        contextualFieldRefs.value[fieldKey] = el;
    }
};

// Watch for field to highlight and scroll to contextual fields
watch(() => fieldToHighlight.value, async (newField) => {
    if (newField) {
        await nextTick();
        // Small delay to ensure drawer is fully rendered
        setTimeout(async () => {
            await nextTick();
            // Check if it's a contextual field
            const contextualField = contextualFieldRefs.value[newField];
            if (contextualField) {
                contextualField.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center',
                    inline: 'nearest'
                });
            }
        }, 300);
    }
}, { immediate: true });
const showManualAdd = ref(false);
const showAdvisors = ref(false);
const showCalendar = ref(false);

// Map business plan fields to questions
const businessPlanQuestions = {
    business_name: "What is the name of your business?",
    company_contact_info: "What is the basic contact and information of your company?",
    industry: "What industry does your business operate in?",
    company_planned_name: "What is the planned name of your company?",
    company_type: "What type of company are you planning (e.g., Sole Entrepreneur, LLC)?",
    address: "What is your business address?",
    zip_code: "What is your ZIP code?",
    postal_district: "What is your postal district?",
    year_of_establishment: "What year was (or will be) your company established?",
    number_of_employees: "How many employees does your company have (or plan to have)?",
    internet_address: "What is your company website URL?",
    business_id: "What is your business ID or registration number?",
    company_owners_holdings: "Who are the company owners and what are their holdings as percentage?",
    business_idea: "Briefly describe your business idea. What does the company sell, who are your customers, and how does the sale take place?",
    competence_skills: "What kind of training and work background do you have that will help you become an entrepreneur? Do you have previous entrepreneurial experience?",
    swot_analysis: "Can you provide a SWOT analysis (strengths, weaknesses, opportunities, threats) for your business?",
    products_services_general: "Describe in general terms what products or services you offer.",
    products_services_detailed: "Name your main products and/or services, their prices, benefits for customers, and competitive advantages.",
    sales_marketing: "How are sales made in practice? How do you reach your customer/target group? What marketing channels/tools do you plan to use?",
    production_logistics: "If you are selling goods, where do they come from and how is logistics handled?",
    distribution_network: "What is your market entry and distribution network strategy?",
    target_market_groups: "Who is your customer? Is your product/service B2C or B2B? What would an ideal customer be like?",
    competitors: "Who are your competitors?",
    competitive_situation: "How do you stand out from other players in the same industry?",
    third_parties_partners: "What other third parties and partners are important to your company (e.g., subcontractors)?",
    operating_environment_risks: "What are potential risks in the operating environment? Are there changes or megatrends that could change buying behaviour?",
    vision_long_term: "What is your long-term vision? How do you see your company in 3-5 years?",
    industry_future_prospects: "Describe the future prospects of your industry. Do you have plans for internationalization?",
    permits_notices: "Do you need a permit or advance notice to start your business?",
    insurance_contracts: "What insurance and contracts do you need or have?",
    intellectual_property_rights: "What intellectual property rights are relevant to your business?",
    support_network: "Do you have mentors, business acquaintances, or a support network who can help you?",
    my_business_comprehensive: "Provide a comprehensive description of your business: What kind of business are you planning? What is the name and where does it come from? What form of business? What domain are you planning? Do you need business premises or employees?"
};

// Create question steps from empty business plan fields
const createQuestionSteps = (businessPlan) => {
    if (!businessPlan) return [];
    
    const questionSteps = [];
    let questionOrder = 1000; // Start from high number to place after regular roadmap steps
    
    Object.keys(businessPlanQuestions).forEach(fieldKey => {
        const value = businessPlan[fieldKey];
        
        // More robust empty check
        let isEmpty = false;
        if (value === null || value === undefined) {
            isEmpty = true;
        } else if (typeof value === 'string' && value.trim() === '') {
            isEmpty = true;
        } else if (Array.isArray(value) && value.length === 0) {
            isEmpty = true;
        } else if (typeof value === 'object' && Object.keys(value).length === 0) {
            isEmpty = true;
        }
        
        if (isEmpty) {
            questionSteps.push({
                id: `question_${fieldKey}`,
                title: businessPlanQuestions[fieldKey],
                description: `This information is needed to complete your business plan. Hover to see the question.`,
                question: businessPlanQuestions[fieldKey],
                fieldKey: fieldKey,
                order: questionOrder++,
                status: 'pending',
                isQuestion: true // Flag to identify question steps
            });
            console.log(`✓ Creating question step for empty field: ${fieldKey}`);
        } else {
            console.log(`✗ Field ${fieldKey} has value:`, JSON.stringify(value), '- NO question step created');
        }
    });
    
    console.log('Created question steps:', questionSteps.map(s => s.fieldKey));
    return questionSteps;
};

// Merge roadmap steps with question steps
const mergeRoadmapWithQuestions = (roadmapData, businessPlan) => {
    if (!roadmapData) {
        roadmapData = { steps: [] };
    }
    if (!roadmapData.steps) {
        roadmapData.steps = [];
    }
    
    // Filter out ALL old question steps (we'll recreate them from current business plan state)
    const regularSteps = roadmapData.steps.filter(step => !step.isQuestion);
    
    console.log('Merging roadmap with questions:', {
        totalStepsBefore: roadmapData.steps.length,
        regularStepsCount: regularSteps.length,
        questionStepsBefore: roadmapData.steps.filter(s => s.isQuestion).length,
        businessPlanFields: Object.keys(businessPlan || {})
    });
    
    // Create new question steps from current business plan (only for empty fields)
    const questionSteps = createQuestionSteps(businessPlan);
    
    // Combine regular steps with question steps
    const allSteps = [...regularSteps, ...questionSteps];
    
    console.log('After merge:', {
        totalSteps: allSteps.length,
        regularSteps: regularSteps.length,
        questionSteps: questionSteps.length,
        questionStepFields: questionSteps.map(s => s.fieldKey)
    });
    
    // Create a completely new object to ensure Vue reactivity
    return {
        ...roadmapData,
        steps: [...allSteps], // Create new array reference
        _updated: Date.now() // Add timestamp to force reactivity
    };
};

const handleRoadmapUpdate = async (roadmapData) => {
    try {
        if (!roadmapData || typeof roadmapData !== 'object') {
            console.warn('Invalid roadmap data received:', roadmapData);
            return;
        }

        // Switch to roadmap tab to show the update
        activeTab.value = 'roadmap-tab';

        // Merge with existing roadmap instead of replacing
        if (roadmap.value && roadmap.value.steps && Array.isArray(roadmap.value.steps)) {
            const existingSteps = roadmap.value.steps.filter(step => !step.isQuestion); // Exclude question steps
            const newSteps = roadmapData.steps || [];
            
            // Merge new steps with existing ones
            const mergedSteps = [...existingSteps];
            newSteps.forEach(newStep => {
                const key = newStep.id !== undefined ? newStep.id : newStep.order;
                const existingIndex = mergedSteps.findIndex(s => 
                    (s.id !== undefined && s.id === newStep.id) || 
                    (s.order === newStep.order && newStep.id === undefined)
                );
                
                if (existingIndex >= 0) {
                    // Update existing step
                    mergedSteps[existingIndex] = {
                        ...mergedSteps[existingIndex],
                        ...newStep,
                        // Preserve original id if new step doesn't have one
                        id: newStep.id !== undefined ? newStep.id : mergedSteps[existingIndex].id
                    };
                } else {
                    // Add new step
                    mergedSteps.push(newStep);
                }
            });
            
            // Sort by order
            mergedSteps.sort((a, b) => (a.order || 0) - (b.order || 0));
            
            // Update roadmap with merged data
            roadmap.value = {
                ...roadmap.value,
                title: roadmapData.title || roadmap.value.title,
                steps: mergedSteps
            };
        } else {
            // No existing roadmap, use new data as-is
            roadmap.value = roadmapData;
        }
        
        // Don't merge question steps - roadmap only shows action steps
        // Question steps are handled separately in BusinessPlanProgress component

        // Save to backend (only roadmap action steps, no question steps)
        const roadmapToSave = {
            ...roadmap.value,
            steps: roadmap.value.steps.filter(step => !step.isQuestion)
        };
        
        try {
            await axios.post('/api/roadmap/update', {
                roadmap_json: roadmapToSave
            });
            console.log('Roadmap saved to backend successfully');
        } catch (err) {
            console.error('Failed to update roadmap in backend:', err);
            if (err.response?.status === 401) {
                error.value = 'Authentication required. Please log in to save your roadmap.';
            } else if (err.response?.status >= 500) {
                error.value = 'Server error. Your roadmap changes are saved locally but not yet synced.';
            }
        }
    } catch (err) {
        console.error('Failed to process roadmap update:', err);
        error.value = 'Failed to process roadmap update. Please try again.';
    }
};

const handleBusinessPlanUpdate = async (businessPlanUpdateData) => {
    try {
        console.log('handleBusinessPlanUpdate called with:', JSON.stringify(businessPlanUpdateData, null, 2));
        
        if (!businessPlanUpdateData || typeof businessPlanUpdateData !== 'object') {
            console.warn('Invalid business plan data received:', businessPlanUpdateData);
            return;
        }

        // If data is nested in business_plan, unwrap it
        const flatData = businessPlanUpdateData.business_plan || businessPlanUpdateData;
        console.log('Flat business plan data:', JSON.stringify(flatData, null, 2));
        console.log('Flat data keys:', Object.keys(flatData));
        console.log('Flat data values:', Object.values(flatData));

        // Track which fields were updated to highlight them
        const updatedFieldKeys = Object.keys(flatData);
        console.log('Updated field keys:', updatedFieldKeys);
        
        // Ensure we have data to send
        if (updatedFieldKeys.length === 0) {
            console.error('No fields to update! flatData is empty:', flatData);
            return;
        }
        if (updatedFieldKeys.length > 0) {
            // Highlight the first updated field
            fieldToHighlight.value = updatedFieldKeys[0];
            // Clear highlight after 5 seconds
            setTimeout(() => {
                fieldToHighlight.value = null;
            }, 5000);
        }
        
        // Switch to business tab to show the update
        activeTab.value = 'business';
        showBusinessInfo.value = false; // Close modal if open, show inline instead

        // Merge with existing business plan data - create new object reference for reactivity
        if (businessPlanData.value) {
            businessPlanData.value = {
                ...businessPlanData.value,
                ...flatData,
                _updated: Date.now() // Force reactivity
            };
        } else {
            businessPlanData.value = {
                ...flatData,
                _updated: Date.now()
            };
        }
        
        console.log('Updated businessPlanData.value:', JSON.stringify(businessPlanData.value, null, 2));

        console.log('Business plan data after merge:', {
            business_name: businessPlanData.value.business_name,
            allFields: Object.keys(businessPlanData.value),
            updateData: businessPlanUpdateData
        });

        // Track which fields were just answered (for reward animation)
        const previousQuestionIds = new Set(
            roadmap.value.steps?.filter(s => s.isQuestion).map(s => s.fieldKey) || []
        );
        
        console.log('Previous question IDs before merge:', Array.from(previousQuestionIds));
        
        // Immediately update roadmap to reflect new question steps (removes filled questions, adds new ones)
        // Force reactivity by creating a new object reference
        const updatedRoadmap = mergeRoadmapWithQuestions(roadmap.value, businessPlanData.value);
        roadmap.value = updatedRoadmap;
        
        // Find which questions were just answered
        const currentQuestionIds = new Set(
            roadmap.value.steps?.filter(s => s.isQuestion).map(s => s.fieldKey) || []
        );
        const newlyAnsweredFields = Array.from(previousQuestionIds).filter(
            id => !currentQuestionIds.has(id)
        );
        
        // Award XP for filling business plan fields (5 XP per field)
        if (newlyAnsweredFields.length > 0) {
            await awardXP(newlyAnsweredFields.length * 5, 'Filled business plan fields');
        }
        
        // Add to recently answered set and trigger reward animation
        if (newlyAnsweredFields.length > 0) {
            newlyAnsweredFields.forEach(field => recentlyAnsweredFields.value.add(field));
            // Clear after animation
            setTimeout(() => {
                newlyAnsweredFields.forEach(field => recentlyAnsweredFields.value.delete(field));
            }, 3000);
        }
        
        // Wait for Vue to process the update
        await nextTick();
        
        console.log('Business plan updated, question steps refreshed:', {
            filledFields: Object.keys(flatData),
            questionStepsCount: roadmap.value.steps.filter(s => s.isQuestion).length,
            allStepsCount: roadmap.value.steps.length,
            updatedFields: flatData,
            newlyAnsweredFields: newlyAnsweredFields,
            previousQuestionIds: Array.from(previousQuestionIds),
            currentQuestionIds: Array.from(currentQuestionIds),
            businessPlanSnapshot: { ...businessPlanData.value }
        });

        // Send partial update to backend (only fields provided will be updated)
        try {
            // Ensure boolean values are properly formatted
            // Create a new object with all fields from flatData
            // IMPORTANT: Include all fields, even if they are null, false, 0, or empty string
            // The backend validation will handle what's valid
            const dataToSend = {};
            Object.keys(flatData).forEach(key => {
                // Include all fields except undefined (null, false, 0, and empty strings are valid)
                if (flatData[key] !== undefined) {
                    dataToSend[key] = flatData[key];
                }
            });
            
            // If dataToSend is empty but flatData has keys, something went wrong
            if (Object.keys(dataToSend).length === 0 && Object.keys(flatData).length > 0) {
                console.error('ERROR: dataToSend is empty but flatData has keys!', {
                    flatData,
                    flatDataKeys: Object.keys(flatData),
                    flatDataValues: Object.values(flatData).map(v => ({ value: v, type: typeof v }))
                });
            }
            
            console.log('dataToSend before boolean conversion:', JSON.stringify(dataToSend, null, 2));
            console.log('dataToSend keys:', Object.keys(dataToSend));
            // Convert boolean strings to actual booleans if needed
            if (dataToSend.has_residence_permit !== undefined) {
                if (typeof dataToSend.has_residence_permit === 'string') {
                    dataToSend.has_residence_permit = dataToSend.has_residence_permit === 'true' || dataToSend.has_residence_permit === '1';
                }
            }
            if (dataToSend.is_eu_resident !== undefined) {
                if (typeof dataToSend.is_eu_resident === 'string') {
                    dataToSend.is_eu_resident = dataToSend.is_eu_resident === 'true' || dataToSend.is_eu_resident === '1';
                }
            }
            if (dataToSend.is_newcomer_to_finland !== undefined) {
                if (typeof dataToSend.is_newcomer_to_finland === 'string') {
                    dataToSend.is_newcomer_to_finland = dataToSend.is_newcomer_to_finland === 'true' || dataToSend.is_newcomer_to_finland === '1';
                }
            }
            if (dataToSend.has_business_experience !== undefined) {
                if (typeof dataToSend.has_business_experience === 'string') {
                    dataToSend.has_business_experience = dataToSend.has_business_experience === 'true' || dataToSend.has_business_experience === '1';
                }
            }
            
            console.log('Sending to backend:', JSON.stringify(dataToSend, null, 2));
            console.log('Data types:', {
                has_residence_permit: typeof dataToSend.has_residence_permit,
                residence_permit_type: typeof dataToSend.residence_permit_type,
                is_eu_resident: typeof dataToSend.is_eu_resident,
            });
            console.log('Request payload size:', JSON.stringify(dataToSend).length, 'bytes');
            console.log('Request payload keys count:', Object.keys(dataToSend).length);
            
            // Ensure we're sending data
            if (Object.keys(dataToSend).length === 0) {
                console.error('CRITICAL ERROR: Attempting to send empty dataToSend!', {
                    flatData,
                    dataToSend,
                    businessPlanUpdateData
                });
                error.value = 'No data to send. Please try again.';
                return;
            }
            
            const response = await axios.post('/api/business-plan/update', dataToSend, {
                headers: {
                    'Content-Type': 'application/json',
                }
            });
            console.log('Business plan updated successfully in backend:', response.data);
            
            // Update local state with response data to ensure sync
            if (response.data && response.data.business_plan) {
                businessPlanData.value = {
                    ...businessPlanData.value,
                    ...response.data.business_plan
                };
                console.log('Updated businessPlanData from backend response:', JSON.stringify(businessPlanData.value, null, 2));
            }
        } catch (err) {
            console.error('Failed to update business plan in backend:', err);
            console.error('Error response:', err.response?.data);
            if (err.response?.status === 401) {
                error.value = 'Authentication required. Please log in to save your business plan.';
            } else if (err.response?.status >= 500) {
                error.value = 'Server error. Your business plan changes may not be saved.';
            } else if (err.response?.status === 422) {
                console.error('Validation errors:', err.response.data);
                error.value = 'Validation error: ' + JSON.stringify(err.response.data);
            }
        }
    } catch (err) {
        console.error('Failed to process business plan update:', err);
        error.value = 'Failed to process business plan update. Please try again.';
    }
};

const handleTranscript = (transcript) => {
    transcripts.value.push(transcript);
};

const handleError = (errorMessage) => {
    error.value = errorMessage;
    console.error('Voice agent error:', errorMessage);
};

// New tool callbacks
const handleMeetingPrep = (prepData) => {
    console.log('Meeting prep requested:', prepData);
    meetingPrepData.value = prepData;
    showMeetingPrepModal.value = true;
};

// Load user progress from API
const loadUserProgress = async () => {
    try {
        const response = await axios.get('/api/user-progress');
        if (response.data) {
            userProgress.value = {
                level: response.data.level || 1,
                xp: response.data.xp || 0,
                streak: response.data.streak || 0,
                dailyXP: response.data.daily_xp || 0,
                dailyGoal: response.data.daily_goal || 50,
                hearts: response.data.hearts || 5
            };
        }
    } catch (err) {
        console.error('Failed to load user progress:', err);
    }
};

// Award XP for completing actions
const awardXP = async (amount, reason = '') => {
    try {
        const response = await axios.post('/api/user-progress/award-xp', {
            amount,
            reason
        });
        if (response.data) {
            const oldXP = userProgress.value.xp;
            userProgress.value.xp = response.data.xp;
            userProgress.value.level = response.data.level;
            userProgress.value.dailyXP = response.data.daily_xp || userProgress.value.dailyXP;
            userProgress.value.streak = response.data.streak || userProgress.value.streak;
            
            // Show XP gain in reward animation
            if (amount > 0) {
                activeReward.value.xpGained = amount;
            }
        }
    } catch (err) {
        console.error('Failed to award XP:', err);
    }
};

const handleLevelUp = (newLevel) => {
    // Show special level up animation
    activeReward.value = {
        title: 'Level Up! 🎉',
        message: `Congratulations! You've reached Level ${newLevel}!`,
        achievement: 'Level Up Champion!',
        progress: 0,
        isLevelUp: true
    };
    showReward.value = true;
};

const handleChecklistComplete = async (data) => {
    console.log('Checklist item completed:', data);
    
    // Switch to roadmap tab to show the completion
    activeTab.value = 'roadmap-tab';
    
    // Update roadmap step status
    if (roadmap.value && roadmap.value.steps) {
        const stepIndex = roadmap.value.steps.findIndex(s => 
            (s.id && s.id === data.stepId) ||
            (s.order && s.order === data.stepId) ||
            (s.title && s.title === data.stepId)
        );
        
        if (stepIndex >= 0) {
            roadmap.value.steps[stepIndex].status = 'completed';
            
            // Save to backend
            try {
                await axios.post('/api/roadmap/update', {
                    roadmap_json: {
                        ...roadmap.value,
                        steps: roadmap.value.steps.filter(s => !s.isQuestion)
                    }
                });
            } catch (err) {
                console.error('Failed to update roadmap:', err);
            }
        }
    }
    
    // Award XP for completing a step (Duolingo-style: 10-20 XP per step)
    await awardXP(15, 'Completed roadmap step');
    
    // Calculate progress
    const roadmapSteps = roadmap.value?.steps?.filter(s => !s.isQuestion) || [];
    const completedCount = roadmapSteps.filter(s => s.status === 'completed').length;
    const totalCount = roadmapSteps.length;
    const progress = totalCount > 0 ? Math.round((completedCount / totalCount) * 100) : 0;
    
    // Show reward animation
    activeReward.value = {
        title: 'Step Completed!',
        message: `You've completed "${data.step?.title || data.stepId}"`,
        step: data.step,
        progress: progress,
        achievement: completedCount >= 5 ? 'Progress Champion!' : completedCount >= 3 ? 'Roadmap Explorer!' : null
    };
    showReward.value = true;
};

const handleDocumentRequest = (request) => {
    console.log('Document requested:', request);
    // Switch to business tab to show document requests
    activeTab.value = 'business';
    
    const docRequest = {
        id: `doc-${documentIdCounter++}`,
        ...request,
        createdAt: new Date().toISOString()
    };
    pendingDocuments.value.push(docRequest);
};

const handleDocumentDismiss = (request) => {
    const index = pendingDocuments.value.findIndex(d => d.id === request.id);
    if (index >= 0) {
        pendingDocuments.value.splice(index, 1);
    }
};

const handleDocumentProvided = async (request) => {
    console.log('Document provided:', request);
    // Could send to backend to track
    handleDocumentDismiss(request);
};

const handleResourceSuggested = (resource) => {
    console.log('Resource suggested:', resource);
    const resourceCard = {
        id: `resource-${resourceIdCounter++}`,
        ...resource,
        createdAt: new Date().toISOString()
    };
    suggestedResources.value.push(resourceCard);
};

const handleResourceDismiss = (resource) => {
    const index = suggestedResources.value.findIndex(r => r.id === resource.id);
    if (index >= 0) {
        suggestedResources.value.splice(index, 1);
    }
};

const handleProgressSummary = (summaryData) => {
    console.log('Progress summary requested:', summaryData);
    // Progress summary shows as modal, no need to switch tabs
    progressSummaryData.value = summaryData;
    showProgressSummaryModal.value = true;
};

const {
    isConnected,
    isListening,
    isSpeaking,
    connectionStatus,
    connect,
    disconnect,
    startSession,
    stopSession
} = useVoiceAgent({
    onRoadmapUpdate: handleRoadmapUpdate,
    onBusinessPlanUpdate: handleBusinessPlanUpdate,
    userName: props.userName,
    onTranscript: handleTranscript,
    onError: handleError,
    onMeetingPrep: handleMeetingPrep,
    onChecklistComplete: handleChecklistComplete,
    onDocumentRequest: handleDocumentRequest,
    onResourceSuggested: handleResourceSuggested,
    onProgressSummary: handleProgressSummary
});

const handleConnect = async () => {
    try {
        error.value = null;
        await connect();
    } catch (err) {
        console.error('Failed to connect:', err);
        error.value = 'Failed to connect to voice agent';
    }
};

const handleStartSession = async () => {
    try {
        error.value = null;
        await startSession();
        isSessionActive.value = true;
    } catch (err) {
        console.error('Failed to start session:', err);
        error.value = 'Failed to start voice session. Please check your microphone permissions.';
    }
};

const handleStopSession = async () => {
    try {
        await stopSession();
        isSessionActive.value = false;
    } catch (err) {
        console.error('Failed to stop session:', err);
    }
};

const handleDisconnect = () => {
    disconnect();
    isSessionActive.value = false;
};

const handleStepUpdate = (step) => {
    console.log('Step updated:', step);
};

// Navigation handler - toggle drawer behavior
const handleNavigation = (tab) => {
    // If clicking the same tab, close it (toggle behavior)
    if (activeTab.value === tab) {
        activeTab.value = 'roadmap'; // Return to home
    } else {
        activeTab.value = tab; // Open the drawer
    }
};

onMounted(async () => {
    // Load user progress on mount
    await loadUserProgress();
    // Data is already loaded via Inertia props
    // Roadmap only shows action steps, no question steps
    // Question steps are handled separately in BusinessPlanProgress component
    console.log('Roadmap loaded:', roadmap.value);
});
</script>

<style scoped>
.drawer-overlay {
    background-color: rgba(0, 0, 0, 0.4);
    animation: fadeIn 0.2s ease-out;
}

.drawer-content {
    animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { 
        opacity: 0;
        transform: translateY(100%);
    }
    to { 
        opacity: 1;
        transform: translateY(0);
    }
}

/* Field highlight animation */
.field-highlight {
    animation: highlightPulse 5s ease-in-out;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 50%, #fcd34d 100%) !important;
    border: 3px solid #f59e0b !important;
    box-shadow: 0 0 20px rgba(245, 158, 11, 0.5), 0 0 40px rgba(245, 158, 11, 0.3);
    transform: scale(1.05);
    z-index: 10;
    position: relative;
}

@keyframes highlightPulse {
    0% {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 50%, #fcd34d 100%);
        border-color: #f59e0b;
        box-shadow: 0 0 20px rgba(245, 158, 11, 0.5), 0 0 40px rgba(245, 158, 11, 0.3);
        transform: scale(1.05);
    }
    50% {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 50%, #fcd34d 100%);
        border-color: #f59e0b;
        box-shadow: 0 0 15px rgba(245, 158, 11, 0.4), 0 0 30px rgba(245, 158, 11, 0.2);
        transform: scale(1.03);
    }
    100% {
        background: var(--original-bg, transparent);
        border-color: var(--original-border, transparent);
        box-shadow: none;
        transform: scale(1);
    }
}
</style>

