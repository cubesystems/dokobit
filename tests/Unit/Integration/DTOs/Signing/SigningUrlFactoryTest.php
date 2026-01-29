<?php

declare(strict_types=1);

namespace Dokobit\Tests\Unit\Integration\DTOs\Signing;

use Dokobit\Integration\DTOs\Signing\Responses\NewSigningResponsePayload;
use Dokobit\Integration\DTOs\Signing\SigningUrlFactory;
use Dokobit\Tests\TestCase;

class SigningUrlFactoryTest extends TestCase
{
    public function test_creates_signing_urls_for_single_signer(): void
    {
        $factory = new SigningUrlFactory('https://example.com');

        $signingResponse = NewSigningResponsePayload::from([
            'status' => 'ok',
            'token' => 'signing-token-123',
            'signers' => [
                'signer1' => 'signer-token-abc',
            ],
        ]);

        $urls = $factory->make($signingResponse);

        $this->assertCount(1, $urls);
        $this->assertEquals(
            'https://example.com/signing/signing-token-123?access_token=signer-token-abc',
            $urls->get('signer1')
        );
    }

    public function test_creates_signing_urls_for_multiple_signers(): void
    {
        $factory = new SigningUrlFactory('https://api.dokobit.com');

        $signingResponse = NewSigningResponsePayload::from([
            'status' => 'ok',
            'token' => 'signing-token-xyz',
            'signers' => [
                'john@example.com' => 'token-john-123',
                'jane@example.com' => 'token-jane-456',
                'bob@example.com' => 'token-bob-789',
            ],
        ]);

        $urls = $factory->make($signingResponse);

        $this->assertCount(3, $urls);
        $this->assertEquals(
            'https://api.dokobit.com/signing/signing-token-xyz?access_token=token-john-123',
            $urls->get('john@example.com')
        );
        $this->assertEquals(
            'https://api.dokobit.com/signing/signing-token-xyz?access_token=token-jane-456',
            $urls->get('jane@example.com')
        );
        $this->assertEquals(
            'https://api.dokobit.com/signing/signing-token-xyz?access_token=token-bob-789',
            $urls->get('bob@example.com')
        );
    }

    public function test_preserves_signer_identifiers_as_collection_keys(): void
    {
        $factory = new SigningUrlFactory('https://example.com');

        $signingResponse = NewSigningResponsePayload::from([
            'status' => 'ok',
            'token' => 'signing-token',
            'signers' => [
                'user_id_1' => 'token-1',
                'user_id_2' => 'token-2',
            ],
        ]);

        $urls = $factory->make($signingResponse);

        $this->assertTrue($urls->has('user_id_1'));
        $this->assertTrue($urls->has('user_id_2'));
        $this->assertFalse($urls->has('user_id_3'));
    }

    public function test_handles_empty_signers_list(): void
    {
        $factory = new SigningUrlFactory('https://example.com');

        $signingResponse = NewSigningResponsePayload::from([
            'status' => 'ok',
            'token' => 'signing-token',
            'signers' => [],
        ]);

        $urls = $factory->make($signingResponse);

        $this->assertCount(0, $urls);
        $this->assertTrue($urls->isEmpty());
    }

    public function test_url_format_is_correct(): void
    {
        $factory = new SigningUrlFactory('https://test.dokobit.com');

        $signingResponse = NewSigningResponsePayload::from([
            'status' => 'ok',
            'token' => 'abc123',
            'signers' => [
                'test' => 'xyz789',
            ],
        ]);

        $urls = $factory->make($signingResponse);
        $url = $urls->first();

        // Verify URL structure
        $this->assertStringStartsWith('https://test.dokobit.com/signing/', $url);
        $this->assertStringContainsString('abc123', $url);
        $this->assertStringContainsString('?access_token=', $url);
        $this->assertStringContainsString('xyz789', $url);
    }
}
