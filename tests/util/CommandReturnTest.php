<?php
declare(strict_types=1);

namespace util;

use edwrodrig\image\util\CommandReturn;
use PHPUnit\Framework\TestCase;

class CommandReturnTest extends TestCase
{

    public function testGetStdOut()
    {
        $command = new CommandReturn(0,"out", "err");
        $this->assertEquals("out", $command->getStdOut());
    }

    public function testGetStdErrOrOutValidStdErr()
    {
        $command = new CommandReturn(0,"out", "err");
        $this->assertEquals("err", $command->getStdErrOrOut());
    }

    public function testGetStdErrOrOutInvalidStdErr()
    {
        $command = new CommandReturn(0,"out", "");
        $this->assertEquals("out", $command->getStdErrOrOut());
    }

    public function testGetExitCode()
    {
        $command = new CommandReturn(0,"out", "err");
        $this->assertEquals(0, $command->getExitCode());
    }

    public function testGetStdErr()
    {
        $command = new CommandReturn(0,"out", "err");
        $this->assertEquals("err", $command->getStdErr());
    }
}
