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

    public function getDownloadConfigAsXmlUrl()
    {
        return Mage::helper('sheep_debug/url')->getDownloadConfig('xml');
    }

    public function getDownloadConfigAsTextUrl()
    {
        return Mage::helper('sheep_debug/url')->getDownloadConfig('txt');
    }

    public function getSearchConfigUrl()
    {
        return Mage::helper('sheep_debug/url')->getSearchConfigUrl();
    }

}
