<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OpenAI API Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for OpenAI API integration. The API key can be stored
    | in the .env file as OPENAI_API_KEY for server-side usage, or
    | VITE_OPENAI_API_KEY for client-side usage.
    |
    */

    'api_key' => env('OPENAI_API_KEY'),

    'realtime_model' => env('OPENAI_REALTIME_MODEL', 'gpt-4o-realtime-preview'),

    'default_voice' => env('OPENAI_DEFAULT_VOICE', 'nova'),

    /*
    |--------------------------------------------------------------------------
    | Voice Agent Instructions
    |--------------------------------------------------------------------------
    |
    | Default system instructions for the voice agent coach.
    |
    */

    'agent_instructions' => env('OPENAI_AGENT_INSTRUCTIONS', 'You are a friendly and supportive startup coach helping entrepreneurs in Espoo, Finland build their business roadmaps.'),
];

