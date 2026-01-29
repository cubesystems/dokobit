<?php

declare(strict_types=1);

namespace Dokobit\Integration\DTOs\Signing\Requests;

use Dokobit\Integration\DTOs\Signing\Entities\Signer;
use Dokobit\Integration\DTOs\Signing\Entities\Token;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

final class NewSigningRequestPayload extends Data
{
    public function __construct(
        public string $type,
        public string $name,
        /** @var Collection<int, Signer> */
        public Collection $signers,
        #[MapName('files')]
        /** @var Collection<int, Token> */
        public Collection $fileTokens,
    ) {
    }
}
