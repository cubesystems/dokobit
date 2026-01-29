<?php

declare(strict_types=1);

namespace Dokobit\Integration\DTOs\Auth;

use Spatie\LaravelData\Attributes\MapName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;
use Spatie\LaravelData\Optional;

#[MapName(SnakeCaseMapper::class)]
final class CreateSessionRequestPayload extends Data
{
    public function __construct(
        public string|Optional $returnUrl,
        public string|Optional $originHost,
        public string|Optional $code,
        public string|Optional $countryCode,
        public string|Optional $phone,
        public array|Optional $authenticationMethods,
        public array|Optional $supportedResidencies,
    ) {
    }

    public static function rules(): array
    {
        return [
          'return_url' => 'required_without:origin_host',
          'origin_host' => 'required_without:return_url',
        ];
    }
}
