<?php

declare(strict_types=1);

namespace Dokobit\Enums;

enum SigningStatus: string
{
    case Archived = 'archived';

    case Completed = 'completed';

    case Failed = 'failed';

    case Pending = 'pending';

    public function isCompleted(): bool
    {
        return $this === self::Completed;
    }

    public function isPending(): bool
    {
        return $this === self::Pending;
    }
}
