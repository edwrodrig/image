<?php
/**
 * Created by PhpStorm.
 * User: edwin
 * Date: 04-05-18
 * Time: 15:19
 */

namespace edwrodrig\image;

use edwrodrig\image\util\Util;

class SvgConverter
{
    private $executable = 'rsvg-convert';

    private $png_output;

    private $width = 1000;

    /**
     * SvgConverter constructor.
     * @param string $filename
     */
    public function __construct() {
        $this->png_output = tempnam(sys_get_temp_dir(), "OUT");
    }

    /**
     * Set rsvg_convert executable
     *
     * In ubuntu you can install it with `sudo apt install librsvg2-bin`
     * @param string $executable
     * @return $this
     */
    public function setExecutable(string $executable) : SvgConverter {
       $this->executable = $executable;
       return $this;
    }

    /**
     * The svg_width is the tentative width of the target png image. It's recommended to put a big value needed to scale down.
     * Because the vectorial nature of SVG, there is are not clear dimension values so the converters try to guess.
     * Sometimes the guess is a bit small so the picture is in very low resolution.
     * @param $width
     * @return SvgConverter
     */
    public function setWidth($width) : SvgConverter {
        assert($width > 0 , 'Width must be positive');
        $this->width = $width;
        return $this;
    }

    /**
     * Check if rsvg_convert command exists
     * @return bool
     */
    public function doesExecutableExists() : bool {
        $version_command = sprintf('%s --version', $this->executable);
        if ( $result = Util::runCommand($version_command) ) {
            if ( $result->getExitCode() == 0 )
                return true;
        }
        return false;
    }

    /**
     * Get the rsvg-convert command used internally.
     *
     * Just provided as public for testing and debugging proposes
     * @param string $input_file
     * @return string
     */
    public function getConvertCommand(string $input_file) {
        return sprintf(
            "%s %s -f png --keep-aspect-ratio -w %s -o %s",
            $this->executable,
            $input_file,
            $this->width,
            $this->png_output
        );
    }

    /**
     * Convert a svg to a image.
     *
     * This function use rsvg-convert internally. In ubuntu you can install it with `sudo apt install librsvg2-bin`
     * @param string $filename
     * @return bool|string
     * @throws exception\ConvertingSvgException
     * @throws exception\InvalidImageException
     */
    public function convert(string $filename) : string
    {
        $tempnam_in = tempnam(sys_get_temp_dir(), "IN");
        file_put_contents($tempnam_in, file_get_contents($filename));

        //need to create a png output of the file
        $result = Util::runCommand($this->getConvertCommand($tempnam_in));

        unlink($tempnam_in);

        if ($result->getExitCode() !== 0) {
            throw new exception\ConvertingSvgException($result->getStdErrOrOut());
        }

        if ( mime_content_type($this->png_output) !== 'image/png' ) {
            throw new exception\InvalidImageException($this->png_output);
        }

        return $this->png_output;
    }

    /**
     * Get the converted PNG filename.
     *
     * It's nice to unlink it when is no longer in use.
     * @return string
     */
    public function getOutputFilename() : string {
        return $this->png_output;
    }
}