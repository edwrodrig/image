<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 03-05-18
 * Time: 11:30
 */

namespace test\edwrodrig\image;

use edwrodrig\image\Size;
use PHPUnit\Framework\TestCase;

class SizeTest extends TestCase
{
    /**
     * @testWith    [[4,4], 2, [2, 2]]
     *              [[1,2], 3, [3, 6]]
     *              [[10, 6], 5, [5, 3]]
     * @param array $size
     * @param int $new_width
     * @param array $expected_size
     */
    public function testScaleByWidth(array $size, int $new_width, array $expected_size) {
        $size = new Size($size[0], $size[1]);
        $size = $size->getScaledByWidth($new_width);
        $this->assertEquals($expected_size[0], $size->getWidth(), "Incorrect Width");
        $this->assertEquals($expected_size[1], $size->getHeight(), "Incorrect Height");
    }

    /**
     * @testWith    [[4,4], 2, [2, 2]]
     *              [[2,1], 3, [6, 3]]
     *              [[6, 10], 5, [3, 5]]
     * @param array $size
     * @param int $new_height
     * @param array $expected_size
     */
    public function testScaleByHeight(array $size, int $new_height, array $expected_size) {
        $size = new Size($size[0], $size[1]);
        $size = $size->getScaledByHeight($new_height);
        $this->assertEquals($expected_size[0], $size->getWidth(), "Incorrect Width");
        $this->assertEquals($expected_size[1], $size->getHeight(), "Incorrect Height");
    }

    /**
     * The expected size must cover the cover area keeping the aspect ratio of original size
     * @testWith    [[4,4], [2, 2], [2, 2]]
     *              [[2,1], [1, 3], [6, 3]]
     *              [[2, 1], [1, 1], [2, 1]]
     *              [[2, 1], [2, 2], [4, 2]]
     *              [[6, 10], [1, 5], [3, 5]]
     * @param array $size Original size
     * @param array $cover_area The area to cover
     * @param array $expected_size the expected size
     */
    public function testScaleByCoverArea(array $size, array $cover_area, array $expected_size) {
        $size = new Size($size[0], $size[1]);
        $size = $size->getScaledByCoverArea(new Size($cover_area[0], $cover_area[1]));
        $this->assertEquals($expected_size[0], $size->getWidth(), "Incorrect Width");
        $this->assertEquals($expected_size[1], $size->getHeight(), "Incorrect Height");
    }

    /**
     * The expected size must be contained in container area keeping the aspect ratio
     * @testWith    [[4,4], [2, 2], [2, 2]]
     *              [[2,1], [10, 30], [10, 5]]
     *              [[2, 1], [10, 10], [10, 5]]
     *              [[2, 1], [20, 20], [20, 10]]
     *              [[5, 10], [10, 50], [10, 20]]
     * @param array $size Original size
     * @param array $contain_area The container area
     * @param array $expected_size The expected size
     */
    public function testScaleByContainArea(array $size, array $contain_area, array $expected_size) {
        $size = new Size($size[0], $size[1]);
        $size = $size->getScaledByContainArea(new Size($contain_area[0], $contain_area[1]));
        $this->assertEquals($expected_size[0], $size->getWidth(), "Incorrect Width");
        $this->assertEquals($expected_size[1], $size->getHeight(), "Incorrect Height");
    }

    /**
     * @testWith    [[0, 1]]
     *              [[1, 0]]
     *              [[0, 0]]
     * @param array $size
     */
    public function testIsEmptyArea(array $size) {
        $size = new Size($size[0], $size[1]);
        $this->assertTrue($size->isAreaEmpty());
    }

    /**
     * @testWith    [[1, 1]]
     *              [[1, 2]]
     *              [[10, 20]]
     * @param array $size
     */
    public function testIsNotEmptyArea(array $size) {
        $size = new Size($size[0], $size[1]);
        $this->assertFalse($size->isAreaEmpty());
    }
}
