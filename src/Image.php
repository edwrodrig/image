<?php
namespace edwrodrig\image;

use Imagick;

class Image
{

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
    public static function create_thumbnail(string $filename, int $columns, int $rows) : Imagick {
        $img = new Imagick($filename);
        $img->setImageFormat('jpeg');
        $img->scaleImage($columns, $rows , \Imagick::FILTER_GAUSSIAN , 1.5);
        $img->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $img->setImageType(\Imagick::IMGTYPE_GRAYSCALE);
        $img->gaussianBlurImage(0, 1);
        $img->setSamplingFactors(['2x2', '1x1', '1x1']);
        $img->setImageCompressionQuality(40);
        $img->stripImage();

        return $img;
    }


    /**
     * Creates an optimized version of a file for web.
     * @param string $filename
     * @param int $svg_width Set the width of the image in the svg. This need to be high or it will be pixelated.
     * @return Imagick
     * @throws \ImagickException
     */
    public static function optimize(string $filename, int $svg_width = 1000) : Imagick
    {
        $type = mime_content_type($filename);

        if ($type === 'image/png') {
            return self::optimize_png($filename);
        } else if ($type === 'image/jpeg') {
            return self::optimize_jpg($filename);
        } else if ($type === 'image/svg+xml' ) {
            return self::optimize_svg($filename, $svg_width);
        } else {
            throw new exception\WrongFormatException($filename);
        }
    }

    public static function optimize_png(string $filename) {
        $img = new Imagick();
        $img->setBackgroundColor(new \ImagickPixel("transparent"));
        $img->readImage($filename);
        $img->stripImage();

        return $img;
    }

    public static function optimize_jpg(string $filename) {
        $img = new Imagick();
        $img->setBackgroundColor(new \ImagickPixel("transparent"));
        $img->readImage($filename);
        $img->setSamplingFactors(['2x2', '1x1', '1x1']);
        $img->setImageCompressionQuality(75);
        $img->stripImage();
        return $img;
    }

    /**
     * Returns a trimmed version of the image trimmed to transparent pixels
     * @param string $filename
     * @param int $svg_width
     * @return Imagick
     * @throws \ImagickException
     */
    public static function optimize_svg(string $filename, int $svg_width = 1000) {
        $tempnam_in = tempnam(sys_get_temp_dir(), "IN");
        $tempnam_out = tempnam(sys_get_temp_dir(), "OUT");
        file_put_contents($tempnam_in, file_get_contents($filename));

        //need to create a png output of the file
        passthru(sprintf("rsvg-convert %s -f png --keep-aspect-ratio -w %s -o %s", $tempnam_in, $svg_width, $tempnam_out));

        $img = new Imagick();
        $img->setBackgroundColor("transparent");
        $img->readImage($tempnam_out);

        $img->setImageBackgroundColor('transparent');
        //to trim transparent area of the image add a transparent border and then trim it
        $img->borderImage('transparent', $img->getImageWidth() + 2, $img->getImageHeight() + 2);
        $img->trimImage(0);
        return $img;
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
     * @param Imagick $img
     * @param int $width
     * @param int $height
     * @return Imagick
     * @throws \ImagickException
     */
    public static function cover(Imagick $img, int $width, int $height) {
        if ( $width == 0 || $height == 0 ) {
            $img->scaleImage($width, $height);
        } else {
            $w = $img->getImageWidth();
            $h = $img->getImageHeight();

            $resize_h_w = $w * $height / $h;
            $resize_h_h = $height;

            $resize_w_w = $width;
            $resize_w_h = $h * $width / $w;



            if ( $resize_h_w > $width ) {
                $resize_w = $resize_h_w;
                $resize_h = $resize_h_h;
            } else {
                $resize_w = $resize_w_w;
                $resize_h = $resize_w_h;
            }

            $img->scaleImage($resize_w, $resize_h, Imagick::FILTER_LANCZOS, 0);

            $img->cropImage($width, $height, ($resize_w - $width) / 2, ($resize_h - $height) / 2);
        }
        return $img;
    }

    /**
     * Makes the image to be contained in a rectangle.
     *
     * The dimension that does not exceed the rectangle area will be centered.
     * @param Imagick $img
     * @param int $width
     * @param int $height
     * @param string $background_color
     * @return Imagick
     * @throws \ImagickException
     * @throws exception\InvalidSizeException
     */
    public static function contain(Imagick $img, int $width, int $height, $background_color = 'transparent') {
        if ( $width == 0 || $height == 0 ) {
            throw new exception\InvalidSizeException($width, $height);
        }
        $w = $img->getImageWidth();
        $h = $img->getImageHeight();

        $resize_h_w = $w * $height / $h;
        $resize_h_h = $height;

        $resize_w_w = $width;
        $resize_w_h = $h * $width / $w;

        if ( $resize_h_w < $width ) {
            $resize_w = $resize_h_w;
            $resize_h = $resize_h_h;
        } else {
            $resize_w = $resize_w_w;
            $resize_h = $resize_w_h;
        }

        $img->scaleImage($resize_w, $resize_h, Imagick::FILTER_LANCZOS, 0);

        $img->setImageBackgroundColor($background_color);
        $img->extentImage($width, $height, ($resize_w - $width) / 2, ($resize_h - $height) / 2);
        return $img;


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