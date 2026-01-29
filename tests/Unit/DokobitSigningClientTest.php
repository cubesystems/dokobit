<?php

declare(strict_types=1);

namespace Dokobit\Tests\Unit;

use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Factory;
use Dokobit\Enums\DokobitFileFormat;
use Dokobit\Enums\FileUploadStatus;
use Dokobit\Enums\SigningDeletionStatus;
use Dokobit\Enums\SigningStatus;
use Dokobit\Integration\DTOs\Signing\Entities\Signer;
use Dokobit\Integration\DTOs\Signing\Entities\Token;
use Dokobit\Integration\DTOs\Signing\Requests\FileUploadRequestPayload;
use Dokobit\Integration\DTOs\Signing\Requests\NewSigningRequestPayload;
use Dokobit\Integration\Exceptions\ConnectivityException;
use Dokobit\Integration\Exceptions\DokobitException;
use Dokobit\Integration\SigningApiClient;
use Dokobit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

use function Dokobit\Tests\{getDokobitDatasetSource, stubUrlWithFixture};

class DokobitSigningClientTest extends TestCase
{
    public function test_builds_with_auth_token(): void
    {
        $baseUrl = 'http://example.com';
        $token = 'some-token';

        $request = SigningApiClient::buildRequest($baseUrl, $token);

        $this->assertArrayHasKey('query', $request->getOptions());
        $this->assertEquals(['access_token' => $token], $request->getOptions()['query']);
    }

    #[DataProvider('fileUploadErrorResponseProvider')]
    public function test_file_upload_throws_api_exception_on_error_responses(int $code, string $payloadFilename, string $expectedExceptionClass): void
    {
        $responsePayload = getDokobitDatasetSource($payloadFilename);
        $expectedResponse = json_decode($responsePayload, true);

        $request = SigningApiClient::buildRequest('some-base-url', 'some-token');
        $request
            ->preventStrayRequests()
            ->stub(function () use ($code, $responsePayload) {
                return Factory::response($responsePayload, $code);
            });

        $client = new SigningApiClient($request);

        $requestPayload = FileUploadRequestPayload::from([
            'name' => 'test.pdf',
            'content' => 'test content',
            'digest' => 'test digest',
        ]);

        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedResponse['message']);

        $client->signing()->postFile($requestPayload);
    }

    public static function fileUploadErrorResponseProvider(): array
    {
        return [
            [SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, 'error_generic', ConnectivityException::class],
            [SymfonyResponse::HTTP_BAD_REQUEST, 'file_upload_invalid', DokobitException::class],
        ];
    }

    public function test_file_upload_uploads_file_and_returns_file_token(): void
    {
        $factory = stubUrlWithFixture('api/file/upload.json', 'file_upload_success');

        $request = $factory
            ->baseUrl('some-base-url');
        $client = new SigningApiClient($request);

        $requestPayload = FileUploadRequestPayload::from([
            'name' => 'test.pdf',
            'content' => 'test content',
            'digest' => 'test digest',
        ]);

        $uploadResponse = $client->signing()->postFile($requestPayload);

        $this->assertEquals('ok', $uploadResponse->status);
        $this->assertEquals('test-file-upload-token', $uploadResponse->uploadToken);
    }

    #[DataProvider('fileUploadStatusCheckErrorResponseProvider')]
    public function test_file_upload_status_check_throws_api_exception_on_error_responses(int $code, string $payloadFilename, string $expectedExceptionClass): void
    {
        $responsePayload = getDokobitDatasetSource($payloadFilename);
        $expectedResponse = json_decode($responsePayload, true);

        $request = SigningApiClient::buildRequest('some-base-url', 'some-token');
        $request
            ->preventStrayRequests()
            ->stub(function () use ($code, $responsePayload) {
                return Factory::response($responsePayload, $code);
            });

        $client = new SigningApiClient($request);

        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedResponse['message']);

        $client->signing()->getFileUploadStatus('some-file-token');
    }

    public static function fileUploadStatusCheckErrorResponseProvider(): array
    {
        return [
            [SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, 'error_generic', ConnectivityException::class],
            [SymfonyResponse::HTTP_OK, 'file_upload_status_error', DokobitException::class],
            [SymfonyResponse::HTTP_BAD_REQUEST, 'file_upload_status_invalid', DokobitException::class],
        ];
    }

    #[DataProvider('fileUploadStatusProvider')]
    public function test_file_upload_status_check_returns_appropriate_file_upload_status(string $payloadFilename, FileUploadStatus $expectedStatus): void
    {
        $factory = stubUrlWithFixture('api/file/upload/upload-token/status.json', $payloadFilename);

        $request = $factory
            ->baseUrl('some-base-url');
        $client = new SigningApiClient($request);

        $uploadResponse = $client->signing()->getFileUploadStatus('upload-token');

        $this->assertEquals($expectedStatus, $uploadResponse->status);
    }

    public static function fileUploadStatusProvider(): array
    {
        return [
            ['file_upload_status_pending', FileUploadStatus::Pending],
            ['file_upload_status_uploaded', FileUploadStatus::Uploaded],
        ];
    }

    #[DataProvider('signingCreationErrorResponseProvider')]
    public function test_signing_creation_throws_api_exception_on_error_responses(int $code, string $payloadFilename, string $expectedExceptionClass): void
    {
        $responsePayload = getDokobitDatasetSource($payloadFilename);
        $expectedResponse = json_decode($responsePayload, true);

        $request = SigningApiClient::buildRequest('some-base-url', 'some-token');
        $request
            ->preventStrayRequests()
            ->stub(function () use ($code, $responsePayload) {
                return Factory::response($responsePayload, $code);
            });

        $client = new SigningApiClient($request);

        $requestPayload = new NewSigningRequestPayload(
            type: 'edoc',
            name: 'test.pdf',
            signers: collect([
                new Signer(
                    id: '040404-1999',
                    name: 'Tester',
                    surname: 'McTester',
                    signingOptions: [
                        'smartid'
                    ]
                )
            ]),
            fileTokens: collect([
                new Token('some-file-upload-token')
            ]),
        );

        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedResponse['message']);

        $client->signing()->postNewSigning($requestPayload);
    }

    public static function signingCreationErrorResponseProvider(): array
    {
        return [
            'server error' => [SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, 'error_generic', ConnectivityException::class],
            'validation error' => [SymfonyResponse::HTTP_BAD_REQUEST, 'create_signing_invalid', DokobitException::class],
            'data error' => [SymfonyResponse::HTTP_OK, 'create_signing_error', DokobitException::class],
        ];
    }

    public function test_signing_creation_creates_signing_process_and_acquires_signing_tokens(): void
    {
        $factory = stubUrlWithFixture('api/signing/create.json', 'create_signing_success_two_signers');

        $request = $factory
            ->baseUrl('some-base-url');
        $client = new SigningApiClient($request);

        $requestPayload = new NewSigningRequestPayload(
            type: 'edoc',
            name: 'test.pdf',
            signers: collect([
                new Signer(
                    id: '040404-1999',
                    name: 'Tester',
                    surname: 'McTester',
                    signingOptions: [
                        'smartid'
                    ]
                ),
                new Signer(
                    id: '040404-1999',
                    name: 'Programmer',
                    surname: 'McProgrammer',
                    signingOptions: [
                        'smartid'
                    ]
                ),
            ]),
            fileTokens: collect([
                new Token('some-file-upload-token')
            ]),
        );

        $uploadResponse = $client->signing()->postNewSigning($requestPayload);

        $this->assertEquals('ok', $uploadResponse->status);
        $this->assertEquals('signing-token', $uploadResponse->signingToken);
        $this->assertCount(2, $uploadResponse->signerTokens);
        $this->assertEquals('signer-token-1', $uploadResponse->signerTokens['signer1']);
        $this->assertEquals('signer-token-2', $uploadResponse->signerTokens['signer2']);
    }

    #[DataProvider('signingStatusCheckErrorResponseProvider')]
    public function test_signing_status_check_throws_api_exception_on_error_responses(int $code, string $payloadFilename, string $expectedExceptionClass): void
    {
        $responsePayload = getDokobitDatasetSource($payloadFilename);
        $expectedResponse = json_decode($responsePayload, true);

        $request = SigningApiClient::buildRequest('some-base-url', 'some-token');
        $request
            ->preventStrayRequests()
            ->stub(function () use ($code, $responsePayload) {
                return Factory::response($responsePayload, $code);
            });

        $client = new SigningApiClient($request);

        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedResponse['message']);

        $client->signing()->getSigningStatus('some-file-token');
    }

    public static function signingStatusCheckErrorResponseProvider(): array
    {
        return [
            'server error' => [SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, 'error_generic', ConnectivityException::class],
            'validation error' => [SymfonyResponse::HTTP_NOT_FOUND, 'signing_not_found', DokobitException::class],
        ];
    }

    #[DataProvider('signingStatusProvider')]
    public function test_signing_status_check_returns_appropriate_signing_status(string $payloadFilename, SigningStatus $expectedStatus): void
    {
        $factory = stubUrlWithFixture('api/signing/signing-token/status.json', $payloadFilename);

        $request = $factory
            ->baseUrl('some-base-url');
        $client = new SigningApiClient($request);

        $signingResponse = $client->signing()->getSigningStatus('signing-token');

        $this->assertEquals($expectedStatus, $signingResponse->status);
    }

    public static function signingStatusProvider(): array
    {
        return [
            'archived' => ['signing_status_archived', SigningStatus::Archived],
            'completed' => ['signing_status_completed', SigningStatus::Completed],
            'pending' => ['signing_status_pending', SigningStatus::Pending],
            'failed' => ['signing_status_failed', SigningStatus::Failed],
        ];
    }

    #[DataProvider('signingStatusWithFileProvider')]
    public function test_signing_status_check_includes_signed_file_information(string $payloadFilename, SigningStatus $expectedStatus): void
    {
        $factory = stubUrlWithFixture('api/signing/signing-token/status.json', $payloadFilename);

        $request = $factory
            ->baseUrl('some-base-url');
        $client = new SigningApiClient($request);

        $signingResponse = $client->signing()->getSigningStatus('signing-token');

        $this->assertEquals($expectedStatus, $signingResponse->status);
        $this->assertEquals('3019df4114c9fb4418cef322e1821c21eb0f01c6fd7ed08a51d42e384a91a4cf', $signingResponse->fileDigest);
        $this->assertEquals('https://documents-gateway.example.com/api/signing/signed-file-1/download', $signingResponse->fileUrl);
        $this->assertInstanceOf(CarbonImmutable::class, $signingResponse->validUntil);
        $this->assertEquals('2017-07-07 14:34:12', $signingResponse->validUntil->toDateTimeString());
    }

    public static function signingStatusWithFileProvider(): array
    {
        return [
            'archived' => ['signing_status_archived', SigningStatus::Archived],
            'completed' => ['signing_status_completed', SigningStatus::Completed],
        ];
    }

    #[DataProvider('signedFileDownloadErrorResponseProvider')]
    public function test_signed_file_download_throws_api_exception_on_error_responses(int $code, string $payloadFilename, string $expectedExceptionClass): void
    {
        $responsePayload = getDokobitDatasetSource($payloadFilename);
        $expectedResponse = json_decode($responsePayload, true);

        $url = 'http://example.com/signed/file';

        $request = SigningApiClient::buildRequest('some-base-url', 'some-token');
        $request
            ->preventStrayRequests()
            ->stub(function () use ($code, $responsePayload) {
                return Factory::response($responsePayload, $code);
            });

        $client = new SigningApiClient($request);

        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedResponse['message']);

        $client->signing()->getSignedFile($url, DokobitFileFormat::EDOC);
    }

    public static function signedFileDownloadErrorResponseProvider(): array
    {
        return [
            'server error' => [SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, 'error_generic', ConnectivityException::class],
            'not found' => [SymfonyResponse::HTTP_NOT_FOUND, 'signed_file_not_found', DokobitException::class],
        ];
    }

    public function test_signed_file_download_downloads_the_signed_file(): void
    {
        $url = 'http://example.com/signed/file';
        $fileFormat = DokobitFileFormat::EDOC;

        $factory = stubUrlWithFixture($url, 'signed_file_retrieval_success');

        $request = $factory
            ->baseUrl('some-base-url');
        $client = new SigningApiClient($request);

        $signedFile = $client->signing()->getSignedFile($url, $fileFormat);

        $this->assertEquals("Test signed file contents\n", $signedFile->content);
        $this->assertEquals($fileFormat, $signedFile->format);
    }

    #[DataProvider('signingDeletionErrorResponseProvider')]
    public function test_signing_deletion_throws_api_exception_on_error_responses(int $code, string $payloadFilename, string $expectedExceptionClass): void
    {
        $responsePayload = getDokobitDatasetSource($payloadFilename);
        $expectedResponse = json_decode($responsePayload, true);

        $request = SigningApiClient::buildRequest('some-base-url', 'some-token');
        $request
            ->preventStrayRequests()
            ->stub(function () use ($code, $responsePayload) {
                return Factory::response($responsePayload, $code);
            });

        $client = new SigningApiClient($request);

        $this->expectException($expectedExceptionClass);
        $this->expectExceptionMessage($expectedResponse['message']);

        $client->signing()->deleteSigning('some-signing-token');
    }

    public static function signingDeletionErrorResponseProvider(): array
    {
        return [
            'server error' => [SymfonyResponse::HTTP_INTERNAL_SERVER_ERROR, 'error_generic', ConnectivityException::class],
            'not found' => [SymfonyResponse::HTTP_NOT_FOUND, 'signing_not_found', DokobitException::class],
        ];
    }

    public function test_signing_deletion_returns_appropriate_signing_deletion_status(): void
    {
        $factory = stubUrlWithFixture('api/signing/signing-token/delete.json', 'signing_deleted_success');

        $request = $factory
            ->baseUrl('some-base-url');
        $client = new SigningApiClient($request);

        $signingResponse = $client->signing()->deleteSigning('signing-token');

        $this->assertEquals(SigningDeletionStatus::Ok, $signingResponse->status);
    }
}
