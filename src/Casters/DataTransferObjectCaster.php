<?php

declare(strict_types=1);

namespace Tizix\DataTransferObject\Casters;

use Tizix\DataTransferObject\Caster;
use Tizix\DataTransferObject\DataTransferObject;

final readonly class DataTransferObjectCaster implements Caster
{
    public function __construct(
        private array $classNames
    ) {}

    public function cast(mixed $value): DataTransferObject
    {
        foreach ($this->classNames as $className) {
            if ($value instanceof $className) {
                return $value;
            }
        }

        return new $this->classNames[0]($value);
    }
}
