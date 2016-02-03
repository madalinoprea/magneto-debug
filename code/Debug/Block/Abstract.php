<?php
class Sheep_Debug_Block_Abstract extends Mage_Core_Block_Template
{
    /** @var Sheep_Debug_Helper_Data */
    protected $helper;


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


    public function getDefaultStoreId(){
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
     * Returns info about current request
     *
     * TODO: Return specified request info
     *
     * @return Sheep_Debug_Model_RequestInfo
     */
    public function getRequestInfo()
    {
        return Mage::getSingleton('sheep_debug/observer')->getRequestInfo();
    }
}
