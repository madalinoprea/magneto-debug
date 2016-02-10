<?php

class Sheep_Debug_Helper_Filter extends Mage_Core_Helper_Abstract
{
    protected $requestFilterValues;
    const DEFAULT_LIMIT_VALUE = 10;

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
                if ($request->getParam($filter, null) !== null) {
                    $this->requestFilterValues[$filter] = $request->getParam($filter);
                }
            }
        }

        return $this->requestFilterValues;
    }

    public function getHttpMethodValues()
    {
        return array(
            'DELETE', 'GET', 'HEAD', 'PATCH', 'POST', 'PUT'
        );
    }


    public function getLimitDefaultValue()
    {
        return self::DEFAULT_LIMIT_VALUE;
    }


    public function getLimitValues()
    {
        return array(10, 50, 100);
    }

}
