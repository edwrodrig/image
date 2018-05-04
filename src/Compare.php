<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 04-05-18
 * Time: 0:04
 */

namespace edwrodrig\image;


class Compare
{
    /**
     * Run a raw compare command.
     *
     * The return value is a float value from 0 to 1 where 0 is most similar and 1 is dissimilar.
     * Compare comes with the ImageMagick package
     * @see http://www.imagemagick.org/Usage/compare/#statistics
     * @param string $filename_1
     * @param string $filename_2
     * @return float
     * @throws \Exception
     */
    public static function runCompareCommand(string $filename_1, string $filename_2) : float {
        $command = sprintf('compare -dissimilarity-threshold 1 -metric RMSE %s %s /tmp/out 2>&1', $filename_1, $filename_2);

        $result = exec($command, $output, $return);
        if ( strpos($result, 'images too dissimilar') !== FALSE ) return 1.0;

        if ( preg_match('/[-+]?[0-9]*\.?[0-9]* \(([-+]?[0-9]*\.?[0-9]*)\)/', $result, $matches) ) {
            $number = filter_var(trim($matches[1]), FILTER_VALIDATE_FLOAT);
            if ( $number !== FALSE ) return $number;
        }
        throw new \Exception('ERROR_COMPARING_FILES');
    }

    /**
     * Compare two images.
     *
     * This command convert images in a small thumbnail and then compare it
     * @param string $filename_1
     * @param string $filename_2
     * @return float|mixed
     * @throws \ImagickException
     * @throws exception\ConvertingSvgException
     * @throws exception\InvalidSizeException
     * @throws exception\WrongFormatException
     */
    public static function compare(string $filename_1, string $filename_2) : float{
        $image_1 = Image::createFromFile($filename_1, 1000);
        $image_1->contain(new Size(200, 200));
        $image_1->makeSuperThumbnail(200, 200);
        $filename_1 = $image_1->writeImage('/tmp/cp1');

        $image_2 = Image::createFromFile($filename_2, 1000);
        $image_2->contain(new Size(200, 200));
        $image_2->makeSuperThumbnail(200, 200);
        $filename_2 = $image_2->writeImage('/tmp/cp2');


        return self::runCompareCommand($filename_1, $filename_2);
    }
}