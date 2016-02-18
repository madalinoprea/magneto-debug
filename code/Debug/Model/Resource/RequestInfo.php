<?php

/**
 * Class Sheep_Debug_Model_Resource_RequestInfo
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Model_Resource_RequestInfo extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('sheep_debug/request_info', 'id');
    }

}
