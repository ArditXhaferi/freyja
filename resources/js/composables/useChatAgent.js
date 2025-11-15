import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { VoiceConversation } from '@elevenlabs/client';

/**
 * Strip CHARACTER tags from ElevenLabs Agent responses
 * Handles multiple formats:
 * - <CHARACTER>default>text</CHARACTER> -> text
 * - <CHARACTER>default</CHARACTER> -> (removed)
 * - <CHARACTER>default>text</CHARACTER>text -> texttext
 */
const stripCharacterTags = (text) => {
    if (!text || typeof text !== 'string') return text;
    
    // Remove all CHARACTER tags (with or without content)
    // Pattern 1: <CHARACTER>default>content</CHARACTER> -> content
    // Pattern 2: <CHARACTER>default</CHARACTER> -> (removed completely)
    let cleaned = text.replace(/<CHARACTER>[^>]*>([^<]*)<\/CHARACTER>/gi, '$1');
    
    // Also handle cases where tag might be standalone
    cleaned = cleaned.replace(/<CHARACTER>[^<]*<\/CHARACTER>/gi, '');
    
    // Clean up any extra whitespace that might result
    cleaned = cleaned.replace(/\s+/g, ' ').trim();
    
    return cleaned;
};

/**
 * useChatAgent Composable
 * Vue composable for ElevenLabs Agents Text Chat integration
 * Uses ElevenLabs REST API directly for text-only chat
 */
export default function useChatAgent({ 
    onRoadmapUpdate, 
    onBusinessPlanUpdate, 
    onMessage,
    onError,
    onMeetingPrep,
    onChecklistComplete,
    onDocumentRequest,
    onResourceSuggested,
    onProgressSummary,
    onScheduleMeeting,
    userName = null
}) {
    const isConnected = ref(false);
    const conversationRef = ref(null);
    const businessPlanDataRef = ref(null);
    const previousRoadmapRef = ref(null);
    const contextSent = ref(false);

    // Format roadmap data as context string (same as voice agent)
    const formatRoadmapContext = (roadmapData) => {
        if (!roadmapData || !roadmapData.roadmap_json) return '';
        
        const roadmap = roadmapData.roadmap_json;
        const steps = roadmap.steps || [];
        
        if (steps.length === 0) {
            return 'ROADMAP STATUS: No roadmap steps have been created yet.';
        }
        
        let context = 'USER ROADMAP STATUS:\n\n';
        context += `Roadmap Title: ${roadmap.title || 'My Startup Roadmap'}\n\n`;
        context += `Current Roadmap Steps (${steps.length} total):\n\n`;
        
        steps.forEach((step, index) => {
            context += `${index + 1}. ${step.title || `Step ${step.order || index + 1}`}\n`;
            context += `   Status: ${(step.status || 'pending').toUpperCase()}\n`;
            if (step.description) {
                const desc = step.description.length > 100 
                    ? step.description.substring(0, 100) + '...' 
                    : step.description;
                context += `   Description: ${desc}\n`;
            }
            context += `   Order: ${step.order || index + 1}\n\n`;
        });
        
        context += '\nINSTRUCTIONS:\n';
        context += '- When creating new roadmap steps, check if similar steps already exist\n';
        context += '- If a step already exists, update it rather than creating a duplicate\n';
        context += '- You can add new steps that don\'t conflict with existing ones\n';
        context += '- Reference existing steps when relevant to the conversation\n';
        
        return context;
    };

    // Format business plan data as context string (same as voice agent)
    const formatBusinessPlanContext = (businessPlanData) => {
        if (!businessPlanData) return '';
        
        const filled = [];
        const missing = [];
        
        const fieldLabels = {
            country_of_origin: 'Country of origin',
            is_eu_resident: 'EU resident status',
            is_newcomer_to_finland: 'Newcomer to Finland',
            has_residence_permit: 'Has residence permit',
            residence_permit_type: 'Residence permit type',
            years_in_finland: 'Years in Finland',
            has_business_experience: 'Has business experience',
            language: 'Preferred language',
            business_name: 'Business name',
            company_contact_info: 'Company contact information',
            industry: 'Industry',
            company_planned_name: 'Company planned name',
            company_type: 'Company type',
            address: 'Address',
            zip_code: 'ZIP code',
            postal_district: 'Postal district',
            year_of_establishment: 'Year of establishment',
            number_of_employees: 'Number of employees',
            internet_address: 'Internet address',
            business_id: 'Business ID',
            company_owners_holdings: 'Company owners and holdings',
            business_idea: 'Business idea',
            competence_skills: 'Competence and skills',
            swot_analysis: 'SWOT analysis',
            products_services_general: 'Products/services (general)',
            products_services_detailed: 'Products/services (detailed)',
            sales_marketing: 'Sales and marketing',
            production_logistics: 'Production and logistics',
            distribution_network: 'Distribution network',
            target_market_groups: 'Target market groups',
            competitors: 'Competitors',
            competitive_situation: 'Competitive situation',
            third_parties_partners: 'Third parties/partners',
            operating_environment_risks: 'Operating environment/risks',
            vision_long_term: 'Vision/long-term goals',
            industry_future_prospects: 'Industry future prospects',
            permits_notices: 'Permits and notices',
            insurance_contracts: 'Insurance contracts',
            intellectual_property_rights: 'Intellectual property rights',
            support_network: 'Support network',
            my_business_comprehensive: 'My business (comprehensive)',
        };
        
        Object.keys(fieldLabels).forEach(key => {
            const value = businessPlanData[key];
            let isFilled = false;
            
            if (value !== null && value !== undefined) {
                if (typeof value === 'boolean') {
                    isFilled = true;
                    filled.push(`${fieldLabels[key]}: ${value}`);
                } else if (typeof value === 'number') {
                    isFilled = true;
                    filled.push(`${fieldLabels[key]}: ${value}`);
                } else if (typeof value === 'string' && value.trim() !== '') {
                    isFilled = true;
                    filled.push(`${fieldLabels[key]}: ${value}`);
                } else if (Array.isArray(value) && value.length > 0) {
                    isFilled = true;
                    filled.push(`${fieldLabels[key]}: ${JSON.stringify(value)}`);
                } else if (typeof value === 'object' && Object.keys(value).length > 0) {
                    isFilled = true;
                    filled.push(`${fieldLabels[key]}: ${JSON.stringify(value)}`);
                }
            }
            
            if (!isFilled) {
                missing.push(fieldLabels[key]);
            }
        });
        
        let context = 'USER BACKGROUND & BUSINESS PLAN INFORMATION:\n\n';
        
        const contextualFields = [
            'country_of_origin', 'is_eu_resident', 'is_newcomer_to_finland', 
            'has_residence_permit', 'residence_permit_type', 'years_in_finland',
            'has_business_experience', 'language'
        ];
        const contextualMissing = missing.filter(f => {
            const fieldKey = Object.keys(fieldLabels).find(k => fieldLabels[k] === f);
            return fieldKey && contextualFields.includes(fieldKey);
        });
        const businessPlanMissing = missing.filter(f => !contextualMissing.includes(f));
        
        const contextualFilled = filled.filter(f => {
            const fieldKey = Object.keys(fieldLabels).find(k => f.includes(fieldLabels[k]));
            return fieldKey && contextualFields.includes(fieldKey);
        });
        const businessPlanFilled = filled.filter(f => !contextualFilled.includes(f));
        
        if (contextualFilled.length > 0) {
            context += 'USER BACKGROUND CONTEXT (use this to personalize roadmap):\n';
            contextualFilled.forEach(field => {
                context += `- ${field}\n`;
            });
            context += '\n';
            
            const isEU = businessPlanData.is_eu_resident;
            const isNewcomer = businessPlanData.is_newcomer_to_finland;
            
            context += 'ROADMAP PERSONALIZATION INSTRUCTIONS:\n';
            if (isEU === false && isNewcomer === true) {
                context += '⚠️ CRITICAL: User is NON-EU and NEWCOMER - MUST include "Apply for residence permit for entrepreneurs" as step 1 or 2 in roadmap\n';
            } else if (isEU === true) {
                context += '✅ User is EU resident - SKIP residence permit steps in roadmap\n';
            }
            if (isNewcomer === true) {
                context += '📚 User is newcomer to Finland - Include steps about Finnish business culture and local networking\n';
            }
            if (businessPlanData.has_business_experience === true) {
                context += '💼 User has business experience - Can skip some basic business planning steps\n';
            }
            context += '\n';
        }
        
        if (contextualMissing.length > 0) {
            context += `⚠️ CRITICAL: You MUST ask about user background FIRST before business plan questions:\n\n`;
            context += 'MISSING BACKGROUND CONTEXT (ASK ABOUT THESE FIRST):\n';
            contextualMissing.forEach((field, index) => {
                context += `${index + 1}. ${field}\n`;
            });
            context += '\n';
            context += 'INSTRUCTIONS FOR BACKGROUND QUESTIONS:\n';
            context += '- Start by greeting the user, then immediately ask about their background (EU status, newcomer status, etc.)\n';
            context += '- Ask: "Are you an EU/EEA citizen, or will you need a residence permit to work in Finland?"\n';
            context += '- Ask: "Are you new to Finland, or have you been living here for a while?"\n';
            context += '- Ask: "Do you have previous business experience?"\n';
            context += '- Save answers using updateUserData with fields like is_eu_resident, is_newcomer_to_finland, has_residence_permit, has_business_experience\n';
            context += '- Use boolean values: true/false for yes/no questions\n';
            context += '- AFTER getting background context, create personalized roadmap, THEN ask about business plan fields\n\n';
        }
        
        if (businessPlanFilled.length > 0) {
            context += 'FILLED BUSINESS PLAN FIELDS (you already know this):\n';
            businessPlanFilled.forEach(field => {
                context += `- ${field}\n`;
            });
            context += '\n';
        }
        
        if (businessPlanMissing.length > 0) {
            context += `⚠️ IMPORTANT: You need to actively ask the user about ${businessPlanMissing.length} missing business plan fields.\n\n`;
            context += 'MISSING BUSINESS PLAN FIELDS (ASK ABOUT THESE AFTER BACKGROUND CONTEXT):\n';
            businessPlanMissing.forEach((field, index) => {
                context += `${index + 1}. ${field}\n`;
            });
            context += '\n';
            context += 'INSTRUCTIONS FOR BUSINESS PLAN QUESTIONS:\n';
            context += '- Ask ONE question at a time, wait for the answer, then IMMEDIATELY use updateUserData tool to save it\n';
            context += '- After saving, immediately ask about the next missing field\n';
            context += '- Continue this process until all missing fields are filled\n';
            context += '- Be conversational and friendly, but be proactive - don\'t wait for the user to volunteer information\n';
            context += '- Once you have enough information, create personalized roadmap steps using updateRoadmap tool\n\n';
        }
        
        if (contextualMissing.length === 0 && businessPlanMissing.length === 0) {
            context += '✅ All fields have been filled. You can now focus on building and refining the roadmap.';
        }
        
        return context;
    };

    // Initialize chat conversation
    const initializeChat = async (businessPlanData, roadmapData, advisorsData) => {
        if (conversationRef.value && isConnected.value) {
            // Already connected
            return;
        }

        try {
            const apiKey = import.meta.env.VITE_ELEVENLABS_API_KEY;
            const agentId = import.meta.env.VITE_ELEVENLABS_AGENT_ID;

            if (!apiKey) {
                throw new Error('ElevenLabs API key not found. Please set VITE_ELEVENLABS_API_KEY in your .env file.');
            }

            if (!agentId) {
                throw new Error('ElevenLabs Agent ID not found. Please set VITE_ELEVENLABS_AGENT_ID in your .env file.');
            }

            // Format context
            businessPlanDataRef.value = businessPlanData;
            const businessPlanContext = formatBusinessPlanContext(businessPlanData);
            const roadmapContext = formatRoadmapContext(roadmapData);
            
            if (roadmapData && roadmapData.roadmap_json) {
                previousRoadmapRef.value = JSON.parse(JSON.stringify(roadmapData.roadmap_json));
            }

            // Build full context
            let contextWithFirstMessage = '';
            
            if (businessPlanContext) {
                contextWithFirstMessage += businessPlanContext;
            }
            
            if (roadmapContext) {
                if (contextWithFirstMessage) {
                    contextWithFirstMessage += '\n\n' + '='.repeat(50) + '\n\n';
                }
                contextWithFirstMessage += roadmapContext;
            }

            // Add advisors context if available
            if (advisorsData && advisorsData.length > 0) {
                if (contextWithFirstMessage) {
                    contextWithFirstMessage += '\n\n' + '='.repeat(50) + '\n\n';
                }
                contextWithFirstMessage += 'AVAILABLE ADVISORS:\n\n';
                advisorsData.forEach(advisor => {
                    contextWithFirstMessage += `- ${advisor.name} (${advisor.email})\n`;
                    if (advisor.specialization) {
                        const specializationLabels = {
                            'residence_permit': 'Residence Permit Applications',
                            'business_registration': 'Business Registration & Trade Register',
                            'tax': 'Tax Matters, VAT & Accounting',
                            'funding': 'Funding, Grants & Investors',
                            'legal': 'Legal Matters, Contracts & IP',
                            'marketing': 'Marketing, Sales & Branding',
                        };
                        contextWithFirstMessage += `  Specialization: ${specializationLabels[advisor.specialization] || advisor.specialization}\n`;
                    }
                    contextWithFirstMessage += '\n';
                });
                contextWithFirstMessage += 'You can suggest these advisors to the user when creating roadmap steps that match their specializations.\n';
            }

            // Build first message
            const buildFirstMessage = (businessPlan, roadmap, userName) => {
                let message = "Hi! I'm Eppu the Bear, your AI startup coach! 🐻\n\n";
                
                if (userName) {
                    message += `Nice to meet you, ${userName}!\n\n`;
                }
                
                const hasExistingRoadmap = roadmap?.roadmap_json?.steps && 
                    roadmap.roadmap_json.steps.filter(s => !s.isQuestion).length > 0;
                
                if (hasExistingRoadmap) {
                    message += "I see you already have a roadmap started - that's great! Let's continue building on that.\n\n";
                } else {
                    message += "Ready to build your startup roadmap? Let's get started!\n\n";
                }
                
                const contextualFields = [
                    'country_of_origin', 'is_eu_resident', 'is_newcomer_to_finland',
                    'has_residence_permit', 'residence_permit_type', 'years_in_finland',
                    'has_business_experience', 'language'
                ];
                const missingBackgroundInfo = contextualFields.some(field => {
                    const value = businessPlan?.[field];
                    return value === null || value === undefined || value === '';
                });
                
                if (missingBackgroundInfo) {
                    message += "First, let me ask a few quick questions to personalize your journey:\n\n";
                    message += "Are you an EU/EEA citizen, or will you need a residence permit to work in Finland?";
                } else {
                    message += "Let's talk about your business idea!";
                }
                
                return message;
            };
            
            const firstMessage = buildFirstMessage(businessPlanData, roadmapData, userName);

            // Add first message to context
            if (contextWithFirstMessage && firstMessage) {
                contextWithFirstMessage += '\n\n' + '='.repeat(50) + '\n\n';
                contextWithFirstMessage += 'FIRST MESSAGE TO SAY (START CHATTING IMMEDIATELY):\n';
                contextWithFirstMessage += firstMessage;
                contextWithFirstMessage += '\n\n⚠️ ACTION REQUIRED: You MUST start the conversation by saying this message (or a natural variation of it) RIGHT NOW. This message is personalized based on the user\'s current data.';
            }

            // Configure for text-only mode with proper structure
            // Use VoiceConversation with text_only configuration override for chat mode
            // This ensures no microphone/audio access is requested
            const conversationConfigOverride = {
                conversation: {
                    text_only: true
                },
                // Explicitly disable audio input/output in the configuration
                // Some SDK versions may require this in addition to text_only
                audio: {
                    input_enabled: false,
                    output_enabled: false
                }
            };

            // Start conversation using VoiceConversation with text_only override for chat mode
            // The JavaScript SDK uses VoiceConversation for both voice and text-only conversations
            // With text_only: true, the SDK will NOT request microphone permissions or use audio
            const conversation = await VoiceConversation.startSession({
                agentId: agentId,
                apiKey: apiKey,
                conversationConfigOverride: conversationConfigOverride,
                // NOTE: text_only: true in conversationConfigOverride ensures:
                // - No microphone access is requested
                // - No audio input/output is used
                // - All communication is text-only via WebSocket
                // CRITICAL: agent_response callback is required for chat mode
                // For chat mode, responses come via agent_response callback
                callback_agent_response: (response) => {
                    console.log('Agent response (chat mode):', response);
                    if (onMessage) {
                        const cleanedResponse = stripCharacterTags(response);
                        onMessage({
                            type: 'assistant',
                            text: cleanedResponse
                        });
                    }
                },
                callback_user_transcript: (transcript) => {
                    console.log('User transcript:', transcript);
                    if (onMessage) {
                        onMessage({
                            type: 'user',
                            text: transcript
                        });
                    }
                },
                // Also use onMessage pattern (JavaScript SDK pattern for voice mode compatibility)
                onMessage: (props) => {
                    console.log('Message received (onMessage):', props);
                    if (onMessage && props.message) {
                        const messageText = stripCharacterTags(props.message || '');
                        onMessage({
                            type: props.source === 'user' ? 'user' : 'assistant',
                            text: messageText
                        });
                    }
                },
                // Handle tool calls
                clientTools: {
                    updateRoadmap: async (parameters) => {
                        console.log('Agent called updateRoadmap tool:', parameters);
                        try {
                            const roadmapData = parameters.roadmap || parameters;
                            if (roadmapData && onRoadmapUpdate) {
                                await onRoadmapUpdate(roadmapData);
                            }
                            return `Roadmap updated successfully`;
                        } catch (error) {
                            console.error('Error in updateRoadmap tool:', error);
                            return `Error updating roadmap: ${error.message}`;
                        }
                    },
                    updateUserData: async (parameters) => {
                        console.log('Agent called updateUserData tool:', parameters);
                        try {
                            const businessPlanData = parameters.business_plan || parameters.businessPlan || parameters;
                            if (businessPlanData && onBusinessPlanUpdate) {
                                await onBusinessPlanUpdate(businessPlanData);
                            }
                            return 'Business plan updated successfully';
                        } catch (error) {
                            console.error('Error in updateUserData tool:', error);
                            return `Error updating user data: ${error.message}`;
                        }
                    },
                    markChecklistComplete: async (parameters) => {
                        console.log('Agent called markChecklistComplete tool:', parameters);
                        try {
                            if (onChecklistComplete) {
                                await onChecklistComplete(parameters);
                            }
                            return 'Step marked as complete';
                        } catch (error) {
                            console.error('Error in markChecklistComplete tool:', error);
                            return `Error marking checklist complete: ${error.message}`;
                        }
                    },
                    requestDocument: async (parameters) => {
                        console.log('Agent called requestDocument tool:', parameters);
                        try {
                            if (onDocumentRequest) {
                                await onDocumentRequest(parameters);
                            }
                            return 'Document request created';
                        } catch (error) {
                            console.error('Error in requestDocument tool:', error);
                            return `Error creating document request: ${error.message}`;
                        }
                    },
                    suggestResource: async (parameters) => {
                        console.log('Agent called suggestResource tool:', parameters);
                        try {
                            if (onResourceSuggested) {
                                await onResourceSuggested(parameters);
                            }
                            return 'Resource suggested successfully';
                        } catch (error) {
                            console.error('Error in suggestResource tool:', error);
                            return `Error suggesting resource: ${error.message}`;
                        }
                    },
                    generateProgressSummary: async (parameters) => {
                        console.log('Agent called generateProgressSummary tool:', parameters);
                        try {
                            if (onProgressSummary) {
                                await onProgressSummary(parameters);
                            }
                            return 'Progress summary generated';
                        } catch (error) {
                            console.error('Error in generateProgressSummary tool:', error);
                            return `Error generating progress summary: ${error.message}`;
                        }
                    },
                    scheduleAdvisorMeeting: async (parameters) => {
                        console.log('Agent called scheduleAdvisorMeeting tool:', parameters);
                        try {
                            if (onScheduleMeeting) {
                                await onScheduleMeeting(parameters);
                            }
                            return 'Meeting scheduling modal opened';
                        } catch (error) {
                            console.error('Error in scheduleAdvisorMeeting tool:', error);
                            return `Error opening scheduling modal: ${error.message}`;
                        }
                    }
                },
                // Handle errors
                onError: (message, context) => {
                    console.error('Conversation error:', message, context);
                    isConnected.value = false;
                    if (onError) {
                        onError(message || 'Chat agent error');
                    }
                }
            });

            conversationRef.value = conversation;
            isConnected.value = true;

            // Explicitly disable microphone/audio input for text-only mode
            // Even with text_only: true, the SDK might still initialize audio
            try {
                // Try to mute/disable audio input immediately after session starts
                if (typeof conversation.setMuted === 'function') {
                    conversation.setMuted(true);
                    console.log('Microphone muted for text-only mode');
                } else if (typeof conversation.mute === 'function') {
                    conversation.mute();
                    console.log('Microphone muted via mute() for text-only mode');
                } else if (typeof conversation.disableAudioInput === 'function') {
                    conversation.disableAudioInput();
                    console.log('Audio input disabled for text-only mode');
                } else {
                    console.warn('No mute/disable method found. Available methods:', Object.keys(conversation).join(', '));
                }
            } catch (error) {
                console.warn('Failed to disable microphone (may not be needed with text_only: true):', error);
            }

            // Send context after session starts
            if (contextWithFirstMessage && !contextSent.value) {
                try {
                    // Use sendContextualUpdate() method as per ElevenLabs SDK
                    if (typeof conversation.sendContextualUpdate === 'function') {
                        await conversation.sendContextualUpdate(contextWithFirstMessage);
                        console.log('Context sent to agent via sendContextualUpdate');
                    } else if (typeof conversation.send_contextual_update === 'function') {
                        // Fallback: snake_case version
                        await conversation.send_contextual_update(contextWithFirstMessage);
                        console.log('Context sent to agent via send_contextual_update');
                    } else {
                        console.warn('sendContextualUpdate not available. Available methods: ' + Object.keys(conversation).join(', '));
                    }
                    contextSent.value = true;
                } catch (error) {
                    console.error('Failed to send context to agent:', error);
                    // Continue anyway - agent might still work, but log the error
                }
            }

            // Agent should respond with first message based on context
            // The callback_agent_response will handle displaying it

        } catch (error) {
            console.error('Failed to initialize chat:', error);
            isConnected.value = false;
            if (onError) {
                onError(error.message || 'Failed to initialize chat agent');
            }
        }
    };

    // Send message using Conversation API send_user_message method
    const sendMessage = async (message) => {
        if (!conversationRef.value || !isConnected.value) {
            // Try to initialize if not connected
            await initializeChat(businessPlanDataRef.value, null, null);
            if (!conversationRef.value) {
                throw new Error('Chat agent not initialized');
            }
        }

        try {
            const conversation = conversationRef.value;
            
            // Use sendUserMessage() method (camelCase) as per ElevenLabs SDK
            if (typeof conversation.sendUserMessage === 'function') {
                // Send message using Conversation API (correct method name)
                await conversation.sendUserMessage(message);
                console.log('Message sent via sendUserMessage:', message);
            } else if (typeof conversation.send_user_message === 'function') {
                // Fallback: snake_case version
                await conversation.send_user_message(message);
                console.log('Message sent via send_user_message:', message);
            } else if (typeof conversation.send === 'function') {
                // Fallback: generic send method
                await conversation.send(message);
                console.log('Message sent via send method:', message);
            } else {
                throw new Error('No send method available on conversation object. Available methods: ' + Object.keys(conversation).join(', '));
            }
                
            // Note: Agent response will be received via callback_agent_response callback
            // which is already configured in initializeChat
        } catch (error) {
            console.error('Error sending message:', error);
            isConnected.value = false;
            if (onError) {
                onError(error.message || 'Failed to send message');
            }
            throw error;
        }
    };

    // Note: Tool calls are now handled directly in the clientTools configuration
    // This function is kept for backwards compatibility but may not be needed
    const handleToolCall = async (toolCall) => {
        console.warn('handleToolCall called - tools should be handled via clientTools configuration:', toolCall);
    };

    // Disconnect
    const disconnect = () => {
        if (conversationRef.value) {
            try {
                // End the conversation session if method is available
                if (typeof conversationRef.value.endSession === 'function') {
                    conversationRef.value.endSession();
                } else if (typeof conversationRef.value.end_session === 'function') {
                    conversationRef.value.end_session();
                } else if (typeof conversationRef.value.end === 'function') {
                    conversationRef.value.end();
                }
            } catch (error) {
                console.warn('Error ending conversation session:', error);
            } finally {
        conversationRef.value = null;
            }
        }
        isConnected.value = false;
        contextSent.value = false;
    };

    onUnmounted(() => {
        disconnect();
    });

    return {
        isConnected,
        initializeChat,
        sendMessage,
        disconnect
    };
}
