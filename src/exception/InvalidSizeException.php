<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 04-04-18
 * Time: 13:17
 */

namespace edwrodrig\image\exception;


use edwrodrig\image\Size;
use Exception;

class InvalidSizeException extends Exception
{

    /**
     * InvalidSizeException constructor.
     * @param Size $size
     */
    public function __construct(Size $size)
    {
        parent::__construct(sprintf("[%s][%s]", $size->getWidth(), $size->getHeight()));
    }
}