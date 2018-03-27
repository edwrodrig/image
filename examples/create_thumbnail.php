<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 26-03-18
 * Time: 16:16
 */

use edwrodrig\image\Image;

include_once __DIR__ . '/../vendor/autoload.php';

$img = Image::create_thumbnail(__DIR__ . '/goku.jpg', 100, 100);
$img->writeImage(__DIR__ . '/goku_thumb.jpg');


$img = Image::optimize(__DIR__ . '/goku.jpg');
$img->scaleImage(500, 200, \Imagick::FILTER_LANCZOS, 1);
$img->writeImage(__DIR__ . '/goku_del.jpg');


$img = Image::create_thumbnail(__DIR__ . '/ssj.png', 100, 100);
$img->writeImage(__DIR__ . '/ssj_thumb.jpg');


$img = Image::optimize(__DIR__ . '/ssj.png');
$img->scaleImage(500, 200, \Imagick::FILTER_LANCZOS, 1);
$img->writeImage(__DIR__ . '/ssj_del.png');