<?php

/**
 * Class Sheep_Debug_Model_Resource_RequestInfo_Collection
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Model_Resource_RequestInfo_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('sheep_debug/requestInfo');
    }


    /**
     * Filters request profiles by session id.
     * We capture encrypted session id. @see \Sheep_Debug_Model_Controller::init
     *
     * @param string $sessionId
     * @return Sheep_Debug_Model_Resource_RequestInfo_Collection
     */
    public function addSessionIdFilter($sessionId)
    {
        return $this->addFieldToFilter('session_id', $sessionId);
    }


    /**
     * Filters request profiles by specified token.
     *
     * @param string $token
     * @return Sheep_Debug_Model_Resource_RequestInfo_Collection
     */
    public function addTokenFilter($token)
    {
        return $this->addFieldToFilter('token', $token);
    }


    /**
     * Filters request profiles by HTTP method
     *
     * @param string $method
     * @return Sheep_Debug_Model_Resource_RequestInfo_Collection
     */
    public function addHttpMethodFilter($method)
    {
        return $this->addFieldToFilter('http_method', $method);
    }


    /**
     * Filters request profiles with request path containing specified value
     *
     * @param string $requestPath
     * @return Sheep_Debug_Model_Resource_RequestInfo_Collection
     */
    public function addRequestPathFilter($requestPath)
    {
        return $this->addFieldToFilter('request_path', array('like' => '%' . $requestPath . '%'));
    }


    /**
     * Filters requests profile that had specified response code
     *
     * @param int $responseCode
     * @return Sheep_Debug_Model_Resource_RequestInfo_Collection
     */
    public function addResponseCodeFilter($responseCode)
    {
        return $this->addFieldToFilter('response_code', (int)$responseCode);
    }


    /**
     * Filters requests profile that had requests initiated for specified ip
     *
     * @param string $ip
     * @return Sheep_Debug_Model_Resource_RequestInfo_Collection
     */
    public function addIpFilter($ip)
    {
        return $this->addFieldToFilter('ip', $ip);
    }


    /**
     * Filters requests that were processed before specified date
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
     * Filters requests that were processed after specified date
     *
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
