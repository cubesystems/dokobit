<?php

declare(strict_types=1);

namespace Dokobit\Tests\Unit\Integration\Config;

use Dokobit\Integration\Config\AuthenticationConfig;
use Dokobit\Tests\TestCase;
use ReflectionClass;

class AuthenticationConfigTest extends TestCase
{
    public function test_authentication_config_stores_and_returns_base_url(): void
    {
        $config = new AuthenticationConfig(
            baseUrl: 'https://api.dokobit.com',
            accessToken: 'test-token',
            supportedAuthMethods: ['id_card', 'mobile_id'],
            supportedResidencies: ['lv', 'lt', 'ee']
        );

        $this->assertEquals('https://api.dokobit.com', $config->getBaseUrl());
    }

    public function test_authentication_config_stores_and_returns_access_token(): void
    {
        $config = new AuthenticationConfig(
            baseUrl: 'https://api.dokobit.com',
            accessToken: 'test-token-123',
            supportedAuthMethods: ['id_card'],
            supportedResidencies: ['lv']
        );

        $this->assertEquals('test-token-123', $config->getAccessToken());
    }

    public function test_authentication_config_stores_and_returns_supported_auth_methods(): void
    {
        $authMethods = ['id_card', 'mobile_id', 'smart_id'];

        $config = new AuthenticationConfig(
            baseUrl: 'https://api.dokobit.com',
            accessToken: 'test-token',
            supportedAuthMethods: $authMethods,
            supportedResidencies: ['lv']
        );

        $this->assertEquals($authMethods, $config->getSupportedAuthMethods());
    }

    public function test_authentication_config_stores_and_returns_supported_residencies(): void
    {
        $residencies = ['lv', 'lt', 'ee'];

        $config = new AuthenticationConfig(
            baseUrl: 'https://api.dokobit.com',
            accessToken: 'test-token',
            supportedAuthMethods: ['id_card'],
            supportedResidencies: $residencies
        );

        $this->assertEquals($residencies, $config->getSupportedResidencies());
    }

    public function test_authentication_config_is_readonly(): void
    {
        $config = new AuthenticationConfig(
            baseUrl: 'https://api.dokobit.com',
            accessToken: 'test-token',
            supportedAuthMethods: ['id_card'],
            supportedResidencies: ['lv']
        );

        $this->assertInstanceOf(AuthenticationConfig::class, $config);

        // Verify it's a readonly class by checking the reflection
        $reflection = new ReflectionClass($config);
        $this->assertTrue($reflection->isReadOnly());
    }
}
