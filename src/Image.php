<?php
declare(strict_types=1);

namespace edwrodrig\image;

use Imagick;
use ImagickException;
use ImagickPixel;

/**
 * Class Image.
 *
 * A class to convert optimized images and manipulate them.
 * To use it you can create a instance with the {@see Image::__construct() constructor} passing a {@see Imagick} object,
 * or {@see Image::createFromFile creating} one directly from a file.
 *
 * Then you can transform it with some of methods provided, checkout the following ones:
 *  - {@see Image::makeSuperThumbnail() make a super thumbnail}.
 *  - {@see Image::cover() cover an area with an image}.
 *  - {@see Image::contain() contain an image inside an area}.
 *  - {@see Image::optimizePhoto() make a optimized jpg for photos}.
 *  - {@see Image::optimizeDocument() make a optimized jpg for documents}.
 *  - {@see Image::enhanceDocument() enhance the colors of a document}.
 *
 * Finally you can {@see Image::writeImage() write the image} to a file
 * @package edwrodrig\image
 * @api
 */
class Image
{
    private Imagick $imagick;

    /**
     * Image constructor.
     * Construct a Image optimizer based on imagick
     * @api
     * @param Imagick $imagick
     */
    protected function __construct(Imagick $imagick) {
        $this->imagick = $imagick;
    }

    /**
     * Trim transparent pixels.
     *
     * I HATE THIS IMPLEMENTATION!!
     * IF YOU KNOW A MORE ELEGANT TO DO THE SAME PLEASE CONTACT A DEVELOPER.
     * @return $this
     */
    public function trimTransparentPixels() : Image {
        $this->imagick->setImageBackgroundColor('transparent');
        //to trim transparent area of the image add a transparent border and then trim it because trimImage function uses the top left pixels and it could lead to undesired results
        $this->imagick->borderImage('transparent', $this->imagick->getImageWidth() + 2, $this->imagick->getImageHeight() + 2);
        $this->imagick->trimImage(0);
        return $this;
    }

    /**
     * Creates a image from a filename.
     *
     * It handles png, jpg and svg format nicely.
     * This is useful when you can't known the type of the image before.
     *
     * @api
     * @param string $filename
     * @param int $svg_width For more information see this {@see SvgConverter::setWidth() these information}
     * @uses SvgConverter To process SVG files
     * @return Image
     * @throws ImagickException
     * @throws exception\ConvertingSvgException
     * @throws exception\InvalidImageException
     * @throws exception\WrongFormatException
     */
    public static function createFromFile(string $filename, int $svg_width = 1000) : Image {
        $type = mime_content_type($filename);

        if ($type === 'image/png' || $type === 'image/jpeg') {
            $img = new Imagick;
            $img->setBackgroundColor(new ImagickPixel("transparent"));
            $img->readImage($filename);
            return new Image($img);
        } else if ($type === 'image/svg+xml' || $type === 'image/svg' ) {
            $converter = new SvgConverter;
            $converter->setWidth($svg_width);

            $imagick = new Imagick($converter->convert($filename));
            unlink($converter->getOutputFilename());
            $image = new Image($imagick);
            $image->trimTransparentPixels();
            return $image;
        } else {
            throw new exception\WrongFormatException($filename, $type);
        }
    }

    /**
     * Create and image element from image blob.
     * It is just a wrapper function for {@see Imagick::readImageBlob()}
     *
     * @param string $imageBlob
     * @return Image
     * @throws ImagickException
     */
    public static function createFromBlob(string $imageBlob) : Image {
        $imagick = new Imagick;
        $imagick->readImageBlob($imageBlob);
        return new Image($imagick);
    }

    /**
     * Scales the image.
     * @uses Imagick::scaleImage() To scale the image
     * @api
     * @param int $width
     * @param int $height
     * @return Image
     * @throws ImagickException
     */
    public function scaleImage(int $width, int $height) : Image {
        $this->imagick->scaleImage($width, $height, true, true);
        return $this;
    }

    /**
     * Create a super low sized thumbnail.
     *
     * This thumbnail is in grayscale, very blurry and in very bad quality.
     * The purpose of this thumbnail is to create a hint of the complete image that can be stored in a column in a database.
     * Generally has <1Kb size.
     *
     * @api
     * @see https://github.com/edwrodrig/image/blob/master/examples/create_super_thumbnail.php An example
     * @param int $columns
     * @param int $rows
     * @return Image
     * @throws ImagickException
     */
    public function makeSuperThumbnail(int $columns, int $rows) : Image {
        $this->imagick->scaleImage($columns, $rows , true , true);
        $this->imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
        $this->imagick->setImageType(Imagick::IMGTYPE_GRAYSCALE);
        $this->setOptimizedChromaSubSampling();
        $this->imagick->gaussianBlurImage(0, 1);
        $this->imagick->setImageCompressionQuality(40);
        $this->imagick->stripImage();

        return $this;
    }

    /**
     * Set the {@see https://en.wikipedia.org/wiki/Chroma_subsampling chroma subsampling} to 4:2:0.
     *
     * @api
     * @return $this
     */
    public function setOptimizedChromaSubSampling() : Image
    {
        /* The ImageMagick sampling factors for 4:2:0 */
        $sampling_factors = ['2x2', '1x1', '1x1'];
        $this->imagick->setSamplingFactors($sampling_factors);
        return $this;
    }

    /**
     * Optimize the image for lossless output.
     *
     * This function just strip the image.
     * @return Image
     * @api
     */
    public function optimizeLossless() : Image {
        $this->imagick->stripImage();

        return $this;
    }

    /**
     * Optimize the image for photo output.
     *
     * This is used when you need photos and images without transparency.
     * Uses a 75% quality and chroma subsampling
     * @api
     * @return Image
     */
    public function optimizePhoto() : Image {
        $this->imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
        $this->setOptimizedChromaSubSampling();
        $this->imagick->setImageCompressionQuality(75);
        $this->imagick->stripImage();
        return $this;
    }

    /**
     * Optimize the image for document output
     *
     * This is used when you need document files with lineart or diagrams.
     * Uses a 80% quality and interlacing
     * @see Image::enhanceDocument()
     * @api
     * @return $this
     */
    public function optimizeDocument() : Image {
        $this->imagick->setImageFormat('jpg');
        $this->imagick->setInterlaceScheme(Imagick::INTERLACE_PLANE);
        $this->imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
        $this->imagick->setImageCompressionQuality(80);
        $this->imagick->stripImage();
        return $this;
    }

    /**
     * Enhance the image for document output
     *
     * This image does something similar to CamScanner filter to enhance document.
     * It makes the paper near to plain white and improves the contras of lines and shaped with more vivid colors.
     * It tries to emulate the following imagemagicks command:
     * ´´´
     * convert input.jpg -normalize -level 10%,90% -contrast output.png
     * ´´´
     *
     * @api
     * @see Image::optimizeDocument()
     * @return $this
     */
    public function enhanceDocument() : Image {
        $this->imagick->normalizeImage();
        $quantum = $this->imagick->getQuantum();
        $gamma = $this->imagick->getImageGamma();


        $this->imagick->levelImage($quantum * 0.1, $gamma, $quantum * 0.9);

        $this->imagick->contrastImage(true);
        return $this;
    }

    /**
     * Optimize according the mime type.
     *
     * This is useful when you don't know which conversion is better due the original format of the image
     * @see Image::optimizePhoto()
     * @see Image::optimizeLossless()
     * @api
     * @return Image
     */
    public function optimize() : Image {
        if ( $this->imagick->getImageMimeType() === 'image/jpeg' ) {
            $this->optimizePhoto();
        } else {
            $this->optimizeLossless();
        }
        return $this;
    }

    /**
     * Rotate image clockwise 90 degrees
     *
     * @api
     * @return $this
     */
    public function rotateClockwise() : Image {
        $this->imagick->rotateImage(new ImagickPixel(), 90);
        return $this;
    }

    /**
     * Applies a color overlay to the image.
     *
     * It is used to colorize white silhouettes. For example, a white square is colorized to a red square.
     * Internally creates a full color image and the use {@see Imagick::compositeImage()} with {@see Imagick::COMPOSITE_COPYOPACITY}
     * @api
     * @param string $color
     * @return Image
     */
    public function colorOverlay(string $color) : Image {
        $overlay = new Imagick;
        $overlay->newImage($this->imagick->getImageWidth(), $this->imagick->getImageHeight(), $color);
        $overlay->compositeImage($this->imagick, Imagick::COMPOSITE_COPYOPACITY, 0, 0);
        $this->imagick = $overlay;
        return $this;
    }

    /**
     * Crop an image
     *
     * It is useful to transform with dpi and other units.
     * @api
     * @param float $x left coordinate
     * @param float $y top coordinate
     * @param float $width width area to crop
     * @param float $height height area to crop
     * @param float|int $resolution how many pixels per unit, if you use inches then use here the dpi
     * @return $this
     */
    public function cropImage(float $x, float $y, float $width, float $height, float $resolution = 1.0) {
        $x_px = intval(ceil($x * $resolution));
        $y_px = intval(ceil($y * $resolution));
        $width_px = intval(ceil($width * $resolution));
        $height_px = intval(ceil($height * $resolution));

        $this->imagick->cropImage($width_px, $height_px, $x_px, $y_px);

        return $this;
    }

    /**
     * Makes the image to cover a rectangle.
     *
     * The dimension that exceeds the rectangle area will be centered.
     * If the width or height are 0 makes to scale the image according the defined dimension
     * @api
     * @param Size $cover_area
     * @return Image
     * @throws ImagickException
     */
    public function cover(Size $cover_area) : Image {
        if ( $cover_area->isAreaEmpty() ) {
            $this->imagick->scaleImage($cover_area->getWidth(), $cover_area->getHeight());
        } else {

            $original_size = Size::createFromImagick($this->imagick);
            $scaled_area = $original_size->getScaledByCoverArea($cover_area);

            $this->imagick->scaleImage($scaled_area->getWidth(), $scaled_area->getHeight(), true, false);

            $this->imagick->cropImage(
                $cover_area->getWidth(),
                $cover_area->getHeight(),
                $scaled_area->getCenteredLeft($cover_area),
                $scaled_area->getCenteredTop($cover_area)
            );
        }
        return $this;
    }

    /**
     * Makes the image to be contained in a rectangle.
     *
     * The dimension that does not exceed the rectangle area will be centered.
     * @api
     * @param Size $contain_area
     * @param string $background_color
     * @return Image
     * @throws ImagickException
     * @throws exception\InvalidSizeException
     */
    public function contain(Size $contain_area, $background_color = 'transparent') : Image {
        $this->containResize($contain_area);

        $scaled_area = Size::createFromImagick($this->imagick);

        $this->imagick->setImageBackgroundColor($background_color);
        $this->imagick->extentImage(
            $contain_area->getWidth(),
            $contain_area->getHeight(),
            $scaled_area->getCenteredLeft($contain_area),
            $scaled_area->getCenteredTop($contain_area)
        );
        return $this;
    }

    /**
     * Makes the image to be contained in a rectangle but not filling the rectanble with blank space
     *
     * It is the behavior of {@see Imagick::scaleImage()}
     * @api
     * @see Image::contain()
     * @param Size $contain_area
     * @return Image
     * @throws ImagickException
     * @throws exception\InvalidSizeException
     */
    public function containResize(Size $contain_area) : Image {
        if ( $contain_area->isAreaEmpty() ) {
            throw new exception\InvalidSizeException($contain_area);
        }

        $original_size = Size::createFromImagick($this->imagick);
        $scaled_area = $original_size->getScaledByContainArea($contain_area);

        $this->imagick->scaleImage($scaled_area->getWidth(), $scaled_area->getHeight(), true, false);

        return $this;
    }

    /**
     * Write the image to a file.
     * @api
     * @param string $filename
     * @return string
     */
    public function writeImage(string $filename) : string {
        $this->imagick->setImageFilename($filename);

        //need to force a fopen because unaccepted streams, like vfs://
        $file = fopen($filename, 'w+');
            $this->imagick->writeImageFile($file);
        fclose($file);
        return $filename;
    }

    /**
     * Get the image raw data as a string.
     * @api
     * @return string
     */
    public function getBlob() : string {
        return $this->imagick->getImageBlob();
    }

    /**
     * Get the imagick image object.
     *
     * @internal Use at your own risk. Just use read only methods.
     * @return Imagick
     */
    public function getImagickImage() : Imagick {
        return $this->imagick;
    }


    public static function normalizeOrientation(Imagick $image) : Imagick {
        switch ($image->getImageOrientation()) {
            case Imagick::ORIENTATION_TOPLEFT:
                break;
            case Imagick::ORIENTATION_TOPRIGHT:
                $image->flopImage();
                break;
            case Imagick::ORIENTATION_BOTTOMRIGHT:
                $image->rotateImage("#000", 180);
                break;
            case Imagick::ORIENTATION_BOTTOMLEFT:
                $image->flopImage();
                $image->rotateImage("#000", 180);
                break;
            case Imagick::ORIENTATION_LEFTTOP:
                $image->flopImage();
                $image->rotateImage("#000", -90);
                break;
            case Imagick::ORIENTATION_RIGHTTOP:
                $image->rotateImage("#000", 90);
                break;
            case Imagick::ORIENTATION_RIGHTBOTTOM:
                $image->flopImage();
                $image->rotateImage("#000", 90);
                break;
            case Imagick::ORIENTATION_LEFTBOTTOM:
                $image->rotateImage("#000", -90);
                break;
            default: // Invalid orientation
                break;
        }
        $image->setImageOrientation(Imagick::ORIENTATION_TOPLEFT);
        return $image;
    }

}