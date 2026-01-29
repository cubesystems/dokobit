<?php

declare(strict_types=1);

namespace Dokobit\Integration\DTOs\Signing\Entities;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

class Signer extends Data
{
    public function __construct(
        public string $id,
        public string $name,
        public string $surname,
        #[MapName('signing_options')]
        public array $signingOptions,
    ) {
    }
}
