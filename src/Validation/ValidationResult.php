<?php

declare(strict_types=1);

namespace Tizix\DataTransferObject\Validation;

use JetBrains\PhpStorm\Immutable;

final class ValidationResult
{
    public function __construct(
        #[Immutable]
        public bool $isValid,
        #[Immutable]
        public ?string $message = null
    ) {
    }

    public static function valid(): self
    {
        return new self(
            isValid: true,
        );
    }

    public static function invalid(string $message): self
    {
        return new self(
            isValid: false,
            message: $message,
        );
    }
}
