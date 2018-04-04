<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 04-04-18
 * Time: 13:17
 */

namespace edwrodrig\image\exception;


use Exception;

class InvalidSizeException extends Exception
{

    /**
     * InvalidSizeException constructor.
     * @param int $width
     * @param int $height
     */
    public function __construct($width, $height)
    {
        parent::__construct("[$width][$height]");
    }
}