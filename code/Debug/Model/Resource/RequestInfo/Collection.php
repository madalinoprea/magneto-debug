<?php

/**
 * Class Sheep_Debug_Model_Resource_RequestInfo_Collection
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Model_Resource_RequestInfo_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('sheep_debug/requestInfo');
    }

    /**
     * Filters requests that were processed before specified data
     *
     * @param string $date Date string using format Y-m-d H
     * @return Sheep_Debug_Model_Resource_RequestInfo_Collection
     */
    public function addEarlierFilter($date)
    {
        return $this->addFieldToFilter('date', array(
            'to'       => $date,
            'datetime' => true,
        ));
    }

}
