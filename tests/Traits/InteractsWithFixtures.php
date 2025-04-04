<?php

namespace Tests\Traits;

trait InteractsWithFixtures
{
    public static function getFixtureContents($path): ?string
    {
        $path = dirname(__FILE__, 3) . "/storage/fixtures/" . $path;
        return file_get_contents($path);
    }

    public static function getFixtureData($path): ?array
    {
        return json_decode(static::getFixtureContents($path), true);
    }
}
