import { ref, onMounted, onUnmounted } from 'vue';
import axios from 'axios';
import { VoiceConversation } from '@elevenlabs/client';

/**
 * useVoiceAgent Composable
 * Vue composable for ElevenLabs Agents Voice Conversation integration
 */
export default function useVoiceAgent({ 
    onRoadmapUpdate, 
    onBusinessPlanUpdate, 
    onTranscript, 
    onError,
    onMeetingPrep,
    onChecklistComplete,
    onDocumentRequest,
    onResourceSuggested,
    onProgressSummary,
    userName = null // Optional: user's name from backend
}) {
    const isConnected = ref(false);
    const isListening = ref(false);
    const isSpeaking = ref(false);
    const connectionStatus = ref('disconnected');
    const conversationRef = ref(null);
    const sessionIdRef = ref(null);
    const businessPlanDataRef = ref(null);
    const previousRoadmapRef = ref(null);

    // Format roadmap data as context string
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

    // Format business plan data as context string
    const formatBusinessPlanContext = (businessPlanData) => {
        if (!businessPlanData) return '';
        
        const filled = [];
        const missing = [];
        
        const fieldLabels = {
            // Contextual fields (for personalized roadmap)
            country_of_origin: 'Country of origin',
            is_eu_resident: 'EU resident status',
            is_newcomer_to_finland: 'Newcomer to Finland',
            has_residence_permit: 'Has residence permit',
            residence_permit_type: 'Residence permit type',
            years_in_finland: 'Years in Finland',
            has_business_experience: 'Has business experience',
            language: 'Preferred language',
            // Business plan fields
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
            products_services_general: 'Products and services (general)',
            products_services_detailed: 'Products and services (detailed)',
            sales_marketing: 'Sales and marketing',
            production_logistics: 'Production and logistics',
            distribution_network: 'Distribution network',
            target_market_groups: 'Target market and target groups',
            competitors: 'Competitors',
            competitive_situation: 'Competitive situation',
            third_parties_partners: 'Third parties and partners',
            operating_environment_risks: 'Operating environment risks',
            vision_long_term: 'Long-term vision',
            industry_future_prospects: 'Industry future prospects',
            permits_notices: 'Permits and notices',
            insurance_contracts: 'Insurance and contracts',
            intellectual_property_rights: 'Intellectual property rights',
            support_network: 'Support network',
            my_business_comprehensive: 'My business comprehensive description',
        };
        
        Object.keys(fieldLabels).forEach(key => {
            const value = businessPlanData[key];
            // Check if field is filled - handle booleans, numbers, strings, arrays, and objects
            let isFilled = false;
            
            if (value !== null && value !== undefined) {
                if (typeof value === 'boolean') {
                    // Boolean values (true or false) are considered filled
                    isFilled = true;
                    filled.push(`${fieldLabels[key]}: ${value}`);
                } else if (typeof value === 'number') {
                    // Numbers are considered filled (including 0)
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
        
        // Separate contextual fields from business plan fields
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
        
        // Show contextual information first
        if (contextualFilled.length > 0) {
            context += 'USER BACKGROUND CONTEXT (use this to personalize roadmap):\n';
            contextualFilled.forEach(field => {
                context += `- ${field}\n`;
            });
            context += '\n';
            
            // Provide roadmap personalization guidance
            const isEU = businessPlanData.is_eu_resident;
            const isNewcomer = businessPlanData.is_newcomer_to_finland;
            const hasPermit = businessPlanData.has_residence_permit;
            
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

    onUnmounted(() => {
        if (conversationRef.value) {
            try {
                conversationRef.value.endSession();
            } catch (error) {
                console.error('Error ending conversation:', error);
            }
        }
    });

    const connect = async () => {
        if (conversationRef.value && isConnected.value) {
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

            const apiKey = import.meta.env.VITE_ELEVENLABS_API_KEY;
            const agentId = import.meta.env.VITE_ELEVENLABS_AGENT_ID;

            if (!apiKey) {
                throw new Error('ElevenLabs API key not found. Please set VITE_ELEVENLABS_API_KEY in your .env file.');
            }

            if (!agentId) {
                throw new Error('ElevenLabs Agent ID not found. Please set VITE_ELEVENLABS_AGENT_ID in your .env file.');
            }

            // Debug: Check environment variables
            console.log('Environment check:', {
                hasApiKey: !!apiKey,
                hasAgentId: !!agentId,
                envKeys: Object.keys(import.meta.env).filter(key => key.includes('ELEVENLABS'))
            });

            // Fetch business plan, roadmap, and advisors data for context
            let businessPlanContext = '';
            let roadmapContext = '';
            let advisorsContext = '';
            
            try {
                // Fetch advisors
                const advisorsResponse = await axios.get('/api/advisors');
                if (advisorsResponse.data && advisorsResponse.data.advisors && advisorsResponse.data.advisors.length > 0) {
                    const advisors = advisorsResponse.data.advisors;
                    advisorsContext = 'AVAILABLE ADVISORS:\n\n';
                    advisors.forEach(advisor => {
                        advisorsContext += `- ${advisor.name} (${advisor.email})\n`;
                        if (advisor.specialization) {
                            const specializationLabels = {
                                'residence_permit': 'Residence Permit Applications',
                                'business_registration': 'Business Registration & Trade Register',
                                'tax': 'Tax Matters, VAT & Accounting',
                                'funding': 'Funding, Grants & Investors',
                                'legal': 'Legal Matters, Contracts & IP',
                                'marketing': 'Marketing, Sales & Branding',
                            };
                            advisorsContext += `  Specialization: ${specializationLabels[advisor.specialization] || advisor.specialization}\n`;
                        }
                        advisorsContext += '\n';
                    });
                    advisorsContext += 'You can suggest these advisors to the user when creating roadmap steps that match their specializations.\n';
                }
                
                const businessPlanResponse = await axios.get('/api/business-plan');
                console.log('Business plan API response:', businessPlanResponse.data);
                businessPlanDataRef.value = businessPlanResponse.data;
                businessPlanContext = formatBusinessPlanContext(businessPlanResponse.data);
                console.log('Business plan context prepared:', businessPlanContext);
                console.log('Context length:', businessPlanContext.length);
                
                // Log filled vs missing fields
                const filledCount = Object.values(businessPlanResponse.data).filter(v => 
                    v !== null && v !== undefined && v !== ''
                ).length;
                console.log(`Business plan: ${filledCount} fields filled, ${33 - filledCount} missing`);
            } catch (error) {
                console.error('Failed to fetch business plan data for context:', error);
                console.error('Error details:', error.response?.data || error.message);
                // Continue without context if fetch fails
            }

            // Fetch roadmap data for context
            let roadmapData = null;
            try {
                const roadmapResponse = await axios.get('/api/roadmap');
                console.log('Roadmap API response:', roadmapResponse.data);
                roadmapData = roadmapResponse.data;
                // Store previous roadmap state for comparison
                if (roadmapData && roadmapData.roadmap_json) {
                    previousRoadmapRef.value = JSON.parse(JSON.stringify(roadmapData.roadmap_json));
                }
                roadmapContext = formatRoadmapContext(roadmapData);
                console.log('Roadmap context prepared:', roadmapContext);
            } catch (error) {
                console.warn('Failed to fetch roadmap data for context:', error);
                // Continue without roadmap context if fetch fails
            }

            // Build first message dynamically from backend data
            const buildFirstMessage = (businessPlan, roadmap, userName) => {
                let message = "Hi! I'm Eppu the Bear, your AI startup coach! 🐻\n\n";
                
                // Add user name if available
                if (userName) {
                    message += `Nice to meet you, ${userName}!\n\n`;
                }
                
                // Check if user has existing roadmap
                const hasExistingRoadmap = roadmap?.roadmap_json?.steps && 
                    roadmap.roadmap_json.steps.filter(s => !s.isQuestion).length > 0;
                
                if (hasExistingRoadmap) {
                    message += "I see you already have a roadmap started - that's great! Let's continue building on that.\n\n";
                } else {
                    message += "Ready to build your startup roadmap? Let's get started!\n\n";
                }
                
                // Check if background info is missing
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
            
            const firstMessage = buildFirstMessage(
                businessPlanDataRef.value, 
                roadmapData, 
                userName
            );
            console.log('Built first message:', firstMessage);

            // Build context with first message instruction
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
            
            if (advisorsContext) {
                if (contextWithFirstMessage) {
                    contextWithFirstMessage += '\n\n' + '='.repeat(50) + '\n\n';
                }
                contextWithFirstMessage += advisorsContext;
            }
            
            // Add first message instruction to context
            if (contextWithFirstMessage) {
                contextWithFirstMessage += '\n\n---\n\n🚨 CRITICAL: YOU MUST SPEAK FIRST 🚨\n\n';
                contextWithFirstMessage += 'FIRST MESSAGE TO SAY (START SPEAKING IMMEDIATELY):\n';
                contextWithFirstMessage += firstMessage;
                contextWithFirstMessage += '\n\n⚠️ ACTION REQUIRED: You MUST start the conversation by speaking this message (or a natural variation of it) RIGHT NOW. Do not wait for the user to speak first. This message is personalized based on the user\'s current data.';
            }
            
            console.log('Full context with first message:', contextWithFirstMessage.substring(0, 500) + '...');

            // Start conversation using the official SDK
            const conversation = await VoiceConversation.startSession({
                agentId: agentId,
                apiKey: apiKey,
                clientTools: {
                    setContext: async (parameters) => {
                        console.log('Agent called setContext tool:', parameters);
                        try {
                            const contextData = parameters.businessPlan || parameters.context || parameters;
                            if (!contextData || typeof contextData !== 'object') {
                                console.warn('Invalid context data from tool call:', parameters);
                                return 'Error: Invalid context data format';
                            }
                            
                            // Store context in ref for later use
                            businessPlanDataRef.value = {
                                ...businessPlanDataRef.value,
                                ...contextData
                            };
                            
                            console.log('Context set successfully:', Object.keys(contextData));
                            return 'Context set successfully. You now have access to the user\'s business plan information.';
                        } catch (error) {
                            console.error('Error in setContext tool:', error);
                            return `Error setting context: ${error.message}`;
                        }
                    },
                    updateRoadmap: async (parameters) => {
                        console.log('Agent called updateRoadmap tool:', parameters);
                        try {
                            const roadmapData = parameters.roadmap || parameters;
                            if (!roadmapData || typeof roadmapData !== 'object') {
                                console.warn('Invalid roadmap data from tool call:', parameters);
                                return 'Error: Invalid roadmap data format';
                            }
                            if (!roadmapData.steps || !Array.isArray(roadmapData.steps)) {
                                console.warn('Roadmap missing steps array:', roadmapData);
                                return 'Error: Roadmap must include a steps array';
                            }
                            
                            // Validate and normalize each step
                            const validatedSteps = roadmapData.steps.map((step, index) => {
                                if (!step || typeof step !== 'object') {
                                    console.warn(`Invalid step at index ${index}:`, step);
                                    return null;
                                }
                                
                                // Ensure required fields exist
                                const validatedStep = {
                                    title: step.title || `Step ${index + 1}`,
                                    description: step.description || step.title || `Complete this step`,
                                    order: step.order !== undefined ? Number(step.order) : (index + 1),
                                    status: step.status || 'pending'
                                };
                                
                                // Validate status
                                if (!['pending', 'in_progress', 'completed'].includes(validatedStep.status)) {
                                    validatedStep.status = 'pending';
                                }
                                
                                // Preserve id if it exists
                                if (step.id !== undefined) {
                                    validatedStep.id = step.id;
                                }
                                
                                // Preserve any additional fields
                                Object.keys(step).forEach(key => {
                                    if (!['title', 'description', 'order', 'status', 'id'].includes(key)) {
                                        validatedStep[key] = step[key];
                                    }
                                });
                                
                                return validatedStep;
                            }).filter(step => step !== null); // Remove invalid steps
                            
                            // Sort steps by order
                            validatedSteps.sort((a, b) => a.order - b.order);
                            
                            // Create validated roadmap data
                            const validatedRoadmap = {
                                title: roadmapData.title || 'My Startup Roadmap',
                                steps: validatedSteps
                            };
                            
                            console.log('Validated roadmap:', {
                                title: validatedRoadmap.title,
                                stepsCount: validatedSteps.length,
                                steps: validatedSteps.map(s => ({ 
                                    id: s.id,
                                    title: s.title, 
                                    order: s.order, 
                                    status: s.status 
                                })),
                                previousRoadmapExists: !!previousRoadmapRef.value,
                                previousStepsCount: previousRoadmapRef.value?.steps?.length || 0
                            });
                            
                            // Detect newly completed steps by comparing with previous state
                            if (onChecklistComplete) {
                                const previousSteps = previousRoadmapRef.value?.steps || [];
                                console.log('Checking for completed steps:', {
                                    previousStepsCount: previousSteps.length,
                                    newStepsCount: validatedSteps.length,
                                    previousSteps: previousSteps.map(s => ({ 
                                        id: s.id, 
                                        order: s.order, 
                                        title: s.title, 
                                        status: s.status 
                                    })),
                                    newSteps: validatedSteps.map(s => ({ 
                                        id: s.id, 
                                        order: s.order, 
                                        title: s.title, 
                                        status: s.status 
                                    }))
                                });
                                
                                validatedSteps.forEach(newStep => {
                                    // Find corresponding step in previous roadmap - try multiple matching strategies
                                    const previousStep = previousSteps.find(ps => {
                                        // Match by ID if both have IDs
                                        if (ps.id !== undefined && newStep.id !== undefined && ps.id === newStep.id) {
                                            return true;
                                        }
                                        // Match by order if orders match and neither has an ID (or both don't have IDs)
                                        if (ps.order === newStep.order && 
                                            (ps.id === undefined || newStep.id === undefined)) {
                                            return true;
                                        }
                                        // Match by title (case-insensitive, trimmed)
                                        if (ps.title && newStep.title && 
                                            ps.title.trim().toLowerCase() === newStep.title.trim().toLowerCase()) {
                                            return true;
                                        }
                                        return false;
                                    });
                                    
                                    const wasCompleted = previousStep?.status === 'completed';
                                    const isNowCompleted = newStep.status === 'completed';
                                    
                                    console.log(`Step "${newStep.title}" (order: ${newStep.order}):`, {
                                        foundPrevious: !!previousStep,
                                        previousStatus: previousStep?.status,
                                        newStatus: newStep.status,
                                        wasCompleted,
                                        isNowCompleted,
                                        shouldTrigger: isNowCompleted && !wasCompleted
                                    });
                                    
                                    // If step is now completed and wasn't before, trigger completion handler
                                    if (isNowCompleted && !wasCompleted) {
                                        console.log('✅ Detected newly completed step via updateRoadmap:', {
                                            stepId: newStep.id || newStep.order || newStep.title,
                                            step: newStep
                                        });
                                        onChecklistComplete({
                                            stepId: newStep.id || newStep.order || newStep.title,
                                            step: newStep,
                                            completed: true
                                        });
                                    }
                                });
                            }
                            
                            // Update previous roadmap state
                            previousRoadmapRef.value = JSON.parse(JSON.stringify(validatedRoadmap));
                            
                            if (onRoadmapUpdate) {
                                onRoadmapUpdate(validatedRoadmap);
                            }
                            console.log('Roadmap updated successfully via tool call');
                            return `Roadmap updated successfully with ${validatedSteps.length} steps`;
                        } catch (error) {
                            console.error('Error in updateRoadmap tool:', error);
                            return `Error updating roadmap: ${error.message}`;
                        }
                    },
                    updateUserData: async (parameters) => {
                        console.log('Agent called updateUserData tool:', JSON.stringify(parameters, null, 2));
                        try {
                            // Unwrap business_plan if it exists, otherwise use parameters directly
                            const businessPlanData = parameters.business_plan || parameters;
                            
                            console.log('Unwrapped business plan data:', JSON.stringify(businessPlanData, null, 2));
                            
                            if (!businessPlanData || typeof businessPlanData !== 'object') {
                                console.warn('Invalid business plan data from tool call:', parameters);
                                return 'Error: Invalid business plan data format';
                            }
                            
                            // Ensure we're passing a flat object, not nested
                            const flatData = { ...businessPlanData };
                            console.log('Sending to onBusinessPlanUpdate:', JSON.stringify(flatData, null, 2));
                            
                            if (onBusinessPlanUpdate) {
                                onBusinessPlanUpdate(flatData);
                            }
                            console.log('Business plan updated successfully via tool call');
                            return 'Business plan updated successfully';
                        } catch (error) {
                            console.error('Error in updateUserData tool:', error);
                            return `Error updating user data: ${error.message}`;
                        }
                    },
                    generateBusinessPlan: async (parameters) => {
                        console.log('Agent called generateBusinessPlan tool:', parameters);
                        try {
                            // Trigger the business plan generation modal
                            if (onMeetingPrep) {
                                onMeetingPrep({}); // Pass empty object to trigger modal
                            }
                            console.log('Business plan generation requested');
                            return 'Business plan PDF generation is ready. A popup will appear to generate and download your business plan PDF.';
                        } catch (error) {
                            console.error('Error in generateBusinessPlan tool:', error);
                            return `Error generating business plan: ${error.message}`;
                        }
                    },
                    markChecklistComplete: async (parameters) => {
                        console.log('Agent called markChecklistComplete tool:', JSON.stringify(parameters, null, 2));
                        try {
                            const stepData = parameters.step || parameters;
                            if (!stepData) {
                                console.warn('Invalid step data from tool call:', parameters);
                                return 'Error: Invalid step data format';
                            }
                            
                            // Update roadmap step status to completed
                            const stepId = stepData.id || stepData.order || stepData.title;
                            if (!stepId) {
                                console.error('markChecklistComplete: Missing step identifier', stepData);
                                return 'Error: Step identifier (id, order, or title) is required';
                            }
                            
                            console.log('markChecklistComplete: Calling onChecklistComplete with stepId:', stepId);
                            if (onChecklistComplete) {
                                onChecklistComplete({
                                    stepId: stepId,
                                    step: stepData,
                                    completed: true
                                });
                                console.log('markChecklistComplete: Successfully triggered completion handler');
                            } else {
                                console.warn('markChecklistComplete: onChecklistComplete callback not provided');
                            }
                            
                            // Update previous roadmap state to mark this step as completed
                            if (previousRoadmapRef.value && previousRoadmapRef.value.steps) {
                                const stepIndex = previousRoadmapRef.value.steps.findIndex(s => 
                                    (s.id !== undefined && stepData.id !== undefined && s.id === stepData.id) ||
                                    (s.order === stepData.order) ||
                                    (s.title === stepData.title)
                                );
                                if (stepIndex >= 0) {
                                    previousRoadmapRef.value.steps[stepIndex].status = 'completed';
                                }
                            }
                            
                            return `Great job! Step "${stepData.title || stepId}" has been marked as complete.`;
                        } catch (error) {
                            console.error('Error in markChecklistComplete tool:', error);
                            return `Error marking checklist complete: ${error.message}`;
                        }
                    },
                    requestDocument: async (parameters) => {
                        console.log('Agent called requestDocument tool:', parameters);
                        try {
                            const docRequest = parameters.document || parameters;
                            if (!docRequest || typeof docRequest !== 'object') {
                                console.warn('Invalid document request from tool call:', parameters);
                                return 'Error: Invalid document request format';
                            }
                            
                            if (onDocumentRequest) {
                                onDocumentRequest({
                                    type: docRequest.type || docRequest.document_type || 'general',
                                    title: docRequest.title || docRequest.name || 'Document Request',
                                    description: docRequest.description || docRequest.info || 'Additional information needed',
                                    required: docRequest.required !== false,
                                    field: docRequest.field || null
                                });
                            }
                            console.log('Document request created');
                            return 'I\'ve noted that we need this information. A request card has been added to your dashboard.';
                        } catch (error) {
                            console.error('Error in requestDocument tool:', error);
                            return `Error creating document request: ${error.message}`;
                        }
                    },
                    suggestResource: async (parameters) => {
                        console.log('Agent called suggestResource tool:', parameters);
                        try {
                            const resource = parameters.resource || parameters;
                            if (!resource || typeof resource !== 'object') {
                                console.warn('Invalid resource data from tool call:', parameters);
                                return 'Error: Invalid resource data format';
                            }
                            
                            if (onResourceSuggested) {
                                onResourceSuggested({
                                    title: resource.title || 'Resource',
                                    description: resource.description || resource.preview || '',
                                    url: resource.url || resource.link || '',
                                    category: resource.category || 'general',
                                    icon: resource.icon || '📚',
                                    preview: resource.preview || resource.description || ''
                                });
                            }
                            console.log('Resource suggested successfully');
                            return 'I\'ve added a helpful resource card for you to check out.';
                        } catch (error) {
                            console.error('Error in suggestResource tool:', error);
                            return `Error suggesting resource: ${error.message}`;
                        }
                    },
                    generateProgressSummary: async (parameters) => {
                        console.log('Agent called generateProgressSummary tool:', parameters);
                        try {
                            const summaryData = parameters.summary || parameters;
                            
                            if (onProgressSummary) {
                                onProgressSummary(summaryData || {});
                            }
                            console.log('Progress summary generated');
                            return 'Your progress summary is ready. Check the popup to see your achievements and next steps.';
                        } catch (error) {
                            console.error('Error in generateProgressSummary tool:', error);
                            return `Error generating progress summary: ${error.message}`;
                        }
                    }
                },
                onStatusChange: (prop) => {
                    console.log('Status changed:', prop.status);
                    if (prop.status === 'connected') {
                        isConnected.value = true;
                        connectionStatus.value = 'connected';
                        isListening.value = true;
                    } else if (prop.status === 'disconnected') {
                        isConnected.value = false;
                        connectionStatus.value = 'disconnected';
                        isListening.value = false;
                        isSpeaking.value = false;
                    } else if (prop.status === 'connecting') {
                        connectionStatus.value = 'connecting';
                    }
                },
                onModeChange: (prop) => {
                    console.log('Mode changed:', prop.mode);
                    if (prop.mode === 'listening') {
                        isListening.value = true;
                        isSpeaking.value = false;
                    } else if (prop.mode === 'speaking') {
                        isSpeaking.value = true;
                        isListening.value = false;
                    }
                },
                onMessage: (props) => {
                    console.log('Message received:', props);
                    if (onTranscript) {
                        onTranscript({
                            type: props.source === 'user' ? 'user' : 'ai',
                            text: props.message || '',
                            timestamp: new Date().toISOString()
                        });
                    }

                    // Try to extract roadmap JSON from AI responses
                    if (props.source === 'assistant' && props.message) {
                        const jsonMatch = props.message.match(/\{[\s\S]*"steps"[\s\S]*\}/);
                        if (jsonMatch) {
                            try {
                                const roadmapData = JSON.parse(jsonMatch[0]);
                                // Update previous roadmap state before calling onRoadmapUpdate
                                previousRoadmapRef.value = JSON.parse(JSON.stringify(roadmapData));
                                if (onRoadmapUpdate) {
                                    onRoadmapUpdate(roadmapData);
                                }
                            } catch (parseError) {
                                console.warn('Failed to parse roadmap JSON:', parseError);
                            }
                        }
                    }
                },
                onError: (message, context) => {
                    console.error('Conversation error:', message, context);
                    connectionStatus.value = 'disconnected';
                    isConnected.value = false;
                    isListening.value = false;
                    isSpeaking.value = false;
                    
                    if (onError) {
                        onError(message || 'Voice agent error');
                    }
                    
                    if (sessionIdRef.value) {
                        axios.patch(`/api/voice-session/${sessionIdRef.value}`, {
                            status: 'failed'
                        }).catch(err => console.error('Failed to update session on error:', err));
                    }
                },
                onAudio: (base64Audio) => {
                    // Audio is automatically played by the SDK
                    console.log('Audio received, length:', base64Audio.length);
                },
            });

            conversationRef.value = conversation;
            connectionStatus.value = 'connected';
            isConnected.value = true;
            isListening.value = true;

            // Send business plan and roadmap context to the agent after session starts
            // (contextWithFirstMessage already includes everything)
            if (contextWithFirstMessage) {
                try {
                    // Send as contextual update (this is the proper way to pass context in ElevenLabs)
                    conversation.sendContextualUpdate(contextWithFirstMessage);
                    console.log('Full context sent to agent via sendContextualUpdate:', {
                        businessPlanLength: businessPlanContext.length,
                        roadmapLength: roadmapContext.length,
                        firstMessageLength: firstMessage.length,
                        totalLength: contextWithFirstMessage.length
                    });
                    
                    // Give the agent a moment to process the context, then it should speak
                    // The context includes explicit instructions to speak first
                    setTimeout(() => {
                        console.log('Context sent, agent should now speak with first message');
                    }, 500);
                } catch (error) {
                    console.warn('Failed to send context to agent:', error);
                }
            }
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
        if (conversationRef.value) {
            try {
                conversationRef.value.endSession();
            } catch (error) {
                console.error('Error ending conversation:', error);
            }
            conversationRef.value = null;
        }
        
        connectionStatus.value = 'disconnected';
        isConnected.value = false;
        isListening.value = false;
        isSpeaking.value = false;
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

            // Session is already active when connected
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
            // Interrupt the conversation
            if (conversationRef.value) {
                try {
                    conversationRef.value.interrupt();
                } catch (error) {
                    console.error('Error interrupting conversation:', error);
                }
                isListening.value = false;
                isSpeaking.value = false;
            }

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
