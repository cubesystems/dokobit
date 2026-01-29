<?php

declare(strict_types=1);

namespace Dokobit\Integration\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;

abstract class ApiException extends Exception
{
    public static function make(Response $response): ConnectivityException | DokobitException
    {
        $exceptionClass = $response->clientError() ? DokobitException::class : ConnectivityException::class;

        return new $exceptionClass(
            $response->json('message', $response->reason()),
            $response->status(),
            $response->toException()
        );
    }
}
