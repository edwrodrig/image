<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 04-05-18
 * Time: 16:28
 */

use edwrodrig\image\SvgConverter;
use PHPUnit\Framework\TestCase;

class SvgConverterTest extends TestCase
{

    public function testDoesExecutableNotExist() {
        $converter = new SvgConverter;
        $converter->setExecutable('unexistant_executable');
        $this->assertFalse($converter->doesExecutableExists());
    }

    public function testConvert() {
        $converter = new SvgConverter;
        $output_file = $converter->convert(__DIR__ . '/files/original/dbz.svg');
        $this->assertEquals(
            'image/png',
            mime_content_type($output_file)
        );
    }
}
