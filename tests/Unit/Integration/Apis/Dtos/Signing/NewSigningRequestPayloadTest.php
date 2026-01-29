<?php

declare(strict_types=1);

namespace Dokobit\Tests\Unit\Integration\Apis\Dtos\Signing;

use Dokobit\Integration\DTOs\Signing\Entities\Signer;
use Dokobit\Integration\DTOs\Signing\Entities\Token;
use Dokobit\Integration\DTOs\Signing\Requests\NewSigningRequestPayload;
use Dokobit\Tests\TestCase;

class NewSigningRequestPayloadTest extends TestCase
{
    public function test_dokobit_new_signing_request_payload_maps_to_external_representation(): void
    {
        $token = new NewSigningRequestPayload(
            type: 'type',
            name: 'name',
            signers: collect([
                new Signer('id1', 'name1', 'surname1', signingOptions: ['aaa1' => 'bbb1']),
                new Signer('id2', 'name2', 'surname2', signingOptions: ['aaa2' => 'bbb2']),
            ]),
            fileTokens: collect([
                new Token('token1'),
                new Token('token2'),
            ])
        );

        $externalRepresentation = $token->toArray();

        $this->assertEquals([
            'type' => 'type',
            'name' => 'name',
            'signers' => [
                [
                    'id' => 'id1',
                    'name' => 'name1',
                    'surname' => 'surname1',
                    'signing_options' => ['aaa1' => 'bbb1'],
                ],
                [
                    'id' => 'id2',
                    'name' => 'name2',
                    'surname' => 'surname2',
                    'signing_options' => ['aaa2' => 'bbb2'],
                ],
            ],
            'files' => [
                [
                    'token' => 'token1'
                ],
                [
                    'token' => 'token2'
                ],
            ]
        ], $externalRepresentation);
    }
}
