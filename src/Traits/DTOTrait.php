<?php
/**
 * @author M.Kulyk
 * @copyright 2025 M.Kulyk
 * @license MIT
 * @link https://github.com/technoquill/dto-core
 * @version 1.0.0
 * @package Technoquill\DTO
 * @since 1.0.0
 */

namespace Technoquill\DTO\Traits;

use Closure;
use Exception;
use LogicException;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use RuntimeException;
use Technoquill\DTO\AbstractDTO;
use Technoquill\DTO\Contracts\DTOInterface;


/**
 * Constructor method to initialize the DTO properties based on the provided arguments.
 * Validates the provided arguments against the class properties. Enforces strict validation
 * if enabled, ensuring only declared properties are initialized.
 *
 * @throws RuntimeException If a property in the arguments does not exist when strict is enabled.
 * @throws RuntimeException If the constructor is called with an empty arguments array.
 * @throws Exception If any other exception occurs during the property initialization process.
 * @throws ReflectionException If reflection-related errors occur.
 *
 * @internal
 *
 * @property static[] $errors Runtime validation errors grouped by class
 * @property static[] $strict Strict mode flags grouped by class
 * @property-read static[] $dtoProperties Data to compare differences between DTO properties and passed properties
 *
 *  Note: The class using this trait must declare:
 *    a protected static array $errors = [];
 *    protected static array $strict = [];
 *
 */
trait DTOTrait
{


    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return empty(static::$errors ?? []);
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        $result = [];
        if (!empty(static::$errors)) {
            foreach (static::$errors as $class => $errors) {
                $result[$class] = $errors;
            }
        }
        return $result;
    }


    /**
     * Validates the provided arguments against the class properties.
     * @param array $arguments
     * @param bool $strict
     * @return array
     * @throws ReflectionException
     */
    private static function propertiesValidate(array $arguments, bool $strict): array
    {
        $class = static::class;
        $reflectionClass = new ReflectionClass($class);
        $arguments = self::normalizeArguments($arguments);

        foreach ($arguments as $key => $value) {

            // Fill data to compare differences between DTO properties and passed properties (only when $this->debug() is enabled)
            if (static::isDebugEnabled()) {
                static::$dtoProperties[$class][$key] = $value;
            }

            if (!property_exists($class, $key)) {
                if (!$strict) {
                    unset($arguments[$key]);
                }
                self::$errors[static::class][] = "Property {$class}::\${$key} doesn't exist!";
            }
            if (property_exists($class, $key)) {
                $propertyType = $reflectionClass->getProperty($key)?->getType()?->getName();
                if (!$value instanceof DTOInterface && static::normalizeType($propertyType) !== gettype($value)) {
                    static::$errors[static::class][] = "Property {$class}::\${$key} must be " . $propertyType . " but " . gettype($value) . " given!";
                }
            }
        }
        if (!empty(static::$errors) && !empty(static::$errors[static::class]) && $strict) {
            throw new RuntimeException(implode("\n", static::$errors[static::class]));
        }

        return $arguments;
    }


    /**
     * Enables or disables strict mode for the DTO.
     *
     * @param bool $strict
     * @return DTOTrait|AbstractDTO
     */
    public function strictMode(bool $strict = true): self
    {
        if (!isset(static::$strict[static::class])) {
            static::$strict[static::class] = $strict;
        }
        return $this;
    }


    /**
     * Normalizes the provided arguments by evaluating closures and
     * converting them to their return values.
     *
     * @param array $arguments
     * @return array
     */
    private static function normalizeArguments(array $arguments): array
    {
        $normalized = [];
        foreach ($arguments as $key => $value) {
            if ($value instanceof Closure) {
                $normalized[$key] = $value();
            } else {
                $normalized[$key] = $value;
            }
        }
        return $normalized;
    }

    private static function normalizeType(string $type): string
    {
        return match ($type) {
            'int' => 'integer',
            'bool' => 'boolean',
            'float' => 'double',
            default => $type,
        };
    }


    /**
     * Ensures that a Data Transfer Object (DTO) does not mix constructor-based
     * and property-based approaches for defining its structure.
     *
     * @return void
     * @throws LogicException If both constructor parameters and public properties
     *         are defined, indicating a mixed structure.
     */
    private static function assertNoMixedStructure(): void
    {
        $ref = new ReflectionClass(static::class);
        $hasConstructor = $ref->getConstructor()?->getParameters() ?? [];
        $props = $ref->getProperties(ReflectionProperty::IS_PUBLIC);

        if (!empty($hasConstructor) && count($props) > count($hasConstructor)) {
            throw new LogicException("Mixing constructor-based and property-based DTO is not allowed.");
        }
    }

}