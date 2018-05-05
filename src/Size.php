<?php
declare(strict_types=1);

namespace edwrodrig\image;


use Imagick;

/**
 * Class Size.
 * Useful to specify files.
 * @package edwrodrig\image
 * @api
 */
class Size
{
    /**
     * The width of the image
     * @var int
     */
    private $width;

    /**
     * The height of the image
     * @var int
     */
    private $height;


    /**
     * Size constructor.
     * @api
     * @param int $width the width, must be >=0
     * @param int $height the height, must be >=0
     */
    public function __construct(int $width, int $height) {
        assert($width >= 0, "Width must be positive or 0");
        assert($height >= 0, "Height must be positive or 0");
        $this->width = $width;
        $this->height = $height;
    }

    /**
     * Creates a dimension form a Imagick object.
     *
     * @api
     * @param Imagick $img
     * @return Size
     */
    public static function createFromImagick(Imagick $img) : Size {
        return new self(
            $img->getImageWidth(),
            $img->getImageHeight()
        );
    }

    /**
     * Get the width
     * @api
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * Get the height
     * @api
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * Check if the area is 0, one of the dimension are 0
     * @api
     * @return bool
     */
    public function isAreaEmpty() : bool {
        return $this->width == 0 || $this->height == 0;
    }


    /**
     * Get a scaled size to a new height keeping aspect ratio
     * @api
     * @param int $new_height
     * @return Size
     */
    public function getScaledByHeight(int $new_height) : Size {
        return new Size(
            intval($this->width * $new_height / $this->height),
            $new_height
        );
    }

    /**
     * Get a scaled size to a new width keeping aspect ratio
     * @api
     * @param int $new_width
     * @return Size
     */
    public function getScaledByWidth(int $new_width) : Size {
        return new Size(
            $new_width,
            intval($this->height * $new_width / $this->width)
        );
    }

    /**
     * Get a scaled size in order to cover an area.
     *
     * Always the size is bigger than or equals the cover area
     * @see \tests\edwrodrig\image\SizeTest::testScaleByCoverArea() Tests
     * @api
     * @param Size $cover_area
     * @return Size
     */
    public function getScaledByCoverArea(Size $cover_area) : Size {
            $scaled_by_height = $this->getScaledByHeight($cover_area->getHeight());
            $scaled_by_width = $this->getScaledByWidth($cover_area->getWidth());

            if ( $scaled_by_height->getWidth() > $cover_area->getWidth() )
                return $scaled_by_height;
            else
                return $scaled_by_width;
    }

    /**
     * Get a scaled size in order to contain an area.
     *
     * Always the size is smaller than or equals the contain area.
     * @see \tests\edwrodrig\image\SizeTest::testScaleByContainArea() Tests
     * @api
     * @param Size $contain_area
     * @return Size
     */
    public function getScaledByContainArea(Size $contain_area) : Size {
        $scaled_by_height = $this->getScaledByHeight($contain_area->getHeight());
        $scaled_by_width = $this->getScaledByWidth($contain_area->getWidth());

        if ( $scaled_by_height->getWidth() < $contain_area->getWidth() )
            return $scaled_by_height;
        else
            return $scaled_by_width;
    }

    /**
     * The top coordinate of a image order to appear centered in the y axis
     * @api
     * @see Size::getCenteredLeft()
     * @param Size $size
     * @return int
     */
    public function getCenteredTop(Size $size) : int {
        return intval(($this->height - $size->height) / 2);
    }

    /**
     * The left coordinate of a image order to appear centered in the x axis
     * @api
     * @see Size::getCenteredTop()
     * @param Size $size
     * @return int
     */
    public function getCenteredLeft(Size $size) : int {
        return intval(($this->width - $size->width) / 2);
    }

}