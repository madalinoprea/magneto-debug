<?php

/**
 * Class Sheep_Debug_Block_Util
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

    public function isTemplateHintsEnabled()
    {
        return (bool)Mage_Core_Block_Template::getShowTemplateHints();
    }

    public function getEnableTemplateHintsUrl()
    {
        return Mage::helper('sheep_debug/url')->getEnableTemplateHintsUrl();
    }

    public function getDisableTemplateHintsUrl()
    {
        return Mage::helper('sheep_debug/url')->getDisableTemplateHintsUrl();
    }

    public function hasFullPageCache()
    {
        return $this->helper->isMagentoEE();
    }

    public function isFPCDebugEnabled()
    {
        return (bool)Mage::getStoreConfig('system/page_cache/debug');
    }

    public function getEnableFPCDebugUrl()
    {
        return Mage::helper('sheep_debug/url')->getEnableFPCDebugUrl();
    }

    public function getDisableFPCDebugUrl()
    {
        return Mage::helper('sheep_debug/url')->getDisableFPCDebugUrl();
    }

    public function isTranslateInlineEnable()
    {
        return (bool)Mage::getSingleton('core/translate_inline')->isAllowed();
    }

    public function getEnableTranslateUrl()
    {
        return Mage::helper('sheep_debug/url')->getEnableTranslateUrl();
    }

    public function getDisableTranslateUrl()
    {
        return Mage::helper('sheep_debug/url')->getDisableTranslateUrl();
    }

    public function isSqlProfilerEnabled()
    {
        return $this->helper->getSqlProfiler()->getEnabled();
    }

    public function isVarienProfilerEnabled()
    {
        return $this->helper->canEnableVarienProfiler();
    }

    public function getEnableSqlProfilerUrl()
    {
        return Mage::helper('sheep_debug/url')->getEnableSqlProfilerUrl();
    }

    public function getDisableSqlProfilerUrl()
    {
        return Mage::helper('sheep_debug/url')->getDisableSqlProfilerUrl();
    }

    public function getEnableVarienProfilerUrl()
    {
        return Mage::helper('sheep_debug/url')->getEnableVarienProfilerUrl();
    }

    public function getDisableVarienProfilerUrl()
    {
        return Mage::helper('sheep_debug/url')->getDisableVarienProfilerUrl();
    }

}
