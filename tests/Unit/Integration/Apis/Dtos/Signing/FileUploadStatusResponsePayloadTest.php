<?php

declare(strict_types=1);

namespace Dokobit\Tests\Unit\Integration\Apis\Dtos\Signing;

use Dokobit\Enums\FileUploadStatus;
use Dokobit\Integration\DTOs\Signing\Responses\FileUploadStatusResponsePayload;
use Dokobit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\LaravelData\Exceptions\CannotCastEnum;

class FileUploadStatusResponsePayloadTest extends TestCase
{
    #[DataProvider('statusProvider')]
    public function test_dokobit_signing_file_upload_status_response_payload_maps_from_external_representation(
        string $rawStatus,
        FileUploadStatus $expectedStatus,
        bool $uploaded,
        bool $pending
    ): void {
        $response = FileUploadStatusResponsePayload::from([
            'status' => $rawStatus,
        ]);

        $this->assertEquals($expectedStatus, $response->status);
        $this->assertEquals($uploaded, $response->status->isUploaded());
        $this->assertEquals($pending, $response->status->isPending());
    }

    public static function statusProvider(): array
    {
        return [
            ['uploaded', FileUploadStatus::Uploaded, true, false],
            ['pending', FileUploadStatus::Pending, false, true],
            ['error', FileUploadStatus::Error, false, false],
        ];
    }

    public function test_throws_on_unexpected_format(): void
    {
        $this->expectException(CannotCastEnum::class);

        FileUploadStatusResponsePayload::from([
            'status' => 'unknown',
        ]);
    }
}
