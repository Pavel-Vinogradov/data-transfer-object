<?php

declare(strict_types=1);

namespace Tizix\DataTransferObject\Attributes;

use Attribute;
use Tizix\DataTransferObject\Caster;
use Tizix\DataTransferObject\Exceptions\InvalidCasterClass;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
final class CastWith
{
    public array $args;

    /**
     * @throws InvalidCasterClass
     */
    public function __construct(
        public string $casterClass,
        mixed ...$args
    ) {
        if (!is_subclass_of($this->casterClass, Caster::class)) {
            throw new InvalidCasterClass($this->casterClass);
        }

        $this->args = $args;
    }
}
