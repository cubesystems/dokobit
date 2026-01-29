<?php

declare(strict_types=1);

namespace Dokobit\Tests\Unit\Integration\Apis\Dtos\Signing;

use Dokobit\Integration\DTOs\Signing\Requests\FileUploadRequestPayload;
use Dokobit\Tests\TestCase;
use Illuminate\Validation\ValidationException;

class FileUploadRequestPayloadTest extends TestCase
{
    public function test_dokobit_signing_file_upload_request_payload_maps_to_external_representation(): void
    {
        $token = new FileUploadRequestPayload(
            name: 'file_name',
            digest: 'digest_value',
            content: 'file_content',
            url: 'www.example.com'
        );

        $externalRepresentation = $token->toArray();

        $this->assertEquals([
            'name' => 'file_name',
            'digest' => 'digest_value',
            'content' => 'file_content',
            'url' => 'www.example.com',
        ], $externalRepresentation);
    }

    public function test_fails_validation_when_mandatory_fields_not_specified(): void
    {
        $this->expectException(ValidationException::class);

        FileUploadRequestPayload::validateAndCreate([
            'content' => 'content'
        ]);
    }

    public function test_fails_validation_when_neither_url_nor_contents_are_specified(): void
    {
        $this->expectException(ValidationException::class);

        FileUploadRequestPayload::validateAndCreate([
            'name' => 'name',
            'digest' => 'digest',
        ]);
    }

    public function test_passes_validation_when_url_is_specified(): void
    {
        $payload = FileUploadRequestPayload::validateAndCreate([
            'name' => 'name',
            'digest' => 'digest',
            'url' => 'www.example.com'
        ]);

        $this->assertInstanceOf(FileUploadRequestPayload::class, $payload);
    }

    public function test_passes_validation_when_contents_are_specified(): void
    {
        $payload = FileUploadRequestPayload::validateAndCreate([
            'name' => 'name',
            'digest' => 'digest',
            'content' => 'contents'
        ]);

        $this->assertInstanceOf(FileUploadRequestPayload::class, $payload);
    }
}
