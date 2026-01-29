<?php

declare(strict_types=1);

namespace Dokobit\Tests\Unit\Integration\Apis\Dtos\Signing;

use Dokobit\Integration\DTOs\Signing\Responses\FileUploadResponsePayload;
use Dokobit\Tests\TestCase;

class FileUploadResponsePayloadTest extends TestCase
{
    public function test_dokobit_signing_file_upload_response_payload_maps_from_external_representation(): void
    {
        $response = FileUploadResponsePayload::from([
            'status' => 'success',
            'token' => 'token',
        ]);

        $this->assertEquals('success', $response->status);
        $this->assertEquals('token', $response->uploadToken);
    }
}
