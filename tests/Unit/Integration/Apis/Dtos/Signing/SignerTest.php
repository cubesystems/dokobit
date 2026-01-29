<?php

declare(strict_types=1);

namespace Dokobit\Tests\Unit\Integration\Apis\Dtos\Signing;

use Dokobit\Integration\DTOs\Signing\Entities\Signer;
use Dokobit\Tests\TestCase;

class SignerTest extends TestCase
{
    public function test_dokobit_signer_dto_maps_to_external_representation(): void
    {
        $signer = new Signer(
            id: '123',
            name: 'John',
            surname: 'Smith',
            signingOptions: ['aaa', 'bbb']
        );

        $externalRepresentation = $signer->toArray();

        $this->assertEquals([
            'id' => '123',
            'name' => 'John',
            'surname' => 'Smith',
            'signing_options' => ['aaa', 'bbb'],
        ], $externalRepresentation);
    }
}
