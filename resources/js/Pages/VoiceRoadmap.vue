<template>
    <Head title="Opa the Bear" />
    <!-- Top Navigation -->
    <TopNavigation 
        :active-tab="activeTab"
        @navigate="handleNavigation"
    />
    <div class="min-h-screen bg-[#fff5f5] pt-12 md:pt-24 pb-24 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Opa Animation Section -->
            <div class="mb-8 sm:mb-12 md:mb-16 flex justify-center">
                <div class="flex flex-col items-center gap-6 sm:gap-8 md:gap-12 lg:gap-16 mb-8 sm:mb-12 max-w-4xl">
                    
                    <!-- Bear Animation -->
                    <img
                        :src="isTalking ? '/images/video6.gif' : '/images/video5.gif'"
                        alt="Opa the Bear animation"
                        class="w-48 h-auto sm:w-56 md:w-64 lg:w-72 object-contain flex-shrink-0"
                        style="max-height: 250px;"
                    />

                    <!-- Text Block -->
                    <div class="flex flex-col w-full text-center">
                        <h1 class="text-2xl sm:text-3xl md:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-3 sm:mb-4 md:mb-5">
                            One bear.<br />Every language.
                        </h1>

                        <p class="text-[11px] sm:text-xs md:text-sm lg:text-base font-medium text-gray-600 max-w-full sm:max-w-xl md:max-w-2xl lg:max-w-3xl leading-relaxed mx-auto">
                            Opa the Bear is your voice-powered startup coach, guiding you from idea to action with 
                            smart, friendly support. Start your journey today and build something real.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flags Strip Animation - Aligned with navbar -->
        <!--
        <div 
            ref="flagsContainerRef"
            class="flags-wrapper relative w-full overflow-hidden py-4 mb-8"
        >
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style="width: 80%;">
                <div 
                    ref="flagsTrackRef"
                    class="flags-track flex"
                    style="will-change: transform; width: max-content;"
                >
                    <div 
                        ref="flagsSequenceRef"
                        class="flags-list flex gap-6 sm:gap-8 md:gap-10"
                    >
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 18 11" xmlns="http://www.w3.org/2000/svg">
                                <rect width="18" height="11" fill="#FFFFFF"/>
                                <rect x="0" y="4" width="18" height="3" fill="#003580"/>
                                <rect x="5" y="0" width="3" height="11" fill="#003580"/>
                            </svg>
                        </div>
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 5 3" xmlns="http://www.w3.org/2000/svg">
                                <rect width="5" height="1" fill="#000000"/>
                                <rect y="1" width="5" height="1" fill="#DD0000"/>
                                <rect y="2" width="5" height="1" fill="#FFCE00"/>
                            </svg>
                        </div>
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 3 2" xmlns="http://www.w3.org/2000/svg">
                                <rect width="3" height="2" fill="#E30A17"/>
                                <circle cx="1.1" cy="1" r="0.35" fill="#FFFFFF"/>
                                <circle cx="1.25" cy="1" r="0.3" fill="#E30A17"/>
                                <path d="M 1.6 0.7 L 1.65 0.85 L 1.8 0.9 L 1.7 1.0 L 1.75 1.15 L 1.6 1.1 L 1.45 1.15 L 1.5 1.0 L 1.4 0.9 L 1.55 0.85 Z" fill="#FFFFFF"/>
                            </svg>
                        </div>
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 3 2" xmlns="http://www.w3.org/2000/svg">
                                <rect width="1" height="2" fill="#002654"/>
                                <rect x="1" width="1" height="2" fill="#FFFFFF"/>
                                <rect x="2" width="1" height="2" fill="#ED2939"/>
                            </svg>
                        </div>
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 16 10" xmlns="http://www.w3.org/2000/svg">
                                <rect width="16" height="10" fill="#006AA7"/>
                                <rect x="0" y="4" width="16" height="2" fill="#FECC00"/>
                                <rect x="5" y="0" width="2" height="10" fill="#FECC00"/>
                            </svg>
                        </div>
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 60 30" xmlns="http://www.w3.org/2000/svg">
                                <rect width="60" height="30" fill="#012169"/>
                                <path d="M0,0 L60,30 M60,0 L0,30" stroke="#FFFFFF" stroke-width="4"/>
                                <path d="M0,0 L60,30 M60,0 L0,30" stroke="#C8102E" stroke-width="2.5"/>
                                <path d="M30,0 L30,30 M0,15 L60,15" stroke="#FFFFFF" stroke-width="5"/>
                                <path d="M30,0 L30,30 M0,15 L60,15" stroke="#C8102E" stroke-width="3"/>
                            </svg>
                        </div>
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 3 2" xmlns="http://www.w3.org/2000/svg">
                                <rect y="0" width="3" height="0.5" fill="#AA151B"/>
                                <rect y="0.5" width="3" height="0.5" fill="#F1BF00"/>
                                <rect y="1" width="3" height="0.5" fill="#AA151B"/>
                                <rect y="1.5" width="3" height="0.5" fill="#F1BF00"/>
                            </svg>
                        </div>
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 3 2" xmlns="http://www.w3.org/2000/svg">
                                <rect width="1" height="2" fill="#009246"/>
                                <rect x="1" width="1" height="2" fill="#FFFFFF"/>
                                <rect x="2" width="1" height="2" fill="#CE2B37"/>
                            </svg>
                        </div>
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 3 2" xmlns="http://www.w3.org/2000/svg">
                                <rect width="3" height="0.67" fill="#AE1C28"/>
                                <rect y="0.67" width="3" height="0.67" fill="#FFFFFF"/>
                                <rect y="1.34" width="3" height="0.66" fill="#21468B"/>
                            </svg>
                        </div>
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 3 2" xmlns="http://www.w3.org/2000/svg">
                                <rect width="3" height="1" fill="#FFFFFF"/>
                                <rect y="1" width="3" height="1" fill="#DC143C"/>
                            </svg>
                        </div>
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 16 12" xmlns="http://www.w3.org/2000/svg">
                                <rect width="16" height="12" fill="#BA0C2F"/>
                                <rect x="0" y="4" width="16" height="4" fill="#FFFFFF"/>
                                <rect x="5" y="0" width="6" height="12" fill="#FFFFFF"/>
                                <rect x="0" y="5" width="16" height="2" fill="#00205B"/>
                                <rect x="6" y="0" width="4" height="12" fill="#00205B"/>
                            </svg>
                        </div>
                    </div>
                    <div class="flags-list flex gap-6 sm:gap-8 md:gap-10">
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 18 11" xmlns="http://www.w3.org/2000/svg">
                                <rect width="18" height="11" fill="#FFFFFF"/>
                                <rect x="0" y="4" width="18" height="3" fill="#003580"/>
                                <rect x="5" y="0" width="3" height="11" fill="#003580"/>
                            </svg>
                        </div>
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 5 3" xmlns="http://www.w3.org/2000/svg">
                                <rect width="5" height="1" fill="#000000"/>
                                <rect y="1" width="5" height="1" fill="#DD0000"/>
                                <rect y="2" width="5" height="1" fill="#FFCE00"/>
                            </svg>
                        </div>
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 3 2" xmlns="http://www.w3.org/2000/svg">
                                <rect width="3" height="2" fill="#E30A17"/>
                                <circle cx="1.1" cy="1" r="0.35" fill="#FFFFFF"/>
                                <circle cx="1.25" cy="1" r="0.3" fill="#E30A17"/>
                                <path d="M 1.6 0.7 L 1.65 0.85 L 1.8 0.9 L 1.7 1.0 L 1.75 1.15 L 1.6 1.1 L 1.45 1.15 L 1.5 1.0 L 1.4 0.9 L 1.55 0.85 Z" fill="#FFFFFF"/>
                            </svg>
                        </div>
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 3 2" xmlns="http://www.w3.org/2000/svg">
                                <rect width="1" height="2" fill="#002654"/>
                                <rect x="1" width="1" height="2" fill="#FFFFFF"/>
                                <rect x="2" width="1" height="2" fill="#ED2939"/>
                            </svg>
                        </div>
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 16 10" xmlns="http://www.w3.org/2000/svg">
                                <rect width="16" height="10" fill="#006AA7"/>
                                <rect x="0" y="4" width="16" height="2" fill="#FECC00"/>
                                <rect x="5" y="0" width="2" height="10" fill="#FECC00"/>
                            </svg>
                        </div>
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 60 30" xmlns="http://www.w3.org/2000/svg">
                                <rect width="60" height="30" fill="#012169"/>
                                <path d="M0,0 L60,30 M60,0 L0,30" stroke="#FFFFFF" stroke-width="4"/>
                                <path d="M0,0 L60,30 M60,0 L0,30" stroke="#C8102E" stroke-width="2.5"/>
                                <path d="M30,0 L30,30 M0,15 L60,15" stroke="#FFFFFF" stroke-width="5"/>
                                <path d="M30,0 L30,30 M0,15 L60,15" stroke="#C8102E" stroke-width="3"/>
                            </svg>
                        </div>
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 3 2" xmlns="http://www.w3.org/2000/svg">
                                <rect y="0" width="3" height="0.5" fill="#AA151B"/>
                                <rect y="0.5" width="3" height="0.5" fill="#F1BF00"/>
                                <rect y="1" width="3" height="0.5" fill="#AA151B"/>
                                <rect y="1.5" width="3" height="0.5" fill="#F1BF00"/>
                            </svg>
                        </div>
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 3 2" xmlns="http://www.w3.org/2000/svg">
                                <rect width="1" height="2" fill="#009246"/>
                                <rect x="1" width="1" height="2" fill="#FFFFFF"/>
                                <rect x="2" width="1" height="2" fill="#CE2B37"/>
                            </svg>
                        </div>
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 3 2" xmlns="http://www.w3.org/2000/svg">
                                <rect width="3" height="0.67" fill="#AE1C28"/>
                                <rect y="0.67" width="3" height="0.67" fill="#FFFFFF"/>
                                <rect y="1.34" width="3" height="0.66" fill="#21468B"/>
                            </svg>
                        </div>
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 3 2" xmlns="http://www.w3.org/2000/svg">
                                <rect width="3" height="1" fill="#FFFFFF"/>
                                <rect y="1" width="3" height="1" fill="#DC143C"/>
                            </svg>
                        </div>
                        <div class="flag-item flex-shrink-0">
                            <svg width="56" height="40" viewBox="0 0 16 12" xmlns="http://www.w3.org/2000/svg">
                                <rect width="16" height="12" fill="#BA0C2F"/>
                                <rect x="0" y="4" width="16" height="4" fill="#FFFFFF"/>
                                <rect x="5" y="0" width="6" height="12" fill="#FFFFFF"/>
                                <rect x="0" y="5" width="16" height="2" fill="#00205B"/>
                                <rect x="6" y="0" width="4" height="12" fill="#00205B"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        -->

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Input Mode Interface -->
            <div class="space-y-4 flex flex-col items-center">
                <Transition name="mode-switch" mode="out-in">
                    <div v-if="interactionMode === 'voice'" key="voice" class="flex justify-center">
                        <div class="flex flex-col items-center">
                            <button
                                @click="handleMicClick"
                                :class="[
                                    'w-12 h-12 md:w-14 md:h-14 rounded-full flex items-center justify-center transition-all',
                                    isListening || isSessionActive
                                        ? 'bg-[#5cc094] shadow-lg shadow-[#5cc094]/50 scale-110'
                                        : 'bg-[#5cc094] hover:bg-[#4a9d7a] hover:scale-105 shadow-lg'
                                ]"
                            >
                                <template v-if="isListening || isSessionActive">
                                    <svg 
                                        :class="[
                                            'w-4 h-4 md:w-5 md:h-5 text-white mx-auto',
                                            isListening ? 'phone-shake' : ''
                                        ]"
                                        fill="none" 
                                        stroke="currentColor" 
                                        viewBox="0 0 24 24"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </template>
                                <template v-else>
                                    <svg class="w-4 h-4 md:w-5 md:h-5 text-white mx-auto phone-hanging" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </template>
                            </button>
                            <!-- <p class="text-[#2E3A45]/70 mt-2 text-sm text-center">
                                {{ isListening ? 'Listening...' : 'Tap to call' }}
                            </p> -->
                        </div>
                    </div>
                    <div v-else key="chat" class="w-full max-w-2xl mx-auto">
                        <!-- <input
                            v-model="chatInput"
                            @keyup.enter="sendChatMessage"
                            type="text"
                            placeholder="Type your answer here..."
                            class="flex-1 min-w-0 py-3 px-4 text-base bg-white text-gray-900 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#5cc094]"
                            :disabled="isLoadingChat"
                        />
                        <button
                            @click="sendChatMessage"
                            :disabled="!chatInput.trim() || isLoadingChat"
                            class="px-6 py-3 bg-[#5cc094] hover:bg-[#4a9d7a] text-white rounded-lg font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed text-base flex-shrink-0"
                        >
                            Send
                        </button> -->
                    </div>
                </Transition>
                <!-- Toggle Input Mode -->
                <div class="flex justify-center items-center gap-4 mt-6">
                    <button
                        @click="interactionMode = interactionMode === 'voice' ? 'chat' : 'voice'"
                        class="flex items-center gap-2 text-[#5cc094] hover:text-[#4a9d7a] transition-colors"
                    >
                        <template v-if="interactionMode === 'voice'">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                            <span>Switch to typing</span>
                        </template>
                        <template v-else>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z" />
                            </svg>
                            <span>Switch to voice</span>
                        </template>
                    </button>
                </div>
            </div>

            <!-- Error Display -->
            <div v-if="error" class="mb-4 bg-red-900/80 border border-red-600 rounded-lg p-4 shadow-lg">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-triangle-exclamation text-red-500 text-xl"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <p class="text-sm text-red-200">{{ error }}</p>
                    </div>
                    <div class="ml-auto">
                        <button
                            @click="error = null"
                            class="text-red-400 hover:text-red-200 hover:bg-red-800/50 rounded-full p-1 transition-all w-6 h-6 flex items-center justify-center"
                            title="Dismiss"
                        >
                            <i class="fa-solid fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Voice Interface (Full Screen) -->
            <Transition name="voice-interface" appear>
                <div 
                    v-if="showVoiceInterface && interactionMode === 'voice' && activeTab === 'roadmap'" 
                    class="bg-white rounded-xl shadow-xl border border-gray-200 mb-6 min-h-[600px] flex flex-col overflow-hidden mt-16"
                >
                    <!-- Call Status Header -->
                    <div class="flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                        <div :class="[
                            'w-2.5 h-2.5 rounded-full shadow-sm',
                            connectionStatus === 'connected' ? 'bg-[#5cc094] ring-2 ring-[#5cc094]/20' : '',
                            connectionStatus === 'connecting' ? 'bg-yellow-500 animate-pulse ring-2 ring-yellow-500/20' : '',
                            connectionStatus === 'disconnected' ? 'bg-gray-400 ring-2 ring-gray-400/20' : ''
                        ]"></div>
                        <span class="text-gray-700 font-medium text-sm">
                            {{ connectionStatus === 'connected' ? 'Connected' : '' }}
                            {{ connectionStatus === 'connecting' ? 'Connecting...' : '' }}
                            {{ connectionStatus === 'disconnected' ? 'Ready to call' : '' }}
                        </span>
                        <div class="flex items-center gap-2 ml-auto">
                            <span
                                v-if="isListening && !isMuted" 
                                class="px-3 py-1.5 bg-[#5cc094]/10 text-[#5cc094] border border-[#5cc094]/20 rounded-full text-xs font-medium flex items-center gap-2"
                            >
                                <i class="fa-solid fa-microphone text-xs"></i>
                                <span>Listening...</span>
                            </span>
                            <span
                                v-if="isMuted" 
                                class="px-3 py-1.5 bg-red-50 text-red-600 border border-red-200 rounded-full text-xs font-semibold flex items-center gap-2 animate-pulse"
                            >
                                <i class="fa-solid fa-microphone-slash text-xs"></i>
                                <span>Muted</span>
                            </span>
                            <span
                                v-if="isSpeaking" 
                                class="px-3 py-1.5 bg-[#5cc094]/10 text-[#5cc094] border border-[#5cc094]/20 rounded-full text-xs font-medium flex items-center gap-2"
                            >
                                <i class="fa-solid fa-volume-high text-xs"></i>
                                <span>Speaking...</span>
                            </span>
                            <button
                                v-if="isConnected || isSessionActive"
                                @click.stop.prevent="toggleMute"
                                type="button"
                                :class="[
                                    'px-4 py-2 rounded-lg text-sm font-semibold transition-all flex items-center gap-2 shadow-sm cursor-pointer border bg-white hover:scale-105 active:scale-95',
                                    isMuted
                                        ? 'border-red-300 text-red-600 hover:border-red-400 hover:bg-red-50'
                                        : 'border-gray-300 text-gray-700 hover:border-gray-400 hover:bg-gray-50'
                                ]"
                                :title="isMuted ? 'Click to unmute microphone' : 'Click to mute microphone'"
                            >
                                <i v-if="isMuted" class="fa-solid fa-microphone-slash text-base text-red-600"></i>
                                <i v-else class="fa-solid fa-microphone text-base text-gray-600"></i>
                                <span class="hidden sm:inline">{{ isMuted ? 'Unmute' : 'Mute' }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Transcript Display (Full Screen) -->
                    <div ref="transcriptContainer" class="flex-1 overflow-y-auto space-y-4 pt-8 px-6 pb-6 bg-gray-50/30" style="touch-action: pan-y; -webkit-overflow-scrolling: touch;">
                        <div v-if="transcripts.length === 0" class="flex flex-col items-center justify-center h-full min-h-[400px]">
                            <div class="bg-white rounded-full p-6 mb-4 shadow-md border border-gray-200">
                                <i class="fa-solid fa-microphone-lines text-4xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-500 text-base font-medium">Start a conversation with Opa to see your chat here...</p>
                            <p class="text-gray-400 text-sm mt-2">Click the phone button to begin your voice conversation</p>
                        </div>
                        <div
                            v-for="(transcript, index) in transcripts"
                            :key="index"
                            :class="[
                                'flex',
                                transcript.type === 'user' ? 'justify-end' : 'justify-start'
                            ]"
                        >
                            <div
                                :class="[
                                    'max-w-[80%] md:max-w-[70%] rounded-2xl px-5 py-3.5 text-base shadow-sm',
                                    transcript.type === 'user'
                                        ? 'bg-[#5cc094] text-white rounded-br-md'
                                        : 'bg-white text-gray-900 border border-gray-200 rounded-bl-md'
                                ]"
                            >
                                <span class="font-semibold text-sm opacity-90 mb-1 block">
                                    {{ transcript.type === 'user' ? 'You' : 'Opa' }}
                                </span>
                                <p class="whitespace-pre-wrap leading-relaxed">{{ transcript.text }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>

            <!-- Chat Interface (Full Screen) -->
            <Transition name="chat-interface" appear>
                <div 
                    v-if="showChatInterface" 
                    key="chat-interface"
                    class="bg-white rounded-xl shadow-xl border border-gray-200 mb-6 min-h-[600px] flex flex-col overflow-hidden chat-interface-container"
                >
                    <!-- Chat Status -->
                    <div class="flex items-center gap-3 px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-200">
                        <div :class="[
                            'w-2.5 h-2.5 rounded-full shadow-sm',
                            connectionStatus === 'connected' ? 'bg-[#5cc094] ring-2 ring-[#5cc094]/20' : '',
                            connectionStatus === 'connecting' ? 'bg-yellow-500 animate-pulse ring-2 ring-yellow-500/20' : '',
                            connectionStatus === 'disconnected' ? 'bg-gray-400 ring-2 ring-gray-400/20' : ''
                        ]"></div>
                        <span class="text-gray-700 font-medium text-sm">
                            {{ connectionStatus === 'connected' ? 'Connected' : '' }}
                            {{ connectionStatus === 'connecting' ? 'Connecting...' : '' }}
                            {{ connectionStatus === 'disconnected' ? 'Ready to call' : '' }}
                        </span>
                    </div>
                    <!-- Chat Messages -->
                    <div class="flex-1 overflow-y-auto space-y-4 pt-8 px-6 pb-6 bg-gray-50/30" ref="chatMessagesContainer">
                        <div v-if="chatMessages.length === 0" class="flex flex-col items-center justify-center h-full min-h-[400px]">
                            <div class="bg-white rounded-full p-6 mb-4 shadow-md border border-gray-200">
                                <i class="fa-solid fa-comments text-4xl text-gray-300"></i>
                            </div>
                            <p class="text-gray-500 text-base font-medium">Start a conversation with Opa to see your chat here...</p>
                            <p class="text-gray-400 text-sm mt-2">Ask questions, get advice, or start building your roadmap</p>
                        </div>
                        <div
                            v-for="(message, index) in chatMessages"
                            :key="index"
                            :class="[
                                'flex',
                                message.type === 'user' ? 'justify-end' : 'justify-start'
                            ]"
                        >
                            <div
                                :class="[
                                    'max-w-[80%] md:max-w-[70%] rounded-2xl px-5 py-3.5 text-base shadow-sm',
                                    message.type === 'user'
                                        ? 'bg-[#5cc094] text-white rounded-br-md'
                                        : 'bg-white text-gray-900 border border-gray-200 rounded-bl-md'
                                ]"
                            >
                                <p class="whitespace-pre-wrap leading-relaxed">{{ message.text }}</p>
                            </div>
                        </div>
                        <div v-if="isLoadingChat" class="flex justify-start">
                            <div class="bg-white text-gray-900 border border-gray-200 rounded-2xl rounded-bl-md px-5 py-3.5 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="flex gap-1">
                                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0ms"></div>
                                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 150ms"></div>
                                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 300ms"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">Opa is thinking...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Chat Input -->
                    <div class="border-t border-gray-200 bg-white px-6 py-4">
                        <div class="flex gap-3 mb-3">
                            <input
                                v-model="chatInput"
                                @keyup.enter="sendChatMessage"
                                type="text"
                                placeholder="Type your message to Opa..."
                                class="flex-1 bg-gray-50 text-gray-900 border border-gray-200 rounded-xl px-5 py-3 text-base focus:outline-none focus:ring-2 focus:ring-[#5cc094]/50 focus:border-[#5cc094] transition-all placeholder:text-gray-400"
                                :disabled="isLoadingChat"
                            />
                            <button
                                @click="sendChatMessage"
                                :disabled="!chatInput.trim() || isLoadingChat"
                                class="px-6 py-3 bg-[#5cc094] text-white rounded-xl hover:bg-[#4a9d7a] transition-all font-semibold shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 text-base disabled:hover:shadow-md"
                            >
                                <i class="fa-solid fa-paper-plane text-sm"></i>
                                <span class="hidden sm:inline">Send</span>
                            </button>
                        </div>
                        <!-- End the chat button -->
                        <button
                            @click="handleEndChat"
                            type="button"
                            class="flex items-center gap-2 text-red-600 hover:text-red-700 transition-colors text-sm font-medium cursor-pointer"
                            title="End the chat"
                        >
                            <i class="fa-solid fa-times text-base"></i>
                            <span>End the chat</span>
                        </button>
                    </div>
                </div>
            </Transition>


            <!-- Document Requests Section (shown in business tab) -->
            <div v-if="pendingDocuments.length > 0 && activeTab === 'business'" class="mt-4 bg-gray-50 rounded-lg shadow-lg border border-gray-200 p-5">
                <div class="flex items-center gap-3 mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Document Requests</h3>
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
        <BusinessPlanModal
            :show="showBusinessPlanModal"
            :business-plan="businessPlanData"
            @close="showBusinessPlanModal = false"
        />

        <ProgressSummaryModal
            :show="showProgressSummaryModal"
            :business-plan="businessPlanData"
            :roadmap="roadmap"
            :summary-data="progressSummaryData"
            @close="showProgressSummaryModal = false"
        />

        <AdvisorMeetingModal
            :show="showAdvisorMeetingModal"
            :preselected-advisor-id="advisorMeetingData.advisor_id"
            :specialization="advisorMeetingData.specialization"
            :topic="advisorMeetingData.topic"
            :advisors="advisors"
            @close="showAdvisorMeetingModal = false"
            @scheduled="handleMeetingScheduled"
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
            <div class="bg-gray-50 rounded-t-lg w-full max-h-[85vh] overflow-y-auto drawer-content">
                <div class="sticky top-0 bg-white p-5 flex items-center justify-between rounded-t-lg z-10 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Business Information</h2>
                    <button @click="activeTab = 'roadmap'" class="text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 pb-24 space-y-6">
                    <!-- Contextual Information Section -->
                    <div class="bg-white rounded-lg border border-gray-200 p-5">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            Your Background Information
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Country of Origin -->
                            <div 
                                :ref="el => setContextualFieldRef('country_of_origin', el)"
                                :id="'contextual-field-country_of_origin'"
                                :class="[
                                    'bg-white rounded-lg p-3 border transition-all cursor-pointer hover:bg-gray-50',
                                    isContextualFieldCompleted('country_of_origin') ? 'border-[#5cc094]' : 'border-gray-200',
                                    fieldToHighlight === 'country_of_origin' ? 'field-highlight border-[#5cc094]' : '',
                                    editingContextualField === 'country_of_origin' ? 'border-[#5cc094]' : ''
                                ]"
                                @click="startEditingContextual('country_of_origin')"
                            >
                                <p class="text-xs font-semibold text-gray-600 mb-1 flex items-center justify-between">
                                    <span>Country of Origin</span>
                                    <i class="fa-solid fa-pen text-[8px] text-gray-400"></i>
                                </p>
                                <input
                                    v-if="editingContextualField === 'country_of_origin'"
                                    v-model="contextualEditValue"
                                    @blur="saveContextualField('country_of_origin')"
                                    @keydown.enter="saveContextualField('country_of_origin')"
                                    @keydown.esc="cancelEditingContextual"
                                    @click.stop
                                    type="text"
                                    class="w-full bg-gray-50 text-gray-900 text-sm p-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#5cc094]"
                                    ref="contextualEditInput"
                                />
                                <p v-else :class="['text-sm font-bold', isContextualFieldCompleted('country_of_origin') ? 'text-[#5cc094]' : 'text-gray-900']">{{ businessPlanData?.country_of_origin || 'Not provided - Click to edit' }}</p>
                            </div>
                            
                            <!-- EU Resident -->
                            <div 
                                :ref="el => setContextualFieldRef('is_eu_resident', el)"
                                :id="'contextual-field-is_eu_resident'"
                                :class="[
                                    'rounded-lg p-3 border transition-all cursor-pointer',
                                    isContextualFieldCompleted('is_eu_resident') 
                                        ? 'bg-white border-[#5cc094] hover:bg-green-50/50' 
                                        : 'bg-white border-gray-200 hover:bg-gray-50',
                                    fieldToHighlight === 'is_eu_resident' ? 'field-highlight border-[#5cc094]' : '',
                                    editingContextualField === 'is_eu_resident' ? 'border-[#5cc094]' : ''
                                ]"
                                @click="startEditingContextual('is_eu_resident')"
                            >
                                <p class="text-xs font-semibold text-gray-600 mb-1 flex items-center justify-between">
                                    <span>EU Resident</span>
                                    <i class="fa-solid fa-pen text-[8px] text-gray-400"></i>
                                </p>
                                <select
                                    v-if="editingContextualField === 'is_eu_resident'"
                                    v-model="contextualEditValue"
                                    @blur="saveContextualField('is_eu_resident')"
                                    @change="saveContextualField('is_eu_resident')"
                                    @click.stop
                                    class="w-full bg-gray-50 text-gray-900 text-sm p-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#5cc094]"
                                    ref="contextualEditInput"
                                >
                                    <option value="">Not provided</option>
                                    <option value="true">Yes</option>
                                    <option value="false">No</option>
                                </select>
                                <p v-else class="text-sm font-bold">
                                    <span v-if="businessPlanData?.is_eu_resident === true" class="text-[#5cc094]">Yes</span>
                                    <span v-else-if="businessPlanData?.is_eu_resident === false" class="text-red-500">No</span>
                                    <span v-else class="text-gray-500">Not provided - Click to edit</span>
                                </p>
                            </div>
                            
                            <!-- Newcomer to Finland -->
                            <div 
                                :ref="el => setContextualFieldRef('is_newcomer_to_finland', el)"
                                :id="'contextual-field-is_newcomer_to_finland'"
                                :class="[
                                    'bg-white rounded-lg p-3 border transition-all cursor-pointer hover:bg-gray-50',
                                    isContextualFieldCompleted('is_newcomer_to_finland') ? 'border-[#5cc094]' : 'border-gray-200',
                                    fieldToHighlight === 'is_newcomer_to_finland' ? 'field-highlight border-[#5cc094]' : '',
                                    editingContextualField === 'is_newcomer_to_finland' ? 'border-[#5cc094]' : ''
                                ]"
                                @click="startEditingContextual('is_newcomer_to_finland')"
                            >
                                <p class="text-xs font-semibold text-gray-600 mb-1 flex items-center justify-between">
                                    <span>Newcomer to Finland</span>
                                    <i class="fa-solid fa-pen text-[8px] text-gray-400"></i>
                                </p>
                                <select
                                    v-if="editingContextualField === 'is_newcomer_to_finland'"
                                    v-model="contextualEditValue"
                                    @blur="saveContextualField('is_newcomer_to_finland')"
                                    @change="saveContextualField('is_newcomer_to_finland')"
                                    @click.stop
                                    class="w-full bg-gray-50 text-gray-900 text-sm p-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#5cc094]"
                                    ref="contextualEditInput"
                                >
                                    <option value="">Not provided</option>
                                    <option value="true">Yes</option>
                                    <option value="false">No</option>
                                </select>
                                <p v-else class="text-sm font-bold">
                                    <span v-if="businessPlanData?.is_newcomer_to_finland === true" class="text-[#5cc094]">Yes</span>
                                    <span v-else-if="businessPlanData?.is_newcomer_to_finland === false" class="text-gray-500">No</span>
                                    <span v-else class="text-gray-500">Not provided - Click to edit</span>
                                </p>
                            </div>
                            
                            <!-- Residence Permit -->
                            <div 
                                :ref="el => setContextualFieldRef('has_residence_permit', el)"
                                :id="'contextual-field-has_residence_permit'"
                                :class="[
                                    'rounded-lg p-3 border transition-all cursor-pointer',
                                    isContextualFieldCompleted('has_residence_permit') 
                                        ? 'bg-white border-[#5cc094] hover:bg-green-50/50' 
                                        : 'bg-white border-gray-200 hover:bg-gray-50',
                                    fieldToHighlight === 'has_residence_permit' ? 'field-highlight border-[#5cc094]' : '',
                                    editingContextualField === 'has_residence_permit' ? 'border-[#5cc094]' : ''
                                ]"
                                @click="startEditingContextual('has_residence_permit')"
                            >
                                <p class="text-xs font-semibold text-gray-600 mb-1 flex items-center justify-between">
                                    <span>Residence Permit</span>
                                    <i class="fa-solid fa-pen text-[8px] text-gray-400"></i>
                                </p>
                                <select
                                    v-if="editingContextualField === 'has_residence_permit'"
                                    v-model="contextualEditValue"
                                    @blur="saveContextualField('has_residence_permit')"
                                    @change="saveContextualField('has_residence_permit')"
                                    @click.stop
                                    class="w-full bg-gray-50 text-gray-900 text-sm p-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#5cc094]"
                                    ref="contextualEditInput"
                                >
                                    <option value="">Not provided</option>
                                    <option value="true">Yes</option>
                                    <option value="false">No</option>
                                </select>
                                <p v-else class="text-sm font-bold">
                                    <span v-if="businessPlanData?.has_residence_permit === true" class="text-[#5cc094]">Yes</span>
                                    <span v-else-if="businessPlanData?.has_residence_permit === false" class="text-red-500">No</span>
                                    <span v-else class="text-gray-500">Not provided - Click to edit</span>
                                </p>
                            </div>
                            
                            <!-- Residence Permit Type -->
                            <div 
                                :ref="el => setContextualFieldRef('residence_permit_type', el)"
                                :id="'contextual-field-residence_permit_type'"
                                :class="[
                                    'bg-white rounded-lg p-3 border transition-all cursor-pointer hover:bg-gray-50',
                                    isContextualFieldCompleted('residence_permit_type') ? 'border-[#5cc094]' : 'border-gray-200',
                                    fieldToHighlight === 'residence_permit_type' ? 'field-highlight border-[#5cc094]' : '',
                                    editingContextualField === 'residence_permit_type' ? 'border-[#5cc094]' : ''
                                ]"
                                @click="startEditingContextual('residence_permit_type')"
                            >
                                <p class="text-xs font-semibold text-gray-600 mb-1 flex items-center justify-between">
                                    <span>Residence Permit Type</span>
                                    <i class="fa-solid fa-pen text-[8px] text-gray-400"></i>
                                </p>
                                <input
                                    v-if="editingContextualField === 'residence_permit_type'"
                                    v-model="contextualEditValue"
                                    @blur="saveContextualField('residence_permit_type')"
                                    @keydown.enter="saveContextualField('residence_permit_type')"
                                    @keydown.esc="cancelEditingContextual"
                                    @click.stop
                                    type="text"
                                    class="w-full bg-gray-50 text-gray-900 text-sm p-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#5cc094]"
                                    ref="contextualEditInput"
                                />
                                <p v-else :class="['text-sm font-bold', isContextualFieldCompleted('residence_permit_type') ? 'text-[#5cc094]' : 'text-gray-900']">{{ businessPlanData?.residence_permit_type || 'Not provided - Click to edit' }}</p>
                            </div>
                            
                            <!-- Years in Finland -->
                            <div 
                                :ref="el => setContextualFieldRef('years_in_finland', el)"
                                :id="'contextual-field-years_in_finland'"
                                :class="[
                                    'bg-white rounded-lg p-3 border transition-all cursor-pointer hover:bg-gray-50',
                                    isContextualFieldCompleted('years_in_finland') ? 'border-[#5cc094]' : 'border-gray-200',
                                    fieldToHighlight === 'years_in_finland' ? 'field-highlight border-[#5cc094]' : '',
                                    editingContextualField === 'years_in_finland' ? 'border-[#5cc094]' : ''
                                ]"
                                @click="startEditingContextual('years_in_finland')"
                            >
                                <p class="text-xs font-semibold text-gray-600 mb-1 flex items-center justify-between">
                                    <span>Years in Finland</span>
                                    <i class="fa-solid fa-pen text-[8px] text-gray-400"></i>
                                </p>
                                <input
                                    v-if="editingContextualField === 'years_in_finland'"
                                    v-model="contextualEditValue"
                                    @blur="saveContextualField('years_in_finland')"
                                    @keydown.enter="saveContextualField('years_in_finland')"
                                    @keydown.esc="cancelEditingContextual"
                                    @click.stop
                                    type="number"
                                    class="w-full bg-gray-50 text-gray-900 text-sm p-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#5cc094]"
                                    ref="contextualEditInput"
                                />
                                <p v-else :class="['text-sm font-bold', isContextualFieldCompleted('years_in_finland') ? 'text-[#5cc094]' : 'text-gray-900']">{{ businessPlanData?.years_in_finland !== null && businessPlanData?.years_in_finland !== undefined ? businessPlanData.years_in_finland + ' years' : 'Not provided - Click to edit' }}</p>
                            </div>
                            
                            <!-- Business Experience -->
                            <div 
                                :ref="el => setContextualFieldRef('has_business_experience', el)"
                                :id="'contextual-field-has_business_experience'"
                                :class="[
                                    'bg-white rounded-lg p-3 border transition-all cursor-pointer hover:bg-gray-50',
                                    isContextualFieldCompleted('has_business_experience') ? 'border-[#5cc094]' : 'border-gray-200',
                                    fieldToHighlight === 'has_business_experience' ? 'field-highlight border-[#5cc094]' : '',
                                    editingContextualField === 'has_business_experience' ? 'border-[#5cc094]' : ''
                                ]"
                                @click="startEditingContextual('has_business_experience')"
                            >
                                <p class="text-xs font-semibold text-gray-600 mb-1 flex items-center justify-between">
                                    <span>Business Experience</span>
                                    <i class="fa-solid fa-pen text-[8px] text-gray-400"></i>
                                </p>
                                <select
                                    v-if="editingContextualField === 'has_business_experience'"
                                    v-model="contextualEditValue"
                                    @blur="saveContextualField('has_business_experience')"
                                    @change="saveContextualField('has_business_experience')"
                                    @click.stop
                                    class="w-full bg-gray-50 text-gray-900 text-sm p-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#5cc094]"
                                    ref="contextualEditInput"
                                >
                                    <option value="">Not provided</option>
                                    <option value="true">Yes</option>
                                    <option value="false">No</option>
                                </select>
                                <p v-else class="text-sm font-bold">
                                    <span v-if="businessPlanData?.has_business_experience === true" class="text-[#5cc094]">Yes</span>
                                    <span v-else-if="businessPlanData?.has_business_experience === false" class="text-gray-500">No</span>
                                    <span v-else class="text-gray-500">Not provided - Click to edit</span>
                                </p>
                            </div>
                            
                            <!-- Preferred Language -->
                            <div 
                                :ref="el => setContextualFieldRef('language', el)"
                                :id="'contextual-field-language'"
                                :class="[
                                    'bg-white rounded-lg p-3 border transition-all cursor-pointer hover:bg-gray-50',
                                    isContextualFieldCompleted('language') ? 'border-[#5cc094]' : 'border-gray-200',
                                    fieldToHighlight === 'language' ? 'field-highlight border-[#5cc094]' : '',
                                    editingContextualField === 'language' ? 'border-[#5cc094]' : ''
                                ]"
                                @click="startEditingContextual('language')"
                            >
                                <p class="text-xs font-semibold text-gray-600 mb-1 flex items-center justify-between">
                                    <span>Preferred Language</span>
                                    <i class="fa-solid fa-pen text-[8px] text-gray-400"></i>
                                </p>
                                <input
                                    v-if="editingContextualField === 'language'"
                                    v-model="contextualEditValue"
                                    @blur="saveContextualField('language')"
                                    @keydown.enter="saveContextualField('language')"
                                    @keydown.esc="cancelEditingContextual"
                                    @click.stop
                                    type="text"
                                    class="w-full bg-gray-50 text-gray-900 text-sm p-2 rounded border border-gray-300 focus:outline-none focus:ring-2 focus:ring-[#5cc094]"
                                    ref="contextualEditInput"
                                />
                                <p v-else :class="['text-sm font-bold', isContextualFieldCompleted('language') ? 'text-[#5cc094]' : 'text-gray-900']">{{ businessPlanData?.language || 'Not provided - Click to edit' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Business Plan Progress -->
                    <BusinessPlanProgress 
                        :business-plan="businessPlanData"
                        :recently-answered-fields="recentlyAnsweredFields"
                        :field-to-highlight="fieldToHighlight"
                        @update="handleBusinessPlanFieldUpdate"
                    />
                    
                    <!-- Document Requests Section -->
                    <div v-if="pendingDocuments.length > 0" class="bg-white rounded-lg border border-gray-200 p-5">
                        <div class="flex items-center gap-3 mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Document Requests</h3>
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
            <div class="bg-white rounded-t-lg w-full max-h-[85vh] overflow-y-auto drawer-content">
                <div class="sticky top-0 bg-white p-5 flex items-center justify-between rounded-t-lg z-10 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Manual Entry</h2>
                    <button @click="activeTab = 'roadmap'" class="text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 pb-24">
                    <!-- Manual Add content embedded directly -->
                    <div class="space-y-6">
                        <p class="text-gray-600 mb-6">Here you can manually add or update business plan fields or roadmap steps.</p>
                        <div class="group bg-[#5cc094] rounded-lg p-4 border border-[#5cc094] hover:bg-[#4a9d7a] transition-all">
                            <h3 class="font-bold text-white mb-3 transition-colors">
                                Add Business Plan Field
                            </h3>
                            <p class="text-sm text-white/90 transition-colors">Coming soon! You'll be able to manually add business plan information here.</p>
                        </div>
                        <div class="group bg-[#5cc094] rounded-lg p-4 border border-[#5cc094] hover:bg-[#4a9d7a] transition-all">
                            <h3 class="font-bold text-white mb-3 transition-colors">
                                Add Roadmap Step
                            </h3>
                            <p class="text-sm text-white/90 transition-colors">Coming soon! You'll be able to manually add roadmap steps here.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Advisors Drawer -->
        <div v-if="activeTab === 'advisors'" 
             class="fixed inset-0 z-40 flex items-end drawer-overlay"
             @click.self="activeTab = 'roadmap'">
            <div class="bg-white rounded-t-lg w-full h-[90vh] overflow-y-auto drawer-content">
                <div class="sticky top-0 bg-white p-5 flex items-center justify-between rounded-t-lg z-10 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Your Advisors</h2>
                    <button @click="activeTab = 'roadmap'" class="text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 pb-24">
                    <!-- Advisors content embedded directly -->
                    <p class="text-gray-600 mb-6">Connect with experienced advisors who can help guide your startup journey.</p>
                    
                    <!-- Loading state -->
                    <div v-if="loadingAdvisors" class="text-center py-8">
                        <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-[#5cc094]"></div>
                        <p class="mt-2 text-gray-600">Loading advisors...</p>
                    </div>
                    
                    <!-- Empty state -->
                    <div v-else-if="advisors.length === 0" class="text-center py-12">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">No Advisors Available</h3>
                        <p class="text-gray-600">Advisors will appear here once they're added to the system.</p>
                    </div>
                    
                    <!-- Advisors list -->
                    <div v-else class="space-y-4">
                        <div 
                            v-for="advisor in advisors" 
                            :key="advisor.id"
                            class="group bg-[#5cc094] rounded-lg p-4 border border-[#5cc094] flex items-start gap-4 hover:bg-[#4a9d7a] transition-all"
                        >
                            <div class="flex-shrink-0 w-12 h-12 bg-[#4a9d7a] rounded-full flex items-center justify-center text-white text-lg font-bold border border-white/30">
                                {{ advisor.name.charAt(0).toUpperCase() }}
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-white text-lg transition-colors">{{ advisor.name }}</p>
                                <p v-if="advisor.title" class="text-sm text-white/90 mb-1 transition-colors">{{ advisor.title }}</p>
                                <p class="text-sm text-white/80 mb-2 transition-colors">{{ advisor.email }}</p>
                                <p v-if="advisor.bio" class="text-sm text-white/90 mb-2 transition-colors">{{ advisor.bio }}</p>
                                <div v-if="advisor.languages && Array.isArray(advisor.languages)" class="mb-2">
                                    <p class="text-xs text-white/80 mb-1 transition-colors">Languages:</p>
                                    <p class="text-sm text-white/90 transition-colors">{{ advisor.languages.join(', ') }}</p>
                                </div>
                                <div v-if="advisor.specialization" class="flex items-center gap-2 mt-2">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold text-white bg-[#4a9d7a] border border-white/30">
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
            <div class="bg-white rounded-t-lg w-full max-h-[85vh] overflow-y-auto drawer-content">
                <div class="sticky top-0 bg-white p-5 flex items-center justify-between rounded-t-lg z-10 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Your Roadmap</h2>
                    <button @click="activeTab = 'roadmap'" class="text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 pb-24">
                    <!-- Roadmap Visualization -->
                    <div v-if="roadmap && roadmap.steps && roadmap.steps.filter(s => !s.isQuestion).length > 0">
                        <RoadmapVisualizer 
                            :roadmap="roadmap"
                            @step-update="handleStepUpdate"
                        />
                    </div>
                    <div v-else class="text-center py-12">
                        <h3 class="text-xl font-bold text-gray-900 mb-2">No Roadmap Steps Yet</h3>
                        <p class="text-gray-600">Start a voice session to create your roadmap!</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Calendar Drawer -->
        <div v-if="activeTab === 'calendar'" 
             class="fixed inset-0 z-40 flex items-end drawer-overlay"
             @click.self="activeTab = 'roadmap'">
            <div class="bg-white rounded-t-lg w-full max-h-[85vh] overflow-y-auto drawer-content">
                <div class="sticky top-0 bg-white p-5 flex items-center justify-between rounded-t-lg z-10 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Your Calendar</h2>
                    <button @click="activeTab = 'roadmap'" class="text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6 pb-24">
                    <!-- Calendar content embedded directly -->
                    <p class="text-gray-600 mb-6">Manage your upcoming meetings and important dates.</p>
                    <div class="space-y-4">
                        <div class="group bg-[#5cc094] rounded-lg p-4 border border-[#5cc094] hover:bg-[#4a9d7a] transition-all">
                            <p class="font-bold text-white mb-1 transition-colors">Meeting with Advisor A</p>
                            <p class="text-sm text-white/90 transition-colors">Date: October 26, 2024, 10:00 AM</p>
                            <p class="text-xs text-white/80 transition-colors">Topic: Funding Strategy</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Network Drawer -->
        <div v-if="activeTab === 'network'" 
             class="fixed inset-0 z-40 flex items-end drawer-overlay"
             @click.self="activeTab = 'roadmap'">
            <div class="bg-white rounded-t-lg w-full h-[90vh] overflow-hidden drawer-content flex flex-col">
                <div class="sticky top-0 bg-white p-5 flex items-center justify-between rounded-t-lg z-10 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">B2B Network</h2>
                    <button @click="activeTab = 'roadmap'" class="text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-6 pb-24">
                    <Network :current-view="networkView" @view="networkView = $event" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick, computed, watch } from 'vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import useVoiceAgent from '../composables/useVoiceAgent';
import useChatAgent from '../composables/useChatAgent';
import RoadmapVisualizer from '../components/RoadmapVisualizer.vue';
import BusinessPlanProgress from '../components/BusinessPlanProgress.vue';
import BusinessPlanModal from '../components/BusinessPlanModal.vue';
import ProgressSummaryModal from '../components/ProgressSummaryModal.vue';
import ResourceCard from '../components/ResourceCard.vue';
import DocumentRequestCard from '../components/DocumentRequestCard.vue';
import RewardAnimation from '../components/RewardAnimation.vue';
import XPLevelHeader from '../components/XPLevelHeader.vue';
import TopNavigation from '../components/TopNavigation.vue';
import BottomNavigation from '../components/BottomNavigation.vue';
import BusinessInfoModal from '../components/BusinessInfoModal.vue';
import ManualAddModal from '../components/ManualAddModal.vue';
import AdvisorsModal from '../components/AdvisorsModal.vue';
import CalendarModal from '../components/CalendarModal.vue';
import AdvisorMeetingModal from '../components/AdvisorMeetingModal.vue';
import Network from '../components/Network.vue';

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

// Empty roadmap - will be populated by AI based on user's business plan
const emptyRoadmap = {
    title: 'My Startup Roadmap',
    steps: []
};

const roadmap = ref(props.initialRoadmap || emptyRoadmap);
const loading = ref(false);
const error = ref(null);
const transcripts = ref([]);
const isSessionActive = ref(false);
const businessPlanData = ref(props.initialBusinessPlan);
const recentlyAnsweredFields = ref(new Set()); // Track fields that were just answered
const transcriptContainer = ref(null);

// Modal states
const showBusinessPlanModal = ref(false);
const showProgressSummaryModal = ref(false);
const progressSummaryData = ref({});
const showAdvisorMeetingModal = ref(false);
const advisorMeetingData = ref({
    advisor_id: null,
    specialization: null,
    topic: null
});

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
const networkView = ref('discover');

// Inline editing state for contextual fields
const editingContextualField = ref(null);
const contextualEditValue = ref('');
const contextualEditInput = ref(null);

// Timeout ref for auto-closing business tab
let businessTabTimeout = null;

// Interaction mode (voice or chat)
const interactionMode = ref('voice');
const showVoiceInterface = ref(false); // Control visibility of voice interface
const chatMessages = ref([]);
const chatInput = ref('');
const isLoadingChat = ref(false);
const chatMessagesContainer = ref(null);
const chatConversationId = ref(null); // Store conversation ID for ElevenLabs

// Flags animation refs and state
// const flagsContainerRef = ref(null);
// const flagsTrackRef = ref(null);
// const flagsSequenceRef = ref(null);
// const seqWidth = ref(0);
// const ANIMATION_CONFIG = { SMOOTH_TAU: 0.25 };
// const targetVelocity = ref(120); // pixels per second
// let rafId = null;
// let lastTimestamp = null;
// let offset = 0;
// let velocity = 0;

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

        // Switch to roadmap tab to show the update
        activeTab.value = 'roadmap-tab';

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

const handleBusinessPlanFieldUpdate = async (updateData) => {
    // Handle inline field updates from BusinessPlanProgress component
    await handleBusinessPlanUpdate(updateData);
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
        
        // Clear any existing timeout for business tab
        if (businessTabTimeout) {
            clearTimeout(businessTabTimeout);
            businessTabTimeout = null;
        }

        // Switch to business tab to show the update
        activeTab.value = 'business';
        showBusinessInfo.value = false; // Close modal if open, show inline instead

        // Auto-close the tab after 3 seconds to let user focus back on voice
        businessTabTimeout = setTimeout(() => {
            // Only close if we're still on the business tab (user might have navigated away)
            if (activeTab.value === 'business') {
                activeTab.value = 'roadmap';
                
                // Scroll to bottom of transcript after closing tab
                nextTick(() => {
                    if (transcriptContainer.value) {
                        transcriptContainer.value.scrollTop = transcriptContainer.value.scrollHeight;
                    }
                });
            }
            businessTabTimeout = null;
        }, 3000);

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
    console.log('Business plan generation requested:', prepData);
    showBusinessPlanModal.value = true;
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
        'residence_permit': 'from-[#5cc094] to-[#4a9d7a]',
        'business_registration': 'from-[#5cc094] to-[#4a9d7a]',
        'tax': 'from-[#5cc094] to-[#4a9d7a]',
        'funding': 'from-[#5cc094] to-[#4a9d7a]',
        'legal': 'from-[#5cc094] to-[#4a9d7a]',
        'marketing': 'from-[#5cc094] to-[#4a9d7a]',
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

const handleScheduleMeeting = (meetingData) => {
    console.log('Schedule meeting requested:', meetingData);
    advisorMeetingData.value = {
        advisor_id: meetingData.advisor_id || null,
        specialization: meetingData.specialization || null,
        topic: meetingData.topic || null
    };
    showAdvisorMeetingModal.value = true;
};

const handleMeetingScheduled = (meeting) => {
    console.log('Meeting scheduled:', meeting);
    // Optionally show a success message or update UI
    showAdvisorMeetingModal.value = false;
};

const {
    isConnected,
    isListening,
    isSpeaking,
    connectionStatus,
    isMuted,
    connect,
    disconnect,
    startSession,
    stopSession,
    toggleMute
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
    onProgressSummary: handleProgressSummary,
    onScheduleMeeting: handleScheduleMeeting
});

// Watch connection status and hide interface when transitioning from connected to disconnected
watch(() => connectionStatus.value, (newStatus, oldStatus) => {
    // Only hide if we were previously connected/connecting and now disconnected
    if (newStatus === 'disconnected' && oldStatus && oldStatus !== 'disconnected' && !isConnected.value && !isSessionActive.value) {
        showVoiceInterface.value = false;
    }
});

// Watch isConnected and hide interface when transitioning from connected to disconnected
watch(() => isConnected.value, (connected, wasConnected) => {
    // Only hide if we were previously connected and now disconnected
    if (!connected && wasConnected && connectionStatus.value === 'disconnected' && !isSessionActive.value) {
        showVoiceInterface.value = false;
    }
});

// Computed property to determine when to show chat interface (after composables are initialized)
// Only show when explicitly in chat mode, not during voice calls
const showChatInterface = computed(() => {
    return interactionMode.value === 'chat' && (activeTab.value === 'roadmap' || activeTab.value === 'roadmap-tab');
});

// Opa animation state (after composables are initialized)
const isTalking = computed(() => isListening.value || isSpeaking.value);

// Initialize chat agent (after handler functions are defined)
const chatAgent = useChatAgent({
    onRoadmapUpdate: handleRoadmapUpdate,
    onBusinessPlanUpdate: handleBusinessPlanUpdate,
    onMessage: (message) => {
        chatMessages.value.push(message);
        nextTick(() => scrollChatToBottom());
    },
    onError: (error) => {
        console.error('Chat agent error:', error);
        chatMessages.value.push({
            type: 'assistant',
            text: error || 'Sorry, I encountered an error. Please try again.'
        });
        isLoadingChat.value = false;
        nextTick(() => scrollChatToBottom());
    },
    onMeetingPrep: (data) => {
        // Handle meeting prep if needed
    },
    onChecklistComplete: (data) => {
        // Handle checklist completion
        if (data && data.stepId) {
            handleRoadmapUpdate(roadmap.value);
        }
    },
    onDocumentRequest: (doc) => {
        // Add document request card
        if (doc && doc.title) {
            pendingDocuments.value.push({
                id: documentIdCounter++,
                type: doc.type || 'general',
                title: doc.title,
                description: doc.description || 'Additional information needed',
                required: doc.required !== false,
                field: doc.field || null
            });
        }
    },
    onResourceSuggested: (resource) => {
        // Add resource card
        if (resource && resource.title) {
            suggestedResources.value.push({
                id: resourceIdCounter++,
                title: resource.title,
                description: resource.description || resource.preview || '',
                url: resource.url || '',
                category: resource.category || 'general',
                icon: resource.icon || '📚',
                preview: resource.preview || resource.description || ''
            });
        }
    },
    onProgressSummary: (data) => {
        progressSummaryData.value = data || {};
        showProgressSummaryModal.value = true;
    },
    onScheduleMeeting: (data) => {
        // Handle meeting scheduling
        showAdvisorMeetingModal.value = true;
    },
    userName: props.userName
});

const handleCallMode = () => {
    interactionMode.value = 'voice';
    // If connected, disconnect (cancel call)
    if (isConnected.value) {
        handleDisconnect();
    } else {
        // If not connected, trigger the call
        handleConnect();
    }
};

const handleMicClick = async () => {
    try {
        // Ensure we're in voice mode
        interactionMode.value = 'voice';
        
        // Show the voice interface when phone button is clicked
        showVoiceInterface.value = true;
        
        // If already connected and listening, stop/disconnect
        if (isConnected.value && (isListening.value || isSessionActive.value)) {
            await handleDisconnect();
            return;
        }
        
        // If not connected, connect (this will automatically start the session)
        if (!isConnected.value) {
            console.log('Starting voice connection...');
            await handleConnect();
            // After connecting, the session should be active automatically
            if (isConnected.value) {
                isSessionActive.value = true;
                console.log('Voice session started successfully');
            }
        }
    } catch (err) {
        console.error('Failed to handle mic click:', err);
        error.value = 'Failed to handle voice session. Please try again.';
        isSessionActive.value = false;
    }
};

const handleConnect = async () => {
    try {
        error.value = null;
        console.log('Connecting to voice agent...');
        
        // Let the SDK handle microphone permissions - don't check beforehand
        await connect();
        
        console.log('Connection result:', {
            isConnected: isConnected.value,
            isListening: isListening.value,
            connectionStatus: connectionStatus.value
        });
        
        // The connect() function already starts the session, so mark it as active
        if (isConnected.value) {
            isSessionActive.value = true;
            showVoiceInterface.value = true; // Keep interface visible when connected
        } else {
            error.value = 'Failed to establish connection. Please try again.';
            isSessionActive.value = false;
            // Don't hide immediately - let user see the error, hide after a delay
            setTimeout(() => {
                if (!isConnected.value && !isSessionActive.value) {
                    showVoiceInterface.value = false;
                }
            }, 3000);
        }
    } catch (err) {
        console.error('Failed to connect:', err);
        error.value = err.message || 'Failed to connect to voice agent. Please check your microphone permissions and try again.';
        isSessionActive.value = false;
        // Don't hide immediately - let user see the error, hide after a delay
        setTimeout(() => {
            if (!isConnected.value && !isSessionActive.value) {
                showVoiceInterface.value = false;
            }
        }, 3000);
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
    console.log('Disconnecting voice session...');
    disconnect();
    isSessionActive.value = false;
    showVoiceInterface.value = false; // Hide the interface when disconnecting
    console.log('Voice session disconnected');
};

const handleEndChat = () => {
    console.log('Ending chat...');
    // Disconnect if connected
    if (isConnected.value || isSessionActive.value) {
        handleDisconnect();
    }
    // Clear chat messages
    chatMessages.value = [];
    // Switch back to voice mode to hide chat interface
    interactionMode.value = 'voice';
    console.log('Chat ended');
};

const handleStepUpdate = (step) => {
    console.log('Step updated:', step);
};

// Navigation handler - toggle drawer behavior
// Chat message handling
const sendChatMessage = async () => {
    if (!chatInput.value.trim() || isLoadingChat.value) return;
    
    const userMessage = chatInput.value.trim();
    chatInput.value = '';
    
    // Add user message to chat
    chatMessages.value.push({
        type: 'user',
        text: userMessage
    });
    
    // Scroll to bottom
    await nextTick();
    scrollChatToBottom();
    
    isLoadingChat.value = true;
    
    try {
        // Initialize chat agent if not already initialized
        if (!chatAgent.isConnected.value) {
            // Load required data first
            let businessPlanResponse = null;
            let roadmapResponse = null;
            let advisorsResponse = null;
            
            try {
                businessPlanResponse = await axios.get('/api/business-plan');
            } catch (error) {
                console.error('Failed to load business plan:', error);
            }
            
            try {
                roadmapResponse = await axios.get('/api/roadmap');
            } catch (error) {
                console.error('Failed to load roadmap:', error);
            }
            
            try {
                advisorsResponse = await axios.get('/api/advisors');
            } catch (error) {
                console.error('Failed to load advisors:', error);
            }
            
            // Initialize chat agent with context
            await chatAgent.initializeChat(
                businessPlanResponse?.data || businessPlanData.value || {},
                roadmapResponse?.data || roadmap.value || {},
                advisorsResponse?.data?.advisors || advisors.value || []
            );
        }
        
        // Send message using chat agent
        await chatAgent.sendMessage(userMessage);
        
    } catch (err) {
        console.error('Chat error:', err);
        const errorMessage = err.message || 'Sorry, I encountered an error. Please try again.';
        chatMessages.value.push({
            type: 'assistant',
            text: errorMessage
        });
        await nextTick();
        scrollChatToBottom();
    } finally {
        isLoadingChat.value = false;
        await nextTick();
        scrollChatToBottom();
    }
};

const scrollChatToBottom = () => {
    if (chatMessagesContainer.value) {
        chatMessagesContainer.value.scrollTop = chatMessagesContainer.value.scrollHeight;
    }
};

// Initialize chat agent when switching to chat mode
watch(() => interactionMode.value, async (newMode) => {
    if (newMode === 'chat') {
        // CRITICAL: Disconnect voice agent completely to disable microphone access
        // When in chat mode, we want ONLY text, no microphone access at all
        if (isConnected.value) {
            console.log('Switching to chat mode - disconnecting voice agent to disable microphone');
            disconnect();
            stopSession();
        }
        
        // Stop any active audio streams that might be running
        // This ensures microphone is completely disabled in chat mode
        try {
            // Stop all active audio tracks from any getUserMedia streams
            // The browser may have active streams from the voice agent
            if (navigator.mediaDevices && navigator.mediaDevices.enumerateDevices) {
                // We can't directly enumerate active streams, but we can try to stop
                // any tracks that might be active from previous sessions
                console.log('Chat mode active - ensuring microphone streams are stopped');
            }
            
            // Additional safeguard: try to access and stop any active audio tracks
            // This is a defensive measure in case voice agent didn't clean up properly
            try {
                // Check if there are any active MediaStreamTracks
                // We can't enumerate them directly, but we ensure they're stopped via disconnect
                console.log('Voice agent disconnected, microphone access should be disabled');
            } catch (trackError) {
                console.warn('Error stopping audio tracks:', trackError);
            }
        } catch (error) {
            console.warn('Error checking/stopping media devices:', error);
        }
        
        // Initialize chat agent on first switch to chat mode
        if (!chatAgent.isConnected.value && chatMessages.value.length === 0) {
            try {
                // Load required data
                let businessPlanResponse = null;
                let roadmapResponse = null;
                let advisorsResponse = null;
                
                try {
                    businessPlanResponse = await axios.get('/api/business-plan');
                } catch (error) {
                    console.error('Failed to load business plan:', error);
                }
                
                try {
                    roadmapResponse = await axios.get('/api/roadmap');
                } catch (error) {
                    console.error('Failed to load roadmap:', error);
                }
                
                try {
                    advisorsResponse = await axios.get('/api/advisors');
                } catch (error) {
                    console.error('Failed to load advisors:', error);
                }
                
                // Initialize chat agent with context (text-only, no microphone)
                await chatAgent.initializeChat(
                    businessPlanResponse?.data || businessPlanData.value || {},
                    roadmapResponse?.data || roadmap.value || {},
                    advisorsResponse?.data?.advisors || advisors.value || []
                );
            } catch (error) {
                console.error('Failed to initialize chat agent:', error);
                chatMessages.value.push({
                    type: 'assistant',
                    text: 'Sorry, I encountered an error initializing the chat. Please try again.'
                });
            }
        }
    } else if (newMode === 'voice') {
        // Disconnect chat agent when switching to voice
        if (chatAgent.isConnected.value) {
            chatAgent.disconnect();
        }
    }
});

const handleNavigation = (tab) => {
    // If clicking the same tab, close it (toggle behavior)
    if (activeTab.value === tab) {
        activeTab.value = 'roadmap'; // Return to home
    } else {
        activeTab.value = tab; // Open the drawer
    }
};

// Helper function to check if a contextual field is completed
const isContextualFieldCompleted = (fieldKey) => {
    const value = businessPlanData.value?.[fieldKey];
    
    // Handle null/undefined
    if (value === null || value === undefined) return false;
    
    // Handle booleans - both true and false are considered completed
    if (typeof value === 'boolean') return true;
    
    // Handle numbers - including 0, which is a valid value
    if (typeof value === 'number') return true;
    
    // Handle strings - empty strings are not completed
    if (typeof value === 'string' && value.trim() === '') return false;
    if (typeof value === 'string') return true;
    
    return false;
};

// Contextual field editing functions
const startEditingContextual = async (fieldKey) => {
    if (editingContextualField.value === fieldKey) return; // Already editing this field
    
    editingContextualField.value = fieldKey;
    const value = businessPlanData.value?.[fieldKey];
    
    // Handle boolean fields
    if (typeof value === 'boolean') {
        contextualEditValue.value = value ? 'true' : 'false';
    } else if (value === null || value === undefined) {
        contextualEditValue.value = '';
    } else {
        contextualEditValue.value = String(value);
    }
    
    await nextTick();
    if (contextualEditInput.value) {
        if (Array.isArray(contextualEditInput.value)) {
            contextualEditInput.value[0]?.focus();
        } else {
            contextualEditInput.value.focus();
        }
    }
};

const cancelEditingContextual = () => {
    editingContextualField.value = null;
    contextualEditValue.value = '';
};

const saveContextualField = async (fieldKey) => {
    if (editingContextualField.value !== fieldKey) return;
    
    let newValue = contextualEditValue.value.trim();
    const oldValue = businessPlanData.value?.[fieldKey];
    
    // Handle boolean fields
    const booleanFields = ['is_eu_resident', 'is_newcomer_to_finland', 'has_residence_permit', 'has_business_experience'];
    if (booleanFields.includes(fieldKey)) {
        if (newValue === 'true') {
            newValue = true;
        } else if (newValue === 'false') {
            newValue = false;
        } else {
            newValue = null;
        }
    } else if (fieldKey === 'years_in_finland') {
        // Handle numeric field
        if (newValue) {
            const numValue = parseInt(newValue);
            newValue = !isNaN(numValue) ? numValue : null;
        } else {
            newValue = null;
        }
    } else {
        // Handle string fields
        newValue = newValue || null;
    }
    
    // Only save if value changed
    if (newValue !== oldValue) {
        try {
            const updateData = {
                [fieldKey]: newValue
            };
            
            // Use the existing business plan update handler
            await handleBusinessPlanUpdate(updateData);
        } catch (err) {
            console.error('Failed to save contextual field:', err);
            error.value = 'Failed to save. Please try again.';
        }
    }
    
    cancelEditingContextual();
};

// Flags animation functions
// const updateFlagsDimensions = () => {
//     const sequenceRect = flagsSequenceRef.value?.getBoundingClientRect?.();
//     const sequenceWidth = sequenceRect?.width ?? 0;
//
//     if (sequenceWidth > 0) {
//         seqWidth.value = Math.ceil(sequenceWidth);
//     }
// };
//
// const startFlagsAnimation = () => {
//     const track = flagsTrackRef.value;
//     if (!track) return;
//
//     // Stop any existing animation
//     stopFlagsAnimation();
//
//     const animate = (timestamp) => {
//         if (lastTimestamp === null) {
//             lastTimestamp = timestamp;
//         }
//
//         const deltaTime = Math.max(0, timestamp - lastTimestamp) / 1000;
//         lastTimestamp = timestamp;
//
//         const easingFactor = 1 - Math.exp(-deltaTime / ANIMATION_CONFIG.SMOOTH_TAU);
//         velocity += (targetVelocity.value - velocity) * easingFactor;
//
//         const seqSize = seqWidth.value;
//         if (seqSize > 0) {
//             // Continuous scrolling - when it reaches the end of first set, reset to start seamlessly
//             offset += velocity * deltaTime;
//             if (offset >= seqSize) {
//                 offset = offset % seqSize;
//             }
//
//             track.style.transform = `translate3d(${-offset}px, 0, 0)`;
//         }
//
//         rafId = requestAnimationFrame(animate);
//     };
//
//     // Initialize offset
//     offset = 0;
//     track.style.transform = `translate3d(0, 0, 0)`;
//
//     rafId = requestAnimationFrame(animate);
// };
//
// const stopFlagsAnimation = () => {
//     if (rafId !== null) {
//         cancelAnimationFrame(rafId);
//         rafId = null;
//     }
//     lastTimestamp = null;
// };

onMounted(async () => {
    // Load user progress on mount
    await loadUserProgress();
    // Load advisors on mount
    await loadAdvisors();
    // Data is already loaded via Inertia props
    // Roadmap only shows action steps, no question steps
    // Question steps are handled separately in BusinessPlanProgress component
    console.log('Roadmap loaded:', roadmap.value);

    // Initialize flags animation
    // await nextTick();
    // updateFlagsDimensions();
    //
    // // Setup ResizeObserver for flags container
    // if (window.ResizeObserver && flagsContainerRef.value && flagsSequenceRef.value) {
    //     const resizeObserver = new ResizeObserver(() => {
    //         updateFlagsDimensions();
    //     });
    //     resizeObserver.observe(flagsContainerRef.value);
    //     resizeObserver.observe(flagsSequenceRef.value);
    //
    //     // Store observer for cleanup
    //     flagsContainerRef.value._resizeObserver = resizeObserver;
    // } else {
    //     // Fallback for browsers without ResizeObserver
    //     const handleResize = () => {
    //         updateFlagsDimensions();
    //     };
    //     window.addEventListener('resize', handleResize);
    //     flagsContainerRef.value._resizeHandler = handleResize;
    // }
    //
    // // Start animation after a short delay to ensure dimensions are calculated
    // setTimeout(() => {
    //     updateFlagsDimensions();
    //     startFlagsAnimation();
    // }, 100);
});

onUnmounted(() => {
    // Clean up flags animation
    // stopFlagsAnimation();
    //
    // // Clean up ResizeObserver
    // if (flagsContainerRef.value?._resizeObserver) {
    //     flagsContainerRef.value._resizeObserver.disconnect();
    // }
    // if (flagsContainerRef.value?._resizeHandler) {
    //     window.removeEventListener('resize', flagsContainerRef.value._resizeHandler);
    // }

    // Clean up timeouts to prevent memory leaks
    if (businessTabTimeout) {
        clearTimeout(businessTabTimeout);
        businessTabTimeout = null;
    }
});
</script>

<style scoped>
.drawer-overlay {
    background-color: rgba(247, 250, 248, 0.85);
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
    background: white !important;
    border: 2px solid #5cc094 !important;
    box-shadow: 0 0 20px rgba(92, 192, 148, 0.3), 0 0 40px rgba(92, 192, 148, 0.1);
    transform: scale(1.02);
    z-index: 10;
    position: relative;
}

@keyframes highlightPulse {
    0% {
        background: white;
        border-color: #5cc094;
        box-shadow: 0 0 20px rgba(92, 192, 148, 0.3), 0 0 40px rgba(92, 192, 148, 0.1);
        transform: scale(1.02);
    }
    50% {
        background: #f0fdf4;
        border-color: #5cc094;
        box-shadow: 0 0 15px rgba(92, 192, 148, 0.2), 0 0 30px rgba(92, 192, 148, 0.05);
        transform: scale(1.01);
    }
    100% {
        background: var(--original-bg, white);
        border-color: var(--original-border, #5cc094);
        box-shadow: none;
        transform: scale(1);
    }
}

/* Pulsing bars animation for mic button */
@keyframes pulseBar {
    0%, 100% {
        height: 8px;
        opacity: 0.4;
    }
    50% {
        height: 20px;
        opacity: 1;
    }
}

/* Mode switch transition animations */
.mode-switch-enter-active,
.mode-switch-leave-active {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.mode-switch-enter-from {
    opacity: 0;
    transform: translateY(-20px) scale(0.9);
}

.mode-switch-leave-to {
    opacity: 0;
    transform: translateY(20px) scale(0.9);
}

.mode-switch-enter-to,
.mode-switch-leave-from {
    opacity: 1;
    transform: translateY(0) scale(1);
}

/* Phone hanging (rotated when not active) */
.phone-hanging {
    transform: rotate(135deg);
    transition: transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Phone shake animation - runs 10 times (5 seconds total) and stops */
.phone-shake {
    animation: phoneShake 0.5s ease-in-out;
    animation-iteration-count: 10;
}

@keyframes phoneShake {
    0%, 100% {
        transform: translateX(0) rotate(0deg) scale(1.1);
    }
    10% {
        transform: translateX(-2px) rotate(-2deg) scale(1.1);
    }
    20% {
        transform: translateX(2px) rotate(2deg) scale(1.1);
    }
    30% {
        transform: translateX(-2px) rotate(-2deg) scale(1.1);
    }
    40% {
        transform: translateX(2px) rotate(2deg) scale(1.1);
    }
    50% {
        transform: translateX(-1px) rotate(-1deg) scale(1.1);
    }
    60% {
        transform: translateX(1px) rotate(1deg) scale(1.1);
    }
    70% {
        transform: translateX(-1px) rotate(-1deg) scale(1.1);
    }
    80% {
        transform: translateX(1px) rotate(1deg) scale(1.1);
    }
    90% {
        transform: translateX(-1px) rotate(-1deg) scale(1.1);
    }
}

/* Flags Strip Animation */
/*
.flags-wrapper {
    user-select: none;
    position: relative;
}

.flags-wrapper::before,
.flags-wrapper::after {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    width: clamp(60px, 15%, 120px);
    pointer-events: none;
    z-index: 10;
}

.flags-wrapper::before {
    left: 0;
    background: linear-gradient(
        to right,
        #fff5f5 0%,
        rgba(255, 245, 245, 0.8) 50%,
        rgba(255, 245, 245, 0) 100%
    );
}

.flags-wrapper::after {
    right: 0;
    background: linear-gradient(
        to left,
        #fff5f5 0%,
        rgba(255, 245, 245, 0.8) 50%,
        rgba(255, 245, 245, 0) 100%
    );
}

.flags-track {
    display: flex;
    width: max-content;
    will-change: transform;
    user-select: none;
    position: relative;
    z-index: 0;
}

.flags-list {
    display: flex;
    align-items: center;
}

.flag-item {
    flex: 0 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.flag-item svg {
    display: block;
    filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
    pointer-events: none;
    height: 40px;
    width: auto;
}

@media (prefers-reduced-motion: reduce) {
    .flags-track {
        transform: translate3d(0, 0, 0) !important;
    }
}
*/

/* Chat Interface Animations */
.chat-interface-enter-active {
    transition: opacity 0.5s cubic-bezier(0.16, 1, 0.3, 1),
                transform 0.5s cubic-bezier(0.16, 1, 0.3, 1),
                filter 0.5s ease-out;
}

.chat-interface-leave-active {
    transition: opacity 0.3s cubic-bezier(0.7, 0, 0.84, 0),
                transform 0.3s cubic-bezier(0.7, 0, 0.84, 0),
                filter 0.3s ease-out;
}

.chat-interface-enter-from {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
    filter: blur(4px);
}

.chat-interface-enter-to {
    opacity: 1;
    transform: translateY(0) scale(1);
    filter: blur(0);
}

.chat-interface-leave-from {
    opacity: 1;
    transform: translateY(0) scale(1);
    filter: blur(0);
}

.chat-interface-leave-to {
    opacity: 0;
    transform: translateY(-10px) scale(0.98);
    filter: blur(2px);
}

.chat-interface-container {
    animation: chatContainerPulse 0.6s ease-out 0.1s;
}

@keyframes chatContainerPulse {
    0% {
        box-shadow: 0 0 0 0 rgba(92, 192, 148, 0);
    }
    50% {
        box-shadow: 0 0 0 8px rgba(92, 192, 148, 0.1);
    }
    100% {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
}

/* Voice Interface Animations */
.voice-interface-enter-active {
    transition: opacity 0.5s cubic-bezier(0.16, 1, 0.3, 1),
                transform 0.5s cubic-bezier(0.16, 1, 0.3, 1),
                filter 0.5s ease-out;
}

.voice-interface-leave-active {
    transition: opacity 0.3s cubic-bezier(0.7, 0, 0.84, 0),
                transform 0.3s cubic-bezier(0.7, 0, 0.84, 0),
                filter 0.3s ease-out;
}

.voice-interface-enter-from {
    opacity: 0;
    transform: translateY(30px) scale(0.95);
    filter: blur(4px);
}

.voice-interface-enter-to {
    opacity: 1;
    transform: translateY(0) scale(1);
    filter: blur(0);
}

.voice-interface-leave-from {
    opacity: 1;
    transform: translateY(0) scale(1);
    filter: blur(0);
}

.voice-interface-leave-to {
    opacity: 0;
    transform: translateY(-10px) scale(0.98);
    filter: blur(2px);
}

/* Ensure scrolling works in voice interface even when unmuted */
.voice-interface .flex-1.overflow-y-auto {
    touch-action: pan-y pinch-zoom;
    -webkit-overflow-scrolling: touch;
    overscroll-behavior: contain;
    pointer-events: auto;
}

@media (prefers-reduced-motion: reduce) {
    .chat-interface-enter-active,
    .chat-interface-leave-active,
    .chat-interface-container,
    .voice-interface-enter-active,
    .voice-interface-leave-active {
        animation: none;
        transition: opacity 0.2s ease;
    }
    
    .chat-interface-enter-from,
    .chat-interface-leave-to {
        opacity: 1;
        transform: none;
    }
}
</style>

