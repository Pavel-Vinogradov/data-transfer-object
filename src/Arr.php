<?php

declare(strict_types=1);

namespace Tizix\DataTransferObject;

use ArrayAccess;

final class Arr
{
    public static function only($array, $keys): array
    {
        return array_intersect_key($array, array_flip((array) $keys));
    }

    public static function except($array, $keys): array
    {
        return self::forget($array, $keys);
    }

    public static function forget($array, $keys): array
    {
        $keys = (array) $keys;

        if (0 === count($keys)) {
            return $array;
        }

        foreach ($keys as $key) {
            if (self::exists($array, $key)) {
                unset($array[$key]);

                continue;
            }

            if (! str_contains($key, '.')) {
                continue;
            }

            $parts = explode('.', $key);
            $key = array_shift($parts);

            if (self::exists($array, $key) && self::accessible($array[$key])) {
                $array[$key] = self::forget($array[$key], implode('.', $parts));

                if (0 === count($array[$key])) {
                    unset($array[$key]);
                }
            }
        }

        return $array;
    }

    public static function get($array, $key, $default = null)
    {
        if (! self::accessible($array)) {
            return $default;
        }

        if (null === $key) {
            return $array;
        }

        if (self::exists($array, $key)) {
            return $array[$key];
        }

        if (! str_contains($key, '.')) {
            return $array[$key] ?? $default;
        }

        foreach (explode('.', $key) as $segment) {
            if (self::accessible($array) && self::exists($array, $segment)) {
                $array = $array[$segment];
            } else {
                return $default;
            }
        }

        return $array;
    }

    public static function accessible($value): bool
    {
        return is_array($value) || $value instanceof ArrayAccess;
    }

    public static function exists($array, $key): bool
    {
        if ($array instanceof ArrayAccess) {
            return $array->offsetExists($key);
        }

        return array_key_exists($key, $array);
    }
}
