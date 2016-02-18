<?php

/**
 * Class Sheep_Debug_Helper_Filter
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Helper_Filter extends Mage_Core_Helper_Abstract
{
    const DEFAULT_LIMIT_VALUE = 10;

    protected $requestFilterValues;

    /**
     * Returns available filter names
     */
    public function getFilterParams()
    {
        return array('ip', 'method', 'path', 'token', 'start', 'limit', 'session_id');
    }


    /**
     * Returns an assoc array with filter and if its value from request.
     * Filters missing from request parameters are ignored.
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return array
     */
    public function getRequestFilters(Mage_Core_Controller_Request_Http $request)
    {
        if (!$this->requestFilterValues) {
            $filters = $this->getFilterParams();
            $this->requestFilterValues = array();

            foreach ($filters as $filter) {
                $param = $request->getParam($filter, null);
                if ($param !== null) {
                    $this->requestFilterValues[$filter] = $param;
                }
            }
        }

        return $this->requestFilterValues;
    }

    /**
     * Returns accepted values for http method filter
     *
     * @return array
     */
    public function getHttpMethodValues()
    {
        return array(
            'DELETE', 'GET', 'HEAD', 'PATCH', 'POST', 'PUT'
        );
    }


    /**
     * Returns default value for limit filter
     *
     * @return int
     */
    public function getLimitDefaultValue()
    {
        return self::DEFAULT_LIMIT_VALUE;
    }


    /**
     * Returns available values for limit filter
     *
     * @return array
     */
    public function getLimitValues()
    {
        return array(10, 50, 100);
    }

}
