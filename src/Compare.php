<?php
declare(strict_types=1);

namespace edwrodrig\image;


use /** @noinspection PhpInternalEntityUsedInspection */
    edwrodrig\image\util\Util;

/**
 * Class Compare.
 * There are two ways to run a comparison. You can create a object and then use {@see Compare::runCompareCommand()},
 * or you can run the static method {@see Compare::compare()} for a fast comparisson.
 *
 * @api
 * @package edwrodrig\image
 */
class Compare
{
    private $executable = 'compare';

    /**
     * Set the compare executable.
     * Just use when you have a standalone instalation of compare.
     * But I recommend to install it with `sudo apt install imagemagick`
     * @api
     * @param string $executable
     * @return $this
     */
    public function setExecutable(string $executable) : Compare {
        $this->executable = $executable;
        return $this;
    }

    /**
     * Check if compare command exists
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
     * Get the compare command used internally.
     *
     * More info about this command {@see http://www.imagemagick.org/Usage/compare/#statistics here}.
     * @internal Just provided as public for testing and debugging proposes.
     * @param string $filename_1
     * @param string $filename_2
     * @return string
     */
    public function getCompareCommand(string $filename_1, string $filename_2) : string {
        return sprintf('%s -metric RMSE %s %s /dev/null',
            $this->executable,
            $filename_1,
            $filename_2
        );
    }

    /**
     * Run a raw compare command.
     *
     * The return value is a float value from 0 to 1 where 0 is most similar and 1 is dissimilar.
     * Internally runs the command generated by {@see Compare::getCompareCommand()}.
     * For more informations about the result see {@see http://www.imagemagick.org/Usage/compare/#statistics this documantation}.
     * Compare comes with the {@see https://www.imagemagick.org/script/index.php ImageMagick} package
     * @api
     * @uses Compare::getCompareCommand()
     * @param string $filename_1
     * @param string $filename_2
     * @return float
     * @throws exception\CompareCommandException
     */
    public function runCompareCommand(string $filename_1, string $filename_2) : float {
        /** @noinspection PhpInternalEntityUsedInspection */
        $command = Util::runCommand($this->getCompareCommand($filename_1, $filename_2));

        /**
         *  From `man convert`
         *
         *  The  compare  program  returns 2 on error otherwise 0 if the images are
         *  similar or 1 if they are dissimilar.
         */
        if ( $command->getExitCode() === 0 || $command->getExitCode() === 1 ) {
            if (preg_match('/[-+]?[0-9]*\.?[0-9]* \(([-+]?[0-9]*\.?[0-9]*)\)/', $command->getStdErrOrOut(), $matches)) {
                $number = filter_var(trim($matches[1]), FILTER_VALIDATE_FLOAT);
                if ($number !== FALSE) return $number;
                else {
                    /** @noinspection PhpInternalEntityUsedInspection */
                    throw new exception\CompareCommandException($command->getStdErrOrOut());
                }
            }
        }
        /** @noinspection PhpInternalEntityUsedInspection */
        throw new exception\CompareCommandException($command->getStdErrOrOut());
    }

    /**
     * Compare two images.
     *
     * This method convert images in a {@see Image::makeSuperThumbnail small thumbnail} and then {@see Compare::runCompareCommand compare it}.
     * @api
     * @param string $filename_1
     * @param string $filename_2
     * @return float|mixed
     * @throws \ImagickException
     * @throws exception\CompareCommandException
     * @throws exception\ConvertingSvgException
     * @throws exception\InvalidImageException
     * @throws exception\InvalidSizeException
     * @throws exception\WrongFormatException
     */
    public static function compare(string $filename_1, string $filename_2) : float{
        $image_1 = Image::createFromFile($filename_1, 1000);
        $image_1->contain(new Size(200, 200));
        $image_1->makeSuperThumbnail(200, 200);
        $filename_1 = $image_1->writeImage(sys_get_temp_dir() . '/cp1');

        $image_2 = Image::createFromFile($filename_2, 1000);
        $image_2->contain(new Size(200, 200));
        $image_2->makeSuperThumbnail(200, 200);
        $filename_2 = $image_2->writeImage(sys_get_temp_dir() . '/cp2');

        $compare = new Compare;
        return $compare->runCompareCommand($filename_1, $filename_2);
    }
}