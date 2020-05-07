edwrodrig\image 
========
Library to create optimized images and thumbnails for web, and compare images.

[![Latest Stable Version](https://poser.pugx.org/edwrodrig/image/v/stable)](https://packagist.org/packages/edwrodrig/image)
[![Total Downloads](https://poser.pugx.org/edwrodrig/image/downloads)](https://packagist.org/packages/edwrodrig/image)
[![License](https://poser.pugx.org/edwrodrig/image/license)](https://packagist.org/packages/edwrodrig/image)
[![Build Status](https://travis-ci.org/edwrodrig/image.svg?branch=master)](https://travis-ci.org/edwrodrig/image)
[![codecov.io Code Coverage](https://codecov.io/gh/edwrodrig/image/branch/master/graph/badge.svg)](https://codecov.io/github/edwrodrig/image?branch=master)
[![Code Climate](https://codeclimate.com/github/edwrodrig/image/badges/gpa.svg)](https://codeclimate.com/github/edwrodrig/image)


## My use cases

 * Make a area contained thumbnail.
 * Create a area cover thumbnail.
 * Load SVG files transparently.
 * Optimize for web to meet the [PageSpeed Insights](https://developers.google.com/speed/pagespeed/insights) suggestions about [optimizing images](https://developers.google.com/speed/docs/insights/OptimizeImages).
 * Create small preview thumbnails of about <1Kb suitable for database columns.
 * Compare images to detect duplicates.
 * Enhance document images (diagrams, text, lineart)
 
 My infrastructure is targeted to __Ubuntu 16.04__ machines with last __php7.4__ installed from [ppa:ondrej/php](https://launchpad.net/~ondrej/+archive/ubuntu/php)

### Examples

* [Create a super thumbnail](https://github.com/edwrodrig/image/blob/master/examples/create_super_thumbnail.php)

  <img width="200" alt="Original" src="https://raw.githubusercontent.com/edwrodrig/image/master/tests/files/original/ssj.png">
  ⇨
  <img alt="Target" src="https://raw.githubusercontent.com/edwrodrig/image/master/tests/files/expected/ssj_thumb.jpg">

* [Enhance document](https://github.com/edwrodrig/image/blob/master/examples/enhance_document.php)

  <img width="250" alt="Original" src="https://raw.githubusercontent.com/edwrodrig/image/master/tests/files/original/mindprint.jpg">
  ⇨
  <img width="250" alt="Target" src="https://raw.githubusercontent.com/edwrodrig/image/master/tests/files/expected/mindprint.jpg">

* Contain

  <img width="200" alt="Original" src="https://raw.githubusercontent.com/edwrodrig/image/master/tests/files/original/ssj.png">
  ⇨
  <img alt="Target" src="https://raw.githubusercontent.com/edwrodrig/image/master/tests/files/expected/ssj_contain_200_200.png"> 
```
$image->contain(new Size(200, 200));
```
* Cover

  <img width="200" alt="Original" src="https://raw.githubusercontent.com/edwrodrig/image/master/tests/files/original/ssj.png">
  ⇨
  <img alt="Target" src="https://raw.githubusercontent.com/edwrodrig/image/master/tests/files/expected/ssj_cover_200_200.png"> 
```
$image->cover(new Size(200, 200));
```    
* Cover with automatic width

  <img width="200" alt="Original" src="https://raw.githubusercontent.com/edwrodrig/image/master/tests/files/original/ssj.png">
  ⇨
  <img alt="Target" src="https://raw.githubusercontent.com/edwrodrig/image/master/tests/files/expected/ssj_cover_0_90.png"> 
```
$image->cover(new Size(0, 90));
```
* Cover with automatic height

  <img width="200" alt="Original" src="https://raw.githubusercontent.com/edwrodrig/image/master/tests/files/original/ssj.png">
  ⇨
  <img alt="Target" src="https://raw.githubusercontent.com/edwrodrig/image/master/tests/files/expected/ssj_cover_90_0.png"> 
```
$image->cover(new Size(90, 0));
```    


## Composer
```
composer require edwrodrig/image
```

## Dependencies
It needs __rsvg-convert__ to convert svg images nicely. Also need __compare__ to make image comparisons.
You can install these dependencies in Ubuntu 16.04 with the following commands.
```
sudo apt install librsvg2-bin php-imagick
```

## My current system information
Output of [system_info.sh](https://github.com/edwrodrig/image/blob/master/scripts/system_info.sh)
```
  Operating System: Ubuntu 16.04.6 LTS
            Kernel: Linux 4.15.0-99-generic
PHP 7.4.5 (cli) (built: Apr 19 2020 07:36:13) ( NTS )
Copyright (c) The PHP Group
Zend Engine v3.4.0, Copyright (c) Zend Technologies
    with Zend OPcache v7.4.5, Copyright (c), by Zend Technologies
    with Xdebug v2.9.3, Copyright (c) 2002-2020, by Derick Rethans
Version: ImageMagick 6.8.9-9 Q16 x86_64 2019-11-12 http://www.imagemagick.org
Copyright: Copyright (C) 1999-2014 ImageMagick Studio LLC
Features: DPC Modules OpenMP
Delegates: bzlib cairo djvu fftw fontconfig freetype jbig jng jpeg lcms lqr ltdl lzma openexr pangocairo png rsvg tiff wmf x xml zlib

rsvg-convert version 2.40.13
```

## Documentation
The source code is documented using [phpDocumentor](http://docs.phpdoc.org/references/phpdoc/basic-syntax.html) style,
so it should pop up nicely if you're using IDEs like [PhpStorm](https://www.jetbrains.com/phpstorm) or similar.


## Testing
The test are built using PhpUnit. It generates images and compare the signature with expected ones. Maybe some test fails due metadata of some generated images, but at the moment I haven't any reported issue.

## License
MIT license. Use it as you want at your own risk.

## About language
I'm not a native english writer, so there may be a lot of grammar and orthographical errors on text, I'm just trying my best. But feel free to correct my language, any contribution is welcome and for me they are a learning instance.

