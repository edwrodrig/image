<?php
declare(strict_types=1);

use edwrodrig\image\Compare;
use edwrodrig\image\exception\CompareCommandException;
use edwrodrig\image\exception\ConvertingSvgException;
use edwrodrig\image\exception\InvalidImageException;
use edwrodrig\image\exception\InvalidSizeException;
use edwrodrig\image\exception\WrongFormatException;
use PHPUnit\Framework\TestCase;

class CompareTest extends TestCase
{

    public function testDoesExecutableNotExist() {
        $converter = new Compare;
        $converter->setExecutable('unexistant_executable');
        $this->assertFalse($converter->doesExecutableExists());
    }

    public function testDoesExecutableExist() {
        $converter = new Compare;
        $this->assertTrue($converter->doesExecutableExists());
    }

    /**
     * @throws ImagickException
     * @throws CompareCommandException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws InvalidSizeException
     * @throws WrongFormatException
     */
    public function testCompareSameFile() {
        $this->assertEquals(
            0,
            Compare::compare(
                __DIR__ . '/files/original/goku.jpg',
                __DIR__ . '/files/original/goku.jpg'
            )
        );
    }

    public function assertSimilar(float $expected, float $actual) {
        $this->assertGreaterThanOrEqual($expected - 0.005, $actual);
        $this->assertLessThanOrEqual($expected + 0.005, $actual);
    }

    /**
     * @throws ImagickException
     * @throws CompareCommandException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws InvalidSizeException
     * @throws WrongFormatException
     */
    public function testCompareDifferent()
    {

        $group_1 = [
            __DIR__ . '/files/expected/ssj_contain_90_132.png',
            __DIR__ . '/files/original/ssj.png'
        ];

        $group_2 = [
            __DIR__ . '/files/expected/goku_500_200.jpg',
            __DIR__ . '/files/original/goku.jpg'
        ];


        $compare_inner_1 = Compare::compare($group_1[0], $group_1[1]);
        $compare_inner_2 = Compare::compare($group_2[0], $group_2[1]);


        $this->assertSimilar(0.03794, $compare_inner_1);
        $this->assertSimilar(0.0580718, $compare_inner_2);

        foreach ($group_1 as $file_1) {
            foreach ($group_2 as $file_2) {
                $value = Compare::compare($file_1, $file_2);
                $this->assertGreaterThan($compare_inner_1, $value);
                $this->assertGreaterThan($compare_inner_2, $value);
            }
        }
    }

    /**
     * @throws CompareCommandException
     */
    public function testCompareDissimilar()
    {
        $compare = new Compare;

        $this->assertEquals(0.866025,
            $compare->runCompareCommand(
                __DIR__ . '/files/original/dissimilar_1.png',
                __DIR__ . '/files/original/dissimilar_2.png'
            )
        );
    }


    /**
     * @throws CompareCommandException
     */
    public function testExecutableNotExistant() {
        $this->expectException(CompareCommandException::class);
        $this->expectExceptionMessage("sh: 1: not_existant: not found");

        $compare = new Compare;
        $compare->setExecutable('not_existant');
        $compare->runCompareCommand('/dev/null', '/dev/null');
    }

    /**
     * @throws CompareCommandException
     */
    public function testFileNotExistant() {
        $this->expectException(CompareCommandException::class);
        $this->expectExceptionMessage("unable to open image");

        $compare = new Compare;
        $compare->runCompareCommand('unexistant_image_1', 'unexistant_image_2');
    }

    public function testParseOutputNice() {
        $this->assertEquals(0.866025, Compare::parseOutput("56755 (0.866025)"));
    }

    public function testParseOutputInvalid() {
        $this->assertNull(Compare::parseOutput("ddghsdthy"));
    }

}
