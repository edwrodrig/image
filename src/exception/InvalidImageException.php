<?php
declare(strict_types=1);

namespace edwrodrig\image\exception;


use Exception;

/**
 * Class InvalidImageException
 * @package edwrodrig\image\exception
 * @api
 */
class InvalidImageException extends Exception
{
    /**
     * InvalidImageException constructor.
     * @param string $image
     * @internal
     */
    public function __construct(string $image) {
        parent::__construct($image);
    }
}