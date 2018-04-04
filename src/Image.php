<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 26-03-18
 * Time: 15:06
 */

namespace edwrodrig\image;

use Imagick;

class Image
{
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


    public static function optimize(string $filename, int $svg_factor = 1) : Imagick
    {
        $img = new Imagick($filename);
        $type = mime_content_type($filename);

        if ($type === 'image/png') {
            $img->stripImage();
        } else if ($type === 'image/jpeg') {
            $img->setSamplingFactors(['2x2', '1x1', '1x1']);
            $img->setImageCompressionQuality(75);
            $img->stripImage();
        } else if ($type === 'image/svg+xml' ) {
            $resolution = $img->getImageResolution();
            $img->removeImage();
            $img->setResolution($resolution['x'] * $svg_factor, $resolution['y'] * $svg_factor);
            $img->readImage($filename);
        }

        return $img;
    }

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

            $img->resizeImage($resize_w, $resize_h, Imagick::FILTER_LANCZOS, 0.9);

            $img->cropImage($width, $height, ($resize_w - $width) / 2, ($resize_h - $height) / 2);
        }
        return $img;
    }

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

        $img->resizeImage($resize_w, $resize_h, Imagick::FILTER_LANCZOS, 0.9);

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