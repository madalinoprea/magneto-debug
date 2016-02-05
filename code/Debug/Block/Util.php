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
    public function isVisible()
    {
        return $this->helper->isPanelVisible('util');
    }

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
        return Mage::getStoreConfig('system/page_cache/debug');
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

    public function getEnableSqlProfilerUrl()
    {
        return Mage::helper('sheep_debug/url')->getEnableSqlProfilerUrl();
    }

    public function getDisableSqlProfilerUrl()
    {
        return Mage::helper('sheep_debug/url')->getDisableSqlProfilerUrl();
    }

}
