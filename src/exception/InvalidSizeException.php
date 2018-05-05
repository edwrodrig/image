<?php
declare(strict_types=1);

namespace edwrodrig\image\exception;


use edwrodrig\image\Size;
use Exception;

/**
 * Class InvalidSizeException
 * @package edwrodrig\image\exception
 * @api
 */
class InvalidSizeException extends Exception
{

    /**
     * InvalidSizeException constructor.
     * @param Size $size
     * @internal
     */
    public function __construct(Size $size)
    {
        parent::__construct(sprintf("[%s][%s]", $size->getWidth(), $size->getHeight()));
    }
}