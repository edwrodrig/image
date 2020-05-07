<?php
declare(strict_types=1);

use edwrodrig\image\Image;

include_once __DIR__ . '/../vendor/autoload.php';

//Open some document image
$image = Image::createFromFile(__DIR__ . '/../tests/files/original/mindprint.jpg');


//Enhance image for document
$image->enhanceDocument();
//Optimize the size
$image->optimizeDocument();

//Write it somewhere
$image->writeImage(__DIR__ . '/out.jpg');



