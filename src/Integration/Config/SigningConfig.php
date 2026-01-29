<?php

declare(strict_types=1);

namespace Dokobit\Integration\Config;

readonly class SigningConfig
{
    public function __construct(
        private string $baseUrl,
        private string $accessToken,
    ) {
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getAccessToken(): string
    {
        return $this->accessToken;
    }
}
