<?php

declare(strict_types=1);

namespace Tizix\DataTransferObject;

use Tizix\DataTransferObject\Validation\ValidationResult;

interface Validator
{
    public function validate(mixed $value): ValidationResult;
}
