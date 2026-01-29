<?php

declare(strict_types=1);

namespace Dokobit\Enums;

enum DokobitFileFormat: string
{
    case PDF = 'pdf';
    case XML = 'xml';
    case EDOC = 'edoc';
    case UNKNOWN = 'unknown';
}
