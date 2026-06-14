<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Tests\Support;

use function file_get_contents;
use function is_array;
use function is_string;
use function json_decode;

trait FixtureLoader
{
    protected function fixtureJson(string $file): string
    {
        $body = file_get_contents(__DIR__ . '/../Fixtures/api/' . $file);
        if (!is_string($body)) {
            self::fail('Unable to load fixture: ' . $file);
        }

        return $body;
    }

    /**
     * @return array<string, mixed>
     */
    protected function fixtureArray(string $file): array
    {
        $decoded = json_decode($this->fixtureJson($file), true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            self::fail('Fixture is not JSON object/array: ' . $file);
        }

        return $decoded;
    }
}
