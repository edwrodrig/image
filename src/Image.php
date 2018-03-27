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
        $img->scaleImage($columns, $rows , \Imagick::FILTER_GAUSSIAN , 1.5);
        $img->setImageCompression(\Imagick::COMPRESSION_JPEG);
        $img->setImageType(\Imagick::IMGTYPE_GRAYSCALE);
        $img->setImageFormat('jpeg');
        $img->gaussianBlurImage(0, 1);
        $img->setSamplingFactors(['2x2', '1x1', '1x1']);
        $img->setImageCompressionQuality(40);
        $img->stripImage();

        return $img;
    }


    public static function optimize(string $filename) : Imagick
    {
        $img = new Imagick($filename);
        if ($img->getFormat() === 'png') {
            $img->stripImage();
        } else if ($img->getFormat() === 'jpeg') {
            $img->setSamplingFactors(['2x2', '1x1', '1x1']);
            $img->setImageCompressionQuality(75);
            $img->stripImage();
        }

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