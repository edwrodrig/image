<?php
declare(strict_types=1);

namespace edwrodrig\image\util;

/**
 * Class CommandReturn
 * A class to contains the command results. It stores the exit code, the standard output and standard error
 * @package edwrodrig\image\util
 */
class CommandReturn
{
    /**
     * @var int
     */
    private $exit_code;
    /**
     * @var string
     */
    private $std_out;
    /**
     * @var string
     */
    private $std_err;
    /**
     * CommandReturn constructor.
     * @param int $exit_code the exit code
     * @param string $std_out the standard output
     * @param string $std_err the standard error
     */
    public function __construct(int $exit_code, string $std_out, string $std_err) {
        $this->exit_code = $exit_code;
        $this->std_out = $std_out;
        $this->std_err = $std_err;
    }
    /**
     * Thee exit code
     * @return int
     */
    public function getExitCode(): int
    {
        return $this->exit_code;
    }
    /**
     * The standard output
     * @return string
     */
    public function getStdOut(): string
    {
        return $this->std_out;
    }
    /**
     * The standard error
     * @return string
     */
    public function getStdErr(): string
    {
        return $this->std_err;
    }
    /**
     * The standard error if not empty, else the standard output
     * @return string
     */
    public function getStdErrOrOut() : string {
        return empty($this->std_err) ? $this->std_out : $this->std_err;
    }
}
