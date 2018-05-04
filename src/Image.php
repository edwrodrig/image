<?php
declare(strict_types=1);

namespace edwrodrig\image;

use Imagick;

class Image
{
    /**
     * @var Imagick
     */
    private $imagick;

    /**
     * Image constructor.
     * Construct a Image optimizer based on imagick
     * @param Imagick $imagick
     */
    public function __construct(Imagick $imagick) {
        $this->imagick = $imagick;
    }

    /**
     * Creates a image from a filename.
     *
     * It handles png, jpg and svg format nicely.
     * This is useful when you can't known the type of the image before.
     * @param string $filename
     * @param int $svg_width
     * @return Image
     * @throws \ImagickException
     * @throws exception\ConvertingSvgException
     * @throws exception\WrongFormatException
     */
    public static function createFromFile(string $filename, int $svg_width = 1000) {
        $type = mime_content_type($filename);

        if ($type === 'image/png' || $type === 'image/jpeg') {
            $img = new Imagick;
            $img->setBackgroundColor(new \ImagickPixel("transparent"));
            $img->readImage($filename);
            return new Image($img);
        } else if ($type === 'image/svg+xml' ) {
            $img = self::loadSvg($filename, $svg_width);
            return new Image($img);
        } else {
            throw new exception\WrongFormatException($filename);
        }
    }

    /**
     * Returns a trimmed version of the image trimmed to transparent pixels.
     *
     * This function uses svgToPng internally.
     * @see Image::svgToPng()
     * @param string $filename
     * @param int $svg_width
     * @return Imagick
     * @throws \ImagickException
     * @throws exception\ConvertingSvgException
     */
    public static function loadSvg(string $filename, int $svg_width = 1000) : Imagick {
        $png_filename = self::svgToPng($filename, $svg_width);
        $img = new Imagick;
        $img->setBackgroundColor("transparent");
        $img->readImage($png_filename);
        unlink($png_filename);

        $img->setImageBackgroundColor('transparent');
        //to trim transparent area of the image add a transparent border and then trim it
        $img->borderImage('transparent', $img->getImageWidth() + 2, $img->getImageHeight() + 2);
        $img->trimImage(0);

        return $img;
    }

    /**
     * Scales the image
     * @see Imagick::scaleImage
     * @param int $width
     * @param int $height
     * @return Image
     * @throws \ImagickException
     */
    public function scaleImage(int $width, int $height) : Image {
        $this->imagick->scaleImage($width, $height, true, true);
        return $this;
    }


    public static function check_svg_converter() : bool {
        exec('rsvg-convert --help',$output, $return_var);
        return $return_var == 0;
    }

    /**
     * Create a super low sized thumbnail.
     *
     * This thumbnail is in grayscale, very blurry and in very bad quality.
     * The purpose of this thumbnail is to create a hint of the complete image that can be stored in a column in a database.
     * Generally has <1Kb size.
     * @param int $columns
     * @param int $rows
     * @return Image
     * @throws \ImagickException
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
     * Set the chroma subsampling to 4:2:0.
     *
     * @return $this
     * @see https://en.wikipedia.org/wiki/Chroma_subsampling
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
     */
    public function optimizeLossless() : Image {
        $this->imagick->stripImage();

        return $this;
    }

    /**
     * Optimize the image for photo output.
     *
     * This is used when you need photos and images without transparency.
     * @return Image
     */
    public function optimizePhoto() : Image {
        $this->imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $this->setOptimizedChromaSubSampling();
        $this->imagick->setImageCompressionQuality(75);
        $this->imagick->stripImage();
        return $this;
    }

    /**
     * Applies a color overlay to the image.
     *
     * It is used to colorize white silhouettes. For example, a white square is colorized to a red square.
     * Internally creates a full color image and the use compositeImage with COMPOSITE_COPYOPACITY
     * @see Imagick::compositeImage()
     * @see Imagick::COMPOSITE_COPYOPACITY
     * @param string $color
     * @return Image
     * @throws \ImagickException
     */
    public function colorOverlay(string $color) {
        $overlay = new Imagick;
        $overlay->newImage($this->imagick->getImageWidth(), $this->imagick->getImageHeight(), $color);
        $overlay->compositeImage($this->imagick, Imagick::COMPOSITE_COPYOPACITY, 0, 0);
        $this->imagick = $overlay;
        return $this;
    }

    /**
     * Makes the image to cover a rectangle.
     *
     * The dimension that exceeds the rectangle area will be centered.
     * If the width or height are 0 makes to scale the image according the defined dimension
     * @param Size $cover_area
     * @return Image
     * @throws \ImagickException
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
     * @param Size $contain_area
     * @param string $background_color
     * @return Image
     * @throws \ImagickException
     * @throws exception\InvalidSizeException
     */
    public function contain(Size $contain_area, $background_color = 'transparent') : Image {
        if ( $contain_area->isAreaEmpty() ) {
            throw new exception\InvalidSizeException($contain_area);
        }

        $original_size = Size::createFromImagick($this->imagick);
        $scaled_area = $original_size->getScaledByContainArea($contain_area);

        $this->imagick->scaleImage($scaled_area->getWidth(), $scaled_area->getHeight(), true, false);

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
     * Write the image to a file.
     * @param string $filename
     * @return string
     */
    public function writeImage(string $filename) : string {
        $this->imagick->writeImage($filename);
        return $filename;
    }

    /**
     * Convert a svg to a image.
     *
     * The svg_width is the tentative width of the target png image. It's recommended to put a big value needed to scale down.
     * Because the vectorial nature of SVG, there is are not clear dimension values so the converters try to guess.
     * Sometimes the guess is a bit small so the picture is in very low resolution.
     *
     * This function use rsvg-convert internally. In ubuntu you can install it with `sudo apt install librsvg2-bin`
     * @param string $svg_filename
     * @param int $svg_width
     * @return bool|string
     * @throws exception\ConvertingSvgException
     */
    public static function svgToPng(string $svg_filename, int $svg_width)
    {
        $tempnam_in = tempnam(sys_get_temp_dir(), "IN");
        $tempnam_out = tempnam(sys_get_temp_dir(), "OUT");
        file_put_contents($tempnam_in, file_get_contents($svg_filename));

        //need to create a png output of the file
        exec(
            sprintf("rsvg-convert %s -f png --keep-aspect-ratio -w %s -o %s", $tempnam_in, $svg_width, $tempnam_out),
            $output,
            $return_var
        );

        unlink($tempnam_in);

        if ($return_var !== 0) {
            unlink($tempnam_out);
            throw new exception\ConvertingSvgException(implode("\n", $output));
        }

        if ( mime_content_type($tempnam_out) !== 'image/png' ) {
            unlink($tempnam_out);
            throw new exception\ConvertingSvgException('Output is not a svg file');
        }

        return $tempnam_out;
    }

    /**
     * Get the imagick image object.
     *
     * Use at your own risk. Just use read only methods.
     * @return Imagick
     */
    public function getImagickImage() : Imagick {
        return $this->imagick;
    }

}