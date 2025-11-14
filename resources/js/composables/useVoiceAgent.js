import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

/**
 * useVoiceAgent Composable
 * Vue composable for ElevenLabs Agents WebSocket integration
 */
export default function useVoiceAgent({ onRoadmapUpdate, onTranscript, onError }) {
    const isConnected = ref(false);
    const isListening = ref(false);
    const isSpeaking = ref(false);
    const connectionStatus = ref('disconnected');
    const wsRef = ref(null);
    const sessionIdRef = ref(null);
    const audioContextRef = ref(null);
    const mediaStreamRef = ref(null);
    const audioQueueRef = ref([]);
    const isPlayingAudioRef = ref(false);

    // Check microphone permissions
    const checkMicrophonePermission = async () => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            stream.getTracks().forEach(track => track.stop());
            return true;
        } catch (error) {
            if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                if (onError) {
                    onError('Microphone permission denied. Please allow microphone access in your browser settings.');
                }
                return false;
            } else if (error.name === 'NotFoundError' || error.name === 'DevicesNotFoundError') {
                if (onError) {
                    onError('No microphone found. Please connect a microphone and try again.');
                }
                return false;
            } else {
                if (onError) {
                    onError('Failed to access microphone: ' + error.message);
                }
                return false;
            }
        }
    };

    // Initialize audio context for playing agent responses
    const initAudioContext = () => {
        if (!audioContextRef.value) {
            audioContextRef.value = new (window.AudioContext || window.webkitAudioContext)();
        }
        return audioContextRef.value;
    };

    // Play audio chunks from the queue
    const playAudioQueue = async () => {
        if (isPlayingAudioRef.value || audioQueueRef.value.length === 0) {
            return;
        }

        isPlayingAudioRef.value = true;
        const audioContext = initAudioContext();

        // Resume audio context if suspended (required for autoplay policies)
        if (audioContext.state === 'suspended') {
            await audioContext.resume();
        }

        while (audioQueueRef.value.length > 0) {
            const audioData = audioQueueRef.value.shift();
            try {
                // Try to decode as MP3/WAV first
                const audioBuffer = await audioContext.decodeAudioData(audioData.slice(0));
                const source = audioContext.createBufferSource();
                source.buffer = audioBuffer;
                source.connect(audioContext.destination);
                
                await new Promise((resolve, reject) => {
                    source.onended = () => {
                        resolve();
                    };
                    source.onerror = (error) => {
                        console.error('Audio source error:', error);
                        reject(error);
                    };
                    try {
                        source.start(0);
                    } catch (startError) {
                        console.error('Error starting audio:', startError);
                        reject(startError);
                    }
                });
            } catch (error) {
                console.log('Web Audio API decode failed, trying PCM format:', error);
                // If decode fails, try treating as raw PCM audio
                try {
                    // Assume 16-bit PCM, 24000 Hz, mono (common for ElevenLabs)
                    const sampleRate = 24000;
                    const numChannels = 1;
                    const pcmData = new Int16Array(audioData);
                    
                    // Convert PCM to Float32Array for Web Audio API
                    const float32Data = new Float32Array(pcmData.length);
                    for (let i = 0; i < pcmData.length; i++) {
                        float32Data[i] = pcmData[i] / 32768.0;
                    }
                    
                    // Create audio buffer from PCM data
                    const audioBuffer = audioContext.createBuffer(numChannels, float32Data.length, sampleRate);
                    audioBuffer.getChannelData(0).set(float32Data);
                    
                    const source = audioContext.createBufferSource();
                    source.buffer = audioBuffer;
                    source.connect(audioContext.destination);
                    
                    await new Promise((resolve, reject) => {
                        source.onended = () => {
                            resolve();
                        };
                        source.onerror = (err) => {
                            console.error('PCM audio source error:', err);
                            reject(err);
                        };
                        try {
                            source.start(0);
                        } catch (startError) {
                            console.error('Error starting PCM audio:', startError);
                            reject(startError);
                        }
                    });
                } catch (pcmError) {
                    console.error('PCM playback failed, trying HTML5 Audio fallback:', pcmError);
                    // Final fallback: try HTML5 Audio (might work for some formats)
                    try {
                        const blob = new Blob([audioData], { type: 'audio/mpeg' });
                        const audioUrl = URL.createObjectURL(blob);
                        const audio = new Audio(audioUrl);
                        
                        await new Promise((resolve, reject) => {
                            audio.onended = () => {
                                URL.revokeObjectURL(audioUrl);
                                resolve();
                            };
                            audio.onerror = (err) => {
                                console.error('HTML5 Audio error:', err);
                                URL.revokeObjectURL(audioUrl);
                                reject(err);
                            };
                            audio.play().catch(reject);
                        });
                    } catch (fallbackError) {
                        console.error('All audio playback methods failed:', fallbackError);
                    }
                }
            }
        }

        isPlayingAudioRef.value = false;
        isSpeaking.value = false;
        isListening.value = true;
    };

    // Initialize ElevenLabs WebSocket connection
    const initializeConnection = async () => {
        try {
            const apiKey = import.meta.env.VITE_ELEVENLABS_API_KEY;
            const agentId = import.meta.env.VITE_ELEVENLABS_AGENT_ID;

            if (!apiKey) {
                throw new Error('ElevenLabs API key not found. Please set VITE_ELEVENLABS_API_KEY in your .env file.');
            }

            if (!agentId) {
                throw new Error('ElevenLabs Agent ID not found. Please set VITE_ELEVENLABS_AGENT_ID in your .env file.');
            }

            // ElevenLabs Agents WebSocket endpoint
            // API key should be in the URL query parameter
            const wsUrl = `wss://api.elevenlabs.io/v1/convai/conversation?agent_id=${agentId}&xi-api-key=${encodeURIComponent(apiKey)}`;
            
            const ws = new WebSocket(wsUrl);

            ws.onopen = () => {
                console.log('ElevenLabs WebSocket connected');
                // No need to send authentication message - it's in the URL
                isConnected.value = true;
                connectionStatus.value = 'connected';
                isListening.value = true;
            };

            ws.onmessage = async (event) => {
                try {
                    // ElevenLabs sends JSON messages
                    const message = JSON.parse(event.data);
                    
                    console.log('Received message:', message.type || 'unknown', message);
                    
                    // Handle audio data from audio_event
                    if (message.type === 'audio' && message.audio_event) {
                        try {
                            const audioEvent = message.audio_event;
                            console.log('Audio event details:', audioEvent);
                            
                            // Check all possible fields where audio data might be
                            // Note: ElevenLabs uses audio_base_64 (with underscore)
                            const audioData = audioEvent.audio_base_64 || 
                                            audioEvent.audio_base64 || 
                                            audioEvent.audio || 
                                            audioEvent.data || 
                                            audioEvent.chunk ||
                                            audioEvent.audio_chunk;
                            
                            if (audioData) {
                                console.log('Found audio data, length:', audioData.length);
                                
                                // Decode base64 to ArrayBuffer
                                const binaryString = atob(audioData);
                                const bytes = new Uint8Array(binaryString.length);
                                for (let i = 0; i < binaryString.length; i++) {
                                    bytes[i] = binaryString.charCodeAt(i);
                                }
                                const arrayBuffer = bytes.buffer;
                                
                                console.log('Decoded audio buffer size:', arrayBuffer.byteLength);
                                
                                // Add to audio queue and play
                                audioQueueRef.value.push(arrayBuffer);
                                isSpeaking.value = true;
                                isListening.value = false;
                                playAudioQueue();
                            } else {
                                console.warn('Audio event received but no audio data found. Available keys:', Object.keys(audioEvent));
                            }
                        } catch (audioError) {
                            console.error('Error processing audio:', audioError);
                        }
                    }
                    
                    // Also check for direct audio field (fallback)
                    if (message.audio && !message.audio_event) {
                        try {
                            // Decode base64 to ArrayBuffer
                            const binaryString = atob(message.audio);
                            const bytes = new Uint8Array(binaryString.length);
                            for (let i = 0; i < binaryString.length; i++) {
                                bytes[i] = binaryString.charCodeAt(i);
                            }
                            const arrayBuffer = bytes.buffer;
                            
                            // Add to audio queue and play
                            audioQueueRef.value.push(arrayBuffer);
                            isSpeaking.value = true;
                            isListening.value = false;
                            playAudioQueue();
                        } catch (audioError) {
                            console.error('Error processing audio:', audioError);
                        }
                    }
                    
                    // Handle text/transcript messages
                    if (message.type === 'conversation_initiation' || message.type === 'conversation_initiation_metadata') {
                        console.log('Conversation initiated:', message);
                    } else if (message.type === 'agent_response') {
                        // Extract text from agent_response_event
                        const responseEvent = message.agent_response_event;
                        const text = responseEvent?.agent_response || message.text || message.response || message.message || '';
                        
                        if (text && onTranscript) {
                            onTranscript({
                                type: 'ai',
                                text: text,
                                timestamp: new Date().toISOString()
                            });
                        }

                        // Try to extract roadmap JSON from response
                        if (text) {
                            const jsonMatch = text.match(/\{[\s\S]*"steps"[\s\S]*\}/);
                            if (jsonMatch) {
                                try {
                                    const roadmapData = JSON.parse(jsonMatch[0]);
                                    if (onRoadmapUpdate) {
                                        onRoadmapUpdate(roadmapData);
                                    }
                                } catch (parseError) {
                                    console.warn('Failed to parse roadmap JSON:', parseError);
                                }
                            }
                        }
                    } else if (message.type === 'response' || message.type === 'agent_response') {
                        const text = message.text || message.response || message.message || '';
                        
                        if (text && onTranscript) {
                            onTranscript({
                                type: 'ai',
                                text: text,
                                timestamp: new Date().toISOString()
                            });
                        }

                        // Try to extract roadmap JSON from response
                        if (text) {
                            const jsonMatch = text.match(/\{[\s\S]*"steps"[\s\S]*\}/);
                            if (jsonMatch) {
                                try {
                                    const roadmapData = JSON.parse(jsonMatch[0]);
                                    if (onRoadmapUpdate) {
                                        onRoadmapUpdate(roadmapData);
                                    }
                                } catch (parseError) {
                                    console.warn('Failed to parse roadmap JSON:', parseError);
                                }
                            }
                        }
                    } else if (message.type === 'user_transcript' || message.type === 'user_message') {
                        const text = message.text || message.transcript || message.message || '';
                        if (text && onTranscript) {
                            onTranscript({
                                type: 'user',
                                text: text,
                                timestamp: new Date().toISOString()
                            });
                        }
                    } else if (message.type === 'error') {
                        console.error('ElevenLabs error:', message);
                        if (onError) {
                            onError(message.message || message.error || 'ElevenLabs API error');
                        }
                    } else if (message.type === 'ping') {
                        // Respond to ping to keep connection alive
                        // ElevenLabs expects a pong message with pong_event
                        const pingEvent = message.ping_event;
                        ws.send(JSON.stringify({ 
                            type: 'pong',
                            pong_event: {
                                event_id: pingEvent?.event_id || 0
                            }
                        }));
                    }
                } catch (error) {
                    // If it's not JSON, try to handle as binary/audio
                    if (event.data instanceof Blob) {
                        try {
                            const arrayBuffer = await event.data.arrayBuffer();
                            audioQueueRef.value.push(arrayBuffer);
                            isSpeaking.value = true;
                            isListening.value = false;
                            playAudioQueue();
                        } catch (blobError) {
                            console.error('Error processing blob audio:', blobError);
                        }
                    } else {
                        console.error('Error processing WebSocket message:', error, event.data);
                    }
                }
            };

            ws.onerror = (error) => {
                console.error('WebSocket error:', error);
                connectionStatus.value = 'disconnected';
                isConnected.value = false;
                if (onError) {
                    onError('WebSocket connection error. Please check your API key and agent ID.');
                }
            };

            ws.onclose = (event) => {
                console.log('WebSocket closed:', event.code, event.reason);
                connectionStatus.value = 'disconnected';
                isConnected.value = false;
                isListening.value = false;
                isSpeaking.value = false;
                
                if (event.code !== 1000) {
                    // Not a normal closure
                    if (onError) {
                        onError('Connection closed unexpectedly. Please try reconnecting.');
                    }
                }
            };

            wsRef.value = ws;
        } catch (error) {
            console.error('Failed to initialize connection:', error);
            if (onError) {
                onError('Failed to initialize voice agent: ' + (error.message || 'Unknown error'));
            }
        }
    };

    onMounted(() => {
        // Initialize audio context
        initAudioContext();
    });

    onUnmounted(() => {
        disconnect();
        
        // Clean up audio context
        if (audioContextRef.value) {
            audioContextRef.value.close().catch(console.error);
        }
        
        // Stop media stream
        if (mediaStreamRef.value) {
            mediaStreamRef.value.getTracks().forEach(track => track.stop());
        }
    });

    const connect = async () => {
        if (wsRef.value && wsRef.value.readyState === WebSocket.OPEN) {
            // Already connected
            return;
        }

        try {
            connectionStatus.value = 'connecting';
            
            const hasPermission = await checkMicrophonePermission();
            if (!hasPermission) {
                connectionStatus.value = 'disconnected';
                return;
            }

            // Debug: Check environment variables
            console.log('Environment check:', {
                hasApiKey: !!import.meta.env.VITE_ELEVENLABS_API_KEY,
                hasAgentId: !!import.meta.env.VITE_ELEVENLABS_AGENT_ID,
                envKeys: Object.keys(import.meta.env).filter(key => key.includes('ELEVENLABS'))
            });

            await initializeConnection();
        } catch (error) {
            console.error('Failed to connect:', error);
            connectionStatus.value = 'disconnected';
            if (onError) {
                let errorMessage = 'Failed to connect to voice agent';
                
                if (error.message) {
                    errorMessage = error.message;
                } else if (error.response) {
                    errorMessage = `API error: ${error.response.status} - ${error.response.statusText}`;
                } else if (error.request) {
                    errorMessage = 'Network error: Unable to reach the server. Please check your internet connection.';
                }
                
                onError(errorMessage);
            }
        }
    };

    const disconnect = () => {
        if (wsRef.value) {
            try {
                wsRef.value.close(1000, 'User disconnected');
            } catch (error) {
                console.error('Error closing WebSocket:', error);
            }
            wsRef.value = null;
        }
        
        connectionStatus.value = 'disconnected';
        isConnected.value = false;
        isListening.value = false;
        isSpeaking.value = false;

        // Stop media stream
        if (mediaStreamRef.value) {
            mediaStreamRef.value.getTracks().forEach(track => track.stop());
            mediaStreamRef.value = null;
        }
    };

    const startSession = async () => {
        if (!isConnected.value) {
            await connect();
            await new Promise(resolve => setTimeout(resolve, 1000));
            if (!isConnected.value) {
                if (onError) {
                    onError('Failed to establish connection. Please try again.');
                }
                return;
            }
        }

        try {
            const hasPermission = await checkMicrophonePermission();
            if (!hasPermission) {
                return;
            }

            // Get user's microphone stream
            const stream = await navigator.mediaDevices.getUserMedia({ 
                audio: {
                    echoCancellation: true,
                    noiseSuppression: true,
                    autoGainControl: true,
                }
            });
            mediaStreamRef.value = stream;

            // Send audio to WebSocket
            const audioContext = new (window.AudioContext || window.webkitAudioContext)();
            const source = audioContext.createMediaStreamSource(stream);
            const processor = audioContext.createScriptProcessor(4096, 1, 1);

            processor.onaudioprocess = (e) => {
                if (wsRef.value && wsRef.value.readyState === WebSocket.OPEN) {
                    const inputData = e.inputBuffer.getChannelData(0);
                    const pcmData = new Int16Array(inputData.length);
                    
                    for (let i = 0; i < inputData.length; i++) {
                        const s = Math.max(-1, Math.min(1, inputData[i]));
                        pcmData[i] = s < 0 ? s * 0x8000 : s * 0x7FFF;
                    }
                    
                    // Convert to base64 for JSON message (efficient method for large buffers)
                    const uint8Array = new Uint8Array(pcmData.buffer);
                    let binaryString = '';
                    for (let i = 0; i < uint8Array.length; i++) {
                        binaryString += String.fromCharCode(uint8Array[i]);
                    }
                    const base64Audio = btoa(binaryString);
                    
                    // Send audio data as JSON message with proper ElevenLabs format
                    wsRef.value.send(JSON.stringify({
                        type: 'audio_input',
                        audio_input_event: {
                            audio_base_64: base64Audio,
                        }
                    }));
                }
            };

            source.connect(processor);
            processor.connect(audioContext.destination);

            // Create voice session in backend
            let sessionResponse;
            try {
                sessionResponse = await axios.post('/api/voice-session', {
                    started_at: new Date().toISOString(),
                    status: 'pending'
                });
                sessionIdRef.value = sessionResponse.data.id;
            } catch (error) {
                console.error('Failed to create voice session in backend:', error);
                if (error.response?.status === 401) {
                    if (onError) {
                        onError('Authentication required. Please log in.');
                    }
                    return;
                }
            }

            isListening.value = true;
        } catch (error) {
            console.error('Failed to start session:', error);
            if (onError) {
                let errorMessage = 'Failed to start voice session';
                if (error.message) {
                    errorMessage += ': ' + error.message;
                } else if (error.response) {
                    errorMessage += ` (${error.response.status})`;
                }
                onError(errorMessage);
            }
        }
    };

    const stopSession = async () => {
        try {
            // Stop sending audio
            if (mediaStreamRef.value) {
                mediaStreamRef.value.getTracks().forEach(track => track.stop());
                mediaStreamRef.value = null;
            }

            isListening.value = false;
            isSpeaking.value = false;

            if (sessionIdRef.value) {
                try {
                    await axios.patch(`/api/voice-session/${sessionIdRef.value}`, {
                        status: 'complete',
                        completed_at: new Date().toISOString()
                    });
                } catch (error) {
                    console.error('Failed to update session status:', error);
                }
                sessionIdRef.value = null;
            }
        } catch (error) {
            console.error('Failed to stop session:', error);
            isListening.value = false;
            isSpeaking.value = false;
        }
    };

    return {
        isConnected,
        isListening,
        isSpeaking,
        connectionStatus,
        connect,
        disconnect,
        startSession,
        stopSession
    };
}
