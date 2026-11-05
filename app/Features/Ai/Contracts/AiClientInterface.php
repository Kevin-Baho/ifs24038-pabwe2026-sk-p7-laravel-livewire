<?php

declare(strict_types=1);

namespace App\Features\Ai\Contracts;

interface AiClientInterface
{
    /**
     * Send a prompt and return the raw response string.
     */
    public function complete(string $prompt, int $maxTokens = 2048): string;
}
