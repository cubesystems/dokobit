<?php

declare(strict_types=1);

namespace Dokobit\Tests\Unit\Integration\Apis\Dtos\Signing;

use Dokobit\Integration\DTOs\Signing\Entities\Token;
use Dokobit\Tests\TestCase;

class TokenTest extends TestCase
{
    public function test_dokobit_token_dto_maps_token_to_external_representation(): void
    {
        $token = new Token(
            value: 'token_value'
        );

        $externalRepresentation = $token->toArray();

        $this->assertEquals([
            'token' => 'token_value',
        ], $externalRepresentation);
    }
}
