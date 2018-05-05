<?php
declare(strict_types=1);

namespace edwrodrig\image\exception;

use Exception;

/**
 * Class CompareCommandException
 * @package edwrodrig\image\exception
 * @api
 */
class CompareCommandException extends Exception
{
    /**
     * CompareCommandException constructor.
     * @param string $output
     * @internal
     */
    public function __construct(string $output) {
        parent::__construct($output);
    }
}