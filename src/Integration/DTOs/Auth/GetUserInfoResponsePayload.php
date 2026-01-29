<?php

declare(strict_types=1);

namespace Dokobit\Integration\DTOs\Auth;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapName(SnakeCaseMapper::class)]
class GetUserInfoResponsePayload extends Data
{
    public function __construct(
        public string $status,
        public string $code,
        public string $name,
        public string $surname,
    ) {
    }
}
