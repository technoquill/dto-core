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

namespace Technoquill\DTO;


use JsonException;
use ReflectionClass;
use ReflectionException;
use Technoquill\DTO\Contracts\DTOInterface;
use Technoquill\DTO\Traits\DebuggableTrait;
use Technoquill\DTO\Traits\DTOTrait;

/**
 * Represents an abstract Data Transfer Object (DTO) class.
 * This class serves as a base implementation for DTOs,
 * ensuring standardized behavior and structure when exchanging data.
 *
 * Implements common functionality and traits for DTO handling.
 */
abstract class AbstractDTO implements DTOInterface
{

    /**
     * Holds the errors that occurred during the DTO initialization process.
     *
     * @var array
     */
    protected static array $errors = [];

    /**
     * Flag to enable strict validation of properties.
     *
     * @var array
     */
    protected static array $strict = [];


    /**
     * Properties passed through DTO
     *
     * @var array
     */
    protected static array $dtoProperties = [];



    use DTOTrait, DebuggableTrait;


    /**
     * Creates a new instance of the DTO class using the provided arguments.
     *
     * @param array $data
     * @param bool $strict
     * @return static
     * @throws ReflectionException
     */
    public static function make(array $data, bool $strict = true): static
    {
        // Set the strict mode flag.
        self::$strict[static::class] = $strict;

        // Ensure that the DTO does not mix constructor-based and property-based approaches for defining its structure.
        self::assertNoMixedStructure();

        // Validate the arguments and normalize them. If strict validation is enabled, throw an exception if any of the arguments are missing.
        $data = self::propertiesValidate($data, $strict);

        // If the class has a constructor, use it to initialize the properties.
        $reflectionClass = new ReflectionClass(static::class);
        if ($reflectionClass->getConstructor()?->getParameters()) {
            return $reflectionClass->newInstanceArgs($data);
        }

        // Otherwise, initialize the properties using the provided arguments.
        $class = new static();
        foreach ($data as $key => $value) {
            $class->$key = $value;
        }
        return $class;
    }


    /**
     * @throws JsonException
     */
    public function toArray(): array
    {
        return json_decode(
            json_encode($this, JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR
        );
    }

}
