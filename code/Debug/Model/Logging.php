<?php

/**
 * Class Sheep_Debug_Model_Logging
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Model_Logging
{
    protected $files = array();
    protected $ranges = array();
    protected $logLineCount;


    /**
     * Adds log file that will be monitored during request.
     * Filename should be relative to global var/log directory.
     *
     * @param string $filename
     */
    public function addFile($filename)
    {
        $this->files[] = $filename;
    }


    /**
     * Returns monitored log files
     *
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }


    /**
     * Adds position range for log file.
     * Position range represents a range that describe what logs were added during current requests.
     *
     * @param string $logFile
     * @param string $start
     * @param int $end
     */
    public function addRange($logFile, $start, $end = 0)
    {
        $this->ranges[$logFile] = array(
            'start' => $start,
            'end'   => $end
        );
    }


    /**
     * Returns position range for specified log file
     *
     * @param string $logFile
     * @return array
     * @throws Exception
     */
    public function getRange($logFile)
    {
        if (!array_key_exists($logFile, $this->ranges)) {
            throw new Exception('Invalid log file');
        }

        return $this->ranges[$logFile];
    }


    /**
     * Returns absolute path for specified log file.
     * Log file is considered to be relative to global log directory.
     *
     * @param string $filename
     * @return string
     */
    public function getLogFilePath($filename)
    {
        return Mage::getBaseDir('var') . DS . 'log' . DS . $filename;
    }


    /**
     * Initiates start range for all registered files
     */
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


    /**
     * Records end range for all registered log files
     */
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
     * @param string $filePath
     * @return int
     */
    public function getLastFilePosition($filePath)
    {
        if (!file_exists($filePath)) {
            return 0;
        }

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
     * @param string $logFile
     * @return int
     * @throws Exception
     */
    public function getLineCount($logFile)
    {
        $content = $this->getLoggedContent($logFile);
        return substr_count($content, "\n");
    }

    /**
     * Returns number of log lines added in all of registered logs
     *
     * @return int
     */
    public function getTotalLineCount()
    {
        if ($this->logLineCount === null) {
            $this->logLineCount = 0;
            foreach ($this->getFiles() as $log) {
                $this->logLineCount += $this->getLineCount($log);
            }
        }

        return $this->logLineCount;
    }


    /**
     * Returns content from specified file between specified range
     *
     * @param string $filePath
     * @param int $startPosition
     * @param int $endPosition
     * @return string
     */
    public function getContent($filePath, $startPosition, $endPosition)
    {
        if (!file_exists($filePath)) {
            return '';
        }

        // End position not saved yet
        if (!$endPosition) {
            return trim(file_get_contents($filePath, null, null, $startPosition));
        }

        // End position exists but is less then start position
        if ($endPosition <= $startPosition) {
            return '';
        }

        return trim(file_get_contents($filePath, null, null, $startPosition, $endPosition - $startPosition));
    }

}
