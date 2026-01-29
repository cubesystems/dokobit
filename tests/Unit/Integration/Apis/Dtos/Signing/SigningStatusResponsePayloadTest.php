<?php

declare(strict_types=1);

namespace Dokobit\Tests\Unit\Integration\Apis\Dtos\Signing;

use Dokobit\Enums\SigningStatus;
use Dokobit\Integration\DTOs\Signing\Responses\SigningStatusResponsePayload;
use Dokobit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\LaravelData\Exceptions\CannotCastEnum;

class SigningStatusResponsePayloadTest extends TestCase
{
    #[DataProvider('statusProvider')]
    public function test_dokobit_signing_status_response_payload_maps_from_external_representation(
        string $rawStatus,
        SigningStatus $expectedStatus,
        bool $expectedCompleted,
        bool $expectedPending
    ): void {
        $response = SigningStatusResponsePayload::from([
            'status' => $rawStatus,
        ]);

        $this->assertEquals($expectedStatus, $response->status);
        $this->assertEquals($expectedCompleted, $response->status->isCompleted());
        $this->assertEquals($expectedPending, $response->status->isPending());
    }

    public static function statusProvider(): array
    {
        return [
            ['archived', SigningStatus::Archived, false, false],
            ['completed', SigningStatus::Completed, true, false],
            ['failed', SigningStatus::Failed, false, false],
            ['pending', SigningStatus::Pending, false, true],
        ];
    }

    public function test_throws_on_unexpected_format(): void
    {
        $this->expectException(CannotCastEnum::class);

        SigningStatusResponsePayload::from([
            'status' => 'unknown',
        ]);
    }
}
