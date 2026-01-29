<?php

declare(strict_types=1);

namespace Dokobit\Integration\DTOs\Signing\Responses;

use Dokobit\Enums\FileUploadStatus;
use Spatie\LaravelData\Data;

class FileUploadStatusResponsePayload extends Data
{
    public function __construct(
        public FileUploadStatus $status,
    ) {
    }
}
