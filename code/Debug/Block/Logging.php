<?php

/**
 * Class Sheep_Debug_Block_Logging
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Block_Logging extends Sheep_Debug_Block_Panel
{
    protected $logLineCount = null;

    /**
     * @return Sheep_Debug_Model_Logging
     */
    public function getLogging()
    {
        return $this->getRequestInfo()->getLogging();
    }


    /**
     * Returns an array with all registered log file names
     *
     * @return array
     */
    public function getLogFiles()
    {
        return $this->getLogging()->getFiles();
    }

}
