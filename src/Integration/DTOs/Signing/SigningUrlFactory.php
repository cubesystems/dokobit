<?php

declare(strict_types=1);

namespace Dokobit\Integration\DTOs\Signing;

use Dokobit\Integration\DTOs\Signing\Responses\NewSigningResponsePayload;
use Illuminate\Support\Collection;

class SigningUrlFactory
{
    private const string SIGNING_URL_PATTERN = '/signing/{signing_token}?access_token={signer_token}';

    public function __construct(
        private readonly string $baseUrl,
    ) {
    }

    public function make(NewSigningResponsePayload $signing): Collection
    {
        $urls = collect();

        foreach ($signing->signerTokens as $signerId => $signerToken) {
            $urls->put($signerId, $this->makeSignerUrl($signing->signingToken, $signerToken));
        }

        return $urls;
    }

    private function makeSignerUrl(string $signingToken, string $signerToken): string
    {
        return $this->baseUrl . strtr(
            self::SIGNING_URL_PATTERN,
            [
                '{signing_token}' => $signingToken,
                '{signer_token}' => $signerToken,
            ]
        );
    }
}
