<?php

declare(strict_types=1);

namespace Dokobit\Integration\Api;

use Dokobit\Integration\DTOs\Auth\CreateSessionRequestPayload;
use Dokobit\Integration\DTOs\Auth\CreateSessionResponsePayload;
use Dokobit\Integration\DTOs\Auth\GetUserInfoResponsePayload;
use Dokobit\Integration\Exceptions\DokobitException;
use Illuminate\Http\Client\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class SessionApi extends ApiAccessor
{
    private const string STATUS_SUCCESS = 'ok';

    /**
     * @throws DokobitException
     */
    public function create(CreateSessionRequestPayload $payload): CreateSessionResponsePayload
    {
        $response = $this->request->post('api/authentication/create', $payload->toArray());

        $this->validateResponseByPayloadStatus($response);

        return CreateSessionResponsePayload::from($response->json());
    }

    public function get(string $token): GetUserInfoResponsePayload
    {
        $response = $this->request->get('api/authentication/' . $token . '/status');

        return GetUserInfoResponsePayload::from($response->json());
    }

    /**
     * @throws DokobitException
     */
    private function validateResponseByPayloadStatus(Response $response): void
    {
        if ($response->json('status') !== self::STATUS_SUCCESS) {
            throw new DokobitException(
                $response->json('message', $response->reason()),
                SymfonyResponse::HTTP_NOT_FOUND,
            );
        }
    }
}
