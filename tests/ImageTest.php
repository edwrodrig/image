<?php
declare(strict_types=1);

use edwrodrig\image\exception\ConvertingSvgException;
use edwrodrig\image\exception\InvalidImageException;
use edwrodrig\image\exception\InvalidSizeException;
use edwrodrig\image\exception\WrongFormatException;
use edwrodrig\image\Image;
use edwrodrig\image\Size;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    /**
     * @var vfsStreamDirectory
     */
    private vfsStreamDirectory $root;

    public function setUp() : void {
        $this->root = vfsStream::setup();
    }

    /**
     * We need to compare ImageSignature
     * because file data are not equals between different generations.
     * Maybe some time related info is saved in png file metadata
     * @param string $expected_image
     * @param string $actual_image
     * @throws ImagickException
     */
    public function assertImageEquals(string $expected_image, string $actual_image)
    {
        $expected_image = new Imagick($expected_image);
        $actual_image = new Imagick($actual_image);
        $result = $actual_image->compareImages($expected_image, Imagick::METRIC_MEANSQUAREERROR);
        $this->assertLessThanOrEqual(
            0.1,
            $result[1]
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testCreateSuperThumbnail()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/goku.jpg');
        $image->makeSuperThumbnail(100, 100);
        $image->writeImage($this->root->url() .'/out.jpg');
        $this->assertImageEquals(
            __DIR__ . '/files/expected/goku_thumb.jpg',
            $this->root->url() .'/out.jpg'
        );
    }

    /**
     * @throws ImagickException
     */
    public function testCreateSuperThumbnailFromBlob()
    {
        $blob = file_get_contents(__DIR__ . '/files/original/goku.jpg');
        $image = Image::createFromBlob($blob);
        $image->makeSuperThumbnail(100, 100);
        $image->writeImage($this->root->url() .'/out.jpg');
        $this->assertImageEquals(
            __DIR__ . '/files/expected/goku_thumb.jpg',
            $this->root->url() .'/out.jpg'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testCreateFromFileWrongImageFormat()
    {
        $this->expectException(WrongFormatException::class);
        try {
            Image::createFromFile(__FILE__);
        } catch ( WrongFormatException $e ) {
            $this->assertEquals("text/x-php", $e->getMimeType());
            throw $e;
        }

    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testOptimizePhoto()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/goku.jpg');
        $image->scaleImage(500, 200);
        $image->optimizePhoto();
        $image->writeImage($this->root->url() .'/out.jpg');
        $this->assertImageEquals(
            __DIR__ . '/files/expected/goku_500_200.jpg',
            $this->root->url() .'/out.jpg'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testOptimizePhotoAuto()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/goku.jpg');
        $image->scaleImage(500, 200);
        $image->optimize();
        $image->writeImage($this->root->url() .'/out.jpg');
        $this->assertImageEquals(
            __DIR__ . '/files/expected/goku_500_200.jpg',
            $this->root->url() .'/out.jpg'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testSuperThumbnail2()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/ssj.png');
        $image->makeSuperThumbnail(100, 100);
        $image->writeImage($this->root->url() .'/out.jpg');

        copy($this->root->url() .'/out.jpg', '/tmp/sdafasdf.jpg');
        $this->assertImageEquals(
            __DIR__ . '/files/expected/ssj_thumb.jpg',
            $this->root->url() .'/out.jpg'
        );

    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testOptimizeLossless()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/ssj.png');
        $image->scaleImage(500, 200);
        $image->optimizeLossless();
        $image->writeImage($this->root->url() .'/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/ssj_500_200.png',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testOptimizeLosslessAuto()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/ssj.png');
        $image->scaleImage(500, 200);
        $image->optimize();
        $image->writeImage($this->root->url() .'/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/ssj_500_200.png',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testThumbnailSvg()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/dbz.svg', 1000);
        $image->makeSuperThumbnail(100, 100);
        $image->writeImage($this->root->url() .'/out.jpg');
        $this->assertImageEquals(
            __DIR__ . '/files/expected/dbz_thumb.jpg',
            $this->root->url() .'/out.jpg'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testOptimizeLosslessFromSvg()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/dbz.svg', 1000);
        $image->scaleImage(500, 500);
        $image->optimizeLossLess();
        $image->writeImage($this->root->url() .'/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/dbz_500_500.png',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testOptimizeLosslessFromSvg2()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/dbz.svg', 2000);
        $image->scaleImage(1500, 1500);
        $image->optimizeLossLess();
        $image->writeImage($this->root->url() .'/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/dbz_1500_1500.png',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testOptimizeLosslessFromSvg3()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/browser.svg', 500);
        $image->scaleImage(500, 500);
        $image->optimizeLossLess();
        $image->writeImage($this->root->url() .'/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/browser_500_500.png',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testOptimizeLosslessCover()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/ssj.png');
        $image->cover(new Size(200, 200));
        $image->optimizeLossless();
        $image->writeImage($this->root->url() .'/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/ssj_cover_200_200.png',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testOptimizeLosslessCover2()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/ssj.png');
        $image->cover(new Size(90, 132));
        $image->optimizeLossless();
        $image->writeImage($this->root->url() .'/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/ssj_cover_90_132.png',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws InvalidSizeException
     * @throws WrongFormatException
     */
    public function testOptimizeLosslessContain()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/ssj.png');
        $image->contain(new Size(200, 200));
        $image->optimizeLossless();
        $image->writeImage($this->root->url() .'/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/ssj_contain_200_200.png',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws InvalidSizeException
     * @throws WrongFormatException
     */
    public function testOptimizeDocumentContain()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/mindprint.jpg');
        $image->contain(new Size(200, 200));
        $image->optimizeDocument();
        $image->writeImage($this->root->url() .'/out.jpg');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/mindprint_contain_200_200.jpg',
            $this->root->url() .'/out.jpg'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws InvalidSizeException
     * @throws WrongFormatException
     */
    public function testOptimizeDocumentContainResize()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/mindprint.jpg');
        $image->containResize(new Size(200, 200));
        $image->optimizeDocument();
        $image->writeImage($this->root->url() .'/out.jpg');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/mindprint_contain_resize_200_200.jpg',
            $this->root->url() .'/out.jpg'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws InvalidSizeException
     * @throws WrongFormatException
     */
    public function testOptimizeLosslessContain2()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/ssj.png');
        $image->contain(new Size(90, 132));
        $image->optimizeLossless();
        $image->writeImage($this->root->url() .'/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/ssj_contain_90_132.png',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testCropImage10x10_100x100()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/ssj.png');
        $image->cropImage(500, 500, 100, 100);
        $image->optimizePhoto();
        $image->writeImage($this->root->url() .'/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/ssj_crop_10_10_100_100.jpg',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testCropImage1x1_2x2_200dpi()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/ssj.png');
        $image->cropImage(1, 1, 2, 2, 300);
        $image->optimizePhoto();
        $image->writeImage($this->root->url() .'/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/ssj_crop_1_1_2_2_300.jpg',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testCover0x90()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/ssj.png');
        $image->cover(new Size(0, 90));
        $image->writeImage($this->root->url() .'/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/ssj_cover_0_90.png',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testCover90x0()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/ssj.png');
        $image->cover(new Size(90, 0));
        $image->writeImage($this->root->url() .'/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/ssj_cover_90_0.png',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testCover0x0()
    {
        $this->expectException(ImagickException::class);
        $this->expectExceptionMessage("Invalid image geometry");
        $image = Image::createFromFile(__DIR__ . '/files/original/ssj.png');
        $image->cover(new Size(0, 0));

    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testRotateClockwise()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/goku.jpg');
        $image->rotateClockwise();
        $image->writeImage($this->root->url() .'/out.jpg');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/goku_rotate_clockwise.jpg',
            $this->root->url() .'/out.jpg'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testRotateLossless()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/ssj.png');
        $image->getImagickImage()->setImageFormat('BMP');
        $expectedBlob = $image->getBlob();
        $image->rotateClockwise();
        $image->rotateClockwise();
        $image->rotateClockwise();
        $image->rotateClockwise();

        $rotatedBlob = $image->getBlob();

        $this->assertEquals($expectedBlob, $rotatedBlob);
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws InvalidSizeException
     * @throws WrongFormatException
     */
    public function testOptimizeLosslessContainBackground()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/favicon.png');
        $image->contain(new Size(20, 20), 'red');
        $image->optimizeLossless();
        $image->writeImage($this->root->url() .'/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/favicon_contain_background_20_20.png',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ConvertingSvgException
     * @throws ImagickException
     * @throws InvalidImageException
     * @throws InvalidSizeException
     * @throws WrongFormatException
     */
    public function testContainInvalidSize()
    {
        $this->expectException(InvalidSizeException::class);
        $image = Image::createFromFile(__DIR__ . '/files/original/favicon.png');
        $image->contain(new Size(0, 132), 'red');

    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws InvalidSizeException
     * @throws WrongFormatException
     */
    public function testOptimizeLosslessContainBackground2()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/favicon.png');
        $image->contain(new Size(90, 132), 'red');
        $image->optimizeLossless();
        $image->writeImage($this->root->url() .'/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/favicon_contain_background_90_132.png',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws InvalidSizeException
     * @throws WrongFormatException
     */
    public function testOptimizeLosslessContainFromSvg()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/eroulette.svg', 1000);
        $image->contain(new Size(152, 152));
        $image->optimizeLossLess();
        $image->writeImage($this->root->url() .'/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/eroulette_contain_152_152.png',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws InvalidSizeException
     * @throws WrongFormatException
     */
    public function testOptimizeLosslessContainFromSvg2()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/eroulette.svg', 1000);
        $image->contain(new Size(30, 50));
        $image->optimizeLossLess();
        $image->writeImage($this->root->url() .'/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/eroulette_contain_30_50.png',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws InvalidSizeException
     * @throws WrongFormatException
     */
    public function testOptimizeLosslessContainFromSvg3()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/eroulette.svg', 1000);
        $image->contain(new Size(50, 30), ' blue');
        $image->optimizeLossLess();
        $image->writeImage($this->root->url() .'/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/eroulette_contain_50_30.png',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws InvalidSizeException
     * @throws WrongFormatException
     */
    public function testColorOverlay()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/amanda.svg', 1000);
        $image->colorOverlay('red');
        $image->contain(new Size(152, 152), ' transparent');
        $image->optimizeLossLess();
        $image->writeImage($this->root->url() .'/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/amanda_152_152.png',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws InvalidSizeException
     * @throws WrongFormatException
     */
    public function testColorOverlay2()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/amanda.svg', 1000);
        $image->colorOverlay('green');
        $image->contain(new Size(16, 16), ' transparent');
        $image->optimizeLossLess();
        $image->writeImage($this->root->url() .'/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/amanda_16_16.png',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws InvalidSizeException
     * @throws WrongFormatException
     */
    public function testColorOverlay3()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/amanda.svg', 1000);
        $image->colorOverlay('blue');
        $image->contain(new Size(30, 50), ' yellow');
        $image->optimizeLossLess();
        $image->writeImage($this->root->url() .'/out.png');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/amanda_30_50.png',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ConvertingSvgException
     * @throws ImagickException
     * @throws InvalidImageException
     * @throws InvalidSizeException
     */
    public function testStrangeHwFile()
    {
        try {
            $image = Image::createFromFile(__DIR__ . '/files/original/hw.svg', 1000);
            $image->contain(new Size(30, 50));
            $image->writeImage($this->root->url() . '/out.png');
            //To generate the initial file
            //copy($this->root->url() . '/hw.png', __DIR__ . '/files/expected/hw_30_50.png');

        } catch ( WrongFormatException $e) {
            $this->fail($e->getMimeType());
        }



        $this->assertImageEquals(
            __DIR__ . '/files/expected/hw_30_50.png',
            $this->root->url() .'/out.png'
        );
    }

    /**
     * @throws ImagickException
     * @throws ConvertingSvgException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testOptimizeEnhanceDocument()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/mindprint.jpg');
        $image->enhanceDocument();
        $image->optimizeDocument();

        $image->writeImage($this->root->url() .'/out.jpg');

        $this->assertImageEquals(
            __DIR__ . '/files/expected/mindprint.jpg',
            $this->root->url() .'/out.jpg'
        );
    }

    /**
     * @throws ConvertingSvgException
     * @throws ImagickException
     * @throws InvalidImageException
     * @throws WrongFormatException
     */
    public function testGetBlob()
    {
        $image = Image::createFromFile(__DIR__ . '/files/original/mindprint.jpg');
        $this->assertEquals($image->getImagickImage()->getImageBlob(), $image->getBlob());
    }

}