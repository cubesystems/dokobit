<?php

declare(strict_types=1);

namespace Dokobit\Integration\DTOs\Signing\Entities;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;

class Token extends Data
{
    public function __construct(
        #[MapName('token')]
        public string $value,
    ) {
    }
}
