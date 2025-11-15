<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Handle chat message and return AI response using ElevenLabs Agents
     */
    public function chat(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'message' => 'required|string',
            'business_plan' => 'sometimes|array',
            'roadmap' => 'sometimes|array',
            'conversation_id' => 'sometimes|string', // For maintaining conversation state
        ]);

        $message = $validated['message'];
        $businessPlan = $validated['business_plan'] ?? [];
        $roadmap = $validated['roadmap'] ?? [];
        $conversationId = $validated['conversation_id'] ?? null;

        try {
            // Get ElevenLabs API key and agent ID
            $apiKey = env('ELEVENLABS_API_KEY');
            $agentId = env('ELEVENLABS_AGENT_ID');
            
            if (empty($apiKey)) {
                Log::error('ElevenLabs API key not configured');
                return response()->json([
                    'error' => 'ElevenLabs API key is not configured. Please set ELEVENLABS_API_KEY in your .env file.'
                ], 500);
            }
            
            if (empty($agentId)) {
                Log::error('ElevenLabs Agent ID not configured');
                return response()->json([
                    'error' => 'ElevenLabs Agent ID is not configured. Please set ELEVENLABS_AGENT_ID in your .env file.'
                ], 500);
            }

            // Build context from business plan and roadmap
            $context = $this->buildContext($businessPlan, $roadmap);

            // ElevenLabs Agents API endpoint for text chat
            // Note: The exact endpoint format may vary - check ElevenLabs documentation
            $endpoint = "https://api.elevenlabs.io/v1/convai/conversation";
            
            // Prepare request payload
            $payload = [
                'agent_id' => $agentId,
                'text_only' => true,
            ];
            
            if ($conversationId) {
                $payload['conversation_id'] = $conversationId;
                $payload['message'] = $message;
            } else {
                // For first message, include context and message
                $payload['message'] = $message;
                if (!empty($context)) {
                    $payload['contextual_update'] = $context;
                }
            }
            
            // Make request to ElevenLabs API
            $response = Http::withHeaders([
                'xi-api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post($endpoint, $payload);

            if (!$response->successful()) {
                $errorBody = $response->json();
                $rawBody = $response->body();
                
                Log::error('ElevenLabs API error:', [
                    'status' => $response->status(),
                    'url' => $response->effectiveUri(),
                    'request_body' => !$conversationId ? [
                        'agent_id' => $agentId,
                        'text_only' => true,
                        'conversation_id' => null,
                        'message' => $message,
                        'contextual_update' => substr($context, 0, 200) . '...',
                    ] : [
                        'agent_id' => $agentId,
                        'text_only' => true,
                        'conversation_id' => $conversationId,
                        'message' => $message,
                    ],
                    'response_body' => $rawBody,
                    'error' => $errorBody
                ]);
                
                // Return more helpful error message
                $errorMessage = 'Failed to get AI response from ElevenLabs';
                if (isset($errorBody['detail']['message'])) {
                    $errorMessage = $errorBody['detail']['message'];
                } elseif (isset($errorBody['detail'])) {
                    $errorMessage = is_string($errorBody['detail']) ? $errorBody['detail'] : json_encode($errorBody['detail']);
                } elseif (isset($errorBody['message'])) {
                    $errorMessage = $errorBody['message'];
                } elseif ($response->status() === 401) {
                    $errorMessage = 'ElevenLabs API key is invalid or not configured. Please check your .env file.';
                } elseif ($response->status() === 422) {
                    $errorMessage = 'Invalid request format. Please check the logs for details. Error: ' . substr($rawBody, 0, 200);
                } elseif ($response->status() === 429) {
                    $errorMessage = 'ElevenLabs API rate limit exceeded. Please try again later.';
                }
                
                return response()->json([
                    'error' => $errorMessage,
                    'status' => $response->status(),
                    'details' => $errorBody
                ], $response->status() >= 400 && $response->status() < 500 ? $response->status() : 500);
            }

            $aiResponse = $response->json();
            
            // Extract response text and conversation ID from ElevenLabs response
            $responseText = $aiResponse['agent_response'] ?? $aiResponse['response'] ?? null;
            $newConversationId = $aiResponse['conversation_id'] ?? $conversationId;
            
            if (!$responseText) {
                Log::error('Unexpected ElevenLabs API response format:', ['response' => $aiResponse]);
                return response()->json([
                    'error' => 'Unexpected response format from AI service'
                ], 500);
            }

            // Check if agent called any tools and handle them
            $updates = [];
            if (isset($aiResponse['tool_calls']) && is_array($aiResponse['tool_calls'])) {
                foreach ($aiResponse['tool_calls'] as $toolCall) {
                    $updates = array_merge($updates, $this->handleToolCall($toolCall, $user));
                }
            }

            $responseData = [
                'response' => $responseText,
                'conversation_id' => $newConversationId,
            ];

            // Add updates if any tools were called
            if (!empty($updates)) {
                $responseData = array_merge($responseData, $updates);
            }

            return response()->json($responseData, 200);

        } catch (\Exception $e) {
            Log::error('Chat error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'An error occurred while processing your message: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle tool calls from ElevenLabs agent
     */
    private function handleToolCall(array $toolCall, $user): array
    {
        $updates = [];
        $toolName = $toolCall['name'] ?? null;
        $parameters = $toolCall['parameters'] ?? [];

        if (!$toolName) {
            return $updates;
        }

        Log::info('Agent tool called:', ['tool' => $toolName, 'parameters' => $parameters]);

        // Handle different tool calls similar to voice agent
        switch ($toolName) {
            case 'updateUserData':
            case 'updateBusinessPlan':
                if (isset($parameters['business_plan'])) {
                    $updates['business_plan'] = $parameters['business_plan'];
                }
                break;

            case 'updateRoadmap':
                if (isset($parameters['roadmap'])) {
                    $updates['roadmap'] = $parameters['roadmap'];
                }
                break;

            // Add other tool handlers as needed
        }

        return $updates;
    }

    /**
     * Build context string from business plan and roadmap
     * Matches the format used in the voice agent for consistency
     */
    private function buildContext(array $businessPlan, array $roadmap): string
    {
        $context = '';
        
        // Format business plan context similar to voice agent
        if (!empty($businessPlan)) {
            $filled = [];
            $missing = [];
            
            $fieldLabels = [
                'country_of_origin' => 'Country of origin',
                'is_eu_resident' => 'EU resident status',
                'is_newcomer_to_finland' => 'Newcomer to Finland',
                'has_residence_permit' => 'Has residence permit',
                'residence_permit_type' => 'Residence permit type',
                'years_in_finland' => 'Years in Finland',
                'has_business_experience' => 'Has business experience',
                'language' => 'Preferred language',
                'business_name' => 'Business name',
                'company_contact_info' => 'Company contact information',
                'industry' => 'Industry',
                'company_planned_name' => 'Company planned name',
                'company_type' => 'Company type',
                'address' => 'Address',
                'zip_code' => 'ZIP code',
                'postal_district' => 'Postal district',
                'year_of_establishment' => 'Year of establishment',
                'number_of_employees' => 'Number of employees',
                'internet_address' => 'Internet address',
                'business_id' => 'Business ID',
                'company_owners_holdings' => 'Company owners and holdings',
                'business_idea' => 'Business idea',
                'competence_skills' => 'Competence and skills',
                'swot_analysis' => 'SWOT analysis',
                'products_services_general' => 'Products/services (general)',
                'products_services_detailed' => 'Products/services (detailed)',
                'sales_marketing' => 'Sales and marketing',
                'production_logistics' => 'Production and logistics',
                'distribution_network' => 'Distribution network',
                'target_market_groups' => 'Target market groups',
                'competitors' => 'Competitors',
                'competitive_situation' => 'Competitive situation',
                'third_parties_partners' => 'Third parties/partners',
                'operating_environment_risks' => 'Operating environment/risks',
                'vision_long_term' => 'Vision/long-term goals',
                'industry_future_prospects' => 'Industry future prospects',
                'permits_notices' => 'Permits and notices',
                'insurance_contracts' => 'Insurance contracts',
                'intellectual_property_rights' => 'Intellectual property rights',
                'support_network' => 'Support network',
                'my_business_comprehensive' => 'My business (comprehensive)',
            ];
            
            foreach ($fieldLabels as $key => $label) {
                $value = $businessPlan[$key] ?? null;
                if ($value !== null && $value !== '' && $value !== false && $value !== []) {
                    if (is_array($value)) {
                        $filled[] = "{$label}: " . implode(', ', $value);
                    } elseif (is_bool($value)) {
                        $filled[] = "{$label}: " . ($value ? 'Yes' : 'No');
                    } else {
                        $filled[] = "{$label}: {$value}";
                    }
                } else {
                    $missing[] = $label;
                }
            }
            
            if (!empty($filled)) {
                $context .= "FILLED BUSINESS PLAN FIELDS (you already know this):\n";
                foreach ($filled as $field) {
                    $context .= "- {$field}\n";
                }
                $context .= "\n";
            }
            
            if (!empty($missing)) {
                $context .= "⚠️ IMPORTANT: You need to actively ask the user about " . count($missing) . " missing business plan fields.\n\n";
                $context .= "MISSING BUSINESS PLAN FIELDS (ASK ABOUT THESE):\n";
                foreach ($missing as $index => $field) {
                    $context .= ($index + 1) . ". {$field}\n";
                }
                $context .= "\n";
            }
        }
        
        // Format roadmap context similar to voice agent
        if (!empty($roadmap)) {
            $steps = $roadmap['steps'] ?? [];
            if (!empty($steps)) {
                $context .= "ROADMAP STATUS:\n\n";
                $context .= "Roadmap Title: " . ($roadmap['title'] ?? 'My Startup Roadmap') . "\n\n";
                $context .= "Current Roadmap Steps (" . count($steps) . " total):\n\n";
                
                foreach ($steps as $index => $step) {
                    if (isset($step['title'])) {
                        $status = strtoupper($step['status'] ?? 'pending');
                        $context .= ($index + 1) . ". " . $step['title'] . "\n";
                        $context .= "   Status: {$status}\n";
                        if (isset($step['description'])) {
                            $desc = strlen($step['description']) > 100 
                                ? substr($step['description'], 0, 100) . '...' 
                                : $step['description'];
                            $context .= "   Description: {$desc}\n";
                        }
                        $context .= "   Order: " . ($step['order'] ?? ($index + 1)) . "\n\n";
                    }
                }
            }
        }
        
        return $context;
    }
}

