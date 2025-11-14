<template>
    <Head title="Voice Roadmap Builder" />
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8 px-4">
        <div class="max-w-6xl mx-auto">
            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-bold text-gray-800 mb-2">
                    🎤 Voice-Driven Roadmap Builder
                </h1>
                <p class="text-gray-600 text-lg">
                    Speak to your AI startup coach and build your roadmap in real-time
                </p>
            </div>

            <!-- Error Display -->
            <div v-if="error" class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded">
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
            <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
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
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium"
                        >
                            Connect
                        </button>
                        <template v-else>
                            <button
                                v-if="!isSessionActive"
                                @click="handleStartSession"
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium flex items-center gap-2"
                            >
                                <span>🎤</span>
                                Start Voice Session
                            </button>
                            <button
                                v-else
                                @click="handleStopSession"
                                class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium flex items-center gap-2"
                            >
                                <span>⏹️</span>
                                Stop Session
                            </button>
                            <button
                                @click="handleDisconnect"
                                class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium"
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

            <!-- Roadmap Visualization -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <RoadmapVisualizer 
                    :roadmap="roadmap"
                    @step-update="handleStepUpdate"
                />
            </div>

            <!-- Help Text -->
            <div v-if="!isConnected" class="mt-8 bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <p class="text-sm text-blue-700">
                    <strong>Getting Started:</strong> Click "Connect" to initialize the voice agent, 
                    then "Start Voice Session" to begin building your roadmap through conversation. 
                    The AI coach will ask you about your business idea and create personalized steps for your startup journey.
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import useVoiceAgent from '../composables/useVoiceAgent';
import RoadmapVisualizer from '../components/RoadmapVisualizer.vue';

const props = defineProps({
    initialRoadmap: {
        type: Object,
        default: null
    }
});

const roadmap = ref(props.initialRoadmap);
const loading = ref(false);
const error = ref(null);
const transcripts = ref([]);
const isSessionActive = ref(false);

const handleRoadmapUpdate = async (roadmapData) => {
    try {
        if (!roadmapData || typeof roadmapData !== 'object') {
            console.warn('Invalid roadmap data received:', roadmapData);
            return;
        }

        roadmap.value = roadmapData;

        try {
            await axios.post('/api/roadmap/update', {
                roadmap_json: roadmapData
            });
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

const handleTranscript = (transcript) => {
    transcripts.value.push(transcript);
};

const handleError = (errorMessage) => {
    error.value = errorMessage;
    console.error('Voice agent error:', errorMessage);
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
    onTranscript: handleTranscript,
    onError: handleError
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

onMounted(async () => {
    try {
        loading.value = true;
        const response = await axios.get('/api/roadmap');
        if (response.data) {
            roadmap.value = response.data.roadmap_json || response.data;
        }
    } catch (err) {
        console.error('Failed to fetch roadmap:', err);
        if (err.response?.status !== 404) {
            error.value = 'Failed to load roadmap';
        }
    } finally {
        loading.value = false;
    }
});
</script>

