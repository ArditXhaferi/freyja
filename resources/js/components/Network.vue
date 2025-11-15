<template>
    <div class="flex flex-col h-full">
        <!-- Header -->
        <div class="flex items-center justify-between mb-4 px-2">
            <div class="flex gap-4">
                <button
                    @click="switchView('discover')"
                    :class="[
                        'px-4 py-2 rounded-lg font-semibold transition-all',
                        currentView === 'discover'
                            ? 'bg-white text-[#012169]'
                            : 'bg-white/10 text-white/70 hover:text-white'
                    ]"
                >
                    Discover
                </button>
                <button
                    @click="switchView('matches')"
                    :class="[
                        'px-4 py-2 rounded-lg font-semibold transition-all relative',
                        currentView === 'matches'
                            ? 'bg-white text-[#012169]'
                            : 'bg-white/10 text-white/70 hover:text-white'
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
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-white mb-4"></div>
                <p class="text-white/80">Loading companies...</p>
            </div>

            <!-- Empty State -->
            <div v-else-if="companies.length === 0" class="text-center py-12 px-4">
                <i class="fa-solid fa-building text-6xl text-white/40 mb-4"></i>
                <h3 class="text-xl font-bold text-white mb-2">No More Companies</h3>
                <p class="text-white/70">You've swiped through all available companies. Check back later!</p>
            </div>

            <!-- Swipe Cards -->
            <div v-else class="relative w-full max-w-md h-[600px]">
                <transition-group name="swipe" tag="div" class="relative w-full h-full">
                    <div
                        v-for="(company, index) in displayedCompanies"
                        :key="company.id"
                        v-show="index === 0"
                        ref="cardRefs"
                        :style="{
                            transform: `translate(${cardPosition.x}px, ${cardPosition.y}px) rotate(${cardRotation}deg)`,
                            opacity: index === 0 ? 1 : 0,
                            zIndex: displayedCompanies.length - index
                        }"
                        class="absolute inset-0 bg-[#012169] rounded-2xl shadow-2xl border border-white/20 overflow-hidden cursor-grab active:cursor-grabbing transition-transform duration-150"
                        @touchstart="startSwipe($event, company)"
                        @touchmove="moveSwipe($event)"
                        @touchend="endSwipe(company)"
                        @mousedown="startSwipe($event, company)"
                        @mousemove="moveSwipe($event)"
                        @mouseup="endSwipe(company)"
                        @mouseleave="endSwipe(company)"
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
                                     swipeDirection === 'right' ? 'bg-green-500/20' : 'bg-red-500/20'
                                 ]">
                                <div :class="[
                                    'text-6xl font-bold',
                                    swipeDirection === 'right' ? 'text-green-400' : 'text-red-400'
                                ]">
                                    {{ swipeDirection === 'right' ? 'LIKE' : 'PASS' }}
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
                    class="w-16 h-16 rounded-full bg-red-500 text-white shadow-lg hover:bg-red-600 transition-all hover:scale-110 flex items-center justify-center"
                >
                    <i class="fa-solid fa-times text-2xl"></i>
                </button>
                <button
                    @click="swipe('super_like')"
                    class="w-16 h-16 rounded-full bg-blue-500 text-white shadow-lg hover:bg-blue-600 transition-all hover:scale-110 flex items-center justify-center"
                >
                    <i class="fa-solid fa-star text-2xl"></i>
                </button>
                <button
                    @click="swipe('like')"
                    class="w-16 h-16 rounded-full bg-green-500 text-white shadow-lg hover:bg-green-600 transition-all hover:scale-110 flex items-center justify-center"
                >
                    <i class="fa-solid fa-heart text-2xl"></i>
                </button>
            </div>
        </div>

        <!-- Matches View -->
        <div v-if="currentView === 'matches'" class="flex-1 overflow-y-auto">
            <!-- Loading State -->
            <div v-if="loadingMatches" class="text-center py-12">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-white mb-4"></div>
                <p class="text-white/80">Loading matches...</p>
            </div>

            <!-- Empty State -->
            <div v-else-if="matches.length === 0" class="text-center py-12 px-4">
                <i class="fa-solid fa-heart text-6xl text-white/40 mb-4"></i>
                <h3 class="text-xl font-bold text-white mb-2">No Likes Yet</h3>
                <p class="text-white/70">Start swiping to like companies and find potential business partners!</p>
            </div>

            <!-- Matches List -->
            <div v-else class="space-y-4">
                <div
                    v-for="match in matches"
                    :key="match.id"
                    class="bg-[#012169] rounded-lg p-5 border border-white/20 hover:bg-[#011135] transition-all"
                >
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-16 h-16 bg-white/20 rounded-full flex items-center justify-center text-white text-2xl font-bold border border-white/20">
                            {{ match.company.business_name.charAt(0).toUpperCase() }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-start justify-between mb-2">
                                <div>
                                    <h3 class="text-xl font-bold text-white">{{ match.company.business_name }}</h3>
                                    <p class="text-white/70 text-sm">{{ match.company.name }}</p>
                                </div>
                                <span v-if="match.is_mutual" class="px-3 py-1 bg-green-500/20 text-green-300 rounded-full text-xs font-semibold border border-green-500/30">
                                    MATCH! 🎉
                                </span>
                                <span v-else class="px-3 py-1 bg-blue-500/20 text-blue-300 rounded-full text-xs font-semibold border border-blue-500/30">
                                    LIKED
                                </span>
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
                            <p v-if="match.company.business_idea" class="text-white/90 text-sm mb-3 line-clamp-2">
                                {{ match.company.business_idea }}
                            </p>
                            <div class="flex items-center gap-3 text-sm text-white/70">
                                <div v-if="match.company.address" class="flex items-center">
                                    <i class="fa-solid fa-location-dot mr-1"></i>
                                    <span>{{ match.company.postal_district || 'Helsinki' }}</span>
                                </div>
                                <div v-if="match.company.internet_address" class="flex items-center">
                                    <i class="fa-solid fa-globe mr-1"></i>
                                    <a :href="'https://' + match.company.internet_address" 
                                       target="_blank" 
                                       class="text-blue-300 hover:underline">
                                        Visit Website
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick, watch } from 'vue';
import axios from 'axios';

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

// Swipe state
const cardPosition = ref({ x: 0, y: 0 });
const cardRotation = ref(0);
const startPos = ref({ x: 0, y: 0 });
const isSwipeActive = ref(false);
const swipeDirection = ref(null);
const showSwipeIndicator = ref(false);

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

const loadMatches = async () => {
    loadingMatches.value = true;
    try {
        const response = await axios.get('/api/network/matches');
        matches.value = response.data.matches || [];
    } catch (error) {
        console.error('Error loading matches:', error);
    } finally {
        loadingMatches.value = false;
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

const performSwipe = async (companyId, action) => {
    try {
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
                // You could show a notification here
                alert('It\'s a match! 🎉');
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
        if (error.response?.data?.message) {
            alert(error.response.data.message);
        } else {
            alert('Failed to save swipe. Please try again.');
        }
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
</style>
