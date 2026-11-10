<?php

return [
    'api_key' => env('OPENAI_API_KEY', ''),
    'organization' => env('OPENAI_ORGANIZATION', ''),
    'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
    'timeout' => env('OPENAI_TIMEOUT', 30),
];
