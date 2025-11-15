import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';

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

    // Update agent's first message via ElevenLabs API
    const updateAgentFirstMessage = async (firstMessage) => {
        try {
            const apiKey = import.meta.env.VITE_ELEVENLABS_API_KEY;
            const agentId = import.meta.env.VITE_ELEVENLABS_AGENT_ID;

            if (!apiKey || !agentId) {
                console.warn('Cannot update agent first message: API key or agent ID missing');
                return false;
            }

            // Update agent configuration with new first message
            // Using PATCH to update only the first_message field
            const response = await axios.patch(
                `https://api.elevenlabs.io/v1/convai/agents/${agentId}`,
                {
                    first_message: firstMessage
                },
                {
                    headers: {
                        'xi-api-key': apiKey,
                        'Content-Type': 'application/json'
                    }
                }
            );

            if (response.data) {
                console.log('Agent first message updated successfully');
                return true;
            }

            return false;
        } catch (error) {
            // Log but don't fail - agent might still work with context-based first message
            console.warn('Failed to update agent first message via API:', error.response?.data || error.message);
            // Try alternative endpoint format if the above fails
            try {
                const apiKey = import.meta.env.VITE_ELEVENLABS_API_KEY;
                const agentId = import.meta.env.VITE_ELEVENLABS_AGENT_ID;
                
                // Alternative: Try PUT with full agent config
                const getResponse = await axios.get(
                    `https://api.elevenlabs.io/v1/convai/agents/${agentId}`,
                    {
                        headers: {
                            'xi-api-key': apiKey
                        }
                    }
                );

                if (getResponse.data) {
                    const agentConfig = getResponse.data;
                    agentConfig.first_message = firstMessage;
                    
                    const putResponse = await axios.put(
                        `https://api.elevenlabs.io/v1/convai/agents/${agentId}`,
                        agentConfig,
                        {
                            headers: {
                                'xi-api-key': apiKey,
                                'Content-Type': 'application/json'
                            }
                        }
                    );

                    if (putResponse.data) {
                        console.log('Agent first message updated successfully (via PUT)');
                        return true;
                    }
                }
            } catch (altError) {
                console.warn('Alternative update method also failed:', altError.response?.data || altError.message);
            }
            
            return false;
        }
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

            // Update agent's first message via API before initializing conversation
            if (firstMessage) {
                await updateAgentFirstMessage(firstMessage);
            }

            // Add first message to context
            if (contextWithFirstMessage && firstMessage) {
                contextWithFirstMessage += '\n\n' + '='.repeat(50) + '\n\n';
                contextWithFirstMessage += 'FIRST MESSAGE TO SAY (START CHATTING IMMEDIATELY):\n';
                contextWithFirstMessage += firstMessage;
                contextWithFirstMessage += '\n\n⚠️ ACTION REQUIRED: You MUST start the conversation by saying this message (or a natural variation of it) RIGHT NOW. This message is personalized based on the user\'s current data.';
            }

            // For text-only chat, use REST API directly
            // VoiceConversation SDK is designed for voice, not text chat
            // Store agent info for REST API calls
            conversationRef.value = {
                agent_id: agentId,
                api_key: apiKey,
                conversation_id: null, // Will be set after first message
                text_only: true
            };
            isConnected.value = true;

            // Show personalized first message immediately to user
            if (firstMessage && !contextSent.value) {
                console.log('Showing personalized first message:', firstMessage);
                if (onMessage) {
                    onMessage({
                        type: 'assistant',
                        text: firstMessage
                    });
                }
            } else if (!firstMessage && !contextSent.value) {
                // Fallback if first message wasn't built
                console.warn('No first message built, using default');
                const defaultMessage = "Hi! I'm Eppu the Bear, your AI startup coach! 🐻\n\nReady to build your startup roadmap? Let's get started!";
                if (onMessage) {
                    onMessage({
                        type: 'assistant',
                        text: defaultMessage
                    });
                }
            }

            // Extract variables for ElevenLabs dynamic variables
            // Simple variable substitution - no conditionals, just pass values (or empty strings)
            const extractVariables = (businessPlan, roadmap, userName) => {
                const variables = {};
                
                // User name (string) - always pass, use empty string if not available
                variables.user_name = userName || '';
                
                // Business name (string) - prefer business_name, fallback to company_planned_name, or empty
                variables.business_name = businessPlan?.business_name || 
                                         businessPlan?.company_planned_name || 
                                         '';
                
                // Industry (string) - or empty string
                variables.industry = businessPlan?.industry || '';
                
                // Business idea (string) - short version for personalization, or empty
                if (businessPlan?.business_idea) {
                    const idea = businessPlan.business_idea;
                    // Get first sentence or first 30 chars
                    const shortIdea = idea.split('.')[0].substring(0, 30).trim();
                    variables.business_idea = shortIdea || '';
                } else {
                    variables.business_idea = '';
                }
                
                return variables;
            };
            
            const dynamicVariables = extractVariables(businessPlanData, roadmapData, userName);
            console.log('Dynamic variables for ElevenLabs:', dynamicVariables);

            // Send context to initialize conversation (without showing trigger message)
            if (contextWithFirstMessage && !contextSent.value) {
                try {
                    console.log('Sending context to agent, context length:', contextWithFirstMessage.length);
                    
                    // Try sending with empty message first
                    let response;
                    try {
                        response = await axios.post(
                            'https://api.elevenlabs.io/v1/convai/conversation',
                            {
                                agent_id: agentId,
                                text_only: true,
                                message: '', // Empty message to just send context
                                contextual_update: contextWithFirstMessage,
                                dynamic_variables: dynamicVariables // Pass variables for first message
                            },
                            {
                                headers: {
                                    'xi-api-key': apiKey,
                                    'Content-Type': 'application/json'
                                }
                            }
                        );
                    } catch (emptyMsgError) {
                        // If empty message doesn't work, try with a minimal trigger
                        console.log('Empty message failed, trying with minimal trigger');
                        response = await axios.post(
                            'https://api.elevenlabs.io/v1/convai/conversation',
                            {
                                agent_id: agentId,
                                text_only: true,
                                message: 'start', // Minimal trigger
                                contextual_update: contextWithFirstMessage,
                                dynamic_variables: dynamicVariables // Pass variables for first message
                            },
                            {
                                headers: {
                                    'xi-api-key': apiKey,
                                    'Content-Type': 'application/json'
                                }
                            }
                        );
                    }

                    // Store conversation ID if provided
                    if (response?.data?.conversation_id) {
                        conversationRef.value.conversation_id = response.data.conversation_id;
                        console.log('Conversation ID stored:', response.data.conversation_id);
                    }

                    contextSent.value = true;
                    console.log('Context sent to agent successfully');
                } catch (error) {
                    console.error('Failed to send context to agent:', error.response?.data || error.message);
                    // Context send failed, but message already shown
                    contextSent.value = true;
                }
            } else if (!contextSent.value) {
                // No context to send, just mark as sent
                contextSent.value = true;
            }

        } catch (error) {
            console.error('Failed to initialize chat:', error);
            isConnected.value = false;
            if (onError) {
                onError(error.message || 'Failed to initialize chat agent');
            }
        }
    };

    // Send message using ElevenLabs REST API directly for text chat
    const sendMessage = async (message) => {
        if (!conversationRef.value || !isConnected.value) {
            // Try to initialize if not connected
            await initializeChat(businessPlanDataRef.value, null, null);
            if (!conversationRef.value) {
                throw new Error('Chat agent not initialized');
            }
        }

        try {
            const apiKey = import.meta.env.VITE_ELEVENLABS_API_KEY;
            const agentId = import.meta.env.VITE_ELEVENLABS_AGENT_ID;
            const conversationId = conversationRef.value.conversation_id || conversationRef.value.session_id || null;

            // Use ElevenLabs REST API for text chat
            const response = await axios.post(
                'https://api.elevenlabs.io/v1/convai/conversation',
                {
                    agent_id: agentId,
                    text_only: true,
                    message: message,
                    ...(conversationId && { conversation_id: conversationId })
                },
                {
                    headers: {
                        'xi-api-key': apiKey,
                        'Content-Type': 'application/json'
                    }
                }
            );

            // Handle response
            if (response.data) {
                const agentResponse = response.data.agent_response || response.data.response || response.data.message;
                if (agentResponse && onMessage) {
                    const cleanedResponse = stripCharacterTags(agentResponse);
                    onMessage({
                        type: 'assistant',
                        text: cleanedResponse
                    });
                }

                // Update conversation ID if provided
                if (response.data.conversation_id) {
                    conversationRef.value.conversation_id = response.data.conversation_id;
                }

                // Handle tool calls if any (tools are executed server-side by ElevenLabs)
                if (response.data.tool_calls && Array.isArray(response.data.tool_calls)) {
                    for (const toolCall of response.data.tool_calls) {
                        await handleToolCall(toolCall);
                    }
                }
                
                // Check if response includes updates from tools
                if (response.data.roadmap && onRoadmapUpdate) {
                    await onRoadmapUpdate(response.data.roadmap);
                }
                if (response.data.business_plan && onBusinessPlanUpdate) {
                    await onBusinessPlanUpdate(response.data.business_plan);
                }
            }
        } catch (error) {
            console.error('Error sending message:', error);
            if (onError) {
                onError(error.response?.data?.detail?.message || error.message || 'Failed to send message');
            }
            throw error;
        }
    };

    // Handle tool calls from ElevenLabs agent response
    const handleToolCall = async (toolCall) => {
        const toolName = toolCall.name || toolCall.tool_name;
        const parameters = toolCall.parameters || toolCall.input || {};

        if (!toolName) {
            console.warn('Tool call missing name:', toolCall);
            return;
        }

        console.log('Handling tool call:', toolName, parameters);

        try {
            switch (toolName) {
                case 'updateRoadmap':
                    if (parameters.roadmap && onRoadmapUpdate) {
                        await onRoadmapUpdate(parameters.roadmap);
                    }
                    break;
                case 'updateUserData':
                case 'updateBusinessPlan':
                    const businessPlanData = parameters.business_plan || parameters.businessPlan || parameters;
                    if (businessPlanData && onBusinessPlanUpdate) {
                        await onBusinessPlanUpdate(businessPlanData);
                    }
                    break;
                case 'markChecklistComplete':
                    if (onChecklistComplete) {
                        await onChecklistComplete(parameters);
                    }
                    break;
                case 'requestDocument':
                    if (onDocumentRequest) {
                        await onDocumentRequest(parameters);
                    }
                    break;
                case 'suggestResource':
                    if (onResourceSuggested) {
                        await onResourceSuggested(parameters);
                    }
                    break;
                case 'generateProgressSummary':
                    if (onProgressSummary) {
                        await onProgressSummary(parameters);
                    }
                    break;
                case 'scheduleAdvisorMeeting':
                    if (onScheduleMeeting) {
                        await onScheduleMeeting(parameters);
                    }
                    break;
                default:
                    console.warn('Unknown tool call:', toolName);
            }
        } catch (error) {
            console.error(`Error handling tool call ${toolName}:`, error);
        }
    };

    // Disconnect
    const disconnect = () => {
        // For REST API, we just need to clear the conversation reference
        conversationRef.value = null;
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
