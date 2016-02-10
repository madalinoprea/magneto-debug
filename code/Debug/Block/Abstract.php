<?php

/**
 * Class Sheep_Debug_Block_Abstract
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 *
 * @method setRequestInfo(Sheep_Debug_Model_RequestInfo $requestInfo)
 */
class Sheep_Debug_Block_Abstract extends Mage_Core_Block_Template
{
    /** @var Sheep_Debug_Helper_Data */
    protected $helper;

    /** @var  Sheep_Debug_Model_RequestInfo */
    protected $requestInfo;


    /**
     * Sheep_Debug_Block_Abstract constructor.
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        parent::__construct($args);
        $this->helper = Mage::helper('sheep_debug');
    }

    /**
     * By default we don't cache our blocks
     *
     * @return null
     */
    public function getCacheLifetime()
    {
        return null;
    }


    public function getDefaultStoreId()
    {
        return Mage::app()
            ->getWebsite()
            ->getDefaultGroup()
            ->getDefaultStoreId();
    }


    public function getShowTemplateHints()
    {
        return false;
    }


    /**
     * Returns info attached to this block or returns current request's info
     *
     * TODO: Return specified request info
     *
     * @return Sheep_Debug_Model_RequestInfo
     */
    public function getRequestInfo()
    {
        if ($this->requestInfo === null) {
            $this->requestInfo = Mage::registry('sheep_debug_request_info') ?: Mage::getSingleton('sheep_debug/observer')->getRequestInfo();
        }

        return $this->requestInfo;
    }

    public function getRequestListUrl($filters = array())
    {
        return Mage::helper('sheep_debug/url')->getRequestListUrl();
    }


    public function getLatestRequestViewUrl($panel = null)
    {
        return Mage::helper('sheep_debug/url')->getLatestRequestViewUrl($panel);
    }


    public function getRequestViewUrl($panel = null, $token = null)
    {
        $token = $token ?: $this->getRequestInfo()->getToken();
        return Mage::helper('sheep_debug/url')->getRequestViewUrl($token, $panel);
    }

    /**
     * Returns number formatted based on current locale
     *
     * @param $number
     * @param int $precision
     * @return string
     */
    public function formatNumber($number, $precision = 2)
    {
        return $this->helper->formatNumber($number, $precision);
    }


    /**
     * Returns an option array where evey element has its value and label filled in
     * with elements from passed array
     *
     * @param array $data
     * @return array
     */
    public function getOptionArray(array $data)
    {
        $options = array();

        foreach ($data as $value) {
            $options[] = array('value' => $value, 'label' => $value);
        }

        return $options;
    }


    /**
     * Returns CSS class based on response status code
     *
     * @param int $statusCode
     * @return string
     */
    public function getStatusCodeClass($statusCode)
    {
        $cssClass = 'status-success';
        if ($statusCode > 399) {
            $cssClass = 'status-error';
        } else if ($statusCode > 299) {
            $cssClass = 'status-warning';
        }

        return $cssClass;
    }
}
