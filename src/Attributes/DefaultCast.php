<?php

declare(strict_types=1);

namespace Tizix\DataTransferObject\Attributes;

use Attribute;
use ReflectionProperty;
use ReflectionNamedType;
use ReflectionUnionType;
use JetBrains\PhpStorm\Immutable;
use Tizix\DataTransferObject\Caster;

#[Attribute(Attribute::TARGET_CLASS | Attribute::IS_REPEATABLE)]
final class DefaultCast
{
    public function __construct(
        #[Immutable]
        private string $targetClass,
        #[Immutable]
        private string $casterClass,
    ) {}

    public function accepts(ReflectionProperty $property): bool
    {
        $type = $property->getType();

        /** @var null|ReflectionNamedType[] $types */
        $types = match ($type::class) {
            ReflectionNamedType::class => [$type],
            ReflectionUnionType::class => $type->getTypes(),
            default => null,
        };

        if (!$types) {
            return false;
        }

        foreach ($types as $type) {
            if ($type->getName() !== $this->targetClass) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function resolveCaster(): Caster
    {
        return new $this->casterClass();
    }
}
