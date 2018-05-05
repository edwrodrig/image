<?php
declare(strict_types=1);

namespace edwrodrig\image\exception;


use Exception;

/**
 * Class ConvertingSvgException
 * @package edwrodrig\image\exception
 * @api
 */
class ConvertingSvgException extends Exception
{
    /**
     * ConvertingSvgException constructor.
     * @param string $output
     * @internal
     */
    public function __construct(string $output) {
        parent::__construct($output);
    }
}