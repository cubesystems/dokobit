<?php

declare(strict_types=1);

namespace Dokobit\Tests\Unit\Integration\Apis\Dtos\Signing;

use Dokobit\Integration\DTOs\Signing\Responses\NewSigningResponsePayload;
use Dokobit\Tests\TestCase;

class NewSigningResponsePayloadTest extends TestCase
{
    public function test_dokobit_new_signing_response_payload_maps_from_external_representation(): void
    {
        $response = NewSigningResponsePayload::from([
            'status' => 'success',
            'token' => 'signing-token',
            'signers' => [
                'signer1' => 'signer-token-1',
                'signer2' => 'signer-token-2'
            ],
        ]);

        $this->assertEquals('success', $response->status);
        $this->assertEquals('signing-token', $response->signingToken);
        $this->assertEquals([
            'signer1' => 'signer-token-1',
            'signer2' => 'signer-token-2'
        ], $response->signerTokens);
    }
}
