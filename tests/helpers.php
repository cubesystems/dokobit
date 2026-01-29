<?php

declare(strict_types=1);

namespace Dokobit\Tests;

use Illuminate\Http\Client\Factory;

if (!function_exists('Dokobit\Tests\getRequestFactory')) {
    function getRequestFactory(): Factory
    {
        /** @var Factory $factory */
        $factory = app(Factory::class);
        $factory->preventStrayRequests();
        return $factory;
    }
}

if (!function_exists('Dokobit\Tests\stubUrlWithFixture')) {
    function stubUrlWithFixture(string $url, string $fixture): Factory
    {
        return getRequestFactory()->stubUrl($url, function () use ($fixture) {
            return Factory::response(getDokobitDatasetSource($fixture));
        });
    }
}

if (!function_exists('Dokobit\Tests\getDokobitDatasetSource')) {
    function getDokobitDatasetSource(string $name): string
    {
        return file_get_contents(__DIR__ . '/fixtures/' . $name . '.json');
    }
}
