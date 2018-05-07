<?php

namespace test\edwrodrig\image;

use edwrodrig\image\Compare;
use PHPUnit\Framework\TestCase;

class CompareTest extends TestCase
{

    /**
     * @throws \ImagickException
     * @throws \edwrodrig\image\exception\CompareCommandException
     * @throws \edwrodrig\image\exception\ConvertingSvgException
     * @throws \edwrodrig\image\exception\InvalidImageException
     * @throws \edwrodrig\image\exception\InvalidSizeException
     * @throws \edwrodrig\image\exception\WrongFormatException
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

    /**
     * @throws \ImagickException
     * @throws \edwrodrig\image\exception\CompareCommandException
     * @throws \edwrodrig\image\exception\ConvertingSvgException
     * @throws \edwrodrig\image\exception\InvalidImageException
     * @throws \edwrodrig\image\exception\InvalidSizeException
     * @throws \edwrodrig\image\exception\WrongFormatException
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


        $this->assertEquals(0.03794, $compare_inner_1);
        $this->assertEquals(0.067887, $compare_inner_2);

        foreach ($group_1 as $file_1) {
            foreach ($group_2 as $file_2) {
                $value = Compare::compare($file_1, $file_2);
                $this->assertGreaterThan($compare_inner_1, $value);
                $this->assertGreaterThan($compare_inner_2, $value);
            }
        }
    }

    /**
     * @throws \edwrodrig\image\exception\CompareCommandException
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
     * @expectedException \edwrodrig\image\exception\CompareCommandException
     * @expectedExceptionMessage sh: 1: not_existant: not found
     */
    public function testExecutableNotExistant() {
        $compare = new Compare;
        $compare->setExecutable('not_existant');
        $compare->runCompareCommand('/dev/null', '/dev/null');
    }
    /**
     * @expectedException \edwrodrig\image\exception\CompareCommandException
     * @expectedExceptionMessage compare: unable to open image
     */
    public function testFileNotExistant() {
        $compare = new Compare;
        $compare->runCompareCommand('unexistant_image_1', 'unexistant_image_2');
    }

}
