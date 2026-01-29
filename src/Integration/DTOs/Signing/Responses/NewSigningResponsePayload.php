<?php

declare(strict_types=1);

namespace Dokobit\Integration\DTOs\Signing\Responses;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;

class NewSigningResponsePayload extends Data
{
    public function __construct(
        public string $status,
        #[MapInputName('token')]
        public string $signingToken,
        #[MapInputName('signers')]
        /** @var array<string, string> */
        public array $signerTokens,
    ) {
    }
}
