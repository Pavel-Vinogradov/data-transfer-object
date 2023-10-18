<?php

declare(strict_types=1);

namespace Tizix\DataTransferObject\Exceptions;

use Exception;
use Tizix\DataTransferObject\Caster;

final class InvalidCasterClass extends Exception
{
    public function __construct(string $className)
    {
        $expected = Caster::class;

        parent::__construct(
            "Class `{$className}` doesn't implement {$expected} and can't be used as a caster"
        );
    }
}
