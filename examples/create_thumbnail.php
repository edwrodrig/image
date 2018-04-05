<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 26-03-18
 * Time: 16:16
 */

use edwrodrig\image\Image;

include_once __DIR__ . '/../vendor/autoload.php';

$img = Image::create_thumbnail(__DIR__ . '/sources/goku.jpg', 100, 100);
$img->writeImage(__DIR__ . '/goku_thumb.jpg');


$img = Image::optimize(__DIR__ . '/sources/goku.jpg');
$img->scaleImage(500, 200, \Imagick::FILTER_LANCZOS, 1);
$img->writeImage(__DIR__ . '/goku_del.jpg');


$img = Image::create_thumbnail(__DIR__ . '/sources/ssj.png', 100, 100);
$img->writeImage(__DIR__ . '/ssj_thumb.jpg');


$img = Image::optimize(__DIR__ . '/sources/ssj.png');
$img->scaleImage(500, 200, \Imagick::FILTER_LANCZOS, 1);
$img->writeImage(__DIR__ . '/ssj_del.png');

$img = Image::create_thumbnail(__DIR__ . '/sources/dgz.svg', 100, 100);
$img->writeImage(__DIR__ . '/dgz_thumb.jpg');

$img = Image::optimize(__DIR__ . '/sources/dgz.svg');
$img->scaleImage(500, 500, \Imagick::FILTER_LANCZOS, 1);
$img->writeImage(__DIR__ . '/dgz_del.png');

$img = Image::optimize(__DIR__ . '/sources/dgz.svg');
$img->scaleImage(1500, 1500, \Imagick::FILTER_LANCZOS, 1);
$img->writeImage(__DIR__ . '/dgz_del_2.png');

$img = Image::optimize(__DIR__ . '/sources/browser.svg', 500);
//$img->scaleImage(500, 500, \Imagick::FILTER_LANCZOS, 1);
$img->writeImage(__DIR__ . '/browser_del.png');

$img = Image::optimize(__DIR__ . '/sources/ssj.png');
$img = Image::cover($img, 200, 200);
//$img = Image::cover($img, 200, 200);
//$img = Image::cover($img, 900, 1328);
$img->writeImage(__DIR__ . '/ssj_cover.png');

$img = Image::optimize(__DIR__ . '/sources/ssj.png');
$img = Image::contain($img, 200, 200, 'red');
//$img = Image::contain($img, 900, 1328);
$img->writeImage(__DIR__ . '/ssj_contain.png');


$img = Image::optimize(__DIR__ . '/sources/favicon.png');
$img = Image::contain($img, 20, 20, 'red');
//$img = Image::contain($img, 900, 1328);
$img->writeImage(__DIR__ . '/favicon_contain.png');

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

$img = Image::optimize(__DIR__ . '/sources/eroulette.svg');
$img = Image::contain($img, 152, 152, 'transparent');
//$img = Image::contain($img, 900, 1328);
$img->writeImage(__DIR__ . '/eroulette.png');
