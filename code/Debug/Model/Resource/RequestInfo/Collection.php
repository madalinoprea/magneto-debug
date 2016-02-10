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
     * @param string $sessionId
     * @return Sheep_Debug_Model_Resource_RequestInfo_Collection
     */
    public function addSessionIdFilter($sessionId)
    {
        return $this->addFieldToFilter('session_id', $sessionId);
    }

    /**
     * @param string $token
     * @return Sheep_Debug_Model_Resource_RequestInfo_Collection
     */
    public function addTokenFilter($token)
    {
        return $this->addFieldToFilter('token', $token);
    }

    /**
     * @param string $method
     * @return Sheep_Debug_Model_Resource_RequestInfo_Collection
     */
    public function addHttpMethodFilter($method)
    {
        return $this->addFieldToFilter('http_method', $method);
    }

    /**
     * @param string $requestPath
     * @return Sheep_Debug_Model_Resource_RequestInfo_Collection
     */
    public function addRequestPathFilter($requestPath)
    {
        return $this->addFieldToFilter('request_path', array('like' => '%' . $requestPath . '%'));
    }

    /**
     * @param int $code
     * @return Sheep_Debug_Model_Resource_RequestInfo_Collection
     */
    public function addResponseCodeFilter($code)
    {
        return $this->addFieldToFilter('response_code', $code);
    }

    /**
     * @param string $ip
     * @return Sheep_Debug_Model_Resource_RequestInfo_Collection
     */
    public function addIpFilter($ip)
    {
        return $this->addFieldToFilter('ip', $ip);
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

    /**
     * @param string $date
     * @return Sheep_Debug_Model_Resource_RequestInfo_Collection
     */
    public function addAfterFilter($date)
    {
        return $this->addFieldToFilter('date', array(
            'from' => $date,
            'datetime' => true
        ));
    }


}
