<?php

declare(strict_types=1);

namespace Tizix\DataTransferObject\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
final class MapTo
{
    public function __construct(
        public string $name,
    ) {
    }
}
