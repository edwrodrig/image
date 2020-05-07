<?php
declare(strict_types=1);

use edwrodrig\image\Image;

include_once __DIR__ . '/../vendor/autoload.php';

//Open some PNG

$image = Image::createFromFile(__DIR__ . '/../tests/files/original/ssj.png');


//Create a supper thumbnail of size 100x100
$image->makeSuperThumbnail(100, 100);

//Write it somewhere
$image->writeImage(__DIR__ . '/out.jpg');



