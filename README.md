edwrodrig\image 
========
Library to create optimized images and thumbnails for web, and compare images.

[![Latest Stable Version](https://poser.pugx.org/edwrodrig/image/v/stable)](https://packagist.org/packages/edwrodrig/image)
[![Total Downloads](https://poser.pugx.org/edwrodrig/image/downloads)](https://packagist.org/packages/edwrodrig/image)
[![License](https://poser.pugx.org/edwrodrig/image/license)](https://packagist.org/packages/edwrodrig/image)

## My use cases

 * Make a area contained thumbnail.
 * Create a area cover thumbnail.
 * Load SVG files transparently.
 * Optimize for web to meet the [PageSpeed Insights](https://developers.google.com/speed/pagespeed/insights) suggestions about [optimizing images](https://developers.google.com/speed/docs/insights/OptimizeImages).
 * Create small preview thumbnails of about <1Kb suitable for database columns.
 * Compare images to detect duplicates.

## Dependencies
It needs __rsvg-convert__ to convert svg images nicely. Also need __compare__ to make image comparisons.
You can install these dependencies in Ubuntu 16.04 with the following commands.
```
sudo apt install librsvg2 imagemagick
```

## Documentation
The source code is documented using [phpDocumentor](http://docs.phpdoc.org/references/phpdoc/basic-syntax.html) style,
so it should pop up nicely if you're using IDEs like [PhpStorm](https://www.jetbrains.com/phpstorm) or similar.

### Examples

* [Create a super thumbnail](https://github.com/edwrodrig/image/blob/master/examples/create_super_thumbnail.php)

  <img width="100" alt="Original" src="https://raw.githubusercontent.com/edwrodrig/image/master/tests/files/original/ssj.png">
  ⇨
  <img alt="Target" src="https://raw.githubusercontent.com/edwrodrig/image/master/tests/files/expected/ssj_thumb.jpg">

  
    

## Composer
```
composer require edwrodrig/image
```

## Testing
The test are built using PhpUnit. It generates images and compare the signature with expected ones. Maybe some test fails due metadata of some generated images, but at the moment I haven't any reported issue.

## License
MIT license. Use it as you want at your own risk.



