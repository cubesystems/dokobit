<?php

declare(strict_types=1);

namespace Dokobit\Integration;

use Dokobit\Integration\Api\SigningApi;
use Dokobit\Integration\Exceptions\ApiException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class SigningApiClient
{
    public function __construct(
        private readonly PendingRequest $request
    ) {
    }

    public static function buildRequest(string $baseUrl, string $accessToken): PendingRequest
    {
        return Http::baseUrl($baseUrl)
            ->withOptions([
                'query' => [
                    'access_token' => $accessToken,
                ],
            ])
            ->throw(function (Response $response) {
                throw ApiException::make($response);
            });
    }

    public function signing(): SigningApi
    {
        return new SigningApi($this->request);
    }
}
