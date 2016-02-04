<?php

/**
 * Class Sheep_Debug_Block_Blocks
 *
 * @category Sheep
 * @package  Sheep_Debug
 * @license  Copyright: Pirate Sheep, 2016, All Rights reserved.
 * @link     https://piratesheep.com
 */
class Sheep_Debug_Block_Util extends Sheep_Debug_Block_Panel
{
    public function getFlushCacheUrl()
    {
        return Mage::helper('sheep_debug/url')->getFlushCacheUrl();
    }

    public function isTemplateHintsEnable()
    {
        return (bool) Mage_Core_Block_Template::getShowTemplateHints();
    }

    public function getEnableTemplateHintsUrl()
    {
        return Mage::helper('sheep_debug/url')->getEnableTemplateHintsUrl();
    }

    public function getDisableTemplateHintsUrl()
    {
        return Mage::helper('sheep_debug/url')->getDisableTemplateHintsUrl();
    }

    public function isTranslateInlineEnable()
    {
        return (bool) Mage::getSingleton('core/translate_inline')->isAllowed();
    }

    public function getEnableTranslateUrl()
    {
        return Mage::helper('sheep_debug/url')->getEnableTranslateUrl();
    }

    public function getDisableTranslateUrl()
    {
        return Mage::helper('sheep_debug/url')->getDisableTranslateUrl();
    }

}
