<?php
declare(strict_types=1);
/**
 * @author M.Kulyk
 * @copyright 2025 M.Kulyk
 * @license MIT
 * @link https://github.com/technoquill/dto-core
 * @version 1.0.2
 * @package Technoquill\DTO
 * @since 1.0.0
 */

namespace Technoquill\DTO\Traits;

use Closure;
use Exception;
use LogicException;
use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionUnionType;
use RuntimeException;
use Technoquill\DTO\Contracts\DTOInterface;
use Technoquill\DTO\Support\LoggerContext;


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
 */
trait DTOTrait
{

    /**
     * Validates the provided arguments against the class properties.
     * @param array $data
     * @param bool $strict
     * @return array
     * @throws ReflectionException
     */
    private static function collect(array $data, bool $strict): array
    {
        $class = static::class;

        $data = self::normalizeDataValue($data);
        if (!$strict) {
            LoggerContext::enable($class);
            // Reset the logger context for the current class.
            LoggerContext::reset($class);
            // Set the strict mode flag.
            LoggerContext::set($class, 'strict', false);
            // Fill data to compare differences between DTO properties and passed properties
            LoggerContext::set($class, 'properties', $data);
        } else {
            LoggerContext::reset($class);
        }

        foreach ($data as $key => $value) {
            if (!property_exists($class, $key) && !$strict) {
                unset($data[$key]);
                LoggerContext::set($class, 'errors', ["Property {$class}::\${$key} doesn't exist!"]);
            }

            // Comparing $data types with DTO properties types
            if (property_exists($class, $key)) {
                $reflection = new ReflectionClass($class);
                $propertyRef = $reflection->getProperty($key);
                $propertyType = $propertyRef->getType();

                $expectedType = '';
                $isTypeMismatch = false;

                if ($propertyType instanceof ReflectionNamedType) {
                    $expectedType = $propertyType->getName();
                    $isTypeMismatch = static::normalizeType($expectedType) !== gettype($value);
                }

                if ($propertyType instanceof ReflectionUnionType) {
                    $types = array_map(
                        static fn($type) => $type->getName(),
                        $propertyType->getTypes()
                    );

                    $expectedType = implode('|', $types);
                    $isTypeMismatch = !in_array(gettype($value), array_map([static::class, 'normalizeType'], $types), true);
                }

                if (!$value instanceof DTOInterface && $isTypeMismatch && !$strict) {
                    LoggerContext::set($class, 'errors', [
                        "Property {$class}::\${$key} must be {$expectedType}, but " . gettype($value) . " given!"
                    ]);
                }
            }

        }
        return $data;
    }


    /**
     * Normalizes the provided arguments by evaluating closures and
     * converting them to their return values.
     *
     * @param array $arguments
     * @return array
     *
     */
    private static function normalizeDataValue(array $arguments): array
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

    /**
     * @param string $type
     * @return string
     */
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
        $reflection = new ReflectionClass(static::class);
        $hasConstructor = $reflection->getConstructor()?->getParameters() ?? [];
        $props = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);

        if (!empty($hasConstructor) && count($props) > count($hasConstructor)) {
            throw new LogicException("Mixing constructor-based and property-based DTO is not allowed.");
        }
    }


    /**
     * @return bool
     * @deprecated
     */
    public function isValid(): bool
    {
        trigger_error(
            'Method isValid() is deprecated. Use debug() or getErrors() instead.', E_USER_DEPRECATED
        );
        return empty(LoggerContext::getAllErrors());
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return array_merge(LoggerContext::getAllErrors(), $this->loggerContextWarning());
    }

    /**
     * @return array|string[]
     * @since 1.0.2
     */
    private function loggerContextWarning(): array
    {
        return !LoggerContext::isEnabled(static::class)
            ? ['warnings' => 'DTO LoggerContext is disabled! LoggerContext works only in lenient mode.']
            : [];
    }


    /**
     * @return array
     */
    public
    function __debugInfo(): array
    {
        return [
            'dto' => static::class,
            'properties' => [
                'available' => array_keys(get_object_vars($this)),
                'passed' => LoggerContext::isset(static::class, 'properties')
                    ? array_keys(LoggerContext::getProperties(static::class))
                    : array_keys(get_object_vars($this)),
                'difference' => LoggerContext::isset(static::class, 'properties')
                    ? array_diff(array_keys(LoggerContext::getProperties(static::class)), array_keys(get_object_vars($this)))
                    : [],
            ],
            'strict' => LoggerContext::getStrict(static::class),
            'enabled_logger_context' => LoggerContext::isEnabled(static::class),
            'errors' => LoggerContext::getErrors(static::class),
        ];
    }


}