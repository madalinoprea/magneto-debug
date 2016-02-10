<?php

/**
 * Class Sheep_Debug_Block_Config
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Block_Config extends Sheep_Debug_Block_Panel
{

    public function isVisible()
    {
        return $this->helper->isPanelVisible('config');
    }


    public function getMagentoVersion()
    {
        return Mage::getVersion();
    }


    public function isDeveloperMode()
    {
        return Mage::getIsDeveloperMode();
    }


    public function getExtensionStatus()
    {
        $status = array();

        $extensions = $this->helper->getExtensionRequirements();
        foreach ($extensions as $extension) {
            $status [$extension] = extension_loaded($extension);
        }

        return $status;
    }


    public function getCurrentStore()
    {
        $currentStore = $this->_getApp()->getStore();
        return sprintf("%s / %s", $currentStore->getWebsite()->getName(),  $currentStore->getName());

    }
}
