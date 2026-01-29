<?php

declare(strict_types=1);

namespace Dokobit\Enums;

enum FileUploadStatus: string
{
    case Uploaded = 'uploaded';

    case Error = 'error';

    case Pending = 'pending';

    public function isUploaded(): bool
    {
        return $this === self::Uploaded;
    }

    public function isPending(): bool
    {
        return $this === self::Pending;
    }
}
