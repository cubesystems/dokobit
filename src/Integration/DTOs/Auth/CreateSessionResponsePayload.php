<?php

declare(strict_types=1);

namespace Dokobit\Integration\DTOs\Auth;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
class CreateSessionResponsePayload extends Data
{
    public function __construct(
        public string $status,
        public string $sessionToken,
        public string $url,
        public int $expiresIn,
    ) {
    }
}
