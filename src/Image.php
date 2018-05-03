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
     * Returns a trimmed version of the image trimmed to transparent pixels
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
     * @param string $filename
     * @param int $columns
     * @param int $rows
     * @return Imagick
     * @throws \ImagickException
     */
    public function makeSuperThumbnail(int $columns, int $rows) : Image {
        $this->imagick->scaleImage($columns, $rows , Imagick::FILTER_GAUSSIAN , 1.5);
        $this->imagick->setImageCompression(Imagick::COMPRESSION_JPEG);
        $this->imagick->setImageType(Imagick::IMGTYPE_GRAYSCALE);
        $this->imagick->gaussianBlurImage(0, 1);
        $this->setOptimizedChromaSubSampling();
        $this->imagick->setImageCompressionQuality(40);
        $this->imagick->stripImage();

        return $this;
    }

    /**
     * Set the chroma subsampling to 4:2:0.
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

    public function optimizeLossless() : Image {
        $this->imagick->stripImage();

        return $this;
    }

    public function optimizePhoto() : Image {
        $this->imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $this->setOptimizedChromaSubSampling();
        $this->imagick->setImageCompressionQuality(75);
        $this->imagick->stripImage();
        return $this;
    }

    /**
     * @param Imagick $img
     * @param $color
     * @return Imagick
     * @throws \ImagickException
     */
    public static function color_overlay(Imagick $img, $color) {
        $overlay = new \Imagick();
        $overlay->newImage($img->getImageWidth(), $img->getImageHeight(), $color);
        $overlay->compositeImage($img, Imagick::COMPOSITE_COPYOPACITY, 0, 0);
        return $overlay;
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

            $this->imagick->scaleImage($scaled_area->getWidth(), $scaled_area->getHeight(), Imagick::FILTER_LANCZOS, 0);

            $this->imagick->cropImage(
                $cover_area->getWidth(),
                $cover_area->getHeight(),
                $cover_area->getCenteredLeft($scaled_area),
                $cover_area->getCenteredTop($scaled_area)
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

        $this->imagick->scaleImage($scaled_area->getWidth(), $scaled_area->getHeight(), Imagick::FILTER_LANCZOS, 0);

        $this->imagick->setImageBackgroundColor($background_color);
        $this->imagick->extentImage(
            $contain_area->getWidth(),
            $contain_area->getHeight(),
            $contain_area->getCenteredLeft($scaled_area),
            $contain_area->getCenteredTop($scaled_area)
        );
        return $this;


    }

    /**
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

    public static function compare(string $filename1, string $filename2) {

        $img1 = new Imagick($filename1);
        $img2 = new Imagick($filename2);
        $command = sprintf('compare -dissimilarity-threshold 1 -subimage-search -metric RMSE %s %s /tmp/out 2>&1', $file1, $file2);
        $result = exec($command);
        $items = explode(' ', $result);
        if ( strpos($result, 'images too dissimilar') !== FALSE ) return 99999.9;
        if ( count($items) >= 2 ) {
            $number = filter_var(trim($items[0]), FILTER_VALIDATE_FLOAT);
            if ( $number !== FALSE ) return $number;
        }
        throw new \Exception('ERROR_COMPARING_FILES');
    }

}