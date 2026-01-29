<?php

declare(strict_types=1);

namespace Dokobit\Integration\Config;

readonly class AuthenticationConfig
{
    public function __construct(
        private string $baseUrl,
        private string $accessToken,
        private array $supportedAuthMethods,
        private array $supportedResidencies,
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

    public function getSupportedAuthMethods(): array
    {
        return $this->supportedAuthMethods;
    }

    public function getSupportedResidencies(): array
    {
        return $this->supportedResidencies;
    }
}
