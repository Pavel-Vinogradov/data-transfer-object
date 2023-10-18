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
        return Arr::forget($array, $keys);
    }

    public static function forget($array, $keys): array
    {
        $keys = (array) $keys;

        if (count($keys) === 0) {
            return $array;
        }

        foreach ($keys as $key) {
            if (Arr::exists($array, $key)) {
                unset($array[$key]);

                continue;
            }

            // Check if the key is using dot-notation
            if (! str_contains($key, '.')) {
                continue;
            }

            // If we are dealing with dot-notation, recursively handle i
            $parts = explode('.', $key);
            $key = array_shift($parts);

            if (Arr::exists($array, $key) && Arr::accessible($array[$key])) {
                $array[$key] = Arr::forget($array[$key], implode('.', $parts));

                if (count($array[$key]) === 0) {
                    unset($array[$key]);
                }
            }
        }

        return $array;
    }

    public static function get($array, $key, $default = null)
    {
        if (! Arr::accessible($array)) {
            return $default;
        }

        if (null === $key) {
            return $array;
        }

        if (Arr::exists($array, $key)) {
            return $array[$key];
        }

        if (!str_contains($key, '.')) {
            return $array[$key] ?? $default;
        }

        foreach (explode('.', $key) as $segment) {
            if (Arr::accessible($array) && Arr::exists($array, $segment)) {
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
