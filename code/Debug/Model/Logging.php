<?php

/**
 * Class Sheep_Debug_Model_Logging
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Model_Logging
{
    protected $files = array();
    protected $ranges = array();


    public function addFile($filename)
    {
        $this->files[] = $filename;
    }


    public function getFiles()
    {
        return $this->files;
    }


    /**
     * @param  string $logFile
     * @param  string $start
     * @param int     $end
     */
    public function addRange($logFile, $start, $end = 0)
    {
        $this->ranges[$logFile] = array(
            'start' => $start,
            'end'   => 0
        );
    }


    public function getRange($logFile)
    {
        if (!array_key_exists($logFile, $this->ranges)) {
            throw new Exception('Invalid log file');
        }

        return $this->ranges[$logFile];
    }


    public function getLogFilePath($filename)
    {
        return Mage::getBaseDir('var') . DS . 'log' . DS . $filename;
    }


    public function startRequest()
    {
        foreach ($this->files as $logFile) {
            $logFilePath = $this->getLogFilePath($logFile);
            $this->ranges[$logFile] = array(
                'start' => $this->getLastFilePosition($logFilePath),
                'end'   => 0
            );
        }
    }


    public function endRequest()
    {
        foreach ($this->files as $logFile) {
            $logFilePath = $this->getLogFilePath($logFile);
            $this->ranges[$logFile]['end'] = $this->getLastFilePosition($logFilePath);
        }
    }


    /**
     * Returns logged content for each log file
     *
     * @return array
     * @throws Exception
     */
    public function getLogging()
    {
        $logging = array();

        foreach ($this->files as $logFile) {
            $logging[$logFile] = $this->getLoggedContent($logFile);
        }

        return $logging;
    }


    /**
     * Returns current end position for specified file path
     *
     * @param $filePath
     * @return int
     */
    public function getLastFilePosition($filePath)
    {
        $f = fopen($filePath, 'r');
        fseek($f, -1, SEEK_END);
        return ftell($f);
    }


    /**
     * Returns content added during this request for specified log file
     *
     * @param string $logFile
     * @return string
     * @throws Exception
     */
    public function getLoggedContent($logFile)
    {
        if (!array_key_exists($logFile, $this->ranges)) {
            throw new Exception('Invalid log file');
        }

        return $this->getContent(
            $this->getLogFilePath($logFile),
            $this->ranges[$logFile]['start'],
            $this->ranges[$logFile]['end']
        );
    }


    /**
     * Returns number of lines added in specified log from start of this request
     *
     * @param $logFile
     * @return int
     * @throws Exception
     */
    public function getLineCount($logFile)
    {
        $content = $this->getLoggedContent($logFile);
        return substr_count($content, "\n");
    }


    /**
     * Returns content from specified file between specified range
     *
     * @param string $filePath
     * @param int    $startPosition
     * @param int    $endPosition
     * @return string
     */
    public function getContent($filePath, $startPosition, $endPosition)
    {
        // End position not saved yet
        if (!$endPosition) {
            return trim(file_get_contents($filePath, null, null, $startPosition));
        }

        // End position exists but is less then start position
        if ($endPosition <= $startPosition) {
            return '';
        }

        return trim(file_get_contents($filePath, null, null, $startPosition, $endPosition = $startPosition));
    }

}
