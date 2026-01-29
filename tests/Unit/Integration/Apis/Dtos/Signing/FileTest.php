<?php

declare(strict_types=1);

namespace Dokobit\Tests\Unit\Integration\Apis\Dtos\Signing;

use Dokobit\Enums\DokobitFileFormat;
use Dokobit\Integration\DTOs\Signing\Entities\File;
use Dokobit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\LaravelData\Exceptions\CannotCastEnum;

class FileTest extends TestCase
{
    #[DataProvider('fileFormatProvider')]
    public function test_dokobit_signing_file_dto_maps_external_record_to_file(string $rawFormat, DokobitFileFormat $expected): void
    {
        $file = File::from([
            'format' => $rawFormat,
            'content' => 'content',
        ]);

        $this->assertEquals('content', $file->content);
        $this->assertEquals($expected, $file->format);
    }

    public static function fileFormatProvider(): array
    {
        return [
            ['pdf', DokobitFileFormat::PDF],
            ['xml', DokobitFileFormat::XML],
            ['edoc', DokobitFileFormat::EDOC],
        ];
    }

    public function test_throws_on_unexpected_format(): void
    {
        $this->expectException(CannotCastEnum::class);

        File::from([
            'format' => 'unexpected',
            'content' => 'content',
        ]);
    }
}
