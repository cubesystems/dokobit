<?php

declare(strict_types=1);

namespace Dokobit\Integration\DTOs\Signing\Requests;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

final class FileUploadRequestPayload extends Data
{
    public function __construct(
        public string $name,
        public string $digest,
        public string|Optional $content,
        public string|Optional $url,
    ) {
    }

    public function defaultWrap(): string
    {
        return 'file';
    }

    public static function rules(): array
    {
        return [
            'content' => 'required_without:url',
            'url' => 'required_without:content',
        ];
    }
}
