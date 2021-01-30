<?php

/**
 * Class with utilities and common functions
 * @author Diego Tejada <diegotejadav@gmail.com>
 */
class Util
{
    const INPUT_FILES_PATH = __DIR__ . "/../files/input";
    const OUTPUT_FILES_PATH = __DIR__ . "/../files/output";

    public function __construct() {}

    /**
     * Returns all the .txt files of each drone
     * @return Array $files the list with the file names
     */
    public static function getInputFiles()
    {
        $drones = [];
        $totalReadedFiles = 0;
        $config = include(__DIR__ . '/../config/default.php');

        if ($handle = opendir(static::INPUT_FILES_PATH)) {
            while (false !== ($fileName = readdir($handle)) && $totalReadedFiles < $config['max_drones']) {
                if ($fileName != "." && $fileName != "..") {
                    $drones[$fileName] = file(static::INPUT_FILES_PATH . "/" . $fileName, FILE_IGNORE_NEW_LINES);
                    $totalReadedFiles++;
                }
            }        
            closedir($handle);
        }

        return $drones;
    }

    /**
     * Generates a txt file with the result of the deliveries of a drone
     */
    public static function saveResults($fileName, $results)
    {
        file_put_contents(static::OUTPUT_FILES_PATH . '/' . $fileName, implode(PHP_EOL, $results));
    }

    /**
     * Generates the output filename
     * @return String
     */
    public static function outputFilename($fileName)
    {
        return str_replace('in', 'out', $fileName);
    }
}

?>