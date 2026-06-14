<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Exception;

use RuntimeException;
use Throwable;

class ApiException extends RuntimeException
{
    public static function invalidJson(string $body, string $message, ?Throwable $previous = null): self
    {
        return new self(sprintf('Unable to decode JSON response: %s. Body: %s', $message, $body), 0, $previous);
    }

    public static function httpError(int $statusCode, string $body): self
    {
        return new self(sprintf('Unexpected HTTP status %d. Body: %s', $statusCode, $body));
    }
}
