<?php
/**
 * @author M.Kulyk
 * @copyright 2021 M.Kulyk
 * @license MIT
 * @link https://github.com/technoquill/dto-core
 * @version 1.1.1
 * @package Technoquill\DTO
 * @since 1.0.0
 */

namespace Technoquill\DTO\Traits;


use Symfony\Component\VarDumper\VarDumper;


/**
 * Utility trait that provides debugging capabilities for classes by implementing magic debug information
 * and a debug method to dump the object state.
 */
trait DebuggableTrait
{

    /**
     * @var bool
     */
    protected static bool $debug = false;

    /**
     * @return array
     */
    public function __debugInfo()
    {
        return [
            'dto' => static::class,
            'properties_available' => array_keys(get_object_vars($this)),
            'properties_passed' => !isset(static::$dtoProperties[static::class])
                ? array_keys(get_object_vars($this))
                : (static::$dtoProperties[static::class] ?? []),
            'properties_diff' => isset(self::$dtoProperties[static::class])
                ? array_diff(self::$dtoProperties[static::class], array_keys(get_object_vars($this)))
                : [],
            'strict_mode' => static::$strict[static::class] ?? true,
            'current_errors' => static::$errors[static::class] ?? [],
            'has_level_errors' => array_sum(array_map('count', static::$errors)),
        ];

    }

    /**
     * @return self
     */
    public function debug(): self
    {
        static::$debug = true;
        VarDumper::dump($this);
        return $this;
    }

    public static function isDebugEnabled(): bool
    {
        return static::$debug;
    }

}