<template>
    <Head title="Voice Roadmap Builder" />
    <div class="min-h-screen bg-[#011135] py-6 px-4 pb-24">
        <div class="max-w-6xl mx-auto">

                    <!-- Professional Header -->
                    <div class="mb-6 bg-[#012169] rounded-lg p-6 text-center shadow-lg">
                        <h1 class="text-2xl font-bold text-white mb-2">
                            Voice Roadmap Builder
                        </h1>
                        <p class="text-sm font-medium text-white/80">
                            Your AI Startup Coach
                        </p>
                    </div>

            <!-- Error Display -->
            <div v-if="error" class="mb-4 bg-red-900/80 border border-red-600 rounded-lg p-4 shadow-lg">
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

            <!-- Voice Controls -->
            <div class="bg-[#012169] rounded-lg shadow-lg border border-[#011135] p-5 mb-6">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div class="flex items-center gap-4">
                        <div :class="[
                            'w-3 h-3 rounded-full',
                            connectionStatus === 'connected' ? 'bg-green-500' : '',
                            connectionStatus === 'connecting' ? 'bg-yellow-500 animate-pulse' : '',
                            connectionStatus === 'disconnected' ? 'bg-gray-400' : ''
                        ]"></div>
                        <span class="text-white font-medium">
                            {{ connectionStatus === 'connected' ? 'Connected' : '' }}
                            {{ connectionStatus === 'connecting' ? 'Connecting...' : '' }}
                            {{ connectionStatus === 'disconnected' ? 'Disconnected' : '' }}
                        </span>
                        <span v-if="isListening" 
                              class="px-3 py-1 bg-white/20 text-white rounded-full text-sm font-medium animate-pulse">
                            🎤 Listening...
                        </span>
                        <span v-if="isSpeaking" 
                              class="px-3 py-1 bg-white/20 text-white rounded-full text-sm font-medium">
                            🔊 Speaking...
                        </span>
                    </div>

                    <div class="flex gap-3">
                        <button
                            v-if="!isConnected"
                            @click="handleConnect"
                            class="px-6 py-3 bg-[#011135] text-white rounded-lg hover:bg-[#012169] transition-all font-semibold shadow-lg border border-white/20"
                        >
                            ✨ Connect
                        </button>
                        <template v-else>
                            <button
                                v-if="!isSessionActive"
                                @click="handleStartSession"
                                class="px-6 py-3 bg-[#011135] text-white rounded-lg hover:bg-[#012169] transition-all font-semibold shadow-lg border border-white/20 flex items-center gap-2"
                            >
                                <span class="text-xl">🎤</span>
                                Start Voice Session
                            </button>
                            <button
                                v-else
                                @click="handleStopSession"
                                class="px-6 py-3 bg-red-700 text-white rounded-lg hover:bg-red-800 transition-all font-semibold shadow-lg border border-red-600 flex items-center gap-2"
                            >
                                <span class="text-xl">⏹️</span>
                                Stop Session
                            </button>
                            <button
                                @click="handleDisconnect"
                                class="px-6 py-3 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition-all font-semibold shadow-lg border border-gray-600"
                            >
                                Disconnect
                            </button>
                        </template>
                    </div>
                </div>

                <!-- Transcript Display -->
                <div v-if="transcripts.length > 0" class="mt-6 pt-6 border-t border-white/20">
                    <h3 class="text-sm font-semibold text-white mb-3">Conversation</h3>
                    <div class="max-h-48 overflow-y-auto space-y-2">
                        <div
                            v-for="(transcript, index) in transcripts.slice(-5)"
                            :key="index"
                            :class="[
                                'p-3 rounded-lg text-sm',
                                transcript.type === 'user' 
                                    ? 'bg-[#011135] text-white ml-8 border border-white/10' 
                                    : 'bg-white/10 text-white mr-8 border border-white/10'
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

            <!-- Help Text -->
            <div v-if="!isConnected" class="mt-4 bg-[#012169] rounded-lg p-4 border border-white/20 shadow-md">
                <div class="flex items-center gap-3">
                    <p class="text-sm font-semibold text-white">
                        <span class="text-white/80">Getting Started:</span> Click "Connect" to start building your roadmap!
                    </p>
                </div>
            </div>

            <!-- Document Requests Section (shown in business tab) -->
            <div v-if="pendingDocuments.length > 0 && activeTab === 'business'" class="mt-4 bg-[#012169] rounded-lg shadow-lg border border-white/20 p-5">
                <div class="flex items-center gap-3 mb-4">
                    <h3 class="text-lg font-bold text-white">Document Requests</h3>
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
            <div class="bg-[#012169] rounded-t-lg shadow-2xl w-full max-h-[85vh] overflow-y-auto drawer-content">
                <div class="sticky top-0 bg-[#011135] p-5 flex items-center justify-between rounded-t-lg shadow-md z-10 border-b border-white/20">
                    <h2 class="text-xl font-bold text-white">Business Information</h2>
                    <button @click="activeTab = 'roadmap'" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 pb-24 space-y-6">
                    <!-- Contextual Information Section -->
                    <div class="bg-[#011135] rounded-lg shadow-lg border border-white/20 p-5">
                        <h3 class="text-lg font-bold text-white mb-4">
                            Your Background Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div 
                                :ref="el => setContextualFieldRef('country_of_origin', el)"
                                :id="'contextual-field-country_of_origin'"
                                :class="[
                                    'bg-[#012169] rounded-lg p-3 border border-white/10 transition-all',
                                    fieldToHighlight === 'country_of_origin' ? 'field-highlight' : ''
                                ]"
                            >
                                <p class="text-xs font-semibold text-white/70 mb-1">Country of Origin</p>
                                <p class="text-sm font-bold text-white">{{ businessPlanData?.country_of_origin || 'Not provided' }}</p>
                            </div>
                            <div 
                                :ref="el => setContextualFieldRef('is_eu_resident', el)"
                                :id="'contextual-field-is_eu_resident'"
                                :class="[
                                    'bg-[#012169] rounded-lg p-3 border border-white/10 transition-all',
                                    fieldToHighlight === 'is_eu_resident' ? 'field-highlight' : ''
                                ]"
                            >
                                <p class="text-xs font-semibold text-white/70 mb-1">EU Resident</p>
                                <p class="text-sm font-bold text-white">
                                    <span v-if="businessPlanData?.is_eu_resident === true" class="text-green-400">Yes</span>
                                    <span v-else-if="businessPlanData?.is_eu_resident === false" class="text-red-400">No</span>
                                    <span v-else class="text-white/50">Not provided</span>
                                </p>
                            </div>
                            <div 
                                :ref="el => setContextualFieldRef('is_newcomer_to_finland', el)"
                                :id="'contextual-field-is_newcomer_to_finland'"
                                :class="[
                                    'bg-[#012169] rounded-lg p-3 border border-white/10 transition-all',
                                    fieldToHighlight === 'is_newcomer_to_finland' ? 'field-highlight' : ''
                                ]"
                            >
                                <p class="text-xs font-semibold text-white/70 mb-1">Newcomer to Finland</p>
                                <p class="text-sm font-bold text-white">
                                    <span v-if="businessPlanData?.is_newcomer_to_finland === true" class="text-green-400">Yes</span>
                                    <span v-else-if="businessPlanData?.is_newcomer_to_finland === false" class="text-gray-400">No</span>
                                    <span v-else class="text-white/50">Not provided</span>
                                </p>
                            </div>
                            <div 
                                :ref="el => setContextualFieldRef('has_residence_permit', el)"
                                :id="'contextual-field-has_residence_permit'"
                                :class="[
                                    'bg-[#012169] rounded-lg p-3 border border-white/10 transition-all',
                                    fieldToHighlight === 'has_residence_permit' ? 'field-highlight' : ''
                                ]"
                            >
                                <p class="text-xs font-semibold text-white/70 mb-1">Residence Permit</p>
                                <p class="text-sm font-bold text-white">
                                    <span v-if="businessPlanData?.has_residence_permit === true" class="text-green-400">Yes</span>
                                    <span v-else-if="businessPlanData?.has_residence_permit === false" class="text-red-400">No</span>
                                    <span v-else class="text-white/50">Not provided</span>
                                </p>
                            </div>
                            <div 
                                :ref="el => setContextualFieldRef('residence_permit_type', el)"
                                :id="'contextual-field-residence_permit_type'"
                                :class="[
                                    'bg-[#012169] rounded-lg p-3 border border-white/10 transition-all',
                                    fieldToHighlight === 'residence_permit_type' ? 'field-highlight' : ''
                                ]"
                            >
                                <p class="text-xs font-semibold text-white/70 mb-1">Residence Permit Type</p>
                                <p class="text-sm font-bold text-white">{{ businessPlanData?.residence_permit_type || 'Not provided' }}</p>
                            </div>
                            <div 
                                :ref="el => setContextualFieldRef('years_in_finland', el)"
                                :id="'contextual-field-years_in_finland'"
                                :class="[
                                    'bg-[#012169] rounded-lg p-3 border border-white/10 transition-all',
                                    fieldToHighlight === 'years_in_finland' ? 'field-highlight' : ''
                                ]"
                            >
                                <p class="text-xs font-semibold text-white/70 mb-1">Years in Finland</p>
                                <p class="text-sm font-bold text-white">{{ businessPlanData?.years_in_finland !== null && businessPlanData?.years_in_finland !== undefined ? businessPlanData.years_in_finland + ' years' : 'Not provided' }}</p>
                            </div>
                            <div 
                                :ref="el => setContextualFieldRef('has_business_experience', el)"
                                :id="'contextual-field-has_business_experience'"
                                :class="[
                                    'bg-[#012169] rounded-lg p-3 border border-white/10 transition-all',
                                    fieldToHighlight === 'has_business_experience' ? 'field-highlight' : ''
                                ]"
                            >
                                <p class="text-xs font-semibold text-white/70 mb-1">Business Experience</p>
                                <p class="text-sm font-bold text-white">
                                    <span v-if="businessPlanData?.has_business_experience === true" class="text-green-400">Yes</span>
                                    <span v-else-if="businessPlanData?.has_business_experience === false" class="text-gray-400">No</span>
                                    <span v-else class="text-white/50">Not provided</span>
                                </p>
                            </div>
                            <div 
                                :ref="el => setContextualFieldRef('language', el)"
                                :id="'contextual-field-language'"
                                :class="[
                                    'bg-[#012169] rounded-lg p-3 border border-white/10 transition-all',
                                    fieldToHighlight === 'language' ? 'field-highlight' : ''
                                ]"
                            >
                                <p class="text-xs font-semibold text-white/70 mb-1">Preferred Language</p>
                                <p class="text-sm font-bold text-white">{{ businessPlanData?.language || 'Not provided' }}</p>
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
                    <div v-if="pendingDocuments.length > 0" class="bg-[#012169] rounded-lg shadow-lg border border-white/20 p-5">
                        <div class="flex items-center gap-3 mb-4">
                            <h3 class="text-lg font-bold text-white">Document Requests</h3>
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
            <div class="bg-[#012169] rounded-t-lg shadow-2xl w-full max-h-[85vh] overflow-y-auto drawer-content">
                <div class="sticky top-0 bg-[#011135] p-5 flex items-center justify-between rounded-t-lg shadow-md z-10 border-b border-white/20">
                    <h2 class="text-xl font-bold text-white">Manual Entry</h2>
                    <button @click="activeTab = 'roadmap'" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 pb-24">
                    <!-- Manual Add content embedded directly -->
                    <div class="space-y-6">
                        <p class="text-white/80 mb-6">Here you can manually add or update business plan fields or roadmap steps.</p>
                        <div class="bg-[#011135] rounded-lg p-4 border border-white/20">
                            <h3 class="font-bold text-white mb-3">
                                Add Business Plan Field
                            </h3>
                            <p class="text-sm text-white/70 mb-4">Coming soon! You'll be able to manually add business plan information here.</p>
                        </div>
                        <div class="bg-[#011135] rounded-lg p-4 border border-white/20">
                            <h3 class="font-bold text-white mb-3">
                                Add Roadmap Step
                            </h3>
                            <p class="text-sm text-white/70 mb-4">Coming soon! You'll be able to manually add roadmap steps here.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advisors Drawer -->
        <div v-if="activeTab === 'advisors'" 
             class="fixed inset-0 z-40 flex items-end drawer-overlay"
             @click.self="activeTab = 'roadmap'">
            <div class="bg-[#012169] rounded-t-lg shadow-2xl w-full h-[90vh] overflow-y-auto drawer-content">
                <div class="sticky top-0 bg-[#011135] p-5 flex items-center justify-between rounded-t-lg shadow-md z-10 border-b border-white/20">
                    <h2 class="text-xl font-bold text-white">Your Advisors</h2>
                    <button @click="activeTab = 'roadmap'" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 pb-24">
                    <!-- Advisors content embedded directly -->
                    <p class="text-white/80 mb-6">Connect with experienced advisors who can help guide your startup journey.</p>
                    
                    <!-- Loading state -->
                    <div v-if="loadingAdvisors" class="text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-white"></div>
                        <p class="mt-2 text-white/80">Loading advisors...</p>
                    </div>
                    
                    <!-- Empty state -->
                    <div v-else-if="advisors.length === 0" class="text-center py-12">
                        <h3 class="text-xl font-bold text-white mb-2">No Advisors Available</h3>
                        <p class="text-white/70">Advisors will appear here once they're added to the system.</p>
                    </div>
                    
                    <!-- Advisors list -->
                    <div v-else class="space-y-4">
                        <div 
                            v-for="advisor in advisors" 
                            :key="advisor.id"
                            class="bg-[#011135] rounded-lg p-4 border border-white/20 flex items-start gap-4 hover:bg-[#012169] transition-all"
                        >
                            <div class="flex-shrink-0 w-12 h-12 bg-[#012169] rounded-full flex items-center justify-center text-white text-lg font-bold border border-white/20">
                                {{ advisor.name.charAt(0).toUpperCase() }}
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-white text-lg">{{ advisor.name }}</p>
                                <p v-if="advisor.title" class="text-sm text-white/70 mb-1">{{ advisor.title }}</p>
                                <p class="text-sm text-white/60 mb-2">{{ advisor.email }}</p>
                                <p v-if="advisor.bio" class="text-sm text-white/80 mb-2">{{ advisor.bio }}</p>
                                <div v-if="advisor.languages && Array.isArray(advisor.languages)" class="mb-2">
                                    <p class="text-xs text-white/60 mb-1">Languages:</p>
                                    <p class="text-sm text-white/80">{{ advisor.languages.join(', ') }}</p>
                                </div>
                                <div v-if="advisor.specialization" class="flex items-center gap-2 mt-2">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold text-white bg-[#012169] border border-white/20">
                                        {{ getSpecializationLabel(advisor.specialization) }}
                                    </span>
                                </div>
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
            <div class="bg-[#012169] rounded-t-lg shadow-2xl w-full max-h-[85vh] overflow-y-auto drawer-content">
                <div class="sticky top-0 bg-[#011135] p-5 flex items-center justify-between rounded-t-lg shadow-md z-10 border-b border-white/20">
                    <h2 class="text-xl font-bold text-white">Your Roadmap</h2>
                    <button @click="activeTab = 'roadmap'" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 pb-24">
                    <!-- Roadmap Visualization -->
                    <div v-if="roadmap && roadmap.steps && roadmap.steps.filter(s => !s.isQuestion).length > 0" 
                         class="bg-[#011135] rounded-lg shadow-lg border border-white/20 p-5">
                        <RoadmapVisualizer 
                            :roadmap="roadmap"
                            @step-update="handleStepUpdate"
                        />
                    </div>
                    <div v-else class="text-center py-12">
                        <h3 class="text-xl font-bold text-white mb-2">No Roadmap Steps Yet</h3>
                        <p class="text-white/70">Start a voice session to create your roadmap!</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Drawer -->
        <div v-if="activeTab === 'calendar'" 
             class="fixed inset-0 z-40 flex items-end drawer-overlay"
             @click.self="activeTab = 'roadmap'">
            <div class="bg-[#012169] rounded-t-lg shadow-2xl w-full max-h-[85vh] overflow-y-auto drawer-content">
                <div class="sticky top-0 bg-[#011135] p-5 flex items-center justify-between rounded-t-lg shadow-md z-10 border-b border-white/20">
                    <h2 class="text-xl font-bold text-white">Your Calendar</h2>
                    <button @click="activeTab = 'roadmap'" class="text-white/80 hover:text-white transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 pb-24">
                    <!-- Calendar content embedded directly -->
                    <p class="text-white/80 mb-6">Manage your upcoming meetings and important dates.</p>
                    <div class="space-y-4">
                        <div class="bg-[#011135] rounded-lg p-4 border border-white/20">
                            <p class="font-bold text-white mb-1">Meeting with Advisor A</p>
                            <p class="text-sm text-white/70">Date: October 26, 2024, 10:00 AM</p>
                            <p class="text-xs text-white/60">Topic: Funding Strategy</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, nextTick, computed, watch } from 'vue';
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
    },
    advisors: {
        type: Array,
        default: () => []
    }
});

const roadmap = ref(props.initialRoadmap || { steps: [] });
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

// Advisors
const advisors = ref(props.advisors || []);
const loadingAdvisors = ref(false);

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
                roadmap_json: roadmapToSave
            });
            console.log('Roadmap saved to backend successfully');
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
const loadAdvisors = async () => {
    try {
        loadingAdvisors.value = true;
        const response = await axios.get('/api/advisors');
        if (response.data && response.data.advisors) {
            advisors.value = response.data.advisors;
        }
    } catch (err) {
        console.error('Failed to load advisors:', err);
    } finally {
        loadingAdvisors.value = false;
    }
};

// Map step content to specializations for advisor matching
const matchAdvisorsToStep = (step) => {
    if (!step || !advisors.value.length) return [];
    
    const stepText = (step.title + ' ' + (step.description || '')).toLowerCase();
    
    const specializationMap = {
        'residence_permit': ['residence permit', 'residence', 'permit', 'migri', 'immigration'],
        'business_registration': ['business registration', 'register', 'trade register', 'company registration', 'y-tunnus'],
        'tax': ['tax', 'vat', 'accounting', 'verohallinto', 'tax office'],
        'funding': ['funding', 'grant', 'investor', 'investment', 'finance', 'loan'],
        'legal': ['legal', 'contract', 'law', 'intellectual property', 'ip', 'patent', 'trademark'],
        'marketing': ['marketing', 'sales', 'branding', 'advertising', 'promotion'],
    };
    
    const matchedSpecializations = [];
    for (const [specialization, keywords] of Object.entries(specializationMap)) {
        for (const keyword of keywords) {
            if (stepText.includes(keyword)) {
                matchedSpecializations.push(specialization);
                break;
            }
        }
    }
    
    if (matchedSpecializations.length === 0) return [];
    
    return advisors.value.filter(advisor => 
        advisor.specialization && matchedSpecializations.includes(advisor.specialization)
    );
};

const getSpecializationLabel = (specialization) => {
    const labels = {
        'residence_permit': 'Residence Permit',
        'business_registration': 'Business Registration',
        'tax': 'Tax & Accounting',
        'funding': 'Funding & Finance',
        'legal': 'Legal & IP',
        'marketing': 'Marketing & Sales',
    };
    return labels[specialization] || specialization;
};

const getSpecializationColor = (specialization) => {
    const colors = {
        'residence_permit': 'from-blue-400 to-blue-600',
        'business_registration': 'from-purple-400 to-purple-600',
        'tax': 'from-green-400 to-green-600',
        'funding': 'from-yellow-400 to-yellow-600',
        'legal': 'from-red-400 to-red-600',
        'marketing': 'from-pink-400 to-pink-600',
    };
    return colors[specialization] || 'from-gray-400 to-gray-600';
};

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
    onBusinessPlanUpdate: handleBusinessPlanUpdate,
    userName: props.userName,
    onTranscript: handleTranscript,
    onError: handleError,
    onMeetingPrep: handleMeetingPrep,
    onChecklistComplete: handleChecklistComplete,
    onDocumentRequest: handleDocumentRequest,
    onResourceSuggested: handleResourceSuggested,
    onProgressSummary: handleProgressSummary
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
    // Load advisors on mount
    await loadAdvisors();
    // Data is already loaded via Inertia props
    // Roadmap only shows action steps, no question steps
    // Question steps are handled separately in BusinessPlanProgress component
    console.log('Roadmap loaded:', roadmap.value);
});
</script>

<style scoped>
.drawer-overlay {
    background-color: rgba(1, 17, 53, 0.8);
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
    background: #012169 !important;
    border: 2px solid #ffffff !important;
    box-shadow: 0 0 20px rgba(255, 255, 255, 0.3), 0 0 40px rgba(255, 255, 255, 0.1);
    transform: scale(1.02);
    z-index: 10;
    position: relative;
}

@keyframes highlightPulse {
    0% {
        background: #012169;
        border-color: #ffffff;
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.3), 0 0 40px rgba(255, 255, 255, 0.1);
        transform: scale(1.02);
    }
    50% {
        background: #012169;
        border-color: #ffffff;
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.2), 0 0 30px rgba(255, 255, 255, 0.05);
        transform: scale(1.01);
    }
    100% {
        background: var(--original-bg, #012169);
        border-color: var(--original-border, rgba(255, 255, 255, 0.1));
        box-shadow: none;
        transform: scale(1);
    }
}
</style>

