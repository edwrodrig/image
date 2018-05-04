<?php

use edwrodrig\image\Compare;
use PHPUnit\Framework\TestCase;

class CompareTest extends TestCase
{

    public function testCompareSameFile() {
        $this->assertEquals(
            0,
            Compare::compare(
                __DIR__ . '/files/original/goku.jpg',
                __DIR__ . '/files/original/goku.jpg'
            )
        );
    }

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

}
