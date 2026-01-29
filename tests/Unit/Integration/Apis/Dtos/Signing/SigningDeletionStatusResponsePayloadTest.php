<?php

declare(strict_types=1);

namespace Dokobit\Tests\Unit\Integration\Apis\Dtos\Signing;

use Dokobit\Enums\SigningDeletionStatus;
use Dokobit\Integration\DTOs\Signing\Responses\SigningDeletionStatusResponsePayload;
use Dokobit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\LaravelData\Exceptions\CannotCastEnum;

class SigningDeletionStatusResponsePayloadTest extends TestCase
{
    #[DataProvider('statusProvider')]
    public function test_dokobit_signing_deletion_status_payload_maps_from_external_representation(
        string $rawStatus,
        SigningDeletionStatus $expectedStatus,
        bool $ok
    ): void {
        $response = SigningDeletionStatusResponsePayload::from([
            'status' => $rawStatus,
        ]);

        $this->assertEquals($expectedStatus, $response->status);
        $this->assertEquals($ok, $response->status->isOk());
    }

    public static function statusProvider(): array
    {
        return [
            ['ok', SigningDeletionStatus::Ok, true],
            ['error', SigningDeletionStatus::Error, false],
        ];
    }

    public function test_throws_on_unexpected_format(): void
    {
        $this->expectException(CannotCastEnum::class);

        SigningDeletionStatusResponsePayload::from([
            'status' => 'unknown',
        ]);
    }
}
