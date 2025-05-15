<?php
/**
 * @author M.Kulyk
 * @copyright 2021 M.Kulyk
 * @license MIT
 * @link https://github.com/technoquill/dto-core
 * @version 1.1.0
 * @package Technoquill\DTO
 * @since 1.0.0
 */

namespace Technoquill\DTO\Traits;


use Symfony\Component\VarDumper\VarDumper;
use Technoquill\DTO\AbstractDTO;


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
     * @return AbstractDTO|DebuggableTrait
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