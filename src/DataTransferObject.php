<?php

declare(strict_types=1);

namespace Tizix\DataTransferObject;

use ReflectionClass;
use ReflectionProperty;
use Tizix\DataTransferObject\Attributes\CastWith;
use Tizix\DataTransferObject\Attributes\MapTo;
use Tizix\DataTransferObject\Casters\DataTransferObjectCaster;
use Tizix\DataTransferObject\Exceptions\UnknownProperties;
use Tizix\DataTransferObject\Exceptions\ValidationException;
use Tizix\DataTransferObject\Reflection\DataTransferObjectClass;

#[CastWith(DataTransferObjectCaster::class)]
abstract class DataTransferObject
{
    protected array $exceptKeys = [];

    protected array $onlyKeys = [];

    /**
     * @param ...$args
     * @throws ValidationException
     * @throws UnknownProperties
     */
    public function __construct(...$args)
    {
        if (is_array($args[0] ?? null)) {
            $args = $args[0];
        }

        $class = new DataTransferObjectClass($this);

        foreach ($class->getProperties() as $property) {
            $property->setValue(Arr::get($args, $property->name, $property->getDefaultValue()));

            $args = Arr::forget($args, $property->name);
        }

        if ($class->isStrict() && count($args)) {
            throw UnknownProperties::new(static::class, array_keys($args));
        }

        $class->validate();
    }

    public static function arrayOf(array $arrayOfParameters): array
    {
        return array_map(
            /**
             * @throws UnknownProperties|ValidationException
             */
            static fn (mixed $parameters) => new static($parameters),
            $arrayOfParameters
        );
    }

    public function all(): array
    {
        $data = [];

        $class = new ReflectionClass(static::class);

        $properties = $class->getProperties(ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $mapToAttribute = $property->getAttributes(MapTo::class);
            $name = count($mapToAttribute) ? $mapToAttribute[0]->newInstance()->name : $property->getName();

            $data[$name] = $property->getValue($this);
        }

        return $data;
    }

    public function only(string ...$keys): static
    {
        $dataTransferObject = clone $this;

        $dataTransferObject->onlyKeys = [...$this->onlyKeys, ...$keys];

        return $dataTransferObject;
    }

    public function except(string ...$keys): static
    {
        $dataTransferObject = clone $this;

        $dataTransferObject->exceptKeys = [...$this->exceptKeys, ...$keys];

        return $dataTransferObject;
    }

    /**
     * @throws UnknownProperties
     * @throws ValidationException
     */
    public function clone(...$args): static
    {
        return new static(...array_merge($this->toArray(), $args));
    }

    public function toArray(): array
    {
        if (count($this->onlyKeys)) {
            $array = Arr::only($this->all(), $this->onlyKeys);
        } else {
            $array = Arr::except($this->all(), $this->exceptKeys);
        }

        return $this->parseArray($array);
    }

    protected function parseArray(array $array): array
    {
        foreach ($array as $key => $value) {
            if ($value instanceof self) {
                $array[$key] = $value->toArray();

                continue;
            }

            if (! is_array($value)) {
                continue;
            }

            $array[$key] = $this->parseArray($value);
        }

        return $array;
    }
}
