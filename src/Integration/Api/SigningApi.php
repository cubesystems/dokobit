<?php

namespace Dokobit\Integration\Api;

use Dokobit\Enums\DokobitFileFormat;
use Dokobit\Enums\FileUploadStatus;
use Dokobit\Integration\DTOs\Signing\Entities\File;
use Dokobit\Integration\DTOs\Signing\Requests\FileUploadRequestPayload;
use Dokobit\Integration\DTOs\Signing\Requests\NewSigningRequestPayload;
use Dokobit\Integration\DTOs\Signing\Responses\FileUploadResponsePayload;
use Dokobit\Integration\DTOs\Signing\Responses\FileUploadStatusResponsePayload;
use Dokobit\Integration\DTOs\Signing\Responses\NewSigningResponsePayload;
use Dokobit\Integration\DTOs\Signing\Responses\SigningDeletionStatusResponsePayload;
use Dokobit\Integration\DTOs\Signing\Responses\SigningStatusResponsePayload;
use Dokobit\Integration\Exceptions\DokobitException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class SigningApi extends ApiAccessor
{
    private const string STATUS_SUCCESS = 'ok';

    /**
     * @throws DokobitException|ConnectionException
     */
    public function postFile(FileUploadRequestPayload $payload): FileUploadResponsePayload
    {
        $response =  $this->request->post('api/file/upload.json', [
            'file' => $payload->toArray()
        ]);

        $this->validateUploadResponseByPayloadStatus($response);

        return FileUploadResponsePayload::from($response->json());
    }

    /**
     * @throws DokobitException|ConnectionException
     */
    public function getFileUploadStatus(string $uploadToken): FileUploadStatusResponsePayload
    {
        $response = $this->request->get('api/file/upload/' . $uploadToken . '/status.json');

        $this->validateUploadCheckResponseByPayloadStatus($response);

        return FileUploadStatusResponsePayload::from($response->json());
    }

    /**
     * @throws DokobitException|ConnectionException
     */
    public function postNewSigning(NewSigningRequestPayload $payload): NewSigningResponsePayload
    {
        $response =  $this->request->post('api/signing/create.json', $payload);

        $this->validateUploadResponseByPayloadStatus($response);

        return NewSigningResponsePayload::from($response->json());
    }

    /**
     * @throws DokobitException|ConnectionException
     */
    public function getSigningStatus(string $signingToken): SigningStatusResponsePayload
    {
        $response = $this->request->get('api/signing/' . $signingToken . '/status.json');

        return SigningStatusResponsePayload::from($response->json());
    }

    public function getSignedFile(string $url, DokobitFileFormat $format = DokobitFileFormat::EDOC): File
    {
        $response = $this->request->get($url);

        return File::from([
            'format' => $format->value,
            'content' => $response->body(),
        ]);
    }

    public function deleteSigning(string $signingToken): SigningDeletionStatusResponsePayload
    {
        $response = $this->request->post('api/signing/' . $signingToken . '/delete.json');

        return SigningDeletionStatusResponsePayload::from($response->json());
    }

    /**
     * @throws DokobitException
     */
    private function validateUploadResponseByPayloadStatus(Response $response): void
    {
        if ($response->json('status') !== self::STATUS_SUCCESS) {
            throw new DokobitException(
                $response->json('message', $response->reason()),
                SymfonyResponse::HTTP_NOT_FOUND,
            );
        }
    }

    /**
     * @throws DokobitException
     */
    private function validateUploadCheckResponseByPayloadStatus(Response $response): void
    {
        $status = $response->json('status');

        $acceptableStates = [
            FileUploadStatus::Uploaded->value,
            FileUploadStatus::Pending->value,
        ];

        if (!in_array($status, $acceptableStates, true)) {
            throw new DokobitException(
                $response->json('message', $response->reason()),
                $response->status(),
            );
        }
    }
}
