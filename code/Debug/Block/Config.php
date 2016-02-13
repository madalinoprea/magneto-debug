<?php

/**
 * Class Sheep_Debug_Block_Config
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Block_Config extends Sheep_Debug_Block_Panel
{

    public function getMagentoVersion()
    {
        return Mage::helper('sheep_debug/config')->getMagentoVersion();
    }


    public function isDeveloperMode()
    {
        return $this->helper->getIsDeveloperMode();
    }


    public function getExtensionStatus()
    {
        return Mage::helper('sheep_debug/config')->getExtensionStatus();
    }


    public function getCurrentStore()
    {
        $currentStore = $this->_getApp()->getStore();
        return sprintf("%s / %s", $currentStore->getWebsite()->getName(),  $currentStore->getName());
    }

}
