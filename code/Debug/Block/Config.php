<?php

/**
 * Class Sheep_Debug_Block_Config
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Block_Config extends Sheep_Debug_Block_Panel
{

    /**
     * Returns version for Magento
     *
     * @return string
     */
    public function getMagentoVersion()
    {
        return Mage::helper('sheep_debug/config')->getMagentoVersion();
    }


    /**
     * Checks if Magento Developer Mode is enabled
     *
     * @return bool
     */
    public function isDeveloperMode()
    {
        return $this->helper->getIsDeveloperMode();
    }


    /**
     * Returns an array with statuses for PHP extensions required by Magento
     *
     * @return array
     */
    public function getExtensionStatus()
    {
        return Mage::helper('sheep_debug/config')->getExtensionStatus();
    }


    /**
     * Returns a string representation for current store (website name and store name)
     *
     * @return string
     */
    public function getCurrentStore()
    {
        $currentStore = $this->_getApp()->getStore();
        return sprintf('%s / %s', $currentStore->getWebsite()->getName(),  $currentStore->getName());
    }

}
