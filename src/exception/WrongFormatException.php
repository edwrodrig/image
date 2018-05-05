<?php
declare(strict_types=1);

namespace edwrodrig\image\exception;


use Exception;

/**
 * Class WrongFormatException
 * @package edwrodrig\image\exception
 * @api
 */
class WrongFormatException extends Exception
{
    /**
     * WrongFormatException constructor.
     * @param string $output
     * @internal
     */
    public function __construct(string $output) {
        parent::__construct($output);
    }
}