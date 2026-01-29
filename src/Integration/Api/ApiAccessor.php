<?php

declare(strict_types=1);

namespace Dokobit\Integration\Api;

use Illuminate\Http\Client\PendingRequest;

abstract class ApiAccessor
{
    public function __construct(
        protected PendingRequest $request
    ) {
    }
}
