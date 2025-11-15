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
     * Handle chat message and return AI response
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
        ]);

        $message = $validated['message'];
        $businessPlan = $validated['business_plan'] ?? [];
        $roadmap = $validated['roadmap'] ?? [];

        try {
            // Build context from business plan and roadmap
            $context = $this->buildContext($businessPlan, $roadmap);

            // Call OpenAI API for chat response
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.openai.api_key'),
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are Eppu the Bear, a friendly and helpful AI startup coach. Help users build their business plans and create roadmaps for starting a business in Finland. Be warm, encouraging, and informative. When users provide information, acknowledge it positively.\n\n" . $context
                    ],
                    [
                        'role' => 'user',
                        'content' => $message
                    ]
                ],
                'temperature' => 0.7,
                'max_tokens' => 500,
            ]);

            if (!$response->successful()) {
                Log::error('OpenAI API error:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return response()->json([
                    'error' => 'Failed to get AI response'
                ], 500);
            }

            $aiResponse = $response->json();
            $responseText = $aiResponse['choices'][0]['message']['content'] ?? 'Sorry, I could not generate a response.';

            return response()->json([
                'response' => $responseText,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Chat error:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'An error occurred while processing your message'
            ], 500);
        }
    }

    /**
     * Build context string from business plan and roadmap
     */
    private function buildContext(array $businessPlan, array $roadmap): string
    {
        $context = "User's Current Information:\n\n";

        // Business plan context
        if (!empty($businessPlan)) {
            $context .= "Business Plan:\n";
            foreach ($businessPlan as $key => $value) {
                if ($value !== null && $value !== '') {
                    $formattedKey = str_replace('_', ' ', ucwords($key, '_'));
                    if (is_array($value)) {
                        $context .= "- {$formattedKey}: " . implode(', ', $value) . "\n";
                    } elseif (is_bool($value)) {
                        $context .= "- {$formattedKey}: " . ($value ? 'Yes' : 'No') . "\n";
                    } else {
                        $context .= "- {$formattedKey}: {$value}\n";
                    }
                }
            }
            $context .= "\n";
        }

        // Roadmap context
        if (!empty($roadmap['steps'])) {
            $context .= "Roadmap Steps:\n";
            foreach ($roadmap['steps'] as $step) {
                if (isset($step['title'])) {
                    $status = $step['status'] ?? 'pending';
                    $context .= "- {$step['title']} ({$status})\n";
                }
            }
            $context .= "\n";
        }

        return $context;
    }
}

