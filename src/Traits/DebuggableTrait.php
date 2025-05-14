<?php
/**
 * @author M.Kulyk
 * @copyright 2021 M.Kulyk
 * @license MIT
 * @link https://github.com/technoquill/dto-core
 * @version 1.0.0
 * @package Technoquill\DTO
 * @since 1.0.0
 */

namespace Technoquill\DTO\Traits;


use Symfony\Component\VarDumper\VarDumper;


/**
 * Utility trait that provides debugging capabilities for classes by implementing magic debug information
 * and a debug method to dump the object state.
 * @internal
 * @property-read static[] $errors Runtime validation errors grouped by class
 * @property-read static[] $strict Strict mode flags grouped by class
 * @property-read static[] $dtoProperties Data to compare differences between DTO properties and passed properties
 *
 *  Note: The class using this trait must declare:
 *    a protected static array $errors = [];
 *    protected static array $strict = [];
 */
trait DebuggableTrait
{

    /**
     * @return array
     */
    public function __debugInfo()
    {

        return [
            'dto' => static::class,
            'properties_available' => $this->propertiesAvailable(),
            'properties_passed' => $this->propertiesPassed(),
            'properties_diff' => $this->propertiesDiff(),
            'mode' => $this->mode(),
            'current_errors' => $this->currentErrors(),
            'has_level_errors' => $this->hasLevelErrors(),
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


    /**
     * Determines the mode based on the class's strictness configuration.
     *
     * @return string The mode, either 'strict' or 'lenient'.
     */
    private function mode(): string
    {
        if (!isset(static::$strict[static::class])) {
            return 'strict';
        }
        return static::$strict[static::class] ? 'strict' : 'lenient';

    }

    /**
     * @return array
     */
    private function propertiesAvailable(): array
    {
        return get_object_vars($this);
    }


    /**
     * @return array
     */
    private function propertiesPassed(): array
    {
        if (!isset(static::$dtoProperties[static::class])) {
            return get_object_vars($this);
        }
        return static::$dtoProperties[static::class] ?? [];
    }


    /**
     * Computes the difference between the defined properties of the current DTO class
     * and the properties currently set on the object.
     *
     * @return array Returns an array of keys representing the properties that are defined in the DTO class
     *               but are not currently set on the object.
     */
    private function propertiesDiff(): array
    {
        if (!isset(self::$dtoProperties[static::class])) {
            return [];
        }
        return array_keys(array_diff_key(self::$dtoProperties[static::class], get_object_vars($this)));

    }

    /**
     * @return array
     */
    private function currentErrors(): array
    {
        return static::$errors[static::class] ?? [];
    }

    /**
     * Calculates the total number of errors across all levels by summing up the count of errors in each level.
     *
     * @return float|int The total count of errors.
     */
    private function hasLevelErrors(): float|int
    {
        return array_sum(array_map('count', static::$errors));
    }

}