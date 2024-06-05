<?php

declare(strict_types=1);

namespace Tizix\DataTransferObject\Casters;

use LogicException;
use Tizix\DataTransferObject\Caster;

final class EnumCaster implements Caster
{
    private string $enumType;

    /**
     * EnumCaster constructor.
     *
     * @param  string  $enumType  - A string name of the enum class to be cast to
     */
    public function __construct(string $enumType)
    {
        if (! class_exists($enumType)) {
            throw new LogicException("Class {$enumType} does not exist.");
        }

        $this->enumType = $enumType;
    }

    /**
     * Casts a value into an instance of an enum class.
     *
     * @param  mixed  $value  - The value to be transformed into an enum instance.
     * @return mixed - The result of the cast operation.
     *
     * @throws LogicException - If the enum class is not a backed enum or if the value can't be cast.
     */
    public function cast(mixed $value): mixed
    {
        if ($value instanceof $this->enumType) {
            return $value;
        }

        if (! is_a($this->enumType, 'BackedEnum', true)) {
            throw new LogicException("Caster [EnumCaster] may only be used to cast backed enums. Received [{$this->enumType}].");
        }

        $castedValue = $this->enumType::tryFrom($value);

        if (null === $castedValue) {
            throw new LogicException("Couldn't cast enum [{$this->enumType}] with value [{$value}]");
        }

        return $castedValue;
    }
}
