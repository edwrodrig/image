<?php
declare(strict_types=1);

namespace edwrodrig\image;

use /** @noinspection PhpInternalEntityUsedInspection */
    edwrodrig\image\util\Util;

/**
 * Class SvgConverter
 * When you are ready you can {@see SvgConverter::convert() convert it providing a source svg file}.
 * Finally you can retrieve the {@see SvgConverter::getOutputFilename() png image filename}
 * @see SvgConverter::setWidth() set the target width of the generated image, this is very important in the output quality.
 * @see SvgConverter::doesExecutableExists() to check if the converter is installed in your system
 * @package edwrodrig\image
 * @api
 */
class SvgConverter
{
    /**
     * @var string
     */
    private $executable = 'rsvg-convert';

    /**
     * @var string
     */
    private $png_output;

    /**
     * @var int
     */
    private $width = 1000;

    /**
     * SvgConverter constructor.
     * @api
     */
    public function __construct() {
        $this->png_output = tempnam(sys_get_temp_dir(), "OUT");
    }

    /**
     * Set rsvg_convert executable
     *
     * In ubuntu you can install it with `sudo apt install librsvg2-bin`
     *
     * @api
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
     *
     * @api
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
     *
     * @api
     * @return bool
     */
    public function doesExecutableExists() : bool {
        $version_command = sprintf('%s --version', $this->executable);
        /** @noinspection PhpInternalEntityUsedInspection */
        if ( $result = Util::runCommand($version_command) ) {
            if ( $result->getExitCode() == 0 )
                return true;
        }
        return false;
    }

    /**
     * Get the rsvg-convert command used internally.
     *
     * @internal Just provided as public for testing and debugging proposes
     * @param string $input_file
     * @return string
     */
    public function getConvertCommand(string $input_file) : string {
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
     * @api
     * @uses Svg::getConvertCommand()
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
        /** @noinspection PhpInternalEntityUsedInspection */
        $result = Util::runCommand($this->getConvertCommand($tempnam_in));

        unlink($tempnam_in);

        if ($result->getExitCode() !== 0) {

            /** @noinspection PhpInternalEntityUsedInspection */
            throw new exception\ConvertingSvgException($result->getStdErrOrOut());
        }

        if ( mime_content_type($this->png_output) !== 'image/png' ) {
            /** @noinspection PhpInternalEntityUsedInspection */
            throw new exception\InvalidImageException($this->png_output);
        }

        return $this->png_output;
    }

    /**
     * Get the converted PNG filename.
     *
     * It's nice to unlink it when is no longer in use.
     * @api
     * @return string
     */
    public function getOutputFilename() : string {
        return $this->png_output;
    }
}