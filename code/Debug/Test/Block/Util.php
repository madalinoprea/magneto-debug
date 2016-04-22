<?php

/**
 * Class Sheep_Debug_Test_Block_Util
 *
 * @category Sheep
 * @package  Sheep_Subscription
 * @license  Copyright: Pirate Sheep, 2016
 * @link     https://piratesheep.com
 *
 * @covers Sheep_Debug_Block_Util
 * @codeCoverageIgnore
 */
class Sheep_Debug_Test_Block_Util extends EcomDev_PHPUnit_Test_Case
{

    public function testFlushCacheUrl()
    {
        $helper = $this->getHelperMock('sheep_debug/url', array('getFlushCacheUrl'));
        $helper->expects($this->once())->method('getFlushCacheUrl')->willReturn('cache flush url');
        $this->replaceByMock('helper', 'sheep_debug/url', $helper);

        $block = $this->getBlockMock('sheep_debug/util', array('toHtml'));
        $actual = $block->getFlushCacheUrl();
        $this->assertEquals('cache flush url', $actual);
    }


    /**
     * We check current setting and assume that was not changed. getShowTemplateHints() is always
     * false for our custom blocks.
     */
    public function testIsTemplateHintsEnabled()
    {
        $block = $this->getBlockMock('sheep_debug/util', array('toHtml'));
        $this->assertFalse($block->isTemplateHintsEnabled());
    }


    public function testGetDisableTemplateHintsUrl()
    {
        $helper = $this->getHelperMock('sheep_debug/url', array('getDisableTemplateHintsUrl'));
        $helper->expects($this->once())->method('getDisableTemplateHintsUrl')->willReturn('disable template hints url');
        $this->replaceByMock('helper', 'sheep_debug/url', $helper);

        $block = $this->getBlockMock('sheep_debug/util', array('toHtml'));
        $actual = $block->getDisableTemplateHintsUrl();
        $this->assertEquals('disable template hints url', $actual);
    }

    public function testGetEnableTemplateHintsUrl()
    {
        $helper = $this->getHelperMock('sheep_debug/url', array('getEnableTemplateHintsUrl'));
        $helper->expects($this->once())->method('getEnableTemplateHintsUrl')->willReturn('enable template hints url');
        $this->replaceByMock('helper', 'sheep_debug/url', $helper);

        $block = $this->getBlockMock('sheep_debug/util', array('toHtml'));
        $actual = $block->getEnableTemplateHintsUrl();
        $this->assertEquals('enable template hints url', $actual);
    }


    public function testHasFullPageCache()
    {
        $helper = $this->getHelperMock('sheep_debug', array('isMagentoEE'));
        $helper->expects($this->once())->method('isMagentoEE')->willReturn(true);
        $this->replaceByMock('helper', 'sheep_debug', $helper);

        $block = $this->getBlockMock('sheep_debug/util', array('toHtml'));
        $actual = $block->hasFullPageCache();
        $this->assertTrue($actual);
    }


    /**
     * Assumes config is off
     */
    public function testIsFPCDebugEnabled()
    {
        $block = $this->getBlockMock('sheep_debug/util', array('toHtml'));
        $this->assertFalse($block->isFPCDebugEnabled());
    }


    public function testGetEnableFPCDebugUrl()
    {
        $helper = $this->getHelperMock('sheep_debug/url', array('getEnableFPCDebugUrl'));
        $helper->expects($this->once())->method('getEnableFPCDebugUrl')->willReturn('enable fpc debug url');
        $this->replaceByMock('helper', 'sheep_debug/url', $helper);

        $block = $this->getBlockMock('sheep_debug/util', array('toHtml'));
        $actual = $block->getEnableFPCDebugUrl();
        $this->assertEquals('enable fpc debug url', $actual);
    }

    public function testGetDisableFPCDebugUrl()
    {
        $helper = $this->getHelperMock('sheep_debug/url', array('getDisableFPCDebugUrl'));
        $helper->expects($this->once())->method('getDisableFPCDebugUrl')->willReturn('disable fpc debug url');
        $this->replaceByMock('helper', 'sheep_debug/url', $helper);

        $block = $this->getBlockMock('sheep_debug/util', array('toHtml'));
        $actual = $block->getDisableFPCDebugUrl();
        $this->assertEquals('disable fpc debug url', $actual);
    }


    /**
     * Assumes config is off
     */
    public function testIsTranslateInlineEnable()
    {
        $block = $this->getBlockMock('sheep_debug/util', array('toHtml'));
        $this->assertFalse($block->isTranslateInlineEnable());
    }


    public function testGetEnableTranslateUrl()
    {
        $helper = $this->getHelperMock('sheep_debug/url', array('getEnableTranslateUrl'));
        $helper->expects($this->once())->method('getEnableTranslateUrl')->willReturn('enable translate url');
        $this->replaceByMock('helper', 'sheep_debug/url', $helper);

        $block = $this->getBlockMock('sheep_debug/util', array('toHtml'));
        $actual = $block->getEnableTranslateUrl();
        $this->assertEquals('enable translate url', $actual);
    }


    public function testGetDisableTranslateUrl()
    {
        $helper = $this->getHelperMock('sheep_debug/url', array('getDisableTranslateUrl'));
        $helper->expects($this->once())->method('getDisableTranslateUrl')->willReturn('disable translate url');
        $this->replaceByMock('helper', 'sheep_debug/url', $helper);

        $block = $this->getBlockMock('sheep_debug/util', array('toHtml'));
        $actual = $block->getDisableTranslateUrl();
        $this->assertEquals('disable translate url', $actual);
    }


    public function testIsSqlProfilerEnabled()
    {
        $profiler = $this->getMock('Zend_Db_Profiler', array('getEnabled'));
        $profiler->expects($this->once())->method('getEnabled')->willReturn(true);

        $helper = $this->getHelperMock('sheep_debug', array('getSqlProfiler'));
        $helper->expects($this->once())->method('getSqlProfiler')->willReturn($profiler);
        $this->replaceByMock('helper', 'sheep_debug', $helper);

        $block = $this->getBlockMock('sheep_debug/util', array('toHtml'));
        $this->assertTrue($block->isSqlProfilerEnabled());
    }


    /**
     * Assume is off
     */
    public function testIsVarienProfilerEnabled()
    {
        $helper = $this->getHelperMock('sheep_debug', array('canEnableVarienProfiler'));
        $helper->expects($this->any())->method('canEnableVarienProfiler')->willReturn(false);
        $this->replaceByMock('helper', 'sheep_debug', $helper);

        $block = $this->getBlockMock('sheep_debug/util', array('toHtml'));
        $this->assertFalse($block->isVarienProfilerEnabled());
    }


    public function testGetEnableSqlProfilerUrl()
    {
        $helper = $this->getHelperMock('sheep_debug/url', array('getEnableSqlProfilerUrl'));
        $helper->expects($this->once())->method('getEnableSqlProfilerUrl')->willReturn('enable sql profiler url');
        $this->replaceByMock('helper', 'sheep_debug/url', $helper);

        $block = $this->getBlockMock('sheep_debug/util', array('toHtml'));
        $actual = $block->getEnableSqlProfilerUrl();
        $this->assertEquals('enable sql profiler url', $actual);
    }


    public function testGetDisableSqlProfilerUrl()
    {
        $helper = $this->getHelperMock('sheep_debug/url', array('getDisableSqlProfilerUrl'));
        $helper->expects($this->once())->method('getDisableSqlProfilerUrl')->willReturn('disable sql profiler url');
        $this->replaceByMock('helper', 'sheep_debug/url', $helper);

        $block = $this->getBlockMock('sheep_debug/util', array('toHtml'));
        $actual = $block->getDisableSqlProfilerUrl();
        $this->assertEquals('disable sql profiler url', $actual);
    }


    public function testGetEnableVarienProfilerUrl()
    {
        $helper = $this->getHelperMock('sheep_debug/url', array('getEnableVarienProfilerUrl'));
        $helper->expects($this->once())->method('getEnableVarienProfilerUrl')->willReturn('enable varien profiler url');
        $this->replaceByMock('helper', 'sheep_debug/url', $helper);

        $block = $this->getBlockMock('sheep_debug/util', array('toHtml'));
        $actual = $block->getEnableVarienProfilerUrl();
        $this->assertEquals('enable varien profiler url', $actual);
    }

    public function testGetDisableVarienProfilerUrl()
    {
        $helper = $this->getHelperMock('sheep_debug/url', array('getDisableVarienProfilerUrl'));
        $helper->expects($this->once())->method('getDisableVarienProfilerUrl')->willReturn('disable varien profiler url');
        $this->replaceByMock('helper', 'sheep_debug/url', $helper);

        $block = $this->getBlockMock('sheep_debug/util', array('toHtml'));
        $actual = $block->getDisableVarienProfilerUrl();
        $this->assertEquals('disable varien profiler url', $actual);
    }

    public function testIsDisabledPersistenceCookieOn()
    {
        $helper = $this->getHelperMock('sheep_debug', array('hasDisablePersistenceCookie'));
        $helper->expects($this->any())->method('hasDisablePersistenceCookie')->willReturn(true);
        $this->replaceByMock('helper', 'sheep_debug', $helper);

        $block = $this->getBlockMock('sheep_debug/util', array('toHtml'));
        $this->assertTrue($block->isDisablePersistenceCookieOn());
    }

}

