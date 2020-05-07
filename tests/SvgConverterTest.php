<?php
declare(strict_types=1);

use edwrodrig\image\exception\ConvertingSvgException;
use edwrodrig\image\exception\InvalidImageException;
use edwrodrig\image\SvgConverter;
use PHPUnit\Framework\TestCase;

class SvgConverterTest extends TestCase
{

    /**
     *
     */
    public function testDoesExecutableNotExist() {
        $converter = new SvgConverter;
        $converter->setExecutable('unexistant_executable');
        $this->assertFalse($converter->doesExecutableExists());
    }

    /**
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     */
    public function testConvert() {
        $converter = new SvgConverter;
        $output_file = $converter->convert(__DIR__ . '/files/original/dbz.svg');
        $this->assertEquals(
            'image/png',
            mime_content_type($output_file)
        );
    }
}
