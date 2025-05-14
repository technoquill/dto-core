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

namespace Technoquill\DTO\Contracts;

/**
 * Represents a Data Transfer Object interface that enforces a structure
 * for creating new instances with specific data initialization.
 */
interface DTOInterface
{
    /**
     * Creates and returns a new instance using the provided data.
     *
     * @param array $data The data to initialize the new instance with.
     * @param bool $strict Whether to throw an exception if the provided data is invalid.
     *
     * @return static Returns a new instance initialized with the provided data.
     */
    public static function make(array $data, bool $strict = true): static;


    /**
     * Converts the current object instance into an array representation.
     *
     * @return array Returns an array containing the data of the object.
     */
    public function toArray(): array;
}
