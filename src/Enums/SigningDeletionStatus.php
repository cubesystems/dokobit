<?php

declare(strict_types=1);

namespace Dokobit\Enums;

enum SigningDeletionStatus: string
{
    case Ok = 'ok';

    case Error = 'error';

    public function isOk(): bool
    {
        return $this === self::Ok;
    }
}
