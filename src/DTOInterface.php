<?php

namespace Technoquill\DTO;


interface DTOInterface
{
    /**
     * Fills the instance with the provided arguments.
     *
     * @param mixed ...$arguments A variable-length list of arguments to be used for filling the instance.
     * @return static Returns the current instance after being filled with the provided arguments.
     */
    public static function fill(...$arguments): static;

    /**
     * Creates and returns a new instance using the provided data.
     *
     * @param array $data The data to initialize the new instance with.
     * @param bool $strict Determines whether a strict mode should be applied while processing the data. Defaults to true.
     * @return static Returns a new instance initialized with the provided data.
     */
    public static function make(array $data, bool $strict = true): static;
}
