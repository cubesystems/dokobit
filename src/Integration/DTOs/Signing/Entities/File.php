<?php

declare(strict_types=1);

namespace Dokobit\Integration\DTOs\Signing\Entities;

use Dokobit\Enums\DokobitFileFormat;
use Spatie\LaravelData\Data;

class File extends Data
{
    public function __construct(
        public string $content,
        public DokobitFileFormat $format,
    ) {
    }
}
