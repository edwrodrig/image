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

    private string $mimeType;

    /**
     * WrongFormatException constructor.
     * @param string $output
     * @param string $mimeType
     * @internal
     */
    public function __construct(string $output, string $mimeType) {
        parent::__construct($output);
        $this->mimeType = $mimeType;
    }

    public function getMimeType() : string {
        return $this->mimeType;
    }
}