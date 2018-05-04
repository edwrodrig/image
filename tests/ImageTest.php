<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 03-05-18
 * Time: 22:14
 */

use edwrodrig\image\Image;
use edwrodrig\image\Size;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    /**
     * We need to compare ImageSignature
     * because file data are not equals between different generations.
     * Maybe some time related info is saved in png file metadata
     * @param string $expected_image
     * @param Imagick $image
     * @throws ImagickException
     */
    public function assertImageEquals(string $expected_image, string $actual_image) {
        $expected_image = new Imagick($expected_image);
        $actual_image = new Imagick($actual_image);
        $this->assertEquals(
            $expected_image->getImageSignature(),
            $actual_image->getImageSignature()
        );
    }

    public function testCreateSuperThumbnail() {
        $image = Image::createFromFile(__DIR__ . '/files/original/goku.jpg');
        $image->makeSuperThumbnail(100, 100);
        $image->writeImage('/tmp/out.jpg');
        $this->assertImageEquals(
            __DIR__ . '/files/expected/goku_thumb.jpg',
            '/tmp/out.jpg'
        );
    }

    public function testOptimizePhoto() {
        $image = Image::createFromFile(__DIR__ . '/files/original/goku.jpg');
        $image->scaleImage(500, 200);
        $image->optimizePhoto();
        $image->writeImage('/tmp/out.jpg');
        $this->assertImageEquals(
            __DIR__ . '/files/expected/goku_500_200.jpg',
            '/tmp/out.jpg'
        );
    }

    public function testSuperThumbnail2() {
        $image = Image::createFromFile(__DIR__ . '/files/original/ssj.png');
        $image->makeSuperThumbnail(100, 100);
        $image->writeImage('/tmp/out.jpg');
        $this->assertImageEquals(
            __DIR__ . '/files/expected/ssj_thumb.jpg',
            '/tmp/out.jpg'
        );
    }

    /**
     * We need to compare ImageSignature
     * because file data are not equals between different generations.
     * Maybe some time related info is saved in png file metadata
     * @throws ImagickException
     * @throws \edwrodrig\image\exception\ConvertingSvgException
     * @throws \edwrodrig\image\exception\WrongFormatException
     */
    public function testOptimizeLossless() {
        $image = Image::createFromFile(__DIR__ . '/files/original/ssj.png');
        $image->scaleImage(500, 200);
        $image->optimizeLossless();
        $image->writeImage('/tmp/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/ssj_500_200.png',
            '/tmp/out.png'
        );
    }

    public function testThumbnailSvg() {
        $image = Image::createFromFile(__DIR__ . '/files/original/dbz.svg', 1000);
        $image->makeSuperThumbnail(100, 100);
        $image->writeImage('/tmp/out.jpg');
        $this->assertImageEquals(
            __DIR__ . '/files/expected/dbz_thumb.jpg',
            '/tmp/out.jpg'
        );
    }

    public function testOptimizeLosslessFromSvg() {
        $image = Image::createFromFile(__DIR__ . '/files/original/dbz.svg', 1000);
        $image->scaleImage(500, 500);
        $image->optimizeLossLess();
        $image->writeImage('/tmp/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/dbz_500_500.png',
            '/tmp/out.png'
        );
    }

    public function testOptimizeLosslessFromSvg2() {
        $image = Image::createFromFile(__DIR__ . '/files/original/dbz.svg', 2000);
        $image->scaleImage(1500, 1500);
        $image->optimizeLossLess();
        $image->writeImage('/tmp/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/dbz_1500_1500.png',
            '/tmp/out.png'
        );
    }

    public function testOptimizeLosslessFromSvg3() {
        $image = Image::createFromFile(__DIR__ . '/files/original/browser.svg', 500);
        $image->scaleImage(500, 500);
        $image->optimizeLossLess();
        $image->writeImage('/tmp/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/browser_500_500.png',
            '/tmp/out.png'
        );
    }

    public function testOptimizeLosslessCover() {
        $image = Image::createFromFile(__DIR__ . '/files/original/ssj.png');
        $image->cover(new Size(200, 200));
        $image->optimizeLossless();
        $image->writeImage('/tmp/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/ssj_cover_200_200.png',
            '/tmp/out.png'
        );
    }

    public function testOptimizeLosslessCover2() {
        $image = Image::createFromFile(__DIR__ . '/files/original/ssj.png');
        $image->cover(new Size(90, 132));
        $image->optimizeLossless();
        $image->writeImage('/tmp/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/ssj_cover_90_132.png',
            '/tmp/out.png'
        );
    }

    public function testOptimizeLosslessContain() {
        $image = Image::createFromFile(__DIR__ . '/files/original/ssj.png');
        $image->contain(new Size(200, 200));
        $image->optimizeLossless();
        $image->writeImage('/tmp/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/ssj_contain_200_200.png',
            '/tmp/out.png'
        );
    }

    public function testOptimizeLosslessContain2() {
        $image = Image::createFromFile(__DIR__ . '/files/original/ssj.png');
        $image->contain(new Size(90, 132));
        $image->optimizeLossless();
        $image->writeImage('/tmp/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/ssj_contain_90_132.png',
            '/tmp/out.png'
        );
    }

    public function testOptimizeLosslessContainBackground() {
        $image = Image::createFromFile(__DIR__ . '/files/original/favicon.png');
        $image->contain(new Size(20, 20), 'red');
        $image->optimizeLossless();
        $image->writeImage('/tmp/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/favicon_contain_background_20_20.png',
            '/tmp/out.png'
        );
    }

    public function testOptimizeLosslessContainBackground2() {
        $image = Image::createFromFile(__DIR__ . '/files/original/favicon.png');
        $image->contain(new Size(90, 132), 'red');
        $image->optimizeLossless();
        $image->writeImage('/tmp/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/favicon_contain_background_90_132.png',
            '/tmp/out.png'
        );
    }

    public function testOptimizeLosslessContainFromSvg() {
        $image = Image::createFromFile(__DIR__ . '/files/original/eroulette.svg', 1000);
        $image->contain(new Size(152, 152));
        $image->optimizeLossLess();
        $image->writeImage('/tmp/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/eroulette_contain_152_152.png',
            '/tmp/out.png'
        );
    }

    public function testOptimizeLosslessContainFromSvg2() {
        $image = Image::createFromFile(__DIR__ . '/files/original/eroulette.svg', 1000);
        $image->contain(new Size(30, 50));
        $image->optimizeLossLess();
        $image->writeImage('/tmp/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/eroulette_contain_30_50.png',
            '/tmp/out.png'
        );
    }

    public function testOptimizeLosslessContainFromSvg3() {
        $image = Image::createFromFile(__DIR__ . '/files/original/eroulette.svg', 1000);
        $image->contain(new Size(50, 30), ' blue');
        $image->optimizeLossLess();
        $image->writeImage('/tmp/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/eroulette_contain_50_30.png',
            '/tmp/out.png'
        );
    }

    public function testColorOverlay() {
        $image = Image::createFromFile(__DIR__ . '/files/original/amanda.svg', 1000);
        $image->colorOverlay('red');
        $image->contain(new Size(152, 152), ' transparent');
        $image->optimizeLossLess();
        $image->writeImage('/tmp/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/amanda_152_152.png',
            '/tmp/out.png'
        );
    }

    public function testColorOverlay2() {
        $image = Image::createFromFile(__DIR__ . '/files/original/amanda.svg', 1000);
        $image->colorOverlay('green');
        $image->contain(new Size(16, 16), ' transparent');
        $image->optimizeLossLess();
        $image->writeImage('/tmp/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/amanda_16_16.png',
            '/tmp/out.png'
        );
    }

    public function testColorOverlay3() {
        $image = Image::createFromFile(__DIR__ . '/files/original/amanda.svg', 1000);
        $image->colorOverlay('blue');
        $image->contain(new Size(30, 50), ' yellow');
        $image->optimizeLossLess();
        $image->writeImage('/tmp/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/amanda_30_50.png',
            '/tmp/out.png'
        );
    }

/*

$img = Image::optimize(__DIR__ . '/sources/amanda.svg');
$img = Image::color_overlay($img, 'red');
$img = Image::contain($img, 16, 16, 'transparent');
//$img = Image::contain($img, 900, 1328);
$img->writeImage(__DIR__ . '/amanda.png');

$img = Image::optimize(__DIR__ . '/sources/amanda.svg');
$img = Image::color_overlay($img, 'red');
$img = Image::contain($img, 152, 152, 'transparent');
//$img = Image::contain($img, 900, 1328);
$img->writeImage(__DIR__ . '/amanda.png');
*/
}
