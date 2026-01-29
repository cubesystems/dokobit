<?php

declare(strict_types=1);

namespace Dokobit\Integration\DTOs\Signing\Responses;

use Dokobit\Enums\SigningDeletionStatus;
use Spatie\LaravelData\Data;

class SigningDeletionStatusResponsePayload extends Data
{
    public function __construct(
        public SigningDeletionStatus $status,
    ) {
    }
}
