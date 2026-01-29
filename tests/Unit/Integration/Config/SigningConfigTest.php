<?php

declare(strict_types=1);

namespace Dokobit\Tests\Unit\Integration\Config;

use Dokobit\Integration\Config\SigningConfig;
use Dokobit\Tests\TestCase;
use ReflectionClass;

class SigningConfigTest extends TestCase
{
    public function test_signing_config_stores_and_returns_base_url(): void
    {
        $config = new SigningConfig(
            baseUrl: 'https://api.dokobit.com',
            accessToken: 'test-token'
        );

        $this->assertEquals('https://api.dokobit.com', $config->getBaseUrl());
    }

    public function test_signing_config_stores_and_returns_access_token(): void
    {
        $config = new SigningConfig(
            baseUrl: 'https://api.dokobit.com',
            accessToken: 'test-token-456'
        );

        $this->assertEquals('test-token-456', $config->getAccessToken());
    }

    public function test_signing_config_is_readonly(): void
    {
        $config = new SigningConfig(
            baseUrl: 'https://api.dokobit.com',
            accessToken: 'test-token'
        );

        $this->assertInstanceOf(SigningConfig::class, $config);

        // Verify it's a readonly class by checking the reflection
        $reflection = new ReflectionClass($config);
        $this->assertTrue($reflection->isReadOnly());
    }
}
