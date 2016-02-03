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

    public function _getViewVars() {
        return $this->_viewVars;
    }

    public function getShowTemplateHints()
    {
        return false;
    }

    /**
     * TODO: use a request info model
     * @return Sheep_Debug_Model_Observer
     */
    public function getRequestInfo()
    {
        return Mage::getSingleton('sheep_debug/observer');
    }
}
