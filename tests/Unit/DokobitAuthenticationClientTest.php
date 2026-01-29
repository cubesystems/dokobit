<?php

declare(strict_types=1);

namespace Dokobit\Tests\Unit;

use Illuminate\Http\Client\Factory;
use Dokobit\Integration\AuthenticationApiClient;
use Dokobit\Integration\DTOs\Auth\CreateSessionRequestPayload;
use Dokobit\Integration\DTOs\Auth\GetUserInfoResponsePayload;
use Dokobit\Integration\Exceptions\ConnectivityException;
use Dokobit\Integration\Exceptions\DokobitException;
use Dokobit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use function Dokobit\Tests\{getDokobitDatasetSource, stubUrlWithFixture};

class DokobitAuthenticationClientTest extends TestCase
{
    public function test_should_build_request_with_auth_token(): void
    {
        $baseUrl = 'http://example.com';
        $token = 'some-token';

        $request = AuthenticationApiClient::buildRequest($baseUrl, $token);

        $this->assertArrayHasKey('query', $request->getOptions());
        $this->assertEquals(['access_token' => $token], $request->getOptions()['query']);
    }

    #[DataProvider('errorResponseProvider')]
    public function test_throws_api_exception_on_error_responses(int $code, string $payloadFilename, string $expectedExceptionClass): void
    {
        $responsePayload = getDokobitDatasetSource($payloadFilename);
        $expectedResponse = json_decode($responsePayload, true);

        $request = AuthenticationApiClient::buildRequest('some-base-url', 'some-token');
        $request
            ->preventStrayRequests()
            ->stub(function () use ($code, $responsePayload) {
                return Factory::response($responsePayload, $code);
            });

        $client = new AuthenticationApiClient($request);

        $requestPayload = CreateSessionRequestPayload::from([
            'origin_host' => 'https://id-sandbox.dokobit.com/',
            'supported_residencies' => ['lv']
        ]);

        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedResponse['message']);

        $client->sessions()->create($requestPayload);
    }

    public static function errorResponseProvider(): array
    {
        return [
            [SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, 'error_generic', ConnectivityException::class],
            [SymfonyResponse::HTTP_OK, 'get_user_info_waiting', DokobitException::class],
            [SymfonyResponse::HTTP_BAD_REQUEST, 'get_user_info_expired', DokobitException::class],
        ];
    }

    public function test_should_create_session(): void
    {
        $factory = stubUrlWithFixture('api/authentication/create', 'create_session_success');

        $request = $factory
            ->baseUrl('some-base-url');
        $client = new AuthenticationApiClient($request);

        $requestData = [
            'origin_host' => 'https://example.com',
            'supported_residencies' => ['lv']
        ];

        $payload = CreateSessionRequestPayload::from($requestData);

        $session = $client->sessions()->create($payload);

        $this->assertEquals('02f922c9917231ea8acbbbcf63796924af548c801d75772f2b1701b413462c61', $session->sessionToken);
    }

    public function test_obtains_user_info_based_on_token(): void
    {
        $token = 'test_token';

        $responsePayload = getDokobitDatasetSource('get_user_info');
        $expectedPayload = json_decode($responsePayload, true);

        $factory = stubUrlWithFixture('api/authentication/' . $token . '/status', 'get_user_info');

        $request = $factory
            ->baseUrl('/');
        $client = new AuthenticationApiClient($request);

        $response = $client->sessions()->get($token);

        $this->assertInstanceOf(GetUserInfoResponsePayload::class, $response);
        $this->assertEquals($expectedPayload['code'], $response->code);
        $this->assertEquals($expectedPayload['name'], $response->name);
        $this->assertEquals($expectedPayload['surname'], $response->surname);
    }
}
