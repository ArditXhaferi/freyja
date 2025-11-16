<template>
    <div class="flex flex-col h-full" :class="{ 'shake': isShaking }">
        <!-- Notification -->
        <Notification
            v-if="notification.message"
            :message="notification.message"
            :type="notification.type"
            @close="notification.message = ''"
        />
        
        <!-- Heart Animation Container -->
        <div v-if="showHearts" class="fixed inset-0 pointer-events-none z-50">
            <div
                v-for="(heart, index) in hearts"
                :key="index"
                class="heart-animation absolute"
                :style="{
                    left: heart.left + 'px',
                    top: heart.top + 'px',
                    animationDelay: heart.delay + 's',
                    '--end-x': heart.endX + 'px',
                    '--end-y': heart.endY + 'px'
                }"
            >
                <i class="fa-solid fa-heart text-red-500 text-3xl"></i>
            </div>
        </div>
        <!-- Header -->
        <div class="flex items-center justify-between mb-4 px-2">
            <div class="flex gap-4">
                <button
                    @click="switchView('discover')"
                    :class="[
                        'px-4 py-2 rounded-lg font-semibold transition-all',
                        currentView === 'discover'
                            ? 'bg-[#5cc094] text-white'
                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                    ]"
                >
                    Discover
                </button>
                <button
                    @click="switchView('matches')"
                    :class="[
                        'px-4 py-2 rounded-lg font-semibold transition-all relative',
                        currentView === 'matches'
                            ? 'bg-[#5cc094] text-white'
                            : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                    ]"
                >
                    Matches
                    <span v-if="matchesCount > 0" 
                          class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                        {{ matchesCount }}
                    </span>
                </button>
            </div>
        </div>

        <!-- Discover View -->
        <div v-if="currentView === 'discover'" class="flex-1 flex flex-col items-center justify-center">
            <!-- Loading State -->
            <div v-if="loading" class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-[#5cc094] mb-4"></div>
                <p class="text-gray-600">Loading companies...</p>
            </div>

            <!-- Empty State -->
            <div v-else-if="companies.length === 0" class="text-center py-12 px-4">
                <i class="fa-solid fa-building text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No More Companies</h3>
                <p class="text-gray-600">You've swiped through all available companies. Check back later!</p>
            </div>

            <!-- Swipe Cards -->
            <div v-else class="relative w-full max-w-md h-[600px]">
                <transition-group name="swipe" tag="div" class="relative w-full h-full">
                    <div
                        v-for="(company, index) in displayedCompanies.slice(0, 3)"
                        :key="company.id"
                        ref="cardRefs"
                        :style="{
                            transform: index === 0 
                                ? `translate(${cardPosition.x}px, ${cardPosition.y}px) rotate(${cardRotation}deg) scale(1)`
                                : index === 1
                                ? `translate(0px, ${Math.min(10, Math.abs(cardPosition.x) * 0.05)}px) rotate(0deg) scale(${Math.min(1, 0.95 + Math.abs(cardPosition.x) * 0.0005)})`
                                : `translate(0px, ${Math.min(5, Math.abs(cardPosition.x) * 0.03)}px) rotate(0deg) scale(0.9)`,
                            opacity: index === 0 
                                ? 1 
                                : index === 1 
                                ? Math.min(1, 0.7 + (Math.abs(cardPosition.x) / 400) * 0.3)
                                : 0.5,
                            zIndex: displayedCompanies.length - index,
                            pointerEvents: index === 0 ? 'auto' : 'none'
                        }"
                        :class="[
                            'absolute inset-0 bg-[#5cc094] rounded-2xl border border-[#5cc094] overflow-hidden transition-all duration-150',
                            index === 0 ? 'cursor-grab active:cursor-grabbing' : ''
                        ]"
                        @touchstart="index === 0 ? startSwipe($event, company) : null"
                        @touchmove="index === 0 ? moveSwipe($event) : null"
                        @touchend="index === 0 ? endSwipe(company) : null"
                        @mousedown="index === 0 ? startSwipe($event, company) : null"
                        @mousemove="index === 0 ? moveSwipe($event) : null"
                        @mouseup="index === 0 ? endSwipe(company) : null"
                        @mouseleave="index === 0 ? endSwipe(company) : null"
                    >
                        <!-- Company Card Content -->
                        <div class="h-full flex flex-col">
                            <!-- Header -->
                            <div class="p-6 pb-4 flex-shrink-0">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h2 class="text-2xl font-bold text-white mb-1">{{ company.business_name }}</h2>
                                        <p class="text-white/70 text-sm">{{ company.name }}</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs text-white/60 mb-1">Established</div>
                                        <div class="text-white font-semibold">{{ company.year_of_establishment }}</div>
                                    </div>
                                </div>
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <span class="px-3 py-1 bg-white/20 text-white rounded-full text-xs font-semibold">
                                        {{ company.industry }}
                                    </span>
                                    <span v-if="company.company_type" 
                                          class="px-3 py-1 bg-white/20 text-white rounded-full text-xs font-semibold">
                                        {{ company.company_type }}
                                    </span>
                                    <span v-if="company.number_of_employees" 
                                          class="px-3 py-1 bg-white/20 text-white rounded-full text-xs font-semibold">
                                        {{ company.number_of_employees }} employees
                                    </span>
                                </div>
                                <div v-if="company.specialization" class="mb-2">
                                    <span class="text-xs text-white/60">Specialization:</span>
                                    <span class="text-sm text-white ml-2">{{ company.specialization }}</span>
                                </div>
                            </div>

                            <!-- Company Info -->
                            <div class="flex-1 overflow-y-auto px-6 pb-4">
                                <div v-if="company.business_idea" class="mb-4">
                                    <h3 class="text-sm font-semibold text-white/80 mb-2">Business Idea</h3>
                                    <p class="text-white/90 text-sm leading-relaxed">{{ company.business_idea }}</p>
                                </div>
                                <div v-if="company.products_services_general" class="mb-4">
                                    <h3 class="text-sm font-semibold text-white/80 mb-2">Products & Services</h3>
                                    <p class="text-white/90 text-sm leading-relaxed">{{ company.products_services_general }}</p>
                                </div>
                                <div v-if="company.bio" class="mb-4">
                                    <h3 class="text-sm font-semibold text-white/80 mb-2">About</h3>
                                    <p class="text-white/90 text-sm leading-relaxed">{{ company.bio }}</p>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div v-if="company.address" class="flex items-center text-white/80">
                                        <i class="fa-solid fa-location-dot w-5"></i>
                                        <span>{{ company.address }}{{ company.postal_district ? ', ' + company.postal_district : '' }}</span>
                                    </div>
                                    <div v-if="company.internet_address" class="flex items-center text-white/80">
                                        <i class="fa-solid fa-globe w-5"></i>
                                        <a :href="'https://' + company.internet_address" 
                                           target="_blank" 
                                           class="text-blue-300 hover:underline">
                                            {{ company.internet_address }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Swipe Indicators -->
                            <div v-if="showSwipeIndicator" 
                                 :class="[
                                     'absolute inset-0 flex items-center justify-center pointer-events-none transition-opacity',
                                     swipeDirection === 'right' ? 'bg-[#5cc094]/20' : 'bg-red-500/20'
                                 ]">
                                <div v-if="swipeDirection === 'right'" class="flex flex-col items-center gap-2">
                                    <i class="fa-solid fa-heart text-6xl text-red-400 animate-pulse"></i>
                                    <span class="text-2xl font-bold text-white">LIKE</span>
                                </div>
                                <div v-else class="flex flex-col items-center gap-2">
                                    <i class="fa-solid fa-times-circle text-6xl text-red-400"></i>
                                    <span class="text-2xl font-bold text-red-400">PASS</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </transition-group>
            </div>

            <!-- Action Buttons -->
            <div v-if="!loading && companies.length > 0" class="flex items-center justify-center gap-6 mt-6">
                <button
                    @click="swipe('pass')"
                    class="w-16 h-16 rounded-full bg-red-500 text-white hover:bg-red-600 transition-all hover:scale-110 flex items-center justify-center"
                >
                    <i class="fa-solid fa-times text-2xl"></i>
                </button>
                <button
                    @click="swipe('super_like')"
                    class="w-16 h-16 rounded-full bg-[#5cc094] text-white hover:bg-[#4a9d7a] transition-all hover:scale-110 flex items-center justify-center"
                >
                    <i class="fa-solid fa-star text-2xl"></i>
                </button>
                <button
                    @click="swipe('like')"
                    class="w-16 h-16 rounded-full bg-[#5cc094] text-white hover:bg-[#4a9d7a] transition-all hover:scale-110 flex items-center justify-center"
                >
                    <i class="fa-solid fa-heart text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Matches View -->
        <div v-if="currentView === 'matches'" class="flex-1 overflow-y-auto">
            <!-- Loading State -->
            <div v-if="loadingMatches" class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-[#5cc094] mb-4"></div>
                <p class="text-gray-600">Loading matches...</p>
            </div>

            <!-- Empty State -->
            <div v-else-if="matches.length === 0" class="text-center py-12 px-4">
                <i class="fa-solid fa-heart text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-900 mb-2">No Likes Yet</h3>
                <p class="text-gray-600">Start swiping to like companies and find potential business partners!</p>
            </div>

            <!-- Matches List -->
            <div v-else class="space-y-4">
                <div
                    v-for="match in matches"
                    :key="match.id"
                    class="group bg-[#5cc094] rounded-lg p-5 border border-[#5cc094] hover:bg-[#4a9d7a] transition-all relative"
                >
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-16 h-16 bg-[#4a9d7a] rounded-full flex items-center justify-center text-white text-2xl font-bold border border-white/30">
                            {{ match.company.business_name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h3 class="text-xl font-bold text-white transition-colors">{{ match.company.business_name }}</h3>
                                    <p class="text-white/90 text-sm transition-colors">{{ match.company.name }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span v-if="match.is_mutual" class="px-3 py-1 bg-white/20 text-white rounded-full text-xs font-semibold border border-white/30">
                                        MATCH! 🎉
                                    </span>
                                    <span v-else class="px-3 py-1 bg-white/20 text-white rounded-full text-xs font-semibold border border-white/30">
                                        LIKED
                                    </span>
                                    <button
                                        @click="dislikeMatch(match.company.id)"
                                        :disabled="match.disliking"
                                        :class="[
                                            'px-3 py-1 rounded-lg font-semibold text-xs transition-all flex items-center gap-1.5',
                                            match.disliking
                                                ? 'bg-red-300/80 text-white cursor-wait'
                                                : 'bg-red-300/70 text-white hover:bg-red-300/90 hover:scale-105 active:scale-95'
                                        ]"
                                        title="Dislike"
                                    >
                                        <i v-if="match.disliking" class="fa-solid fa-spinner fa-spin text-xs"></i>
                                        <i v-else class="fa-solid fa-times text-xs"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 mb-3">
                                <span class="px-3 py-1 bg-white/20 text-white rounded-full text-xs font-semibold">
                                    {{ match.company.industry }}
                                </span>
                                <span v-if="match.company.specialization" 
                                      class="px-3 py-1 bg-white/20 text-white rounded-full text-xs font-semibold">
                                    {{ match.company.specialization }}
                                </span>
                            </div>
                            <p v-if="match.company.business_idea" class="text-white/90 text-sm mb-3 line-clamp-2 transition-colors">
                                {{ match.company.business_idea }}
                            </p>
                            <div class="flex items-center gap-3 text-sm text-white/80">
                                <div v-if="match.company.address" class="flex items-center">
                                    <i class="fa-solid fa-location-dot mr-1"></i>
                                    <span>{{ match.company.postal_district || 'Helsinki' }}</span>
                                </div>
                                <div v-if="match.company.internet_address" class="flex items-center">
                                    <i class="fa-solid fa-globe mr-1"></i>
                                    <a :href="'https://' + match.company.internet_address" 
                                       target="_blank" 
                                       class="text-white hover:underline">
                                        Visit Website
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Action Buttons -->
                    <div class="mt-4 flex items-center justify-end">
                        <button
                            v-if="match.hasPendingRequest"
                            @click="deleteMeetingRequest(match.company.id)"
                            :disabled="match.deletingRequest"
                            :class="[
                                'px-5 py-2.5 rounded-lg font-semibold text-sm transition-all flex items-center gap-2 shadow-lg',
                                match.deletingRequest
                                    ? 'bg-white/20 text-white cursor-wait'
                                    : 'bg-white/30 text-white hover:bg-white/40 hover:scale-105 active:scale-95'
                            ]"
                        >
                            <i v-if="match.deletingRequest" class="fa-solid fa-spinner fa-spin"></i>
                            <i v-else class="fa-solid fa-trash"></i>
                            <span>{{ match.deletingRequest ? 'Cancelling...' : 'Cancel Request' }}</span>
                        </button>
                        <button
                            v-else
                            @click="requestMeeting(match.company.id)"
                            :disabled="match.requestingMeeting"
                            :class="[
                                'px-5 py-2.5 rounded-lg font-semibold text-sm transition-all flex items-center gap-2 shadow-lg',
                                match.requestingMeeting
                                    ? 'bg-white/20 text-white cursor-wait'
                                    : 'bg-white text-[#5cc094] hover:bg-gray-50 hover:scale-105 active:scale-95'
                            ]"
                        >
                            <i v-if="match.requestingMeeting" class="fa-solid fa-spinner fa-spin"></i>
                            <i v-else class="fa-solid fa-calendar-plus"></i>
                            <span>{{ match.requestingMeeting ? 'Sending...' : 'Request Meeting' }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch } from 'vue';
import axios from 'axios';
import Notification from './Notification.vue';

const props = defineProps({
    currentView: {
        type: String,
        default: 'discover'
    }
});

const emit = defineEmits(['view']);

const companies = ref([]);
const matches = ref([]);
const loading = ref(false);
const loadingMatches = ref(false);
const displayedCompanies = ref([]);
const pendingMeetingRequests = ref(new Set());

// Swipe state
const cardPosition = ref({ x: 0, y: 0 });
const cardRotation = ref(0);
const startPos = ref({ x: 0, y: 0 });
const isSwipeActive = ref(false);
const swipeDirection = ref(null);
const showSwipeIndicator = ref(false);

// Visual effects
const showHearts = ref(false);
const hearts = ref([]);
const isShaking = ref(false);

// Notification state
const notification = ref({
    message: '',
    type: 'success'
});

const showNotification = (message, type = 'success') => {
    notification.value = { message, type };
};

const matchesCount = computed(() => matches.value.length);

const loadCompanies = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/api/network/companies');
        companies.value = response.data.companies || [];
        displayedCompanies.value = companies.value.slice(0, 3); // Show top 3 cards
    } catch (error) {
        console.error('Error loading companies:', error);
    } finally {
        loading.value = false;
    }
};

const loadPendingMeetingRequests = async () => {
    try {
        const response = await axios.get('/api/meeting-requests');
        const advisorIds = response.data.meeting_requests || [];
        // Add all advisor IDs to the set
        advisorIds.forEach(advisorId => {
            pendingMeetingRequests.value.add(advisorId);
        });
    } catch (error) {
        console.error('Error loading pending meeting requests:', error);
    }
};

const loadMatches = async () => {
    loadingMatches.value = true;
    try {
        // Load pending requests first to check status
        await loadPendingMeetingRequests();
        
        const response = await axios.get('/api/network/matches');
        const matchesData = response.data.matches || [];
        // Add requestingMeeting, hasPendingRequest, disliking, and deletingRequest flags to each match
        matches.value = matchesData.map(match => ({
            ...match,
            requestingMeeting: false,
            hasPendingRequest: pendingMeetingRequests.value.has(match.company.id),
            disliking: false,
            deletingRequest: false
        }));
    } catch (error) {
        console.error('Error loading matches:', error);
    } finally {
        loadingMatches.value = false;
    }
};

const requestMeeting = async (advisorId) => {
    // Find the match and update its requestingMeeting state
    const matchIndex = matches.value.findIndex(m => m.company.id === advisorId);
    if (matchIndex === -1) return;

    matches.value[matchIndex].requestingMeeting = true;

    try {
        const response = await axios.post('/api/network/meeting-request', {
            advisor_id: advisorId
        });

        if (response.status === 201) {
            // Mark as having pending request
            pendingMeetingRequests.value.add(advisorId);
            matches.value[matchIndex].hasPendingRequest = true;
            matches.value[matchIndex].requestingMeeting = false;
            
            // Show success notification
            showNotification('Meeting request sent successfully!', 'success');
        }
    } catch (error) {
        matches.value[matchIndex].requestingMeeting = false;
        console.error('Error requesting meeting:', error);
        
        const errorMessage = error.response?.data?.message || 'Failed to send meeting request. Please try again.';
        showNotification(errorMessage, 'error');
    }
};

const dislikeMatch = async (companyId) => {
    // Find the match and update its disliking state
    const matchIndex = matches.value.findIndex(m => m.company.id === companyId);
    if (matchIndex === -1) return;

    matches.value[matchIndex].disliking = true;

    try {
        const response = await axios.delete(`/api/network/matches/${companyId}`);

        if (response.status === 200) {
            // Remove the match from the list (no shake effect)
            matches.value.splice(matchIndex, 1);
            
            // Show success notification
            showNotification('Match removed successfully', 'success');
        }
    } catch (error) {
        matches.value[matchIndex].disliking = false;
        console.error('Error disliking match:', error);
        
        const errorMessage = error.response?.data?.message || 'Failed to remove match. Please try again.';
        showNotification(errorMessage, 'error');
    }
};

const deleteMeetingRequest = async (advisorId) => {
    // Find the match and update its deletingRequest state
    const matchIndex = matches.value.findIndex(m => m.company.id === advisorId);
    if (matchIndex === -1) return;

    matches.value[matchIndex].deletingRequest = true;

    try {
        const response = await axios.delete(`/api/meeting-requests/${advisorId}`);

        if (response.status === 200) {
            // Remove from pending requests set
            pendingMeetingRequests.value.delete(advisorId);
            matches.value[matchIndex].hasPendingRequest = false;
            matches.value[matchIndex].deletingRequest = false;
            
            // Show success notification
            showNotification('Meeting request cancelled successfully', 'success');
        }
    } catch (error) {
        matches.value[matchIndex].deletingRequest = false;
        console.error('Error deleting meeting request:', error);
        
        const errorMessage = error.response?.data?.message || 'Failed to cancel meeting request. Please try again.';
        showNotification(errorMessage, 'error');
    }
};

const startSwipe = (event, company) => {
    isSwipeActive.value = true;
    const clientX = event.touches ? event.touches[0].clientX : event.clientX;
    const clientY = event.touches ? event.touches[0].clientY : event.clientY;
    startPos.value = { x: clientX, y: clientY };
    showSwipeIndicator.value = false;
    swipeDirection.value = null;
};

const moveSwipe = (event) => {
    if (!isSwipeActive.value) return;
    
    const clientX = event.touches ? event.touches[0].clientX : event.clientX;
    const clientY = event.touches ? event.touches[0].clientY : event.clientY;
    
    const deltaX = clientX - startPos.value.x;
    const deltaY = clientY - startPos.value.y;
    
    cardPosition.value = { x: deltaX, y: deltaY };
    cardRotation.value = deltaX * 0.1;
    
    // Show swipe indicator
    if (Math.abs(deltaX) > 50) {
        showSwipeIndicator.value = true;
        swipeDirection.value = deltaX > 0 ? 'right' : 'left';
    } else {
        showSwipeIndicator.value = false;
        swipeDirection.value = null;
    }
};

const endSwipe = async (company) => {
    if (!isSwipeActive.value) return;
    
    const deltaX = cardPosition.value.x;
    const threshold = 100;
    
    if (Math.abs(deltaX) > threshold) {
        const action = deltaX > 0 ? 'like' : 'pass';
        await performSwipe(company.id, action);
    } else {
        // Reset card position
        cardPosition.value = { x: 0, y: 0 };
        cardRotation.value = 0;
    }
    
    isSwipeActive.value = false;
    showSwipeIndicator.value = false;
    swipeDirection.value = null;
};

const swipe = async (action) => {
    if (displayedCompanies.value.length === 0) return;
    const company = displayedCompanies.value[0];
    await performSwipe(company.id, action);
};

const switchView = (view) => {
    emit('view', view);
};

const triggerHeartAnimation = () => {
    showHearts.value = true;
    hearts.value = [];
    
    // Get center of viewport
    const centerX = window.innerWidth / 2;
    const centerY = window.innerHeight / 2;
    
    // Create multiple hearts at different positions
    for (let i = 0; i < 8; i++) {
        const angle = (Math.PI * 2 * i) / 8;
        const distance = 150 + Math.random() * 100;
        const endX = Math.cos(angle) * distance;
        const endY = Math.sin(angle) * distance;
        
        hearts.value.push({
            left: centerX,
            top: centerY,
            endX: endX,
            endY: endY - 100, // Float upward
            delay: i * 0.1
        });
    }
    
    setTimeout(() => {
        showHearts.value = false;
        hearts.value = [];
    }, 2000);
};

const triggerShake = () => {
    isShaking.value = true;
    setTimeout(() => {
        isShaking.value = false;
    }, 500);
};

const performSwipe = async (companyId, action) => {
    try {
        // Trigger visual effects
        if (action === 'like' || action === 'super_like') {
            triggerHeartAnimation();
        } else if (action === 'pass') {
            triggerShake();
        }
        
        const response = await axios.post('/api/network/swipe', {
            company_id: companyId,
            action: action
        });
        
        // Only proceed if the response was successful
        if (response.status === 201 || response.status === 200) {
            console.log('Swipe saved successfully:', response.data);
            
            if (response.data.is_mutual) {
                // Reload matches if there's a mutual match
                await loadMatches();
                showNotification('It\'s a match! 🎉', 'success');
            } else if (action === 'like' || action === 'super_like') {
                showNotification('Company liked!', 'success');
            } else {
                showNotification('Company passed', 'info');
            }
            
            // Remove the swiped company and show next one
            displayedCompanies.value.shift();
            
            // If we're running low on cards, load more
            if (displayedCompanies.value.length <= 1) {
                // Reload companies to get fresh list excluding the one we just swiped
                await loadCompanies();
                // Add next companies to displayed list
                const remainingCompanies = companies.value.filter(
                    c => !displayedCompanies.value.find(dc => dc.id === c.id)
                );
                displayedCompanies.value.push(...remainingCompanies.slice(0, 2));
            }
            
            // Reset card position
            cardPosition.value = { x: 0, y: 0 };
            cardRotation.value = 0;
        }
    } catch (error) {
        console.error('Error performing swipe:', error);
        console.error('Error response:', error.response?.data);
        const errorMessage = error.response?.data?.message || 'Failed to save swipe. Please try again.';
        showNotification(errorMessage, 'error');
    }
};

// Watch for view changes
watch(() => props.currentView, (newView) => {
    if (newView === 'matches') {
        loadMatches();
    } else if (newView === 'discover') {
        loadCompanies();
    }
}, { immediate: true });

onMounted(async () => {
    await loadCompanies();
    if (props.currentView === 'matches') {
        await loadMatches();
    }
});

// Expose methods
defineExpose({
    loadCompanies,
    loadMatches
});
</script>

<style scoped>
.swipe-enter-active,
.swipe-leave-active {
    transition: all 0.3s ease;
}

.swipe-enter-from {
    opacity: 0;
    transform: scale(0.9) translateY(20px);
}

.swipe-leave-to {
    opacity: 0;
    transform: translateX(300px) rotate(30deg);
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Heart Animation */
@keyframes heartFloat {
    0% {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1) rotate(0deg);
    }
    50% {
        opacity: 0.9;
        transform: translate(calc(-50% + var(--end-x)), calc(-50% + var(--end-y) * 0.5)) scale(1.3) rotate(180deg);
    }
    100% {
        opacity: 0;
        transform: translate(calc(-50% + var(--end-x)), calc(-50% + var(--end-y))) scale(0.3) rotate(360deg);
    }
}

.heart-animation {
    animation: heartFloat 2s ease-out forwards;
    --end-x: 0px;
    --end-y: -100px;
}

/* Shake Animation */
@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-7px); }
    20%, 40%, 60%, 80% { transform: translateX(7px); }
}

.shake {
    animation: shake 0.5s ease-in-out;
}
</style>
