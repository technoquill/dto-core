<?php
declare(strict_types=1);
/**
 * @author M.Kulyk
 * @copyright 2021 M.Kulyk
 * @license MIT
 * @link https://github.com/technoquill/dto-core
 * @version 1.0.0
 * @package Technoquill\DTO
 * @since 1.1.0
 */


namespace Technoquill\DTO\Support;

/**
 * A final class LoggerContext that provides a static context management utility.
 */
final class LoggerContext
{

    private static array $loggerEnabled = [];

    /**
     * @var array
     */
    private static array $context = [];


    /**
     * Resets the context for the specified class.
     *
     * @param string $class The class name whose context should be reset.
     * @return void
     */
    public static function reset(string $class): void
    {
        self::$context[$class] = [
            'strict' => false,
            'errors' => [],
            'properties' => [],
        ];
    }

    /**
     * @param string $class
     * @return void
     */
    public static function enable(string $class): void
    {
        self::$loggerEnabled[$class] = true;
    }

    /**
     * @param string $class
     * @return void
     */
    public static function disable(string $class): void
    {
        self::$loggerEnabled[$class] = false;
    }

    /**
     * @param string $class
     * @return bool
     */
    public static function isEnabled(string $class): bool
    {
        return self::$loggerEnabled[$class] ?? false;
    }


    /**
     * Sets a value in the context for the specified class and key.
     *
     * @param string $class The class name whose context should be updated.
     * @param string $key The key within the class context to set the value for.
     * @param mixed $value The value to be set for the specified key in the class context.
     * @return void
     */
    public static function set(string $class, string $key, mixed $value): void
    {
        self::$context[$class][$key] = $value;
    }

    /**
     * Retrieves a value from the context array based on the given class and key.
     *
     * @param string $class The class name used as the first-level key in the context array.
     * @param string $key The key used as the second-level key in the context array.
     * @return mixed Returns the value if found in the context array, or null if not found.
     */
    public static function get(string $class, string $key): mixed
    {
        return self::$context[$class][$key] ?? null;
    }


    /**
     * @param string $class
     * @param string $key
     * @return bool
     */
    public static function isset(string $class, string $key): bool
    {
        return isset(self::$context[$class][$key]);
    }


    /**
     * @param string $class
     * @return array
     */
    public static function getErrors(string $class): array
    {
        return self::$context[$class]['errors'] ?? [];
    }

    /**
     * @return array
     */
    public static function getAllErrors(): array
    {
        return array_map(static function ($value) {
            return $value['errors'] ?? [];
        }, self::$context);
    }

    public static function getAllErrorsCount(): int
    {
        return count(self::getAllErrors());
    }

    /**
     * @param string $class
     * @return bool
     */
    public static function getStrict(string $class): bool
    {
        return self::$context[$class]['strict'] ?? false;
    }

    /**
     * @param string $class
     * @return array
     */
    public static function getProperties(string $class): array
    {
        return self::$context[$class]['properties'] ?? [];
    }

    /**
     * Retrieves all elements from the context.
     *
     * @return array An array containing all elements from the context.
     */
    public static function getAll(): array
    {
        return self::$context;
    }

}
