<?php

declare(strict_types=1);

namespace Dokobit\Integration\DTOs\Signing\Responses;

use Carbon\CarbonImmutable;
use Dokobit\Enums\SigningStatus;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

class SigningStatusResponsePayload extends Data
{
    public function __construct(
        public SigningStatus $status,
        #[MapInputName('file')]
        public string|Optional $fileUrl,
        #[MapName(SnakeCaseMapper::class)]
        public string|Optional $fileDigest,
        #[MapInputName('valid_to')]
        #[WithCast(DateTimeInterfaceCast::class, type: CarbonImmutable::class, format: 'Y-m-d H:i:s')]
        public CarbonImmutable|Optional $validUntil,
    ) {
    }
}
