<?php

namespace App\Domains\Shared\Services\StreamService;

use GetStream\StreamChat\Client;

class StreamService
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client(
            config('services.stream.key'),
            config('services.stream.secret')
        );
    }

    /**
     * Stream Token for current user
     */
    public function generateToken(string $userId): string
    {
        return $this->client->createToken($userId);
    }
}
