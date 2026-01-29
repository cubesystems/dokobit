<?php

declare(strict_types=1);

namespace Dokobit\Tests\Unit\Integration\DTOs\Auth;

use Dokobit\Integration\DTOs\Auth\CreateSessionRequestPayload;
use Dokobit\Tests\TestCase;

class CreateSessionRequestPayloadTest extends TestCase
{
    public function test_create_session_request_payload_maps_from_array(): void
    {
        $payload = CreateSessionRequestPayload::from([
            'origin_host' => 'https://example.com',
            'supported_residencies' => ['lv', 'lt', 'ee'],
        ]);

        $this->assertEquals('https://example.com', $payload->originHost);
        $this->assertEquals(['lv', 'lt', 'ee'], $payload->supportedResidencies);
    }

    public function test_create_session_request_payload_has_validation_rules(): void
    {
        $rules = CreateSessionRequestPayload::rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('return_url', $rules);
        $this->assertArrayHasKey('origin_host', $rules);
        $this->assertEquals('required_without:origin_host', $rules['return_url']);
        $this->assertEquals('required_without:return_url', $rules['origin_host']);
    }

    public function test_create_session_request_payload_with_return_url(): void
    {
        $payload = CreateSessionRequestPayload::from([
            'return_url' => 'https://example.com/callback',
            'supported_residencies' => ['lv'],
        ]);

        $this->assertEquals('https://example.com/callback', $payload->returnUrl);
    }

    public function test_create_session_request_payload_with_all_fields(): void
    {
        $payload = CreateSessionRequestPayload::from([
            'return_url' => 'https://example.com/callback',
            'origin_host' => 'https://example.com',
            'code' => 'LV',
            'country_code' => '+371',
            'phone' => '12345678',
            'authentication_methods' => ['id_card', 'mobile_id'],
            'supported_residencies' => ['lv', 'lt'],
        ]);

        $this->assertEquals('https://example.com/callback', $payload->returnUrl);
        $this->assertEquals('https://example.com', $payload->originHost);
        $this->assertEquals('LV', $payload->code);
        $this->assertEquals('+371', $payload->countryCode);
        $this->assertEquals('12345678', $payload->phone);
        $this->assertEquals(['id_card', 'mobile_id'], $payload->authenticationMethods);
        $this->assertEquals(['lv', 'lt'], $payload->supportedResidencies);
    }
}
