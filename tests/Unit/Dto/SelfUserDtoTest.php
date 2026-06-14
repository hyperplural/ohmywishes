<?php

declare(strict_types=1);

namespace Hyperplural\Ohmywishes\Tests\Unit\Dto;

use Hyperplural\Ohmywishes\Dto\User\SelfUserDto;
use Hyperplural\Ohmywishes\Tests\Support\FixtureLoader;
use PHPUnit\Framework\TestCase;

final class SelfUserDtoTest extends TestCase
{
    use FixtureLoader;

    public function testItMapsSelfUserResponse(): void
    {
        $payload = $this->fixtureArray('users-self.json');

        $dto = SelfUserDto::fromArray($payload);

        self::assertSame('bb677c0ecd14900917853653', $dto->id);
        self::assertSame('dream', $dto->username);
        self::assertSame('vasily ', $dto->fullName);
        self::assertSame('alexei.petrov@example.com', $dto->email);
        self::assertTrue($dto->isEmailConfirmed);
        self::assertSame(11, $dto->wishesCount);
    }
}
