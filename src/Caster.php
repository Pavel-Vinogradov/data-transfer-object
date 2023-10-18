<?php

declare(strict_types=1);

namespace Tizix\DataTransferObject;

interface Caster
{
    public function cast(mixed $value): mixed;
}
